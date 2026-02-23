<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * @param  array{room_id: int, start_at: string, end_at: string}  $data
     *
     * @throws ValidationException
     */
    public function create(array $data, User $user): Booking
    {
        return DB::transaction(function () use ($data, $user): Booking {
            $startAt = Carbon::parse($data['start_at']);
            $endAt = Carbon::parse($data['end_at']);

            $this->checkForOverlap((int) $data['room_id'], $startAt, $endAt);

            return Booking::create([
                'room_id' => $data['room_id'],
                'user_id' => $user->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
            ]);
        });
    }

    public function delete(Booking $booking): void
    {
        $booking->delete();
    }

    /**
     * Check if there are any overlapping bookings for the given room and time period.
     *
     * @throws ValidationException
     */
    private function checkForOverlap(int $roomId, Carbon $startAt, Carbon $endAt): void
    {
        $hasOverlap = Booking::where('room_id', $roomId)
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->lockForUpdate()
            ->exists();

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'room_id' => ['This room is already booked for the selected time period.'],
            ]);
        }
    }
}
