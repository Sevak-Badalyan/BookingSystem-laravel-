<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

class RoomService
{
    /**
     * @return Collection<int, Room>
     */
    public function getAll(): Collection
    {
        return Room::all();
    }

    public function findOrFail(int $id): Room
    {
        return Room::findOrFail($id);
    }

    /**
     * @param  array{name: string, capacity: int}  $data
     */
    public function create(array $data): Room
    {
        return Room::create($data);
    }

    /**
     * @param  array{name?: string, capacity?: int}  $data
     */
    public function update(Room $room, array $data): Room
    {
        $room->update($data);

        return $room;
    }

    public function delete(Room $room): void
    {
        $room->delete();
    }
}
