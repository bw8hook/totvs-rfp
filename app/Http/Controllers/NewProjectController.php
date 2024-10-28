<?php

namespace App\Http\Controllers;

class NewProjectController extends Controller
{
    public function index() {
        $userId = auth()->user()->id;
        $data = [
            'userId' => $userId
        ];

        return view('newproject')->with($data);
    }
}