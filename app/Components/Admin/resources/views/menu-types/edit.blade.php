@extends('admin::layouts.admin')
@section('title', 'Edit Menu Type')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Menu Type</h1>
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
            <x-form
                :action="route('admin.menu-types.update', $menuType)"
                method="PUT"
            >
                <x-input-field
                    name="name"
                    label="Name"
                    :value="old('name', $menuType->name)"
                    required
                    maxlength="100"
                    placeholder="Enter menu type name"
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
                        placeholder="Enter menu type description">{{ old('description', $menuType->description) }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">
                        Provide a clear description of what this menu type represents. Maximum 255 characters.
                    </p>
                </div>

                <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Menu Type
                        </button>
                    </div>
                </x-auth-check>
            </x-form>

            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                @if($menuType->navigation_items_count > 0)
                                    This menu type has {{ $menuType->navigation_items_count }} associated navigation items and cannot be deleted.
                                @else
                                    Once you delete a menu type, it cannot be recovered.
                                @endif
                            </p>
                        </div>
                        <x-form
                            :action="route('admin.menu-types.destroy', $menuType)"
                            method="DELETE"
                            onsubmit="return confirm('Are you sure you want to delete this menu type? This action cannot be undone.');"
                        >
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline {{ $menuType->navigation_items_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $menuType->navigation_items_count > 0 ? 'disabled' : '' }}>
                                Delete Menu Type
                            </button>
                        </x-form>
                    </div>
                </div>
            </x-auth-check>
        </div>

        @if($menuType->navigation_items_count > 0)
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8">
                <h2 class="text-lg font-semibold mb-4">Associated Navigation Items</h2>
                <p class="text-sm text-gray-600 mb-2">
                    This menu type is currently being used by {{ $menuType->navigation_items_count }} navigation items.
                    You must remove or reassign these items before this menu type can be deleted.
                </p>
                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                    View Navigation Items
                </a>
            </div>
        @endif
    </div>
@endsection
