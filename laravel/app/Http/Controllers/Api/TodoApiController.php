<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TodoApiController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $todos = $user->todos();

        if ($request->has('search')) {
            $todos->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            $todos->where('is_done', $request->status === 'done');
        }

        return response($todos->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $todo = Auth::user()->todos()->create($request->all());

        return response($todo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        $this->authorize('view', $todo);
        return response($todo, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'is_done' => 'sometimes|boolean',
        ]);

        $todo->update($request->all());

        return response($todo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        return response(null, 204);
    }
}
