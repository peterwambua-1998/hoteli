<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = RoomType::all();
        return view('room_types.index', compact('roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
        ]);

        $room_type = new RoomType();
        $room_type->type = $request->type;
        if ($room_type->save()) {
            return redirect()->back()->with('success','Record stored successfully');
        }
        return redirect()->back()->with('error','System error please try again');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required',
        ]);

        $room_type = RoomType::find($id);
        $room_type->type = $request->type;
        if ($room_type->update()) {
            return redirect()->back()->with('success','Record updated successfully');
        }
        return redirect()->back()->with('error','System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $room = RoomType::find($id);
        if ($room) {
            if ($room->delete()) {
                return redirect()->back()->with('success','Record deleted successfully');
            } else {
                return redirect()->back()->with('error','System error please try again');
            }
        } else {
            return redirect()->back()->with('error','System error please try again');
        }
    }
}
