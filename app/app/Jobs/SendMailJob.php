<?php

namespace App\Jobs;

use App\Mail\SendMail;
use App\Models\Attachment;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    private $mailData = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
        $this->mailData['from'] = $email->mail_from;
        $this->mailData['to'] = $email->mail_to;
        $this->mailData['subject'] = $email->subject;
        $this->mailData['message'] = $email->message;
        $this->mailData['options'] = $email->options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $attachments = Attachment::query()->where('email_id', $this->email->id)->pluck('path')->toArray();
        $this->mailData['options']['attachments'] = $attachments;

        $mail = new SendMail(
            $this->mailData['from'],
            $this->mailData['to'],
            $this->mailData['subject'],
            $this->mailData['message'],
            $this->mailData['options']
        );

        Mail::send($mail);

        $this->email->status = 'sent';
        $this->email->save();
    }

    public function failed(\Exception $e = null)
    {
        //handle error
        $mailObj = $this->email;
        $mailObj->status = 'failed';
        $options = $mailObj->options;
        $options['err_msg'] = $e ? $e->getMessage() : 'N/A';
        $mailObj->options = $options;
        $mailObj->email->save();
    }
}
