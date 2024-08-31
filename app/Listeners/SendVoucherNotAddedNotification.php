<?php

namespace App\Listeners;

use App\Events\Vouchers\VouchersNotCreated;
use App\Mail\VoucherNotCreatedMail;
use Illuminate\Support\Facades\Mail;

class SendVoucherNotAddedNotification
{
    public function handle(VouchersNotCreated $event): void
    {
        $mail = new VoucherNotCreatedMail($event->vouchers, $event->user);
        Mail::to($event->user->email)->send($mail);
    }
}
