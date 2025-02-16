@extends('admin::layouts.admin')
@section('title', 'Migration Details')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Migration Details</h1>
        @if(auth()->user()->checkResourcePermission($thisResourceType, $thisResourceValue, 'view'))
            <a href="{{ route('admin.migrations.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        @endif
    </div>

    <x-alert type="error" :message="session('error')" />

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="grid grid-cols-1 gap-6">
            <!-- ID -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    ID
                </label>
                <p class="text-gray-900">
                    {{ $migration->id }}
                </p>
            </div>

            <!-- Migration Name -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Migration Name
                </label>
                <p class="text-gray-900">
                    {{ $migration->formatted_name }}
                </p>
            </div>

            <!-- Full Migration Name -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Full Migration Name
                </label>
                <p class="text-gray-500 font-mono text-sm">
                    {{ $migration->migration }}
                </p>
            </div>

            <!-- Batch -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Batch Number
                </label>
                <p class="text-gray-900">
                    {{ $migration->batch }}
                </p>
            </div>

            <!-- Timestamp Info -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Timestamp
                </label>
                <p class="text-gray-500">
                    @php
                        $timestamp = preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}/', $migration->migration, $matches)
                            ? $matches[0]
                            : null;
                        if ($timestamp) {
                            $datetime = \Carbon\Carbon::createFromFormat('Y_m_d_His', $timestamp);
                            echo $datetime->format('F j, Y g:i:s A');
                        } else {
                            echo 'No timestamp available';
                        }
                    @endphp
                </p>
            </div>

            <!-- Migration Type -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Migration Type
                </label>
                <p class="text-gray-900">
                    @php
                        $type = 'Unknown';
                        if (str_contains(strtolower($migration->migration), 'create')) {
                            $type = 'Create Table';
                        } elseif (str_contains(strtolower($migration->migration), 'add')) {
                            $type = 'Add Column';
                        } elseif (str_contains(strtolower($migration->migration), 'update')) {
                            $type = 'Update Table';
                        } elseif (str_contains(strtolower($migration->migration), 'alter')) {
                            $type = 'Alter Table';
                        }
                    @endphp
                    {{ $type }}
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="mt-6 flex justify-between">
        @if($previousMigration = \App\Models\Migration::where('id', '<', $migration->id)->orderBy('id', 'desc')->first())
            <a href="{{ route('admin.migrations.show', $previousMigration->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded">
                ← Previous Migration
            </a>
        @else
            <div></div>
        @endif

        @if($nextMigration = \App\Models\Migration::where('id', '>', $migration->id)->orderBy('id')->first())
            <a href="{{ route('admin.migrations.show', $nextMigration->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded">
                Next Migration →
            </a>
        @endif
    </div>
@endsection
