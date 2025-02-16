@extends('admin::layouts.admin')
@section('title', 'User Roles')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">User Role Assignments</h1>
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
            <a href="{{ route('admin.user-roles.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Assign New Role
            </a>
        </x-auth-check>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="error" :message="session('error')" />

    <!-- Search Section -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <x-form
            :action="route('admin.user-roles.index')"
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
                       placeholder="Search by user name, role name or description...">
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sort">
                    Sort By
                </label>
                <select id="sort"
                        name="sort"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="user_name" {{ request('sort') === 'user_name' ? 'selected' : '' }}>User Name</option>
                    <option value="role_name" {{ request('sort') === 'role_name' ? 'selected' : '' }}>Role Name</option>
                    <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>Date Assigned</option>
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
                @if(request('search') || request('sort') || request('direction'))
                    <a href="{{ route('admin.user-roles.index') }}"
                       class="ml-2 text-gray-600 hover:text-gray-900">
                        Clear
                    </a>
                @endif
            </div>
        </x-form>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @if($userRoles->isEmpty())
            <div class="text-center py-4">
                <p class="text-gray-500">No user role assignments found.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date Assigned
                    </th>
                    @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'edit') ||
                        auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'delete'))
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($userRoles as $userRole)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $userRole->user->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $userRole->user->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $userRole->role->name }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $userRole->description ?: 'No description' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $userRole->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'edit') ||
                            auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'delete'))
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                                    <a href="{{ route('admin.user-roles.edit', $userRole) }}"
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                </x-auth-check>

                                <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                                    <x-form
                                        :action="route('admin.user-roles.destroy', $userRole)"
                                        method="DELETE"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to remove this role from the user?');"
                                    >
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900">
                                            Remove
                                        </button>
                                    </x-form>
                                </x-auth-check>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $userRoles->links() }}
            </div>
        @endif
    </div>
@endsection
