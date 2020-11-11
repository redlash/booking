<?php namespace App\Patterns;

use Illuminate\Support\Arr;

/**
 * Trait BookingFilterPattern
 *
 * @package App\Patterns
 */
trait BookingFilterPattern
{
    public function setFilters($filters = [])
    {
        $this->filters = $filters;
    }

    public function getFilters($field = null) {

        return $field === null ? $this->filters : Arr::get($this->filters, $field, null);
    }

    public function applyFilters($query)
    {
        $this->applyUserFilter($query, Arr::get($this->filters, 'user_id', null));
        $this->applyMeetingRoomFilter($query, Arr::get($this->filters, 'meeting_room_id', null));
        $this->applyDateFilter(
            $query, Arr::get($this->filters, 'start_date', null),
            Arr::get($this->filters, 'end_date', null)
        );

        return $query;
    }

    protected function applyDateFilter($query, $startDate = null, $endDate = null)
    {
        if ($startDate === null && $endDate === null) {
            return $query;
        }

        if ($endDate === null) {
            return $query->where('start_at', '>=', $startDate);
        }

        if ($startDate === null) {
            return $query->where('start_at', '<=', $endDate);
        }

        return $query->whereBetween('start_at', [$startDate, $endDate]);
    }

    protected function applyMeetingRoomFilter($query, $meetingRoomId = null)
    {
        if ($meetingRoomId === null) {
            return $query;
        }

        return $query->where('meeting_room_id', $meetingRoomId);
    }

    protected function applyUserFilter($query, $userId = null)
    {
        if ($userId === null) {
            return $query;
        }

        return $query->where('user_id', $userId);
    }
}