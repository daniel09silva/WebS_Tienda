<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->redirectGuestsTo('/auth/login-basic');
    $middleware->redirectUsersTo('/');
    // Render/Railway terminate TLS at their proxy and forward plain HTTP;
    // trust the X-Forwarded-* headers so Laravel generates https:// URLs.
    $middleware->trustProxies(at: '*');
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })->create();