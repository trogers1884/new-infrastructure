@extends('admin::layouts.admin')
@section('title', 'Create Resource Association')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create Resource Association</h1>
            @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'view'))
                <a href="{{ route('admin.resource-associations.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            @endif
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Please fix the following errors:</strong>
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'create'))
                <x-form :action="route('admin.resource-associations.store')" method="POST">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">
                            User <span class="text-red-500">*</span>
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('user_id') border-red-500 @enderror"
                                id="user_id"
                                name="user_id"
                                required>
                            <option value="">Select a user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="resource_type_id">
                            Resource Type <span class="text-red-500">*</span>
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('resource_type_id') border-red-500 @enderror"
                                id="resource_type_id"
                                name="resource_type_id"
                                required>
                            <option value="">Select a resource type</option>
                            @foreach($resourceTypes as $resourceType)
                                <option value="{{ $resourceType->id }}" {{ old('resource_type_id') == $resourceType->id ? 'selected' : '' }}>
                                    {{ $resourceType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('resource_type_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role_id">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role_id') border-red-500 @enderror"
                                id="role_id"
                                name="role_id"
                                required
                                disabled>
                            <option value="">Select a user first</option>
                        </select>
                        @error('role_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Only roles assigned to the selected user will be shown.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="resource_id">
                            Resource
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('resource_id') border-red-500 @enderror"
                                id="resource_id"
                                name="resource_id"
                                disabled>
                            <option value="">Select a resource type first</option>
                        </select>
                        @error('resource_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Select a resource type first to load available resources.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description
                        </label>
                        <textarea
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Enter a description">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'view'))
                            <a href="{{ route('admin.resource-associations.index') }}"
                               class="text-gray-600 hover:text-gray-800">
                                Cancel
                            </a>
                        @else
                            <div></div>
                        @endif
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Create Resource Association
                        </button>
                    </div>
                </x-form>
            @else
                <div class="text-red-600 text-center py-4">
                    You do not have permission to create resource associations.
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const userSelect = document.getElementById('user_id');
                const roleSelect = document.getElementById('role_id');
                const resourceTypeSelect = document.getElementById('resource_type_id');
                const resourceSelect = document.getElementById('resource_id');

                // Function to load roles for selected user
                const loadRoles = async () => {
                    const userId = userSelect.value;
                    roleSelect.disabled = true;

                    if (!userId) {
                        roleSelect.innerHTML = '<option value="">Select a user first</option>';
                        roleSelect.disabled = true;
                        return;
                    }

                    try {
                        roleSelect.innerHTML = '<option value="">Loading...</option>';

                        const response = await fetch(`/admin/resource-associations/roles?user_id=${userId}`);
                        if (!response.ok) {
                            if (response.status === 403) {
                                roleSelect.innerHTML = '<option value="">Unauthorized to view roles</option>';
                                return;
                            }
                            throw new Error('Failed to fetch roles');
                        }

                        const roles = await response.json();

                        roleSelect.innerHTML = '<option value="">Select a role</option>';
                        roles.forEach(role => {
                            const option = document.createElement('option');
                            option.value = role.id;
                            option.textContent = role.name;
                            option.title = role.description || '';
                            if (role.id == {{ old('role_id', 0) }}) {
                                option.selected = true;
                            }
                            roleSelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading roles:', error);
                        roleSelect.innerHTML = '<option value="">Error loading roles</option>';
                    } finally {
                        roleSelect.disabled = false;
                    }
                };

                // Function to load resources for selected resource type
                const loadResources = async () => {
                    const resourceTypeId = resourceTypeSelect.value;
                    resourceSelect.disabled = true;

                    if (!resourceTypeId) {
                        resourceSelect.innerHTML = '<option value="">Select a resource type first</option>';
                        resourceSelect.disabled = false;
                        return;
                    }

                    try {
                        resourceSelect.innerHTML = '<option value="">Loading...</option>';

                        const response = await fetch(`/admin/resource-associations/resources?resource_type_id=${resourceTypeId}`);
                        if (!response.ok) {
                            if (response.status === 403) {
                                resourceSelect.innerHTML = '<option value="">Unauthorized to view resources</option>';
                                return;
                            }
                            throw new Error('Failed to fetch resources');
                        }

                        const resources = await response.json();

                        resourceSelect.innerHTML = '<option value="">Select a resource</option>';
                        resources.forEach(resource => {
                            const option = document.createElement('option');
                            option.value = resource.id;
                            option.textContent = resource.value || `Resource ${resource.id}`;
                            if (resource.id === {{ old('resource_id', 0) }}) {
                                option.selected = true;
                            }
                            resourceSelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading resources:', error);
                        resourceSelect.innerHTML = '<option value="">Error loading resources</option>';
                    } finally {
                        resourceSelect.disabled = false;
                    }
                };

                // Load initial values if they exist
                if (userSelect.value) {
                    loadRoles();
                }

                if (resourceTypeSelect.value) {
                    loadResources();
                }

                // Add event listeners
                userSelect.addEventListener('change', loadRoles);
                resourceTypeSelect.addEventListener('change', loadResources);
            });
        </script>
    @endpush
@endsection
