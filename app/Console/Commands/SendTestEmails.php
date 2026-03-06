<?php

namespace App\Console\Commands;

use App\Enums\ObligationType;
use App\Mail\ObligationDueSoonMail;
use App\Mail\ObligationPresentedMail;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentMethodUpdatedMail;
use App\Mail\PaymentSucceededMail;
use App\Mail\SubscriptionActivatedMail;
use App\Mail\TrialEndingMail;
use App\Mail\WelcomeMail;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendTestEmails extends Command
{
    protected $signature = 'app:send-test-emails {email? : Dirección destino (default: alainttlm@gmail.com)}';

    protected $description = 'Envía todos los correos transaccionales a una dirección para revisión visual';

    public function handle(): int
    {
        $to = $this->argument('email') ?? 'alainttlm@gmail.com';

        // Usuario ficticio para los correos que lo requieren
        $user = new User;
        $user->name = 'Alain Lemus';
        $user->email = $to;
        $user->trial_ends_at = Carbon::now()->addDays(14);

        // Para ObligationPresentedMail la vista accede a:
        //   $obligation->client->user->email  (recipient)
        //   $obligation->client->name
        //   $obligation->type->label()
        //   $obligation->periodLabel()
        //   $obligation->due_date
        //   $obligation->presented_at
        //   $obligation->reference (opcional)
        //
        // Construimos un objeto FiscalObligation real con relaciones inyectadas.
        $fakeClientUser = new \stdClass;
        $fakeClientUser->email = $to;

        $fakeClient = new \stdClass;
        $fakeClient->name = 'Constructora Ejemplo SA de CV';
        $fakeClient->user = $fakeClientUser;

        $obligation = new FiscalObligation;
        $obligation->type = ObligationType::DeclaracionAnualPm;
        $obligation->period_year = 2026;
        $obligation->period_month = 2;
        $obligation->due_date = Carbon::now()->addDays(5);
        $obligation->status = 'presented';
        $obligation->presented_at = Carbon::now();
        $obligation->reference = 'SAT-2026-00123';
        $obligation->setRelation('client', $fakeClient);

        // Para ObligationDueSoonMail la vista accede a:
        //   $obligation->client->name
        //   $obligation->type->label()
        //   $obligation->periodLabel()
        //   $obligation->due_date
        $obligation2 = new FiscalObligation;
        $obligation2->type = ObligationType::IvaMensual;
        $obligation2->period_year = 2026;
        $obligation2->period_month = 2;
        $obligation2->due_date = Carbon::now()->addDays(5);
        $obligation2->status = 'pending';
        $obligation2->setRelation('client', $fakeClient);

        $obligations = collect([$obligation2]);

        $mails = [
            'WelcomeMail' => new WelcomeMail($user),
            'TrialEndingMail' => new TrialEndingMail($user),
            'SubscriptionActivatedMail' => new SubscriptionActivatedMail($user),
            'PaymentSucceededMail' => new PaymentSucceededMail($user, 29900, Carbon::now()->format('d/m/Y')),
            'PaymentFailedMail' => new PaymentFailedMail($user),
            'PaymentMethodUpdatedMail' => new PaymentMethodUpdatedMail($user),
            'ObligationPresentedMail' => new ObligationPresentedMail($obligation),
            'ObligationDueSoonMail' => new ObligationDueSoonMail($user, $obligations, 5),
        ];

        $this->info("Enviando {$to}:");
        $this->newLine();

        foreach ($mails as $name => $mailable) {
            $this->output->write("  → {$name} ... ");
            Mail::to($to)->send($mailable);
            $this->line('<fg=green>✓</>');
        }

        $this->newLine();
        $this->info('Listo. Revisa tu bandeja de entrada (8 correos).');

        return self::SUCCESS;
    }
}
