<?php

// app/Http/Responses/CustomLogoutResponse.php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class CustomLogoutResponse implements LogoutResponse
{
  public function toResponse($request)
  {
    $request->session()->forget('url.intended');
    return redirect('/auth/login');
  }
}
