<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\AgentInfo;
use App\Models\AgentInventory;
use App\Models\AgentStats;
use App\Models\Cracked;
use App\Models\Hashlist;
use App\Models\Hashtype;
use App\Models\Inventory;
use App\Models\Job;
use App\Models\Pot;
use App\Models\Task;
use App\Models\TaskChain;
use App\Models\Template;
use App\Models\TemplateSpeedStat;
use Carbon\Carbon;
use App\Http\Controllers\Web\TasksController;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\PasswordsCracked;
use App\Notifications\TaskDone;
class JobsController extends Controller
{

    public function put(Request $req, $id)
    {
        $agent = Agent::where(['id' => $req->input('agent_id') ])
            ->firstOrFail();
        $job = Job::where('id', $id)->firstOrFail();
        if (!$req->has(['outfile', 'potfile', 'speed', 'error']))
        {
            return response()
                ->json('not found', 404);
        }

        if ($req->input('potfile') !== null)
        {
            $potfile = base64_decode($req->input('potfile'));
            $potfileEntries = explode(PHP_EOL, $potfile);
            foreach ($potfileEntries as $entry)
            {
                if (strlen($entry) == 0) continue;
                $pot = Pot::where('hashlist_id', $job
                    ->task_chain
                    ->task
                    ->hashlist_id)
                    ->where('pot_data', $entry)->first();
                if ($pot == null)
                {
                    $pot = new Pot;
                    $pot->pot_data = $entry;
                    $pot->hashlist_id = $job
                        ->task_chain
                        ->task->hashlist_id;
                    $pot->save();

                }

            }
        }
        if ($req->input('outfile') !== null)
        {
            $outfile = base64_decode($req->input('outfile'));
            $outFileEntries = explode(PHP_EOL, $outfile);

            foreach ($outFileEntries as $entry)
            {
                if (strlen($entry) == 0) continue;

                $cracked = Cracked::where('hashlist_id', $job
                    ->task_chain
                    ->task
                    ->hashlist_id)
                    ->where('plain', $entry)->first();
                if ($cracked == null)
                {
                    $cracked = new Cracked;
                    $cracked->plain = $entry;
                    $cracked->hashlist_id = $job
                        ->task_chain
                        ->task->hashlist_id;
                    $cracked->save();
                    $job->cracked = $job->cracked + 1;
                }

            }

        }
        $job->status = 'done';
        if ($req->input('error') !== null)
        {
            $job->errors = $job->errors + 1;
            if ($job->errors < 5) $job->status = 'todo';
            else $job->status = 'error';
        }
        if ($job->status == 'done')
        {
            $job->spend_time = $req->input('time');
            if ($job->cracked > 0) $job->notify(new PasswordsCracked($job));
        }
        $job->agent_id = null;
        $job->save();

        //update task_chain
        $task_chain = TaskChain::where('id', $job
            ->task_chain
            ->id)
            ->first();
        if ($task_chain->progress == 100)
        {
            $task_chain->status = 'done';
            $job->notify(new TaskDone($job));
            $task_chain->save();
        }

        //update tasks
        $task = $job
            ->task_chain->task;
        if ($task->progress == 100)
        {
            $task->status = 'done';
            $job->notify(new TaskDone($job));
            $task->save();
        }

        //need to switch chain
        if ($task
            ->template->type == 'chain')
        {
            if ($task->next_chain !== false)
            {

                $taskChain = new TaskChain;
                $taskChain->task_id = $task->id;
                $template = $task->next_chain;
                $taskChain->template_id = $task
                    ->next_chain->id;
                $taskChain->save();
                //calculate parts for chain
                $parts = $this->parts_calculation($template->id, $task
                    ->hashlist
                    ->id);

                $insertJob = array();
                for ($i = 0;$i <= $template->keyspace;$i += $parts)
                {
                    if ($i >= $template->keyspace) break;
                    array_push($insertJob, array(
                        'task_chain_id' => $taskChain->id,
                        'skip' => $i,
                        'limit' => $parts
                    ));
                }
                Job::insert($insertJob);

            }

        }

        //
        //update actual speed
        if ($req->input('speed') > 0)
        {

            $templateStat = TemplateSpeedStat::where(['agent_id' => $agent->id, 'hashtype_id' => $job
                ->task_chain
                ->task
                ->hashlist->hashtype_id, 'template_id' => $job
                ->task_chain
                ->task
                ->template_id])
                ->first();
            if ($templateStat == null)
            {
                $templateStat = new TemplateSpeedStat;
                $templateStat->agent_id = $agent->id;
                $templateStat->hashtype_id = $job
                    ->task_chain
                    ->task
                    ->hashlist->hashtype_id;
                $templateStat->template_id = $job
                    ->task_chain
                    ->task->template_id;

            }
            $templateStat->speed = $req->input('speed');
            $templateStat->save();
        }

    }

