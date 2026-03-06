<?php

namespace App\Jobs;

use App\Mail\ObligationDueSoonMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendObligationDueSoonEmail implements ShouldQueue
{
    use Queueable;

    /** @param  \Illuminate\Support\Collection<int, \App\Models\FiscalObligation>  $obligations */
    public function __construct(
        public User $user,
        public \Illuminate\Support\Collection $obligations,
        public int $daysUntilDue,
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)->send(
            new ObligationDueSoonMail($this->user, $this->obligations, $this->daysUntilDue)
        );
    }
}
