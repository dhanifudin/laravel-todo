<?php

namespace Tests\Feature\Api;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_the_todo_api()
    {
        $response = $this->getJson('/api/todos');
        $response->assertStatus(401);
    }

    public function test_authenticated_users_can_see_their_todos()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id, 'name' => 'My Todo']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/todos');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'My Todo']);
    }

    public function test_authenticated_users_can_create_a_new_todo()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/todos', [
            'name' => 'New API Todo',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('todos', [
            'name' => 'New API Todo',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_users_can_mark_a_todo_as_complete()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id, 'is_done' => false]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/todos/{$todo->id}", [
            'is_done' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_done' => true,
        ]);
    }

    public function test_authenticated_users_can_delete_a_todo()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    public function test_users_cannot_see_or_modify_todos_that_dont_belong_to_them()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other User Todo']);

        // Cannot see
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/todos');
        $response->assertJsonMissing([['name' => 'Other User Todo']]);

        // Cannot view single
        $response = $this->actingAs($user, 'sanctum')->getJson("/api/todos/{$todo->id}");
        $response->assertStatus(403);

        // Cannot update
        $response = $this->actingAs($user, 'sanctum')->putJson("/api/todos/{$todo->id}", ['name' => 'Updated']);
        $response->assertStatus(403);

        // Cannot delete
        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/todos/{$todo->id}");
        $response->assertStatus(403);
    }

    public function test_authenticated_users_can_search_todos()
    {
        $user = User::factory()->create();
        $matchingTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'name' => 'Buy milk',
        ]);
        $nonMatchingTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'name' => 'Walk the dog',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/todos?search=milk');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $matchingTodo->name]);
        $response->assertJsonMissing([['name' => $nonMatchingTodo->name]]);
    }
}
