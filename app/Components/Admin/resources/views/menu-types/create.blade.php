@extends('admin::layouts.admin')
@section('title', 'Create Menu Type')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create Menu Type</h1>
            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                <a href="{{ route('admin.menu-types.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </x-auth-check>
        </div>

        @if($errors->any())
            <x-alert type="error" message="Please fix the following errors:">
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
                <x-form :action="route('admin.menu-types.store')" method="POST">
                    <x-input-field
                        name="name"
                        label="Name"
                        :value="old('name')"
                        required
                        maxlength="100"
                        placeholder="Enter menu type name"
                        autofocus
                    >
                        <p class="text-gray-600 text-xs mt-1">
                            The name must be unique and can only contain letters, numbers, spaces, hyphens, and underscores.
                            Maximum 100 characters.
                        </p>
                    </x-input-field>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description
                        </label>
                        <textarea
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                            id="description"
                            name="description"
                            rows="4"
                            maxlength="255"
                            placeholder="Enter menu type description">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">
                            Provide a clear description of what this menu type represents. Maximum 255 characters.
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                            <a href="{{ route('admin.menu-types.index') }}"
                               class="text-gray-600 hover:text-gray-800">
                                Cancel
                            </a>
                        </x-auth-check>

                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Create Menu Type
                        </button>
                    </div>
                </x-form>
            </x-auth-check>
        </div>
    </div>
@endsection
