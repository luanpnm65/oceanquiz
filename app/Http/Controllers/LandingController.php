<?php
namespace App\Http\Controllers;

use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * UC01: Xem trang hero lặn xuống biển (/).
     */
    public function index(): View
    {
        return view('landing');
    }
}