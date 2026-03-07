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

    public static function getUserTasks(int $userId)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getUserTasksByStatus(int $userId, string $status)
    {
        return static::where('user_id', $userId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
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
}
