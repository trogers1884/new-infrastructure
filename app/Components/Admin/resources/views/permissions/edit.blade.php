@extends('admin::layouts.admin')
@section('title', 'Edit Permission')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Permission</h1>
            <a href="{{ route('admin.permissions.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>

        @if($errors->any())
            <x-alert type="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <x-form :action="route('admin.permissions.update', $permission)" method="PUT">
                <x-input-field
                    name="name"
                    label="Name"
                    :value="old('name', $permission->name)"
                    required
                />

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Description
                    </label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                        id="description"
                        name="description"
                        rows="3">{{ old('description', $permission->description) }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Permission
                    </button>
                </div>
            </x-form>

            <!-- Danger Zone -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
                    <x-form
                        :action="route('admin.permissions.destroy', $permission)"
                        method="DELETE"
                        onsubmit="return confirm('Are you sure you want to delete this permission? This action cannot be undone.');"
                    >
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Delete Permission
                        </button>
                    </x-form>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Once you delete this permission, there is no going back. Please be certain.
                </p>
            </div>
        </div>
    </div>
@endsection
