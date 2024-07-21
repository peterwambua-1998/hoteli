<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accountApprovals = AccountApproval::where('approved', '=', 0)->orderBy('created_at', 'desc')->get();
        return view('approvals.index', compact('accountApprovals'));
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
            'type' => 'required',
        ]);

        $account = new AccountApproval();
        $account->type = $request->type;
        $account->name = $request->name;
        $account->email = $request->email;
        $account->telephone = $request->telephone;
        $account->location = $request->location;
        $account->vat_registration_number = $request->vat_registration_number;
        $account->stored_by = Auth::user()->id;
        if ($account->save()) {
            return redirect()->back()->with('success', 'Account sent for approval.');
        }
        return redirect()->back()->with('error', 'System error please try again.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountApproval $accountApproval)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountApproval $accountApproval)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'approval_id' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $accountApproval = AccountApproval::find($request->approval_id);

            $account = new Account;
            $account->type = $accountApproval->type;
            $account->name = $accountApproval->name;
            $account->email = $accountApproval->email;
            $account->telephone = $accountApproval->telephone;
            $account->location = $accountApproval->location;
            $account->vat_registration_number = $accountApproval->vat_registration_number;
            $account->save();

            DB::commit();
                
            return redirect()->back()->with('success', 'Account saved successfully');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'System error please try again.');

        }

       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountApproval $accountApproval)
    {
        //
    }
}
