<?php

namespace App\Http\Controllers;

use App\Models\Lpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LpoController extends Controller
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
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xlsm,xlsb,xltx,xls|max:2048',
        ]);

        if ($request->file('file')->isValid()) {
            $file = $request->file('file');
            $path = $file->store('uploads');

            $fileRecord = new Lpo();
            $fileRecord->account_id = $request->account_id;
            $fileRecord->name = $file->getClientOriginalName();
            $fileRecord->path = $path;
            $fileRecord->save();

            return redirect()->back()->with('success', 'File uploaded successfully!');
        }

        return redirect()->back()->with('error', 'File upload failed!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lpo $lpo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lpo $lpo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lpo $lpo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lpo $lpo)
    {
        //
    }

    // Method to handle file download
    public function downloadFile($id)
    {
        $file = Lpo::findOrFail($id);
        return Storage::download($file->path, $file->name);
    }
}
