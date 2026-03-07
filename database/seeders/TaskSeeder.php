<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
            ]
        );

        echo "\nTest User Created/Found:\n";
        echo "Email: test@example.com\n";
        echo "Password: password123\n\n";

        Task::factory()
            ->count(15)
            ->pending()
            ->create(['user_id' => $user->id]);

        Task::factory()
            ->count(12)
            ->completed()
            ->create(['user_id' => $user->id]);

        $deletedTasks = Task::factory()
            ->count(8)
            ->create(['user_id' => $user->id]);

        foreach ($deletedTasks as $task) {
            $task->delete();
        }

        echo "Seeding completed successfully!\n";
        echo "Created:\n";
        echo "- 15 pending tasks\n";
        echo "- 12 completed tasks\n";
        echo "- 8 deleted tasks (in recycle bin)\n";
        echo "Total: 35 tasks\n\n";
    }
}
