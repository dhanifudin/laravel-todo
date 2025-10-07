<?php

namespace Tests\Unit;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_todo_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $todo->user);
    }

    public function test_a_todo_can_be_marked_as_done()
    {
        $todo = Todo::factory()->create(['is_done' => false]);

        $todo->update(['is_done' => true]);

        $this->assertTrue($todo->is_done);
    }

    public function test_a_todo_can_be_marked_as_undone()
    {
        $todo = Todo::factory()->create(['is_done' => true]);

        $todo->update(['is_done' => false]);

        $this->assertFalse($todo->is_done);
    }
}
