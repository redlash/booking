<?php namespace App\Patterns;

/**
 * Trait BookingSortingPattern
 * @package App\Patterns
 */
trait BookingSortingPattern
{
    public function setSortBy($param)
    {
        $params = explode('.', $param);
        if (count($params) === 0) {
            return;
        }
        if (! in_array($params[0], ['meeting_room', 'user', 'date'])) {
            return;
        }

        if (!isset($params[1]) || !in_array($params[1], ['asc', 'desc'])) {
            $params[1] = 'ASC';
        }

        $methods = [
            'date' => 'applyDateSorter',
            'meeting_room' => 'applyMeetingRoomSorter',
            'user' => 'applyUserSorter'
        ];
        $this->sortBy = [];
        $this->sortBy['method'] = $methods[$params[0]];
        $this->sortBy['type'] = $params[1];
    }

    public function applyMeetingRoomSorter($query, $type = 'ASC')
    {
        return $query->orderBy('meeting_rooms.name', $type);
    }

    public function applyUserSorter($query, $type = 'ASC')
    {
        return $query->orderBy('users.name', $type);
    }

    public function applyDateSorter($query, $type = 'ASC')
    {
        return $query->orderBy('users_meeting_rooms.occupy_at', $type);
    }
}