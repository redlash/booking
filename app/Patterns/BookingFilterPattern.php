<?php namespace App\Patterns;

use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Trait BookingFilterPattern
 *
 * @package App\Patterns
 */
trait BookingFilterPattern
{
    /**
     * Set filters for query results.
     *
     * @param array $filters
     */
    public function setFilters($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get current filters.
     *
     * @param null $field
     * @return mixed
     */
    public function getFilters($field = null) {

        return $field === null ? $this->filters : Arr::get($this->filters, $field, null);
    }

    /**
     * Apply filters.
     *
     * @param $query
     * @return mixed
     */
    public function applyFilters($query)
    {
        $this->applyUserFilter($query, Arr::get($this->filters, 'user_id', null));
        $this->applyMeetingRoomFilter($query, Arr::get($this->filters, 'meeting_room_id', null));
        $this->applyDateFilter(
            $query,
            Arr::get($this->filters, 'date_from', null),
            Arr::get($this->filters, 'date_to', null)
        );

        return $query;
    }

    /**
     * Filter by date range.
     *
     * @param $query
     * @param string $startDate
     * @param string $endDate
     * @return mixed
     */
    protected function applyDateFilter($query, $startDate = null, $endDate = null)
    {
        if ($startDate === null && $endDate === null) {
            return $query;
        }

        if ($endDate === null) {
            return $query->where('occupy_at', Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay());
        }

        if ($startDate === null) {
            return $query->where('occupy_at', Carbon::createFromFormat('d/m/Y', $endDate)->startOfDay());
        }

        return $query->whereBetween(
            'occupy_at',
            [
                Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay(),
                Carbon::createFromFormat('d/m/Y', $endDate)->addDays(1)->startOfDay()
            ]
        );
    }

    /**
     * Filter by meeting room id.
     *
     * @param $query
     * @param integer $meetingRoomId
     * @return mixed
     */
    protected function applyMeetingRoomFilter($query, $meetingRoomId = null)
    {
        if ($meetingRoomId === null) {
            return $query;
        }

        return $query->where('meeting_room_id', $meetingRoomId);
    }

    /**
     * Filter by user id.
     *
     * @param $query
     * @param integer $userId
     * @return mixed
     */
    protected function applyUserFilter($query, $userId = null)
    {
        if ($userId === null) {
            return $query;
        }

        return $query->where('user_id', $userId);
    }
}