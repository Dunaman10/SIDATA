<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectByRole
{
  public function handle(Request $request, Closure $next)
  {
    if (Auth::check()) {
      $user = Auth::user();
      switch ((int) $user->role_id) {
        case 1:
          return redirect()->route('filament.admin.pages.dashboard');
        case 2:
          return redirect()->route('filament.teacher.pages.dashboard');
        case 3:
          return redirect()->route('filament.parent.pages.dashboard');
        case 4:
          return redirect()->route('filament.keamanan.pages.dashboard');
      }
    }
    return $next($request);
  }
}
