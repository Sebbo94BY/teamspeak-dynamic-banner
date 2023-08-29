<?php

namespace App\Console\Commands\Process;

use Error;
use Illuminate\Console\Command;

class SendPcntlSignal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:send-signal 
        {pcntl_signal : The PCNTL signal, which you want to send to the `process_id`.}
        {process_id : The process ID (PID), which you want to send the `pcntl_signal`.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the `pcntl_signal` to the `process_id`.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $pcntl_signal = constant($this->argument('pcntl_signal'));
        } catch (Error) {
            $pcntl_signal = (int) $this->argument('pcntl_signal');
        }
        $process_id = (int) $this->argument('process_id');

        if (! file_exists("/proc/$process_id")) {
            $this->error("The process ID `$process_id` does not exist anymore. I can not send any signal to it.");

            return;
        }

        $this->info("Sending the PCNLT signal `$pcntl_signal` to the process ID `$process_id`...");

        if (! posix_kill($process_id, $pcntl_signal)) {
            $this->error('The signal failed.');

            return;
        }

        $this->info('The signal was successful.');
    }
}
