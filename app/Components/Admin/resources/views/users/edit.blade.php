@extends('admin::layouts.admin')

@section('title', 'Edit User')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Edit User: {{ $user->name }}</h1>
                <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                    <a href="{{ route('admin.users.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                </x-auth-check>
            </div>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <x-form :action="route('admin.users.update', $user)" method="PUT">
                    <x-input-field
                        name="name"
                        label="Name"
                        :value="old('name', $user->name)"
                        required
                    />

                    <x-input-field
                        type="email"
                        name="email"
                        label="Email"
                        :value="old('email', $user->email)"
                        required
                    />

                    <x-input-field
                        type="password"
                        name="password"
                        label="Password"
                    />
                    <p class="text-gray-600 text-xs mt-1">Leave blank to keep current password</p>

                    <x-input-field
                        type="password"
                        name="password_confirmation"
                        label="Confirm Password"
                    />

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="active"
                                   value="1"
                                   {{ old('active', $user->active) ? 'checked' : '' }}
                                   class="form-checkbox">
                            <span class="ml-2">Active</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                            <a href="{{ route('admin.users.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </a>
                        </x-auth-check>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update User
                        </button>
                    </div>
                </x-form>
            </div>

            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                <div class="bg-white shadow-md rounded px-8 py-6 mt-4">
                    <h2 class="text-xl font-bold text-red-600 mb-4">Danger Zone</h2>
                    <x-form
                        :action="route('admin.users.destroy', $user)"
                        method="DELETE"
                        onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"
                    >
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Delete User
                        </button>
                    </x-form>
                </div>
            </x-auth-check>
        </x-auth-check>
    </div>
@endsection
