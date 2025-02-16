@extends('admin::layouts.admin')
@section('title', 'Edit Resource Type')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Resource Type</h1>
            <a href="{{ route('admin.resource-types.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
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
            <x-form :action="route('admin.resource-types.update', $resourceType)" method="PUT">
                <x-input-field
                    name="name"
                    label="Name"
                    :value="old('name', $resourceType->name)"
                    placeholder="Enter resource type name"
                    required
                >
                    <p class="text-gray-600 text-xs mt-1">The name must be unique and cannot exceed 255 characters.</p>
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
                        placeholder="Enter resource type description">{{ old('description', $resourceType->description) }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Provide a clear description of what this resource type represents.</p>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Resource Type
                    </button>
                </div>
            </x-form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
                    <x-form
                        :action="route('admin.resource-types.destroy', $resourceType)"
                        method="DELETE"
                        onsubmit="return confirm('Are you sure you want to delete this resource type? This action cannot be undone.');"
                    >
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Delete Resource Type
                        </button>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection
