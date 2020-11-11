<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
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
    protected $casts = [];

    /**
     * Users that have booked the meeting room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_meeting_rooms');
    }

    /**
     * All booking records relates to the meeting room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookingRecords()
    {
        return $this->hasMany(Booking::class, 'meeting_room_id', 'id');
    }

    public function getOccupiedSlots($date)
    {
        return $this->bookingRecords();
//            ->whereBetween('start_at', [$date, $date->addHours(7)])
//            ->orderBy('start_at')
//            ->select('start_at', 'end_at');
    }

    public function getAvailableSlots($date)
    {
        return $this->bookingRecords;
    }
}