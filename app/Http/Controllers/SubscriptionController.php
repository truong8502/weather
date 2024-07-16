<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $email = $request->input('email');
        if (!$email)
        {
            return response()->json(['success' => false]);
        }
        $token = Str::random(32);

        $subscription = Subscription::create([
            'email' => $email,
            'token' => $token,
        ]);

        Mail::send('emails.confirm_subscription', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Xác nhận đăng ký nhận thông tin thời tiết');
        });

        return response()->json(['success' => true]);
    }

    public function confirmSubscription($token)
    {
        $subscription = Subscription::where('token', $token)->first();
        if ($subscription) {
            $subscription->confirmed = true;
            $subscription->save();
            return view('emails.subscription.confirmed');
        }
        return view('emails.subscription.invalid_token');
    }

    public function unsubscribe(Request $request)
    {
        $email = $request->input('email');
        $subscription = Subscription::where('email', $email)->first();
        if ($subscription) {
            $token = Str::random(32);
            $subscription->unsub_token = $token;
            $subscription->save();

            Mail::send('emails.confirm_unsubscription', ['token' => $token], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Xác nhận hủy đăng ký nhận thông tin thời tiết');
            });

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Email không tồn tại trong hệ thống']);
    }

    public function confirmUnsubscription($token)
    {
        $subscription = Subscription::where('unsub_token', $token)->first();
        if ($subscription) {
            $subscription->delete();
            return view('emails.subscription.unsubscribed');
        }
        return view('emails.subscription.invalid_token');
    }
}
