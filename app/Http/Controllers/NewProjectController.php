<?php

namespace App\Http\Controllers;

class NewProjectController extends Controller
{
    public function index() {
        $data = [

        ];

        return view('newproject')->with($data);
    }
}