@extends('admin::layouts.admin')
@section('title', 'Edit Web Page')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Web Page</h1>
            <a href="{{ route('admin.web-pages.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
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
            <x-form :action="route('admin.web-pages.update', $webPage)" method="PUT">
                <div class="mb-6">
                    <x-input-field
                        type="text"
                        name="url"
                        label="URL"
                        :value="old('url', $webPage->url)"
                        placeholder="Enter web page URL"
                        required>
                        <x-slot name="labelSuffix">
                            <span class="text-red-500">*</span>
                        </x-slot>
                        <x-slot name="hint">
                            <p class="text-gray-600 text-xs mt-1">The URL must be unique and cannot exceed 255 characters.</p>
                        </x-slot>
                    </x-input-field>
                </div>

                <div class="mb-6">
                    <x-input-field
                        type="textarea"
                        name="description"
                        label="Description"
                        :value="old('description', $webPage->description)"
                        placeholder="Enter web page description"
                        rows="4">
                        <x-slot name="hint">
                            <p class="text-gray-600 text-xs mt-1">Provide a clear description of what this web page represents.</p>
                        </x-slot>
                    </x-input-field>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Web Page
                    </button>
                </div>
            </x-form>

            <!-- Danger Zone -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
                    <x-form
                        :action="route('admin.web-pages.destroy', $webPage)"
                        method="DELETE"
                        onsubmit="return confirm('Are you sure you want to delete this web page? This action cannot be undone.');">
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Delete Web Page
                        </button>
                    </x-form>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Once you delete this web page, there is no going back. Please be certain.
                </p>
            </div>
        </div>
    </div>
@endsection
