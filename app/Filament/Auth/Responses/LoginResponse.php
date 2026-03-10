<?php

namespace App\Filament\Auth\Responses;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Filament::auth()->user();

        if ($user && ! $user->isAdmin()) {
            return redirect()->intended(ClientResource::getUrl('index'));
        }

        return redirect()->intended(Filament::getUrl());
    }
}
