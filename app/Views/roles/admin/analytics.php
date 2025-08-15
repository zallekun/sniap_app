<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('title') ?>Analytics & Reports<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-header">
    <div class="admin-header-content">
        <h1>Analytics & Reports</h1>
        <p>Comprehensive insights and reporting for conference management</p>
        <div class="admin-header-actions">
            <select id="dateRangeFilter" class="admin-select">
                <option value="7">Last 7 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
                <option value="custom">Custom range</option>
            </select>
            <button class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>
</div>

<div class="admin-content">
    <!-- Key Performance Indicators -->
    <div class="admin-analytics-kpis">
        <div class="admin-kpi-card">
            <div class="admin-kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="admin-kpi-content">
                <h3 id="totalParticipants">0</h3>
                <p>Total Participants</p>
                <span class="admin-kpi-change positive" id="participantsChange">+0%</span>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="admin-kpi-content">
                <h3 id="totalEvents">0</h3>
                <p>Total Events</p>
                <span class="admin-kpi-change positive" id="eventsChange">+0%</span>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="admin-kpi-content">
                <h3 id="totalRevenue">Rp 0</h3>
                <p>Total Revenue</p>
                <span class="admin-kpi-change positive" id="revenueChange">+0%</span>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="admin-kpi-content">
                <h3 id="totalAbstracts">0</h3>
                <p>Abstracts Submitted</p>
                <span class="admin-kpi-change positive" id="abstractsChange">+0%</span>
            </div>
        </div>
    </div>

    <!-- Charts and Visualizations -->
    <div class="admin-analytics-charts">
        <div class="admin-chart-container">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Registration Trends</h3>
                    <div class="admin-chart-controls">
                        <select id="registrationChartType" class="admin-select-sm">
                            <option value="line">Line Chart</option>
                            <option value="bar">Bar Chart</option>
                        </select>
                    </div>
                </div>
                <div class="admin-card-body">
                    <canvas id="registrationChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="admin-chart-container">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Revenue by Event</h3>
                </div>
                <div class="admin-card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="admin-chart-container">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Participant Demographics</h3>
                </div>
                <div class="admin-card-body">
                    <canvas id="demographicsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="admin-chart-container">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Event Formats Distribution</h3>
                </div>
                <div class="admin-card-body">
                    <canvas id="formatChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Tables -->
    <div class="admin-analytics-tables">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Top Performing Events</h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-table-container">
                    <table id="topEventsTable" class="admin-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Participants</th>
                                <th>Revenue</th>
                                <th>Rating</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Recent Activity</h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-activity-feed">
                    <!-- Activity items will be loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Export and Report Options -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Generate Custom Reports</h3>
        </div>
        <div class="admin-card-body">
            <form id="customReportForm" class="admin-report-form">
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="reportType">Report Type</label>
                        <select id="reportType" name="report_type" class="admin-select">
                            <option value="registration">Registration Report</option>
                            <option value="financial">Financial Report</option>
                            <option value="attendance">Attendance Report</option>
                            <option value="abstract">Abstract Report</option>
                            <option value="comprehensive">Comprehensive Report</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label for="reportFormat">Format</label>
                        <select id="reportFormat" name="format" class="admin-select">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label for="startDate">Start Date</label>
                        <input type="date" id="startDate" name="start_date" class="admin-input">
                    </div>
                    <div class="admin-form-group">
                        <label for="endDate">End Date</label>
                        <input type="date" id="endDate" name="end_date" class="admin-input">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label for="reportEvents">Include Events</label>
                    <select id="reportEvents" name="events[]" class="admin-select" multiple>
                        <!-- Events will be loaded via JavaScript -->
                    </select>
                </div>
                <div class="admin-form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-download"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Analytics functionality
let charts = {};

document.addEventListener('DOMContentLoaded', function() {
    loadAnalyticsData();
    initializeCharts();
    setupEventListeners();
});

