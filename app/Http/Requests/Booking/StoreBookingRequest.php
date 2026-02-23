<?php

declare(strict_types=1);

namespace App\Http\Requests\Booking;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    private const MIN_DURATION_MINUTES = 30;

    private const MAX_DURATION_MINUTES = 480; // 8 hours

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'start_at.after' => 'The booking must be for a future time.',
            'end_at.after' => 'The end time must be after the start time.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateDuration($validator);
        });
    }

    private function validateDuration(Validator $validator): void
    {
        $startAt = strtotime($this->input('start_at'));
        $endAt = strtotime($this->input('end_at'));

        if ($startAt === false || $endAt === false) {
            return;
        }

        $durationMinutes = ($endAt - $startAt) / 60;

        if ($durationMinutes < self::MIN_DURATION_MINUTES) {
            $validator->errors()->add('end_at', 'The booking must be at least 30 minutes.');
        }

        if ($durationMinutes > self::MAX_DURATION_MINUTES) {
            $validator->errors()->add('end_at', 'The booking cannot exceed 8 hours.');
        }
    }
}
