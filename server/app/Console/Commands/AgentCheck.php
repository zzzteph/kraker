<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agent;
use Carbon\Carbon;

use App\Notifications\AgentDown;
class AgentCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AgentCheck:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $agents = Agent::where('updated_at', '<', Carbon::now()->subHours(1)
            ->toDateTimeString())
            ->get();
        foreach ($agents as $agent)
        {
            $agent->notify(new AgentDown($agent));
        }
        Agent::where('updated_at', '<', Carbon::now()->subHours(1)
            ->toDateTimeString())
            ->delete();

    }
}
