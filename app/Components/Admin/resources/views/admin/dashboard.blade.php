@extends('admin::layouts.admin')
@section('title', 'Admin Dashboard')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <main class="flex-1">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- System Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">System Statistics</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Users</span>
                        <span class="font-semibold">{{ $systemStats['users_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Roles</span>
                        <span class="font-semibold">{{ $systemStats['roles_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">User Roles</span>
                        <span class="font-semibold">{{ $systemStats['user_roles_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Resource Types</span>
                        <span class="font-semibold">{{ $systemStats['resources_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Resource Type Mappings</span>
                        <span class="font-semibold">{{ $systemStats['resource_mappings_count'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Database Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Database Statistics</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Database Size</span>
                        <span class="font-semibold">{{ $dbStats[0]->db_size }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Schemas</span>
                        <span class="font-semibold">{{ $dbStats[0]->schema_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tables</span>
                        <span class="font-semibold">{{ $dbStats[0]->table_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Views</span>
                        <span class="font-semibold">{{ $dbStats[0]->view_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Materialized Views</span>
                        <span class="font-semibold">{{ $dbStats[0]->materialized_view_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Indexes</span>
                        <span class="font-semibold">{{ $dbStats[0]->index_count }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database I/O Monitor -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Database I/O Activity</h2>
            <div class="h-64">
                <canvas id="ioChart"></canvas>
            </div>
        </div>
        <!-- Database Operations Monitor -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Database Operations</h2>
            <div class="h-64">
                <canvas id="operationsChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the I/O chart
            const ctx = document.getElementById('ioChart').getContext('2d');
            const ioChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Blocks Read/sec',
                        borderColor: '#EF4444',
                        data: [],
                        fill: false
                    }, {
                        label: 'Cache Hits/sec',
                        borderColor: '#10B981',
                        data: [],
                        fill: false
                    }, {
                        label: 'Tuples Returned/sec',
                        borderColor: '#3B82F6',
                        data: [],
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    animation: false
                }
            });

            // Initialize the operations chart
            const opCtx = document.getElementById('operationsChart').getContext('2d');
            const operationsChart = new Chart(opCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Inserts/sec',
                        borderColor: '#10B981',
                        data: [],
                        fill: false
                    }, {
                        label: 'Updates/sec',
                        borderColor: '#F59E0B',
                        data: [],
                        fill: false
                    }, {
                        label: 'Deletes/sec',
                        borderColor: '#EF4444',
                        data: [],
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    animation: false
                }
            });

            // Function to update both charts
            async function updateCharts() {
                try {
                    const response = await fetch('{{ route('admin.database.io') }}');
                    const data = await response.json();

                    const timestamp = new Date().toLocaleTimeString();

                    // Update I/O Chart
                    ioChart.data.labels.push(timestamp);
                    ioChart.data.datasets[0].data.push(data.blks_read);
                    ioChart.data.datasets[1].data.push(data.blks_hit);
                    ioChart.data.datasets[2].data.push(data.tup_returned);

                    // Update Operations Chart
                    operationsChart.data.labels.push(timestamp);
                    operationsChart.data.datasets[0].data.push(data.tup_inserted);
                    operationsChart.data.datasets[1].data.push(data.tup_updated);
                    operationsChart.data.datasets[2].data.push(data.tup_deleted);

                    // Keep only last 20 points for both charts
                    if (ioChart.data.labels.length > 20) {
                        ioChart.data.labels.shift();
                        ioChart.data.datasets.forEach(dataset => dataset.data.shift());

                        operationsChart.data.labels.shift();
                        operationsChart.data.datasets.forEach(dataset => dataset.data.shift());
                    }

                    // Update both charts
                    ioChart.update();
                    operationsChart.update();
                } catch (error) {
                    console.error('Error fetching database stats:', error);
                }
            }

            // Update every 2 seconds
            setInterval(updateCharts, 2000);
            // Initial update
            updateCharts();
        });
    </script>
@endsection
