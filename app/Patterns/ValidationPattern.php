<?php namespace App\Patterns;

use App\Models\MeetingRoom;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

/**
 * Trait ValidationPattern
 *
 * @package App\Patterns
 */
trait ValidationPattern
{
    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return Validator
     * @throws \Exception
     */
    public function validate($data = [], $rules = [], $messages = [])
    {

        /**
         * Create the service.
         *
         * @var Validator $validator.
         */
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails() === true) {
            /**
             * Throw back the validation exceptions.
             *
             * @var \FormValidationException $error
             */
            throw new \Exception($validator->errors()->first());
        }

        return $validator;
    }

    /**
     * Check if time is between the available time.
     *
     * @param string    $time
     * @throws \Exception
     */
    public function validateTime($time)
    {
        $time = Carbon::createFromFormat('H:i', $time);
        $min = Carbon::createFromFormat('H:i', '08:00');
        $max = Carbon::createFromFormat('H:i', '17:00');
        if ($time->lt($min) || $time->gt($max)) {
            throw new \Exception('Sorry, the meeting room is available between 8am and 5pm.');
        }
    }

    /**
     * Check if either start time or end time falls between the booking period.
     *
     * @param integer   $meetingRoomId
     * @param string    $date   Format: Y-m-d
     * @param string    $stime  Format: H:i
     * @param string    $etime  Format: H:i
     *
     * @throws \Exception
     */
    public function validateDoubleBook($meetingRoomId, $date, $stime, $etime)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $stime = Carbon::createFromFormat('H:i', $stime);
        $etime = Carbon::createFromFormat('H:i', $etime);
        $meetingRoom = MeetingRoom::findOrFail($meetingRoomId);
        $bookings = $meetingRoom->getBookingRecords($date)->get()
            ->filter(function ($booking) use ($stime, $etime) {
                $startAt = Carbon::createFromFormat('H:i', $booking->start_at->format('H:i'));
                $endAt = Carbon::createFromFormat('H:i', $booking->end_at->format('H:i'));
            return $startAt->lte($stime) && $endAt->gt($stime) || $startAt->lt($etime) && $endAt->gte($etime) ;
        });

        if ($bookings->count() > 0) {
            throw new \Exception(sprintf(
                'Sorry, this meeting room has been booked between %s and %s.',
                $bookings->first()->start_at->format('H:i'),
                $bookings->first()->end_at->format('H:i')
            ));
        };
    }
}