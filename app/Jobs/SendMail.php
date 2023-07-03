<?php

namespace App\Jobs;

use App\Mail\SendingEmailGeneral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("Khởi chạy job lúc: " . date('H:i:s d-m-Y'));
            $check_list_email = DB::table('queue_mail')->where('is_send', 2)->get();
            // Lo
            if ($check_list_email && count($check_list_email) > 0) {
                foreach ($check_list_email as $key => $mail_item) {
                    $title = $mail_item->title ?? "";
                    $content = htmlspecialchars_decode($mail_item->content);
                    $email_target = $mail_item->email;
                    $sending_mail = new SendingEmailGeneral($title, $content);
                    Mail::to($email_target)->send($sending_mail);

                    // if ( !$isEmailSent ) {
                    DB::table('queue_mail')->where('id', $mail_item->id)->update(['is_send' => 1, 'send_at' => date('Y-m-d H:i:s')]);
                    // } else {
                    //     Log::error("Lỗi gửi mail id: " . $mail_item->id );
                    // }
                    Log::info("Đã gửi mail " . $email_target . " lúc " . date('H:i:s d-m-y'));
                }
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage() . " - line: " . $th->getLine() );
        }

    }
}
