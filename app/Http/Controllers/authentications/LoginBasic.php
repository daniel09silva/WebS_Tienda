<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function authenticate(Request $request)
  {
    $request->validate([
      'login' => ['required', 'email'],
      'password' => ['required'],
    ]);

    $credentials = [
      'email' => $request->input('login'),
      'password' => $request->input('password'),
    ];

    if (! Auth::attempt($credentials, $request->boolean('remember'))) {
      return back()->withErrors([
        'login' => 'Las credenciales no coinciden con nuestros registros.',
      ])->onlyInput('login');
    }

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard-analytics'));
  }

  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('auth-login-basic');
  }
}
