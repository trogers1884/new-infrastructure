@extends('admin::layouts.admin')
@section('title', 'Resource Type Mappings')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Resource Type Mappings</h1>
        <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'create']">
            <a href="{{ route('admin.resource-type-mappings.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Mapping
            </a>
        </x-auth-check>
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="error" :message="session('error')" />

    <!-- Search and Filter Section -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <x-form
            :action="route('admin.resource-type-mappings.index')"
            method="GET"
            class="flex flex-wrap gap-4 items-end"
        >
            <!-- Search Field -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="search">
                    Search
                </label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="Search by schema, table, column or resource type...">
            </div>

            <!-- Schema Filter -->
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="schema">
                    Schema
                </label>
                <select id="schema"
                        name="schema"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">All Schemas</option>
                    @foreach($resource_type_mappings->pluck('table_schema')->unique() as $schema)
                        <option value="{{ $schema }}" {{ request('schema') == $schema ? 'selected' : '' }}>
                            {{ $schema }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort Field -->
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sort">
                    Sort By
                </label>
                <select id="sort"
                        name="sort"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="table_schema" {{ request('sort', 'table_schema') === 'table_schema' ? 'selected' : '' }}>Schema</option>
                    <option value="table_name" {{ request('sort') === 'table_name' ? 'selected' : '' }}>Table</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                </select>
            </div>

            <!-- Sort Direction -->
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="direction">
                    Order
                </label>
                <select id="direction"
                        name="direction"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Apply Filters
                </button>
                @if(request('search') || request('schema') || request('sort') || request('direction'))
                    <a href="{{ route('admin.resource-type-mappings.index') }}"
                       class="text-gray-600 hover:text-gray-900">
                        Clear Filters
                    </a>
                @endif
            </div>
        </x-form>
    </div>

    <!-- Results Table -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @if($resource_type_mappings->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No resource type mappings found.</p>
                @if(request('search') || request('schema') || request('sort') || request('direction'))
                    <p class="text-gray-400 mt-2">Try adjusting your search filters</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Resource Type
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Schema
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Table
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Value Column
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
                    @foreach($resource_type_mappings as $mapping)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $mapping->resourceType->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $mapping->table_schema }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $mapping->table_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $mapping->resource_value_column }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ $mapping->created_at->format('Y-m-d H:i:s') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'edit']">
                                        <a href="{{ route('admin.resource-type-mappings.edit', $mapping->resource_type_id) }}"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                    </x-auth-check>

                                    <x-auth-check :permission="['type' => $thisResourceType, 'value' => $thisResourceValue, 'action' => 'delete']">
                                        <x-form
                                            :action="route('admin.resource-type-mappings.destroy', $mapping->resource_type_id)"
                                            method="DELETE"
                                            class="inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this mapping? This action cannot be undone.');"
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
                {{ $resource_type_mappings->links() }}
            </div>
        @endif
    </div>
@endsection
