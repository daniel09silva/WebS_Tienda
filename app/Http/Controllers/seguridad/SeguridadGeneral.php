<?php

namespace App\Http\Controllers\seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeguridadGeneral extends Controller
{
  public function index()
  {
    return view('content.seguridad.seguridad');
  }
}
