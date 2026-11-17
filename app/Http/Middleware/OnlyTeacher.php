<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyTeacher
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (auth()->check() && (int) auth()->user()->role_id !== 2) {
      return match ((int) auth()->user()->role_id) {
        1 => redirect()->route('filament.admin.pages.dashboard'),
        3 => redirect()->route('filament.parent.pages.dashboard'),
        4 => redirect()->route('filament.keamanan.pages.dashboard'),
        default => abort(403, 'Unauthorized'),
      };
    }
    return $next($request);
  }
}
