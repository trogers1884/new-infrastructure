@extends('admin::layouts.admin')
@section('title', 'Manage User Roles')
@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Manage Roles: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Users
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Current Role Assignments --}}
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-semibold mb-4">Current Role Assignments</h2>

            @if($userAccessRoles->isEmpty())
                <p class="text-gray-500 mb-4">No roles currently assigned.</p>
            @else
                <table class="min-w-full mb-4">
                    <thead>
                    <tr>
                        <th class="text-left">Role</th>
                        <th class="text-left">Context</th>
                        <th class="text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($userAccessRoles as $userRole)
                        <tr class="border-t">
                            <td class="py-2">{{ $userRole->role->name }}</td>
                            <td>
                                @if($userRole->store)
                                    Store: {{ $userRole->store->name }}
                                @elseif($userRole->group)
                                    Group: {{ $userRole->group->name }}
                                @endif
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.users.roles.remove', ['user' => $user->id, 'userAccessRole' => $userRole->id]) }}"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to remove this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            {{-- Assign New Role Form --}}
            <h2 class="text-xl font-semibold mb-4">Assign New Role</h2>
            <form method="POST" action="{{ route('admin.users.roles.assign', $user) }}">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    {{-- Role Selection --}}
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role_id" id="role_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Select a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Context Selection --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Store Selection --}}
                        <div>
                            <label for="store_id" class="block text-sm font-medium text-gray-700">Store (Optional)</label>
                            <select name="store_id" id="store_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Select a store...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Group Selection --}}
                        <div>
                            <label for="group_id" class="block text-sm font-medium text-gray-700">Group (Optional)</label>
                            <select name="group_id" id="group_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Select a group...</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Assign Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Make store and group selections mutually exclusive
        document.getElementById('store_id').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('group_id').value = '';
            }
        });

        document.getElementById('group_id').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('store_id').value = '';
            }
        });
    </script>
@endsection
