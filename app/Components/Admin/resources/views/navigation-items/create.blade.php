@extends('admin::layouts.admin')
@section('title', 'Create Navigation Item')

@section('content')
    <div class="w-full max-w-4xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create Navigation Item</h1>
            <x-auth-check
                :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                <a href="{{ route('admin.navigation-items.index') }}"
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
            <x-form :action="route('admin.navigation-items.store')" method="POST">
                <!-- Menu Type -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="menu_type_id">
                        Menu Type <span class="text-red-500">*</span>
                    </label>
                    <select
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('menu_type_id') border-red-500 @enderror"
                        id="menu_type_id"
                        name="menu_type_id"
                        required>
                        <option value="">Select Menu Type</option>
                        @foreach($menuTypes as $menuType)
                            <option
                                value="{{ $menuType->id }}" {{ old('menu_type_id') == $menuType->id ? 'selected' : '' }}>
                                {{ $menuType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('menu_type_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Select the type of menu this item belongs to.</p>
                </div>

                <x-input-field
                    name="name"
                    label="Name"
                    :value="old('name')"
                    required
                    maxlength="255"
                    placeholder="Enter navigation item name"
                >
                    <p class="text-gray-600 text-xs mt-1">The display name of the navigation item.</p>
                </x-input-field>

                <!-- Route Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="route">
                        Route <span class="text-red-500">*</span>
                    </label>
                    <select
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('route') border-red-500 @enderror"
                        id="route"
                        name="route"
                        required>
                        <option value="">Select a Route</option>
                        @foreach($availableRoutes as $group => $routes)
                            <optgroup label="{{ Str::title($group) }}">
                                @foreach($routes as $route)
                                    <option value="{{ $route['name'] }}"
                                            {{ old('route') == $route['name'] ? 'selected' : '' }}
                                            title="{{ $route['uri'] }}">
                                        {{ $route['name'] }} ({{ $route['uri'] }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('route')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Select the route that this navigation item should link to.</p>
                </div>

                <!-- Icon Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="icon">
                        Icon
                    </label>
                    <div class="relative">
                        <select
                            class="shadow appearance-none border rounded w-full py-2 pl-10 pr-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('icon') border-red-500 @enderror"
                            id="icon"
                            name="icon">
                            <option value="">No Icon</option>
                            @foreach(\App\Helpers\IconHelper::getCommonIcons() as $icon)
                                <option value="{{ $icon['value'] }}"
                                        {{ old('icon') == $icon['value'] ? 'selected' : '' }}
                                        data-icon-class="{{ $icon['classes'] }}">
                                    {{ $icon['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i id="selectedIcon" class="text-gray-500"></i>
                        </div>
                    </div>
                    @error('icon')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Select an icon for this navigation item</p>

                    <!-- Icon Preview -->
                    <div class="mt-4">
                        <p class="text-sm font-bold mb-2">Available Icons:</p>
                        <div class="grid grid-cols-4 gap-4">
                            @foreach(\App\Helpers\IconHelper::getCommonIcons() as $previewIcon)
                                <div class="flex items-center p-2 cursor-pointer hover:bg-gray-100 rounded"
                                     onclick="document.getElementById('icon').value='{{ $previewIcon['value'] }}'; updateIconPreview();">
                                    <i class="{{ $previewIcon['classes'] }} mr-2"></i>
                                    <span class="text-sm">{{ $previewIcon['name'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <x-input-field
                    type="number"
                    name="order_index"
                    label="Order Index"
                    :value="old('order_index', 0)"
                    min="0"
                    step="1"
                >
                    <p class="text-gray-600 text-xs mt-1">Determines the display order of the navigation item (0 =
                        first).</p>
                </x-input-field>

                <!-- Parent Item -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="parent_id">
                        Parent Item
                    </label>
                    <select
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('parent_id') border-red-500 @enderror"
                        id="parent_id"
                        name="parent_id">
                        <option value="">No Parent</option>
                        @foreach($parentItems as $parentItem)
                            <option
                                value="{{ $parentItem->id }}" {{ old('parent_id') == $parentItem->id ? 'selected' : '' }}>
                                {{ $parentItem->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Optional parent item for creating nested navigation.</p>
                </div>

                <!-- Active Status -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                    @error('is_active')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Whether this navigation item should be visible in the
                        menu.</p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between">
                    <x-auth-check
                        :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'view']">
                        <a href="{{ route('admin.navigation-items.index') }}"
                           class="text-gray-600 hover:text-gray-800">
                            Cancel
                        </a>
                    </x-auth-check>

                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create Navigation Item
                    </button>
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Update icon preview initially
            updateIconPreview();

            // Add change event listener
            document.getElementById('icon').addEventListener('change', updateIconPreview);
        });

        function updateIconPreview() {
            const select = document.getElementById('icon');
            const selectedIcon = document.getElementById('selectedIcon');
            const option = select.options[select.selectedIndex];

            if (option && option.value) {
                const iconClass = option.getAttribute('data-icon-class');
                selectedIcon.className = iconClass;
            } else {
                selectedIcon.className = '';
            }
        }
    </script>
@endpush
