<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createTask(array $data): Task
    {
        return static::create($data);
    }

    public static function findByIdAndUser(int $taskId, int $userId): ?Task
    {
        return static::where('id', $taskId)
            ->where('user_id', $userId)
            ->first();
    }

    public static function getUserTasks(int $userId, int $perPage = 10)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public static function getUserTasksByStatus(int $userId, string $status, int $perPage = 10)
    {
        return static::where('user_id', $userId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateTask(array $data): bool
    {
        return $this->update($data);
    }

    public function markAsCompleted(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    public function markAsPending(): bool
    {
        return $this->update(['status' => 'pending']);
    }

    public function softDeleteTask(): bool
    {
        return $this->delete();
    }

    public function forceDeleteTask(): bool
    {
        return $this->forceDelete();
    }

    public static function getTrashedTasks(int $userId, int $perPage = 10)
    {
        return static::onlyTrashed()
            ->where('user_id', $userId)
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage);
    }

    public static function findTrashedTask(int $taskId, int $userId): ?Task
    {
        return static::onlyTrashed()
            ->where('id', $taskId)
            ->where('user_id', $userId)
            ->first();
    }

    public function restoreTask(): bool
    {
        return $this->restore();
    }
}
