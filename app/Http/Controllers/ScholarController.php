<?php

namespace App\Http\Controllers;

use App\Models\Scholar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScholarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scholars = Scholar::orderBy('birth_ah')->get();
        return view('scholars.index', compact('scholars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Scholar $scholar)
    {
        $scholar->load([
            'teachings',
            'quotes',
            'students',
            'works',
        ]);

        return view('scholars.show', compact('scholar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scholar $scholar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scholar $scholar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scholar $scholar)
    {
        //
    }
}
