@extends('admin::layouts.admin')
@section('title', 'Menu Types')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Menu Types</h1>
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
            <a href="{{ route('admin.menu-types.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Menu Type
            </a>
        </x-auth-check>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="error" :message="session('error')" />

    <!-- Search Section -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <x-form
            :action="route('admin.menu-types.index')"
            method="GET"
            class="flex gap-4 items-end"
        >
            <div class="flex-1">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="search">
                    Search
                </label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="Search menu types...">
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sort">
                    Sort By
                </label>
                <select id="sort"
                        name="sort"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Name</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                    <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="direction">
                    Order
                </label>
                <select id="direction"
                        name="direction"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filter
                </button>
                @if(request('search') || request('sort') || request('direction'))
                    <a href="{{ route('admin.menu-types.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Clear
                    </a>
                @endif
            </div>
        </x-form>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @if($menuTypes->isEmpty())
            <div class="text-center py-4">
                <p class="text-gray-500">No menu types found.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Navigation Items
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Last Updated
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($menuTypes as $menuType)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $menuType->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $menuType->description ?: 'No description' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $menuType->navigation_items_count }} items
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $menuType->updated_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                                <a href="{{ route('admin.menu-types.edit', $menuType) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Edit
                                </a>
                            </x-auth-check>

                            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                                <x-form
                                    :action="route('admin.menu-types.destroy', $menuType)"
                                    method="DELETE"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this menu type? This action cannot be undone.');"
                                >
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 {{ $menuType->navigation_items_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $menuType->navigation_items_count > 0 ? 'disabled' : '' }}>
                                        Delete
                                    </button>
                                </x-form>
                            </x-auth-check>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $menuTypes->links() }}
            </div>
        @endif
    </div>
@endsection