    private function canAgentExecuteTemplateWordlist($agent_id, $wordlist_id, $rule_id)
    {
        if ($this->checkIfAgentHasInventory($agent_id, $wordlist_id))
        {
            if (!$this->checkIfAgentHasInventory($agent_id, $rule_id)) return false;
            return true;
        }
        return false;
    }

    private function checkIfAgentHasInventory($agent_id, $inventory_id)
    {
        //empty inv
        if (is_null($inventory_id)) return true;

        if (AgentInventory::where('agent_id', $agent_id)->where('inventory_id', $inventory_id)->count() > 0) return true;
        return false;
    }

    function calc_charset_size($charset)
    {
        $l = 26;
        $u = 26;
        $d = 10;
        $s = 41;
        $a = $l + $u + $d + $s;
        $b = 256;
        $size = 0;

        //we can met charset only one time
        $bool_l = false;
        $bool_u = false;
        $bool_d = false;
        $bool_s = false;
        $bool_a = false;
        $bool_b = false;

        for ($i = 0;$i < strlen($charset);$i++)
        {
            if ($charset[$i] == '?' && ($i + 1) < strlen($charset))
            {
                switch ($charset[$i + 1])
                {
                    case "l":
                        if (!$bool_l)
                        {
                            $size += $l;
                            $i++;
                            $bool_l = true;
                        }
                    break;
                    case "u":
                        if (!$bool_u)
                        {
                            $size += $u;
                            $i++;
                            $bool_u = true;
                        }
                    break;
                    case "d":
                        if (!$bool_d)
                        {
                            $size += $d;
                            $i++;
                            $bool_d = true;
                        }
                    break;
                    case "s":
                        if (!$bool_s)
                        {
                            $size += $s;
                            $i++;
                            $bool_s = true;
                        }
                    break;
                    case "a":
                        if (!$bool_a)
                        {
                            $size += $a;
                            $i++;
                            $bool_a = true;
                        }
                    break;
                    case "b":
                        if (!$bool_b)
                        {
                            $size += $b;
                            $i++;
                            $bool_b = true;
                        }
                    break;
                    default:
                        return NULL;
                }
            }
            else $size++;
        }
        return $size;
    }

    function keyspace_mask($mask, $charset_1, $charset_2, $charset_3, $charset_4)
    {

        /*
        ? | Charset
        ===+=========
        l | abcdefghijklmnopqrstuvwxyz
        u | ABCDEFGHIJKLMNOPQRSTUVWXYZ
        d | 0123456789
        h | 0123456789abcdef
        H | 0123456789ABCDEF
        s |  !"#$%&'()*+,-./:;<=>?@[\]^_`{|}~
        a | ?l?u?d?s
        b | 0x00 - 0xff
        */

        $l = 26;
        $u = 26;
        $d = 10;
        $s = 33;
        $h = 16;
        $a = $l + $u + $d + $s;
        $b = 256;

        $sizemul = - 1; //size for charset iteration
        $singlesize = 0; //single characters
        $totalsize = 0;
        $charset_1_size = $this->calc_charset_size($charset_1);
        $charset_2_size = $this->calc_charset_size($charset_2);
        $charset_3_size = $this->calc_charset_size($charset_3);
        $charset_4_size = $this->calc_charset_size($charset_4);

        if ($charset_1 != null && $charset_1_size == null) return null;
        if ($charset_2 != null && $charset_2_size == null) return null;
        if ($charset_3 != null && $charset_3_size == null) return null;
        if ($charset_4 != null && $charset_4_size == null) return null;
        for ($i = 0;$i < strlen($mask);$i++)
        {
            if ($mask[$i] == '?' && ($i + 1) < strlen($mask))
            {
                if ($sizemul == - 1) $sizemul = 1;
                switch ($mask[$i + 1])
                {
                    case "l":
                        $sizemul *= $l;
                        $i++;
                    break;
                    case "u":
                        $sizemul *= $u;
                        $i++;
                    break;
                    case "d":
                        $sizemul *= $d;
                        $i++;
                    break;
                    case "s":
                        $sizemul *= $s;
                        $i++;
                    break;
                    case "a":
                        $sizemul *= $a;
                        $i++;
                    break;
                    case "b":
                        $sizemul *= $b;
                        $i++;
                    break;
                    case "h":
                        $sizemul *= $h;
                        $i++;
                    break;
                    case "H":
                        $sizemul *= $h;
                        $i++;
                    break;
                    case "1":
                        $sizemul *= $charset_1_size;
                        $i++;
                    break;
                    case "2":
                        $sizemul *= $charset_2_size;
                        $i++;
                    break;
                    case "3":
                        $sizemul *= $charset_3_size;
                        $i++;
                    break;
                    case "4":
                        $sizemul *= $charset_4_size;
                        $i++;
                    break;
                    default:
                        return NULL;
                }
            }
            else if ($mask[$i] != '?' && $sizemul <= 1 && $singlesize == 0) $singlesize = 1;
        }

        if ($sizemul == 0) return NULL;
        if ($sizemul > 0) $totalsize = $sizemul;
        else $totalsize = $singlesize;
        return $this->check_keyspace($totalsize);
    }

