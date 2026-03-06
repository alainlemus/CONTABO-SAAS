<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Avisa a admins cuyo trial vence en 2 días
Schedule::command('app:notify-trial-ending-users')->dailyAt('09:00');

// Marca obligaciones vencidas — corre cada día a las 00:05
Schedule::command('app:mark-overdue-obligations')->dailyAt('00:05');

// Genera obligaciones del mes anterior — corre el día 1 de cada mes a las 01:00
Schedule::command('app:generate-monthly-obligations')->monthlyOn(1, '01:00');

// Avisa sobre obligaciones que vencen en 3 días — corre cada día a las 09:00
Schedule::command('app:notify-obligations-due-soon')->dailyAt('09:00');

// Genera obligaciones anuales del año anterior — corre el 1 de enero a las 02:00
Schedule::command('app:generate-annual-obligations')->yearlyOn(1, 1, '02:00');
