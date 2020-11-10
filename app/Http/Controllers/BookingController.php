<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Resources\Booking as BookingResource;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookings = Booking::paginate(10);

        return response(BookingResource::collection($bookings), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * @todo validation.
         */
        Booking::insert(
            [
                'user_id' => $request->input('user_id'),
                'meeting_room_id' => $request->input('meeting_room_id'),
                'start_at' => $request->input('start_at'),
                'end_at' => $request->input('start_at'),
            ]
        );

        return response(
            [
                'message' => 'You have booked the meeting room successfully.',
                'data' => $this->index()
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        return response(new BookingResource($booking), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * @todo: validation
         */
        $booking = Booking::findOrFail($id);

        foreach($request->all() as $key => $value) {
            $booking->$key = $value;
        }
        $booking->save();

        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /**
         * @todo: grab the booking from user's booking.
         */
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response(
            [
                'message' => 'You have canceled you booking.',
                'data' => $this->index()
            ],
            200
        );
    }
}
