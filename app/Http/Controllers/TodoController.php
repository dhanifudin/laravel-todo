<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(Request $request)
    {
        $todos = auth()->user()->todos()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('is_done')
            ->orderByDesc('created_at')
            ->get();

        return view('todos.index', [
            'todos' => $todos,
            'search' => $request->input('search'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        auth()->user()->todos()->create([
            'name' => $request->name,
        ]);

        return redirect()->route('todos.index');
    }

    public function update(Todo $todo)
    {
        $this->authorize('update', $todo);

        $todo->update([
            'is_done' => ! $todo->is_done,
        ]);

        return redirect()->route('todos.index');
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);
        
        $todo->delete();

        return redirect()->route('todos.index');
    }
}
