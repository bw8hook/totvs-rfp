<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index() {
        $userId = auth()->user()->id;
        $data = [
            'userId' => $userId
        ];

      return view('dashboard')->with( $data);
    }
}