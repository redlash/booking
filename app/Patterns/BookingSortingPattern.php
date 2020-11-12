<?php namespace App\Patterns;

/**
 * Trait BookingSortingPattern
 * @package App\Patterns
 */
trait BookingSortingPattern
{
    /**
     * Set params for sorting.
     *
     * @param string    $param
     */
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

    /**
     * Get sorting params.
     *
     * @return array
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * Run the sorting methods.
     *
     * @param $query
     * @return mixed
     */
    public function applySorting($query)
    {
        if (count($this->sortBy) > 0) {
            $sortingMethod = $this->sortBy['method'];
            $this->$sortingMethod($query, $this->sortBy['type']);
        }

        return $query;
    }

    /**
     * Run sorting by meeting rooms.
     *
     * @param $query
     * @param string $type
     * @return mixed
     */
    public function applyMeetingRoomSorter($query, $type = 'ASC')
    {
        return $query->join('meeting_rooms', 'meeting_room_id', '=', 'meeting_rooms.id')
                ->orderBy('meeting_rooms.name', $type);
    }

    /**
     * Run sorting by users.
     *
     * @param $query
     * @param string $type
     * @return mixed
     */
    public function applyUserSorter($query, $type = 'ASC')
    {
        return $query->join('users', 'user_id', '=', 'users.id')
            ->orderBy('users.name', $type);
    }

    /**
     * Run sorting by date and start time.
     *
     * @param $query
     * @param string $type
     * @return mixed
     */
    public function applyDateSorter($query, $type = 'ASC')
    {
        return $query->orderBy('occupy_at', $type)
            ->orderBy('start_at', $type);
    }
}