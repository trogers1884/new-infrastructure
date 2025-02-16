@extends('admin::layouts.admin')
@section('title', 'Resource Associations')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Resource Associations</h1>
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
            <a href="{{ route('admin.resource-associations.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Resource Association
            </a>
        </x-auth-check>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="error" :message="session('error')" />

    <!-- Search Section -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <x-form :action="route('admin.resource-associations.index')" method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <x-input-field
                    name="search"
                    label="Search"
                    :value="request('search')"
                    placeholder="Search by user, resource type, role or description..."
                />
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sort">
                    Sort By
                </label>
                <select id="sort"
                        name="sort"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                    <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>Date Updated</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="direction">
                    Order
                </label>
                <select id="direction"
                        name="direction"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'sort', 'direction']))
                    <a href="{{ route('admin.resource-associations.index') }}"
                       class="ml-2 text-gray-600 hover:text-gray-900">
                        Clear
                    </a>
                @endif
            </div>
        </x-form>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @if($resourceAssociations->isEmpty())
            <div class="text-center py-4">
                <p class="text-gray-500">No resource associations found.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resource Type
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resource
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created At
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($resourceAssociations as $resourceAssociation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $resourceAssociation->user->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $resourceAssociation->user->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $resourceAssociation->resourceType->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $resourceAssociation->role->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $resourceAssociation->resource_value }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $resourceAssociation->description ?: 'No description' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $resourceAssociation->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                                <a href="{{ route('admin.resource-associations.edit', $resourceAssociation) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            </x-auth-check>

                            <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                                <x-form
                                    :action="route('admin.resource-associations.destroy', $resourceAssociation)"
                                    method="DELETE"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this resource association?');"
                                >
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </x-form>
                            </x-auth-check>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $resourceAssociations->links() }}
            </div>
        @endif
    </div>
@endsection
