<?php namespace App\Patterns;

/**
 * Trait BookingSortingPattern
 * @package App\Patterns
 */
trait BookingSortingPattern
{
    public function applyMeetingRoomSorter($query, $asc = true)
    {
        return $asc ? $query->orderBy('meeting_rooms.name') : $query->orderBy('meeting_rooms.name', 'DESC');
    }

    public function applyUserSorter($query, $asc = true)
    {
        return $asc ? $query->orderBy('users.name') : $query->orderBy('users.name', 'DESC');
    }

    public function applyDateSorter($query, $asc = true)
    {
        return $asc ? $query->orderBy('users_meeting_rooms.start_at') : $query->orderBy('users.name', 'DESC');
    }
}