<?php

namespace App\Services;

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;

class TimeEntryService
{
    public function create(array $data, User $user): TimeEntry
    {
        $this->validateTimes($data['start_time'], $data['end_time']);

        $this->validateNoOverlap($data, $data['user_id'] ?? $user->id);

        $this->validateActiveRelations($data['activity_type_id'], $data['client_id']);

        $data['duration_minutes'] = $this->calculateDuration(
            $data['start_time'],
            $data['end_time']
        );

        $data['user_id'] = $data['user_id'] ?? $user->id;
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        return TimeEntry::create($data);
    }

    public function delete(TimeEntry $timeEntry, User $user): TimeEntry
    {
        $timeEntry->update([
            'status' => 'deleted',
            'updated_by' => $user->id,
        ]);

        return $timeEntry->fresh();
    }

    public function update(TimeEntry $timeEntry, array $data, User $user): TimeEntry
    {
        $targetUserId = $data['user_id'] ?? $timeEntry->user_id;

        $this->validateTimes($data['start_time'], $data['end_time']);

        $this->validateNoOverlapForUpdate($timeEntry, $data, $targetUserId);

        $this->validateActiveRelations(
            $data['activity_type_id'],
            $data['client_id']
        );

        $data['duration_minutes'] = $this->calculateDuration(
            $data['start_time'],
            $data['end_time']
        );

        $data['updated_by'] = $user->id;

        $timeEntry->update($data);

        return $timeEntry->fresh();
    }

    private function calculateDuration(string $startTime, string $endTime): int
    {
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);

        return $start->diffInMinutes($end);
    }

    private function validateTimes(string $startTime, string $endTime): void
    {
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $startTime);
        $end = \Carbon\Carbon::createFromFormat('H:i:s', $endTime);

        if ($end->lessThanOrEqualTo($start)) {
            throw new \Exception('A hora de fim deve ser superior à hora de início.');
        }
    }

    private function validateNoOverlap(array $data, int $userId): void
    {
        $exists = TimeEntry::query()
            ->where('user_id', $userId)
            ->where('date', $data['date'])
            ->where('status', '!=', 'deleted')
            ->where(function ($query) use ($data) {
                $query
                    ->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($query) use ($data) {
                        $query
                            ->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($exists) {
            throw new \Exception('Já existe um registo sobreposto para este utilizador nesta data.');
        }
    }

    private function validateActiveRelations(int $activityTypeId, int $clientId): void
    {
        $activityType = ActivityType::find($activityTypeId);
        $client = Client::find($clientId);

        if (!$activityType || !$activityType->is_active) {
            throw new \Exception('Tipo de atividade inválido ou inativo.');
        }

        if (!$client || !$client->is_active) {
            throw new \Exception('Cliente inválido ou inativo.');
        }
    }

    private function validateNoOverlapForUpdate(TimeEntry $timeEntry, array $data, int $userId): void
    {
        $exists = TimeEntry::query()
            ->where('id', '!=', $timeEntry->id)
            ->where('user_id', $userId)
            ->where('date', $data['date'])
            ->where('status', '!=', 'deleted')
            ->where(function ($query) use ($data) {
                $query
                    ->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($query) use ($data) {
                        $query
                            ->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($exists) {
            throw new \Exception('Já existe um registo sobreposto para este utilizador nesta data.');
        }
    }
}
