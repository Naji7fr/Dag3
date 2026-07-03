<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Controller voor de homepagina van de eigenaar.
 */
class HomeController extends Controller
{
    /**
     * Toont het module-overzicht (Wireframe-01).
     */
    public function index(): View
    {
        return view('home.index', [
            'pageTitle' => 'Home - Kniploket Tiko',
            'activeNav' => 'home',
        ]);
    }
}