    function check_keyspace($keyspace)
    {
        if ($keyspace > 1000000000000000000 || $keyspace <= 0) return NULL;
        return $keyspace;
    }

    function calculate_real_keyspace($template_id, $hashlist_id)
    {
        $hashlist = Hashlist::where('id', $hashlist_id)->firstOrFail();
        $template = Template::where('id', $template_id)->firstOrFail();
        //salted hash?
        $hashtype = Hashtype::where('id', $hashlist->hashtype_id)
            ->firstOrFail();

        $salted = $hashtype->salted;
        //
        $totalKeyspace = 0;

        if ($template->type == 'mask')
        {
            $totalKeyspace = $this->keyspace_mask($template
                ->content->mask, $template
                ->content->charset1, $template
                ->content->charset2, $template
                ->content->charset3, $template
                ->content
                ->charset4);

            if ($salted == 1)
            {
                $totalKeyspace = $totalKeyspace * $hashlist->count;
            }
        }

        if ($template->type == 'wordlist')
        {

            $wordlist = Inventory::where('id', $template
                ->content
                ->wordlist_id)
                ->firstOrFail();
            $rule = null;
            if ($template
                ->content->rule_id !== null) $rule = Inventory::where('id', $template
                ->content
                ->rule_id)
                ->firstOrFail();

            $totalKeyspace = $template->keyspace;
            if ($rule !== null)
            {
                if ($rule->count != 0) $totalKeyspace = $template->keyspace * $rule->count;
            }
            if ($salted == 1)
            {
                $totalKeyspace = $totalKeyspace * $hashlist->count;
            }
        }
        return $totalKeyspace;

    }

    function calculate_avg_agents_speed($template_id, $hashlist_id)
    {
        $hashlist = Hashlist::where('id', $hashlist_id)->firstOrFail();
        $template = Template::where('id', $template_id)->firstOrFail();
        $agents = Agent::where('enabled', 1)->get();
        $totalSpeed = 0;
        $validAgents = 0;
        foreach ($agents as $agent)
        {
            foreach ($agent->speed_stats as $agent_speed)
            {
                if ($agent_speed->hashtype_id == $hashlist->hashtype_id)
                {
                    if ($template->type == 'wordlist')
                    {
                        if (!$this->canAgentExecuteTemplateWordlist($agent->id, $template
                            ->content->wordlist_id, $template
                            ->content
                            ->rule_id)) continue;
                    }
                    //check if speed for template and hashlist exists
                    $templateStats = TemplateSpeedStat::where(['agent_id' => $agent->id, 'hashtype_id' => $hashlist->hashtype_id, 'template_id' => $template
                        ->id])
                        ->first();
                    if ($templateStats != null)
                    {
                        $totalSpeed += $templateStats->speed;
                    }
                    else
                    {
                        $totalSpeed += $agent_speed->speed;
                    }

                    $validAgents++;
                }
            }
        }
        if ($validAgents == 0) return false;
        return round($totalSpeed / $validAgents);

    }

    public function calculate(Request $req)
    {
        if (!$req->filled(['hashlist_id', 'template_id'])) return response()
            ->json(0);

        $totalKeyspace = $this->calculate_real_keyspace($req->input('template_id') , $req->input('hashlist_id'));
        $avgSpeed = $this->calculate_avg_agents_speed($req->input('template_id') , $req->input('hashlist_id'));

        if ($avgSpeed == false || $avgSpeed == 0) return response()->json(false);

        return response()
            ->json(round($totalKeyspace / $avgSpeed));
    }

    public function parts_calculation($template_id, $hashlist_id)
    {

        $hashlist = Hashlist::where('id', $hashlist_id)->firstOrFail();
        $template = Template::where('id', $template_id)->firstOrFail();

        $totalKeyspace = $this->calculate_real_keyspace($template_id, $hashlist_id);
        $avgSpeed = $this->calculate_avg_agents_speed($template_id, $hashlist_id);
        if ($avgSpeed == false || $avgSpeed == 0) return false;

        $diffKeyspace = round($totalKeyspace / $template->keyspace);
        if ($diffKeyspace <= 1) $diffKeyspace = 1;

        //how many parts can be done in 10 minutes (600 secs)
        $parts = round((300 * $avgSpeed) / $diffKeyspace);

        $parts = min($parts, $template->keyspace);
        return $parts;
    }

}

