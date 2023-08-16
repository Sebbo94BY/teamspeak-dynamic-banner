<?php

namespace App\Console\Commands\Process;

use App\Models\InstanceProcess;
use Illuminate\Console\Command;

class CleanupDeadPids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:cleanup-dead-pids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up dead process IDs from the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $instance_processes = InstanceProcess::all(['id', 'process_id']);

        $this->info('Checking '.$instance_processes->count().' PIDs from the database...');

        foreach ($instance_processes as $process) {
            if (file_exists("/proc/$process->process_id") and (strpos(file_get_contents("/proc/$process->process_id/cmdline"), 'instance:start-teamspeak-bot') !== false)) {
                $this->info("PID $process->process_id is still active. Skipping.");
                continue;
            }

            if ($process->delete()) {
                $this->warn("PID $process->process_id is not active anymore. Successfully removed it from the database.");
            } else {
                $this->error("PID $process->process_id is not active anymore. Failed to remove it from the database.");
            }
        }
    }
}
