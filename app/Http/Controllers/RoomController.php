<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();
        $roomStatuses = RoomStatus::all();
        $roomTypes = RoomType::all();
        return view('rooms.index', compact('rooms', 'roomStatuses', 'roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_type' => 'required',
            'room_status' => 'required',
            'number' => 'required',
            'capacity' => 'required',
        ]);


        if ($request->room_type == 0) {
            return redirect()->back()->with('error', 'Kindly select room type');
        }

        
        $room = new Room();
        $room->room_type = $request->room_type;
        $room->room_status = $request->room_status;
        $room->number = $request->number;
        $room->capacity = $request->capacity;
        $room->price = 0;
        $room->description = $request->description;
        if ($room->save()) {
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
            'room_type' => 'required',
            'room_status' => 'required',
            'number' => 'required',
            'capacity' => 'required',
        ]);

        if ($request->room_type == 0) {
            return redirect()->back()->with('error', 'Kindly select room type');
        }

        $room = Room::find($id);
        $room->room_type = $request->room_type;
        $room->room_status = $request->room_status;
        $room->number = $request->number;
        $room->capacity = $request->capacity;
        $room->price = 0;
        $room->description = $request->description;
        if ($room->update()) {
            return redirect()->back()->with('success','Record updated successfully');
        }
        return redirect()->back()->with('error','System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $room = Room::find($id);
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

    /**
     * search rooms
     */
    public function searchRoom(Request $request)
    {
        $query = Room::query();

        if($request->room_type != 0) {
            $query->where('room_type', '=', $request->room_type);
        }

        if ($request->capacity != 0) {
            $query->where('capacity', '=', $request->capacity);
        }

        if ($request->price) {
            $query->where('price', '>=', $request->price);
        }

        if ($request->room_number != 0) {
            $query->where('number', 'LIKE','%' .$request->room_number.'%');
        }

        $rooms = $query->where('room_status', '=', 0)->get();
        foreach ($rooms as $key => $room) {
            $roomType = $room->type->type;
            $room['type'] = $roomType;
        }
        return response($rooms);

    }
}
