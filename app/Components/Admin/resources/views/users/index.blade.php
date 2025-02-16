@extends('admin::layouts.admin')
@section('title', 'Users')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Users</h1>
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
            <a href="{{ route('admin.users.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New User
            </a>
        </x-auth-check>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="error" :message="session('error')" />

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
            <!-- Search and Filter Section -->
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <x-form :action="route('admin.users.index')" method="GET" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="search">
                            Search
                        </label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               placeholder="Search by name or email...">
                    </div>
                    <div class="w-48">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                            Status
                        </label>
                        <select id="status"
                                name="status"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="w-48">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="sort">
                            Sort By
                        </label>
                        <select id="sort"
                                name="sort"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'sort']))
                            <a href="{{ route('admin.users.index') }}"
                               class="ml-2 text-gray-600 hover:text-gray-900">
                                Clear
                            </a>
                        @endif
                    </div>
                </x-form>
            </div>

            <div class="mb-4 text-sm text-gray-600">
                Total Users: {{ $users->total() }}
            </div>

            @if($users->isEmpty())
                <div class="text-center py-4">
                    <p class="text-gray-500">No users found.</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                </x-auth-check>

                                <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                                    <x-form
                                        :action="route('admin.users.destroy', $user)"
                                        method="DELETE"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');"
                                    >
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900">
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
                    {{ $users->links() }}
                </div>
            @endif
        </x-auth-check>
    </div>
@endsection
