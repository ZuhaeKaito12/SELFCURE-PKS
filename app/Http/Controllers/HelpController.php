<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Show the User Manual, with content scoped to the viewer's role.
     */
    public function index(): View
    {
        return view('help.index', [
            'isAdmin' => Auth::user()->isAdmin(),
        ]);
    }
}
