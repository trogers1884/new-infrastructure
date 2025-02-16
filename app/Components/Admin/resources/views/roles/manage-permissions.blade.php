@extends('admin::layouts.admin')
@section('title', 'Manage Role Permissions')
@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">
                Manage Permissions: {{ $role->name }}
            </h1>
            <div class="space-x-2">
                <a href="{{ route('admin.roles.edit', $role) }}"
                   class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Role
                </a>
                <a href="{{ route('admin.roles.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Roles
                </a>
            </div>
        </div>

        <x-alert type="success" :message="session('success')" />
        <x-alert type="error" :message="session('error')" />

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <x-form :action="route('admin.roles.permissions.update', $role)" method="POST">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700">Role Details</h2>
                    <p class="text-gray-600 mt-1">{{ $role->description ?: 'No description provided' }}</p>

                    @if($isSystemRole)
                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-yellow-700 text-sm">
                                <strong>Warning:</strong> Modifying permissions for this role may affect system-wide access controls.
                                Please proceed with caution.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Permissions</h2>

                        <div class="space-x-2">
                            <button type="button"
                                    onclick="selectAll()"
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                Select All
                            </button>
                            <button type="button"
                                    onclick="deselectAll()"
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                Deselect All
                            </button>
                        </div>
                    </div>

                    @if($permissions->isEmpty())
                        <p class="text-gray-500 text-center py-4">No permissions available.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($permissions as $permission)
                                <div class="relative flex items-start p-2 hover:bg-gray-50 rounded">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox"
                                               id="permission_{{ $permission->id }}"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                            {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}
                                            {{ ($criticalPermissions[$permission->id] && !$isSuperAdmin) ? 'disabled' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="permission_{{ $permission->id }}"
                                               class="font-medium text-gray-700">
                                            {{ $permission->name }}
                                        </label>
                                        @if($permission->description)
                                            <p class="text-gray-500">{{ $permission->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Permission Categories:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="font-medium">View:</span> Read-only access
                                </div>
                                <div>
                                    <span class="font-medium">Create:</span> Ability to add new items
                                </div>
                                <div>
                                    <span class="font-medium">Edit:</span> Modify existing items
                                </div>
                                <div>
                                    <span class="font-medium">Delete:</span> Remove items
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <button type="reset"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Reset Changes
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Permissions
                    </button>
                </div>
            </x-form>
        </div>
    </div>

    <script>
        function selectAll() {
            document.querySelectorAll('input[name="permissions[]"]:not([disabled])')
                .forEach(checkbox => checkbox.checked = true);
        }

        function deselectAll() {
            document.querySelectorAll('input[name="permissions[]"]:not([disabled])')
                .forEach(checkbox => checkbox.checked = false);
        }
    </script>
@endsection
