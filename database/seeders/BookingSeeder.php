<?php

namespace Database\Seeders;

use App\Models\MeetingRoom;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Create users.
         */
        User::factory(2)->create();

        /**
         * Create meeting rooms.
         */
        MeetingRoom::factory(3)->create();
    }
}
