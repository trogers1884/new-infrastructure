@extends('admin::layouts.admin')
@section('title', 'Migrations')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Database Migrations</h1>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="error" :message="session('error')" />

    <!-- Search Section -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <x-form :action="route('admin.migrations.index')" method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="search">
                    Search
                </label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="Search migrations...">
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sort">
                    Sort By
                </label>
                <select id="sort"
                        name="sort"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="id" {{ request('sort', 'id') === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="migration" {{ request('sort') === 'migration' ? 'selected' : '' }}>Migration Name</option>
                    <option value="batch" {{ request('sort') === 'batch' ? 'selected' : '' }}>Batch</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="direction">
                    Direction
                </label>
                <select id="direction"
                        name="direction"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                </select>
            </div>
            <div>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'sort', 'direction']))
                    <a href="{{ route('admin.migrations.index') }}"
                       class="ml-2 text-gray-600 hover:text-gray-900">
                        Clear
                    </a>
                @endif
            </div>
        </x-form>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @if($migrations->isEmpty())
            <div class="text-center py-4">
                <p class="text-gray-500">No migrations found.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Migration
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Batch
                    </th>
                    @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'view'))
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($migrations as $migration)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $migration->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $migration->formatted_name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $migration->migration }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $migration->batch }}
                        </td>
                        @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'view'))
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.migrations.show', $migration->id) }}"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    Details
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $migrations->links() }}
            </div>
        @endif
    </div>
@endsection
