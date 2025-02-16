@extends('admin::layouts.admin')
@section('title', 'Edit Resource Type Mapping')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Resource Type Mapping</h1>
            @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'view'))
                <a href="{{ route('admin.resource-type-mappings.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            @endif
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'edit'))
                <x-form
                    :action="route('admin.resource-type-mappings.update', $resource_type_mapping->resource_type_id)"
                    method="PUT"
                    id="editForm">

                    <!-- Resource Type Selection -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="resource_type_id">
                            Resource Type <span class="text-red-500">*</span>
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('resource_type_id') border-red-500 @enderror"
                                id="resource_type_id"
                                name="resource_type_id"
                                required>
                            <option value="">Select Resource Type</option>
                            @foreach($resourceTypes as $resourceType)
                                <option value="{{ $resourceType->id }}"
                                    {{ (old('resource_type_id', $resource_type_mapping->resource_type_id) == $resourceType->id) ? 'selected' : '' }}>
                                    {{ $resourceType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('resource_type_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Select the resource type to map to a database table</p>
                    </div>

                    <!-- Schema Selection -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="table_schema">
                            Database Schema <span class="text-red-500">*</span>
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('table_schema') border-red-500 @enderror"
                                id="table_schema"
                                name="table_schema"
                                required>
                            <option value="">Select Schema</option>
                            @foreach($schemas as $schema)
                                <option value="{{ $schema->schema_name }}"
                                    {{ old('table_schema', $resource_type_mapping->table_schema) == $schema->schema_name ? 'selected' : '' }}>
                                    {{ $schema->schema_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('table_schema')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Select the database schema containing the target table</p>
                    </div>

                    <!-- Table Selection -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="table_name">
                            Table Name <span class="text-red-500">*</span>
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('table_name') border-red-500 @enderror"
                                id="table_name"
                                name="table_name"
                                required>
                            <option value="">Select Table</option>
                            @if($resource_type_mapping->table_name)
                                <option value="{{ $resource_type_mapping->table_name }}" selected>
                                    {{ $resource_type_mapping->table_name }}
                                </option>
                            @endif
                        </select>
                        @error('table_name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Select the table to map the resource type to</p>
                    </div>

                    <!-- Column Selection -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="resource_value_column">
                            Value Column <span class="text-red-500">*</span>
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('resource_value_column') border-red-500 @enderror"
                                id="resource_value_column"
                                name="resource_value_column"
                                required>
                            <option value="">Select Column</option>
                            @if($resource_type_mapping->resource_value_column)
                                <option value="{{ $resource_type_mapping->resource_value_column }}" selected>
                                    {{ $resource_type_mapping->resource_value_column }}
                                </option>
                            @endif
                        </select>
                        @error('resource_value_column')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-xs mt-1">Select the column that contains the resource identifier</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'view'))
                            <button type="button"
                                    onclick="window.location.href='{{ route('admin.resource-type-mappings.index') }}'"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </button>
                        @endif
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Mapping
                        </button>
                    </div>
                </x-form>

                <!-- Delete Section -->
                @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'delete'))
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
                            <x-form
                                :action="route('admin.resource-type-mappings.destroy', $resource_type_mapping->resource_type_id)"
                                method="DELETE"
                                onsubmit="return confirm('Are you sure you want to delete this mapping? This action cannot be undone.');">
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Delete Mapping
                                </button>
                            </x-form>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-red-500 text-center py-4">
                    You do not have permission to edit resource type mappings.
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            function initializeDynamicSelects() {
                console.log('Initializing dynamic selects');

                const schemaSelect = document.getElementById('table_schema');
                const tableSelect = document.getElementById('table_name');
                const columnSelect = document.getElementById('resource_value_column');

                // Helper function to show loading state
                function setLoading(select, loading) {
                    select.disabled = loading;
                    if (loading) {
                        select.innerHTML = '<option value="">Loading...</option>';
                    }
                }

                // Helper function to handle errors
                function handleError(message) {
                    console.error('Error:', message);
                    alert(message);
                }

                // Load tables when schema changes
                schemaSelect.addEventListener('change', async function() {
                    const schema = this.value;

                    // Reset and disable dependent fields
                    tableSelect.disabled = !schema;
                    columnSelect.disabled = true;
                    columnSelect.innerHTML = '<option value="">Select Column</option>';

                    if (schema) {
                        try {
                            setLoading(tableSelect, true);

                            const response = await fetch('/admin/resource-type-mappings/ajax/tables?schema=' + encodeURIComponent(schema));
                            if (!response.ok) throw new Error(`Failed to fetch tables: ${response.status}`);

                            const tables = await response.json();

                            tableSelect.innerHTML = '<option value="">Select Table</option>';
                            tables.forEach(table => {
                                const option = new Option(table.table_name, table.table_name);
                                if (table.table_name === '{{ $resource_type_mapping->table_name }}') {
                                    option.selected = true;
                                }
                                tableSelect.add(option);
                            });

                            tableSelect.disabled = false;

                            // If we have a selected table, trigger the columns load
                            if (tableSelect.value) {
                                tableSelect.dispatchEvent(new Event('change'));
                            }
                        } catch (error) {
                            handleError('Error loading tables: ' + error.message);
                        }
                    }
                });

                // Load columns when table changes
                tableSelect.addEventListener('change', async function() {
                    const schema = schemaSelect.value;
                    const table = this.value;

                    columnSelect.disabled = !table;

                    if (schema && table) {
                        try {
                            setLoading(columnSelect, true);

                            const response = await fetch('/admin/resource-type-mappings/ajax/columns?schema=' +
                                encodeURIComponent(schema) + '&table=' + encodeURIComponent(table));
                            if (!response.ok) throw new Error(`Failed to fetch columns: ${response.status}`);

                            const columns = await response.json();

                            columnSelect.innerHTML = '<option value="">Select Column</option>';
                            columns.forEach(column => {
                                const option = new Option(column.column_name, column.column_name);
                                if (column.column_name === '{{ $resource_type_mapping->resource_value_column }}') {
                                    option.selected = true;
                                }
                                columnSelect.add(option);
                            });

                            columnSelect.disabled = false;
                        } catch (error) {
                            handleError('Error loading columns: ' + error.message);
                        }
                    }
                });

                // Trigger initial loads if we have existing values
                if (schemaSelect.value) {
                    schemaSelect.dispatchEvent(new Event('change'));
                }
            }

            // Initialize when the document is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeDynamicSelects);
            } else {
                initializeDynamicSelects();
            }
        </script>
    @endpush
@endsection
