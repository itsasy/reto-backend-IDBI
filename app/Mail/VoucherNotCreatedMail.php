<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoucherNotCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;
    private array $vouchers;

    public function __construct(array $vouchers, User $user)
    {
        $this->user = $user;
        $this->vouchers = $vouchers;
    }


    public function build(): self
    {
        return $this->view('emails.vouchers_not_created')
            ->with(['comprobantes' => $this->vouchers, 'user' => $this->user]);
    }
}
