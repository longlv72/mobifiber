<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use Illuminate\Console\Command;

class SendingMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:insert_data_command';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $data = [
                'count' => 102,
                'created_at'    => date('Y-m-d H:i:s')
            ];
        SendMail::dispatch();
        return Command::SUCCESS;
    }
}
