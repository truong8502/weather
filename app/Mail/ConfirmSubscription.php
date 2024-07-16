<?php

// app/Mail/ConfirmSubscription.php
namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmSubscription extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function build()
    {
        return $this->view('emails.confirm_subscription')
            ->subject('Xác nhận đăng ký nhận thông tin thời tiết hàng ngày')
            ->with([
                'confirmationUrl' => route('subscription.confirm', $this->subscription->confirmation_token),
            ]);
    }
}
