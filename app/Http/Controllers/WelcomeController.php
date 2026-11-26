<?php

namespace App\Http\Controllers;

use App\Services\InvitationThemeResolver;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __construct(protected InvitationThemeResolver $themeResolver)
    {
    }

    public function index(): View
    {
        return view('welcome', [
            'themes' => $this->themeResolver->activeThemes(),
        ]);
    }
}
