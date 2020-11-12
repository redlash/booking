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
        try {
            $service = app(BookingManager::class);

            /* Set filters. */
            $filters = [];
            foreach (['user_id', 'meeting_room_id', 'date_from', 'date_to'] as $filter) {
                if (isset(request()->$filter)) {
                    $filters[$filter] = request()->$filter;
                }
            }
            $service->setFilters($filters);

            /* Set sorting. */
            if (isset(request()->sort_by)) {
                $service->setSortBy(request()->sort_by);
            }

            $results = $service->getAll();

        } catch (\Exception $exception) {

            return response(
                ['error' => $exception->getMessage()],
                400
            );
        }

        return response($results, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        try {
            $service = app(BookingManager::class);

            /* Set filters. */
            $filters = [];
            foreach (['user_id', 'meeting_room_id', 'occupy_at'] as $filter) {
                if (isset(request()->$filter)) {
                    $filters[$filter] = request()->$filter;
                }
            }
            $service->setFilters($filters);

            /* Set sorting. */
            if (isset(request()->sort_by)) {
                $service->setSortBy(request()->sort_by);
            }

            $results = $service->get($id);

        } catch (\Exception $exception) {

            return response(
                ['error' => $exception->getMessage()],
                400
            );
        }

//        $user = User::findOrFail($id);
//        $bookings = $user->bookings->orderBy('start_at')->paginate(5);

        return response($results, 200);
        //return response(BookingResource::collection($bookings), 200);
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
        try {
            $service = app(BookingManager::class);
            $service->update($id, $request->all());

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

//        $booking = Booking::findOrFail($id);
//
//        foreach($request->all() as $key => $value) {
//            $booking->$key = $value;
//        }
//        $booking->save();
//
//        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $service = app(BookingManager::class);
            $service->delete($id);

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
                'message' => 'You have cancel the booking successfully.',
                'data' => $service->getAll()
            ],
            200
        );

//        $booking = Booking::findOrFail($id);
//        $booking->delete();
//
//        return response(
//            [
//                'message' => 'You have canceled your booking.',
//                'data' => $this->index()
//            ],
//            200
//        );
    }
}
