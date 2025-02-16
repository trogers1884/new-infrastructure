@extends('admin::layouts.admin')
@section('title', 'Edit User Role Assignment')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit User Role Assignment</h1>
            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                <a href="{{ route('admin.user-roles.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </x-auth-check>
        </div>

        @if($errors->any())
            <x-alert type="error" :message="'Please fix the following errors:'">
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                <x-form :action="route('admin.user-roles.update', $userRole)" method="PUT">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">
                            User <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id"
                                id="user_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('user_id') border-red-500 @enderror"
                                required>
                            <option value="">Select a user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id', $userRole->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Select the user to assign the role to.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role_id">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role_id"
                                id="role_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role_id') border-red-500 @enderror"
                                required>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $userRole->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Select the role to assign to the user.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description
                        </label>
                        <textarea
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Enter description for this role assignment">{{ old('description', $userRole->description) }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Optional: Provide a reason or note for this role assignment.</p>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Role Assignment
                        </button>
                    </div>
                </x-form>

                <!-- Danger Zone -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
                        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                            <x-form
                                :action="route('admin.user-roles.destroy', $userRole)"
                                method="DELETE"
                                onsubmit="return confirm('Are you sure you want to remove this role from the user? This action cannot be undone.');"
                            >
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Remove Role Assignment
                                </button>
                            </x-form>
                        </x-auth-check>
                    </div>
                </div>
            </x-auth-check>
        </div>
    </div>
@endsection
