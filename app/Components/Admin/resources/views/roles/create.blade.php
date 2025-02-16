@extends('admin::layouts.admin')
@section('title', 'Create Role')
@section('content')
    <div class="max-w-2xl mx-auto">
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Create Role</h1>
                <a href="{{ route('admin.roles.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <x-form :action="route('admin.roles.store')" method="POST" class="space-y-6">
                    <x-input-field
                        name="name"
                        label="Name"
                        :value="old('name')"
                        required
                        autofocus
                        placeholder="Enter role name"
                    />
                    <p class="text-gray-600 text-xs mt-1">Choose a descriptive name for this role (e.g., "Content Editor", "Store Manager")</p>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description
                        </label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                                  id="description"
                                  name="description"
                                  rows="4"
                                  placeholder="Enter role description">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Provide a clear description of what this role represents and its responsibilities</p>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('admin.roles.index') }}"
                           class="text-gray-600 hover:text-gray-800">
                            Cancel
                        </a>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Create Role
                        </button>
                    </div>
                </x-form>
            </div>
        </x-auth-check>
    </div>
@endsection
