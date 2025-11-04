<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Todos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                    <form action="{{ route('todos.index') }}" method="GET" class="flex items-center">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="{{ __('Search todos...') }}"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                        >
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring ring-indigo-300 transition ease-in-out duration-150">
                            {{ __('Search') }}
                        </button>
                    </form>

                    <form action="{{ route('todos.store') }}" method="POST" class="flex items-center">
                        @csrf
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="{{ __('Add a new todo...') }}"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                        >
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Add Todo') }}
                        </button>
                    </form>

                    @if (isset($errors) && $errors->has('name'))
                        <p class="text-sm text-red-600">{{ $errors->first('name') }}</p>
                    @endif

                    <div>
                        @forelse ($todos as $todo)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="{{ $todo->is_done ? 'line-through text-gray-500' : '' }}">
                                        {{ $todo->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $todo->created_at->format('M d, Y H:i') }}
                                    </p>
                                </div>

                                <div class="flex items-center">
                                    <form action="{{ route('todos.update', $todo) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-green-500 hover:text-green-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('todos.destroy', $todo) }}" method="POST" class="ml-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">{{ __('No todos found.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
