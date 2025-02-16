@extends('admin::layouts.admin')

@section('title', 'Create User')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Create User</h1>
                <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                    <a href="{{ route('admin.users.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                </x-auth-check>
            </div>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <x-form :action="route('admin.users.store')" method="POST">
                    <x-input-field
                        name="name"
                        label="Name"
                        :value="old('name')"
                        required
                    />

                    <x-input-field
                        type="email"
                        name="email"
                        label="Email"
                        :value="old('email')"
                        required
                    />

                    <x-input-field
                        type="password"
                        name="password"
                        label="Password"
                        required
                    />

                    <x-input-field
                        type="password"
                        name="password_confirmation"
                        label="Confirm Password"
                        required
                    />

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="active"
                                   value="1"
                                   {{ old('active', true) ? 'checked' : '' }}
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
                            Create User
                        </button>
                    </div>
                </x-form>
            </div>
        </x-auth-check>

        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']" else>
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="text-center py-4">
                    <p class="text-red-500">You don't have permission to create users.</p>
                    <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                        <a href="{{ route('admin.users.index') }}"
                           class="mt-4 inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Users List
                        </a>
                    </x-auth-check>
                </div>
            </div>
        </x-auth-check>
    </div>
@endsection
