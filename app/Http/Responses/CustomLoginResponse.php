<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Auth::user();

        // Hapus url.intended agar tidak terlempar ke panel role lain yang memicu 403 Forbidden
        $request->session()->forget('url.intended');

        if ($user) {
            return match ((int) $user->role_id) {
                1 => redirect()->route('filament.admin.pages.dashboard'),
                2 => redirect()->route('filament.teacher.pages.dashboard'),
                3 => redirect()->route('filament.parent.pages.dashboard'),
                4 => redirect()->route('filament.keamanan.pages.dashboard'),
                default => redirect('/'),
            };
        }

        return redirect('/auth/login');
    }
}
