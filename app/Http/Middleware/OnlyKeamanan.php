<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyKeamanan
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (auth()->check() && (int) auth()->user()->role_id !== 4) {
      return match ((int) auth()->user()->role_id) {
        1 => redirect()->route('filament.admin.pages.dashboard'),
        2 => redirect()->route('filament.teacher.pages.dashboard'),
        3 => redirect()->route('filament.parent.pages.dashboard'),
        default => abort(403, 'Unauthorized'),
      };
    }
    return $next($request);
  }
}
