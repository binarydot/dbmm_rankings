<?php

namespace App\Controllers;

class RosterEditor extends BaseController
{
    public function index(): string
    {
        return view('roster/editor', [
            'title' => 'Roster Editor',
            'menuItem' => 'roster',
        ]);
    }
}
