<?php

namespace App\Http\Controllers;

use App\Http\Resources\Booking as BookingResource;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\MeetingRoom as MeetingRoomResource;

class MeetingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(
            MeetingRoomResource::collection(MeetingRoom::all()->sortBy('name')),
            200
        );
    }

    public function getRecords($id, $date)
    {
        $meetingRoom = MeetingRoom::findOrFail($id);

        $date = Carbon::createFromFormat('Ymd', $date);

        return response(
            BookingResource::collection($meetingRoom->getBookingRecords($date)->orderBy('start_at')->get()),
            200
        );
    }
}
