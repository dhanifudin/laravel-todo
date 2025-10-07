<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_the_todo_page()
    {
        $response = $this->get(route('todos.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_see_their_todos()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('todos.index'));

        $response->assertStatus(200);
        $response->assertSee($todo->name);
    }

    public function test_authenticated_users_can_create_a_new_todo()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('todos.store'), [
            'name' => 'New Todo',
        ]);

        $response->assertRedirect(route('todos.index'));
        $this->assertDatabaseHas('todos', [
            'name' => 'New Todo',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_users_can_mark_a_todo_as_complete()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('todos.update', $todo));

        $response->assertRedirect(route('todos.index'));
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_done' => true,
        ]);
    }

    public function test_authenticated_users_can_delete_a_todo()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('todos.destroy', $todo));

        $response->assertRedirect(route('todos.index'));
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    public function test_users_cannot_see_or_modify_todos_that_dont_belong_to_them()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('todos.index'));
        $response->assertDontSee($todo->name);

        $response = $this->actingAs($user)->put(route('todos.update', $todo));
        $response->assertForbidden();

        $response = $this->actingAs($user)->delete(route('todos.destroy', $todo));
        $response->assertForbidden();
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

        $response = $this->actingAs($user)->get(route('todos.index', [
            'search' => 'milk',
        ]));

        $response->assertStatus(200);
        $response->assertSee($matchingTodo->name);
        $response->assertDontSee($nonMatchingTodo->name);
    }
}
