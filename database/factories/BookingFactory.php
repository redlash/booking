<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::all();
        if ($users->count() === 0) {
            return [];
        }

        $meetingRooms = MeetingRoom::all();
        if ($meetingRooms->count() === 0) {
            return [];
        }

        return [
            'user_id' => $users->first()->id,
            'meeting_room_id' => $meetingRooms->first()->id,
            'start_at' => now(),
            'end_at' => now()->addMinutes(60)
        ];
    }
}
