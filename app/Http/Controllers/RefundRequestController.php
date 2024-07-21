<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $refundRequests = RefundRequest::orderBy('created_at', 'desc')->get();
        return view('refund-request.index', compact('refundRequests'));
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
            'receipt_id' => 'required',
            'amount' => 'required'
        ]);

        $refundRequest = new RefundRequest();
        $refundRequest->receipt_id = $request->receipt_id;
        $refundRequest->amount = $request->amount;
        if ($refundRequest->save()) {
            return redirect()->back()->with('success', 'Request has been sent to accounts for approval');
        }
        return redirect()->back()->with('error', 'System error please try again!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RefundRequest $refundRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RefundRequest $refundRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RefundRequest $refundRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RefundRequest $refundRequest)
    {
        //
    }

    /**
     * 
     */
    public function approve(Request $request)
    {
        $request->validate([
            'approval_status' => 'required'
        ]);

        $refundRequest = RefundRequest::find($request->refund_request_id);
        $refundRequest->approved = $request->approval_status;
        $refundRequest->reason = $request->reason;
        if ($refundRequest->update()) {
            return redirect()->back()->with('success', 'Record updated successfully');
        }
        return redirect()->back()->with('error', 'System error please try again!');
    }
}
