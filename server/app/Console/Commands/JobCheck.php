<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Job;
use Carbon\Carbon;
class JobCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'JobCheck:cron';

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
        $jobs = Job::where('updated_at', '<', Carbon::now()->subMinutes(5)
            ->toDateTimeString())
            ->where('status', 'running')
            ->get();
        foreach ($jobs as $job)
        {
            $job->agent_id = null;
            $job->errors = $job->errors + 1;
            if ($job->errors < 5) $job->status = 'todo';
            else $job->status = 'error';
            $job->save();

        }

    }
}