function loadAnalyticsData() {
    // TODO: Load analytics data from server
    console.log('Loading analytics data...');
    
    // Simulate loading data
    updateKPIs({
        totalParticipants: 1250,
        participantsChange: 15.2,
        totalEvents: 24,
        eventsChange: 8.3,
        totalRevenue: 45750000,
        revenueChange: 23.7,
        totalAbstracts: 186,
        abstractsChange: 12.4
    });
}

function updateKPIs(data) {
    document.getElementById('totalParticipants').textContent = data.totalParticipants.toLocaleString();
    document.getElementById('participantsChange').textContent = `+${data.participantsChange}%`;
    
    document.getElementById('totalEvents').textContent = data.totalEvents;
    document.getElementById('eventsChange').textContent = `+${data.eventsChange}%`;
    
    document.getElementById('totalRevenue').textContent = `Rp ${data.totalRevenue.toLocaleString()}`;
    document.getElementById('revenueChange').textContent = `+${data.revenueChange}%`;
    
    document.getElementById('totalAbstracts').textContent = data.totalAbstracts;
    document.getElementById('abstractsChange').textContent = `+${data.abstractsChange}%`;
}

function initializeCharts() {
    // Registration Trends Chart
    const registrationCtx = document.getElementById('registrationChart').getContext('2d');
    charts.registration = new Chart(registrationCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Registrations',
                data: [45, 67, 89, 123],
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    charts.revenue = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['Event A', 'Event B', 'Event C', 'Event D'],
            datasets: [{
                label: 'Revenue (IDR)',
                data: [15000000, 8500000, 12300000, 9800000],
                backgroundColor: '#2ecc71'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Demographics Chart
    const demographicsCtx = document.getElementById('demographicsChart').getContext('2d');
    charts.demographics = new Chart(demographicsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Student', 'Professional', 'Academic', 'Other'],
            datasets: [{
                data: [40, 35, 20, 5],
                backgroundColor: ['#3498db', '#e74c3c', '#f39c12', '#9b59b6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Format Chart
    const formatCtx = document.getElementById('formatChart').getContext('2d');
    charts.format = new Chart(formatCtx, {
        type: 'pie',
        data: {
            labels: ['Online', 'Offline', 'Hybrid'],
            datasets: [{
                data: [50, 30, 20],
                backgroundColor: ['#1abc9c', '#e67e22', '#8e44ad']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function setupEventListeners() {
    document.getElementById('dateRangeFilter').addEventListener('change', function() {
        loadAnalyticsData();
    });

    document.getElementById('registrationChartType').addEventListener('change', function() {
        updateRegistrationChart(this.value);
    });

    document.getElementById('customReportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateCustomReport();
    });
}

function updateRegistrationChart(type) {
    charts.registration.config.type = type;
    charts.registration.update();
}

function exportReport() {
    // TODO: Export current analytics report
    console.log('Exporting analytics report...');
}

function generateCustomReport() {
    // TODO: Generate custom report
    console.log('Generating custom report...');
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

// Utility function to format percentage
function formatPercentage(value) {
    return `${value > 0 ? '+' : ''}${value}%`;
}
</script>

<style>
.admin-analytics-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.admin-kpi-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.admin-kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2980b9);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.admin-kpi-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
}

.admin-kpi-content p {
    margin: 0.25rem 0;
    color: #7f8c8d;
    font-weight: 500;
}

.admin-kpi-change {
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.admin-kpi-change.positive {
    background: #d5edda;
    color: #155724;
}

.admin-kpi-change.negative {
    background: #f8d7da;
    color: #721c24;
}

.admin-analytics-charts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.admin-chart-container canvas {
    height: 300px !important;
}

.admin-chart-controls {
    display: flex;
    gap: 0.5rem;
}

.admin-select-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.admin-analytics-tables {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.admin-activity-feed {
    max-height: 400px;
    overflow-y: auto;
}

.admin-report-form {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .admin-analytics-charts {
        grid-template-columns: 1fr;
    }
    
    .admin-analytics-tables {
        grid-template-columns: 1fr;
    }
    
    .admin-kpi-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>
<?= $this->endSection() ?>