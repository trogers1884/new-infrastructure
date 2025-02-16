@extends('admin::layouts.admin')
@section('title', 'Navigation Items')

@section('content')
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Navigation Items</h1>
            <x-auth-check
                :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
                <a href="{{ route('admin.navigation-items.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New Item
                </a>
            </x-auth-check>
        </div>

        <x-alert type="success" :message="session('success')"/>
        <x-alert type="error" :message="session('error')"/>

        <!-- Search Section -->
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <x-form
                :action="route('admin.navigation-items.index')"
                method="GET"
                class="flex gap-4 items-end"
            >
                <div class="flex-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="search">
                        Search
                    </label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="Search by name or route...">
                </div>

                <div class="w-64">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="menu_type_id">
                        Menu Type
                    </label>
                    <select id="menu_type_id"
                            name="menu_type_id"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">All Menu Types</option>
                        @foreach($menuTypes as $menuType)
                            <option
                                value="{{ $menuType->id }}" {{ request('menu_type_id') == $menuType->id ? 'selected' : '' }}>
                                {{ $menuType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Filter
                    </button>
                    @if(request('search') || request('menu_type_id'))
                        <a href="{{ route('admin.navigation-items.index') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Clear
                        </a>
                    @endif
                </div>
            </x-form>
        </div>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @if($navItems->isEmpty())
                <div class="text-center py-4">
                    <p class="text-gray-500">No navigation items found.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Menu Type
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Icon
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Route
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Parent
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($navItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $item->menuType->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item->icon)
                                            <i class="{{ \App\Helpers\IconHelper::getIconClasses($item->icon) }} mr-2"></i>
                                        @endif
                                        {{ $item->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->icon)
                                        {{ $item->icon }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm">{{ $item->route }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    {{ $item->order_index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $item->parent?->name ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <x-auth-check
                                            :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                                            <a href="{{ route('admin.navigation-items.edit', $item) }}"
                                               class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </a>
                                        </x-auth-check>

                                        <x-auth-check
                                            :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                                            <x-form
                                                :action="route('admin.navigation-items.destroy', $item)"
                                                method="DELETE"
                                                class="inline-block"
                                                onsubmit="return confirm('Are you sure you want to delete this navigation item? This action cannot be undone.');"
                                            >
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </x-form>
                                        </x-auth-check>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $navItems->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
