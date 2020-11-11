<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
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
}
