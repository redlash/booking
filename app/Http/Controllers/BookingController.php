<?php

namespace App\Http\Controllers;

use App\Managers\BookingManager;
use App\Models\Booking;
use App\Http\Resources\Booking as BookingResource;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct()
    {
        //$this->middleware('api:auth', ['except' => ['indexAll']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAll()
    {
        $service = app(BookingManager::class);

        return response($service->getAll(), 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = User::findOrFail($id);
        $bookings = $user->bookings->orderBy('start_at')->paginate(5);

        return response(BookingResource::collection($bookings), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $service = app(BookingManager::class);
            $service->create($request->all());

        } catch (\Exception $exception) {
            return response(
                [
                    'error' => $exception->getMessage()
                ],
                400
            );
        }

        return response(
            [
                'message' => 'You have booked the meeting room successfully.',
                'data' => $service->getAll()
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
                'message' => 'You have canceled your booking.',
                'data' => $this->index()
            ],
            200
        );
    }

    public function listSlots($date)
    {
        return response([], 200);
    }
}
