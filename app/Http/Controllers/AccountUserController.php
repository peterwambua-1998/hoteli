<?php

namespace App\Http\Controllers;

use App\Models\AccountUser;
use Illuminate\Http\Request;

class AccountUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'account_id' => 'required',
            'name' => 'required',
        ]);

        $accountUser = new AccountUser();
        $accountUser->account_id = $request->account_id;
        $accountUser->name = $request->name;
        if ($accountUser->save()) {
            return redirect()->back()->with('success', 'Record created successfully');
        }
        return redirect()->back()->with('error', 'System error please try again');
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountUser $accountUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountUser $accountUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountUser $accountUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountUser $accountUser)
    {
        //
    }
}
