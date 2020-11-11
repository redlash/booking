<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'users_meeting_rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'meeting_room_id',
        'occupy_at',
        'start_at',
        'end_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'meeting_room_id' => 'integer',
        'occupy_at' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * User of the booking.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Meeting room of the booking.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function meetingRoom()
    {
        return $this->hasOne(MeetingRoom::class, 'id', 'meeting_room_id');
    }
}
