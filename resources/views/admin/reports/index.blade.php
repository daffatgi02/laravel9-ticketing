@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Reports & Analytics</h2>
            <div>
                <a href="{{ route('admin.reports.export') }}" class="btn btn-outline-success">
                    <i class="fas fa-file-excel me-1"></i> Export to Excel
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-chart-line me-2"></i> Ticket Trends</span>
                            <div>
                                <select id="trendPeriod" class="form-select form-select-sm" style="width: 150px;">
                                    <option value="month">Last 6 Months</option>
                                    <option value="quarter">By Quarter</option>
                                    <option value="year">By Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="ticketTrendsChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i> Tickets by Category
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i> Tickets by Department
                    </div>
                    <div class="card-body">
                        <canvas id="departmentChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-2"></i> Tickets by Priority
                        </div>
                        <div class="card-body">
                            <canvas id="priorityChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-2"></i> Tickets by Status
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-tachometer-alt me-2"></i> Performance Metrics
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">Average Resolution Time</h6>
                                            <h2 class="mb-0">{{ $metrics['avg_resolution_time'] ?? '0h 0m' }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">First Response Time</h6>
                                            <h2 class="mb-0">{{ $metrics['avg_first_response'] ?? '0h 0m' }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">Resolution Rate</h6>
                                            <h2 class="mb-0">{{ $metrics['resolution_rate'] ?? '0%' }}</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">Reopened Rate</h6>
                                            <h2 class="mb-0">{{ $metrics['reopened_rate'] ?? '0%' }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list me-2"></i> Ticket Summary</span>
                        <div>
                            <form action="{{ url('/admin/reports') }}" method="GET" class="d-flex">
                                <select name="period" class="form-select form-select-sm me-2" style="width: 150px;">
                                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week
                                    </option>
                                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month
                                    </option>
                                    <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>This
                                        Quarter</option>
                                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year
                                    </option>
                                    <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>All Time
                                    </option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Apply</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>New</th>
                                    <th>Assigned</th>
                                    <th>In Progress</th>
                                    <th>Resolved</th>
                                    <th>Closed</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary ?? [] as $category => $data)
                                    <tr>
                                        <td>{{ $category }}</td>
                                        <td>{{ $data['new'] ?? 0 }}</td>
                                        <td>{{ $data['assigned'] ?? 0 }}</td>
                                        <td>{{ $data['in_progress'] ?? 0 }}</td>
                                        <td>{{ $data['resolved'] ?? 0 }}</td>
                                        <td>{{ $data['closed'] ?? 0 }}</td>
                                        <td>{{ $data['total'] ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total</th>
                                    <th>{{ $totals['new'] ?? 0 }}</th>
                                    <th>{{ $totals['assigned'] ?? 0 }}</th>
                                    <th>{{ $totals['in_progress'] ?? 0 }}</th>
                                    <th>{{ $totals['resolved'] ?? 0 }}</th>
                                    <th>{{ $totals['closed'] ?? 0 }}</th>
                                    <th>{{ $totals['total'] ?? 0 }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartColors = [
                    '#3498db', '#2ecc71', '#9b59b6', '#e74c3c', '#f39c12',
                    '#1abc9c', '#d35400', '#34495e', '#16a085', '#27ae60',
                    '#8e44ad', '#f1c40f', '#e67e22', '#95a5a6', '#bdc3c7'
                ];

                // Tickets by Category
                const categoryData = @json($categoryStats ?? []);
                if (document.getElementById('categoryChart')) {
                    new Chart(document.getElementById('categoryChart'), {
                        type: 'doughnut',
                        data: {
                            labels: categoryData.map(item => item.name),
                            datasets: [{
                                data: categoryData.map(item => item.count),
                                backgroundColor: chartColors,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        }
                    });
                }

                // Tickets by Department
                const departmentData = @json($departmentStats ?? []);
                if (document.getElementById('departmentChart')) {
                    new Chart(document.getElementById('departmentChart'), {
                        type: 'pie',
                        data: {
                            labels: departmentData.map(item => item.name),
                            datasets: [{
                                data: departmentData.map(item => item.count),
                                backgroundColor: chartColors.slice(0, departmentData.length),
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        }
                    });
                }

                // Tickets by Priority
                const priorityData = @json($priorityStats ?? []);
                if (document.getElementById('priorityChart')) {
                    new Chart(document.getElementById('priorityChart'), {
                        type: 'bar',
                        data: {
                            labels: ['Low', 'Medium', 'High', 'Urgent'],
                            datasets: [{
                                label: 'Tickets by Priority',
                                data: [
                                    priorityData.low ?? 0,
                                    priorityData.medium ?? 0,
                                    priorityData.high ?? 0,
                                    priorityData.urgent ?? 0
                                ],
                                backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c'],
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                }
                            }
                        }
                    });
                }

                // Tickets by Status
                const statusData = @json($statusStats ?? []);
                if (document.getElementById('statusChart')) {
                    new Chart(document.getElementById('statusChart'), {
                        type: 'bar',
                        data: {
                            labels: ['New', 'Assigned', 'In Progress', 'Resolved', 'Closed', 'Rejected'],
                            datasets: [{
                                label: 'Tickets by Status',
                                data: [
                                    statusData.new ?? 0,
                                    statusData.assigned ?? 0,
                                    statusData.in_progress ?? 0,
                                    statusData.resolved ?? 0,
                                    statusData.closed ?? 0,
                                    statusData.rejected ?? 0
                                ],
                                backgroundColor: ['#3498db', '#f39c12', '#9b59b6', '#2ecc71', '#7f8c8d',
                                    '#e74c3c'
                                ],
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                }
                            }
                        }
                    });
                }

                // Ticket Trends
                const trendData = @json($trendStats ?? []);
                const updateTrendChart = (period) => {
                    let labels, data;

                    switch (period) {
                        case 'quarter':
                            labels = trendData.quarter.labels;
                            data = trendData.quarter.data;
                            break;
                        case 'year':
                            labels = trendData.year.labels;
                            data = trendData.year.data;
                            break;
                        default:
                            labels = trendData.month.labels;
                            data = trendData.month.data;
                    }

                    if (document.getElementById('ticketTrendsChart')) {
                        new Chart(document.getElementById('ticketTrendsChart'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Tickets Created',
                                    data: data,
                                    borderColor: '#3498db',
                                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                    tension: 0.3,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    }
                };

                // Initialize with month data
                updateTrendChart('month');

                // Change trend period
                document.getElementById('trendPeriod').addEventListener('change', function() {
                    updateTrendChart(this.value);
                });
            });
        </script>
    @endsection
