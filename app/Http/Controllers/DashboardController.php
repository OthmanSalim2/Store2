<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        // here mean will executed the all actions at controller if was user logged
        // this a second way to recognize an middleware the best apply the middleware on route
        // $this->middleware(['auth'])->except('index');
    }

    // Actions
    public function index()
    {
        return view('dashboard.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy()
    {
        //
    }
}
