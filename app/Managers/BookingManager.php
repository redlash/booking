<?php

namespace App\Managers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\User;
use App\Patterns\BookingFilterPattern;
use App\Patterns\BookingSortingPattern;
use App\Patterns\ValidationPattern;

/**
 * Class BookingManager
 *
 * @package App\Managers
 */
class BookingManager
{
    use BookingFilterPattern, BookingSortingPattern, ValidationPattern;

    protected $filters = [];

    protected $sortBy = [];

    /**
     * Get all the booking records.
     *
     * @return mixed
     */
    public function getAll()
    {
        $records = Booking::with(['user', 'meetingRoom']);

        $this->applyFilters($records);

        $this->applySorting($records);

        return $records->paginate(5);
    }

    /**
     * Get booking records for a specific user.
     *
     * @param integer   $userId
     * @return mixed
     */
    public function get($userId)
    {
        $user = User::findOrFail($userId);

        return $user->bookingRecords->with(['meetingRoom'])
            ->orderBy('start_at')->paginate(5);
    }

    /**
     * Create a booking record for a specific user.
     *
     * @param array $data
     * @param null $userId
     * @return mixed
     * @throws \Exception
     */
    public function create($data = [], $userId = null)
    {

        $user = $this->getUser($userId);

        $rules = [
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'occupy_at' => 'required|date',
            'start_at' => 'required',
            'end_at' => 'required'
        ];

        $this->validate($data, $rules, trans('validation'));

        return Booking::insert(
            [
                'user_id' => $user->id,
                'meeting_room_id' => $data['meeting_room_id'],
                'occupy_at' => $data['occupy_at'],
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Update a
     * @param $bookingId
     * @param array $data
     * @param $userId
     * @return mixed
     * @throws \Exception
     */
    public function update($bookingId, $data = [], $userId = null)
    {
        $user = $this->getUser($userId);

        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== $user->id) {
            throw new \Exception('You have no permission.');
        }

        $rules = [
            'meeting_room_id' => 'exists:meeting_rooms,id',
            'start_at' => 'date_format',
            'end_at' => 'date_format'
        ];

        $this->validate($data, $rules, trans('app.booking.update.validation'));

        foreach($data as $key => $value) {
            $booking->$key = $value;
        }
        $booking->save();

        return $booking;
    }

    public function delete($bookingId, $userId = null)
    {
        $user = $this->getUser($userId);
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== $user->id) {
            throw new \Exception('You have no permission.');
        }

        $booking->delete();

        return;
    }

    public function getSlots($meetingRoomId, $date)
    {
        $meetRoom = MeetingRoom::findOrFail($meetingRoomId);

        return $meetRoom->getAvailableSlots($date);
    }

    protected function getUser($userId = null)
    {
        $user = $userId === null ? auth()->user() : User::findOrFail($userId);

        if ($user === null) {
            throw new \Exception('User does not exist.');
        }

        return $user;
    }
}