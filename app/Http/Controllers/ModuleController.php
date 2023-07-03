<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModulesController extends Controller
{
    public function __construct()
    {
    }
    public function view(Request $request)
    {
        return view('modules.list');
    }
}
