<?= $this->extend('shared/layouts/admin_simple') ?>

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
            <div class="admin-kpi-icon participants-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="admin-kpi-content">
                <h3 id="totalParticipants">0</h3>
                <p>Total Participants</p>
                <span class="admin-kpi-change positive" id="participantsChange">+0%</span>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-icon events-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="admin-kpi-content">
                <h3 id="totalEvents">0</h3>
                <p>Total Events</p>
                <span class="admin-kpi-change positive" id="eventsChange">+0%</span>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-icon revenue-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="admin-kpi-content">
                <h3 id="totalRevenue">Rp 0</h3>
                <p>Total Revenue</p>
                <span class="admin-kpi-change positive" id="revenueChange">+0%</span>
            </div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-icon abstracts-icon">
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
                    <h3><i class="fas fa-chart-line"></i> Registration Trends</h3>
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
                    <h3><i class="fas fa-money-check-alt"></i> Revenue by Event</h3>
                </div>
                <div class="admin-card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="admin-chart-container">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3><i class="fas fa-users-cog"></i> Participant Demographics</h3>
                </div>
                <div class="admin-card-body">
                    <canvas id="demographicsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="admin-chart-container">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3><i class="fas fa-chart-pie"></i> Event Formats Distribution</h3>
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
                <h3><i class="fas fa-trophy"></i> Top Performing Events</h3>
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
                            <tr class="empty-state">
                                <td colspan="5" class="text-center">
                                    <div class="empty-state-content">
                                        <i class="fas fa-chart-bar fa-3x"></i>
                                        <p>No event data available yet</p>
                                        <small>Create your first event to see analytics</small>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h3><i class="fas fa-clock"></i> Recent Activity</h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-activity-feed">
                    <div class="activity-item empty-activity">
                        <div class="activity-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>No recent activity</strong></p>
                            <small>Activity will appear here when events are created and participants register</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Report Generator -->
    <div class="admin-card custom-report-card">
        <div class="admin-card-header custom-report-header">
            <div class="report-header-content">
                <i class="fas fa-file-export report-header-icon"></i>
                <div>
                    <h3>Generate Custom Reports</h3>
                    <p>Create detailed reports with custom filters and export options</p>
                </div>
            </div>
        </div>
        <div class="admin-card-body">
            <form id="customReportForm" class="custom-report-form">
                <!-- Report Configuration -->
                <div class="report-section">
                    <div class="section-header">
                        <i class="fas fa-cog"></i>
                        <h4>Report Configuration</h4>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="reportType">
                                <i class="fas fa-file-alt"></i> Report Type
                            </label>
                            <select id="reportType" name="report_type" class="form-select">
                                <option value="registration">Registration Report</option>
                                <option value="financial">Financial Report</option>
                                <option value="attendance">Attendance Report</option>
                                <option value="abstract">Abstract Report</option>
                                <option value="comprehensive">Comprehensive Report</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reportFormat">
                                <i class="fas fa-file-export"></i> Export Format
                            </label>
                            <select id="reportFormat" name="format" class="form-select">
                                <option value="pdf">PDF Document</option>
                                <option value="excel">Excel Spreadsheet</option>
                                <option value="csv">CSV File</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="report-section">
                    <div class="section-header">
                        <i class="fas fa-calendar-alt"></i>
                        <h4>Date Range</h4>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="startDate">Start Date</label>
                            <input type="date" id="startDate" name="start_date" class="form-input">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="endDate">End Date</label>
                            <input type="date" id="endDate" name="end_date" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Event Selection -->
                <div class="report-section">
                    <div class="section-header">
                        <i class="fas fa-calendar-check"></i>
                        <h4>Event Selection</h4>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="reportEvents">Include Events (Optional)</label>
                        <select id="reportEvents" name="events[]" class="form-select" multiple size="4">
                            <option disabled class="placeholder-option">No events available - Create events first</option>
                        </select>
                        <small class="form-help">Hold Ctrl/Cmd to select multiple events. Leave empty to include all events.</small>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="report-section">
                    <div class="section-header">
                        <i class="fas fa-sliders-h"></i>
                        <h4>Additional Options</h4>
                    </div>
                    
                    <div class="checkbox-grid">
                        <label class="checkbox-item">
                            <input type="checkbox" name="include_charts" checked>
                            <span class="checkbox-mark"></span>
                            <span class="checkbox-label">Include Charts & Graphs</span>
                        </label>
                        
                        <label class="checkbox-item">
                            <input type="checkbox" name="include_details" checked>
                            <span class="checkbox-mark"></span>
                            <span class="checkbox-label">Include Detailed Breakdowns</span>
                        </label>
                        
                        <label class="checkbox-item">
                            <input type="checkbox" name="include_summary">
                            <span class="checkbox-mark"></span>
                            <span class="checkbox-label">Executive Summary</span>
                        </label>
                        
                        <label class="checkbox-item">
                            <input type="checkbox" name="include_recommendations">
                            <span class="checkbox-mark"></span>
                            <span class="checkbox-label">AI-Generated Insights</span>
                        </label>
                    </div>
                </div>

                <!-- Generate Button -->
                <div class="report-actions">
                    <button type="submit" class="btn btn-primary btn-generate">
                        <i class="fas fa-magic"></i>
                        <span>Generate Report</span>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetReportForm()">
                        <i class="fas fa-undo"></i>
                        Reset Form
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
    loadEventsForReport();
    setDefaultDates();
});

function loadAnalyticsData() {
    // Simulate loading with empty data
    console.log('Loading analytics data...');
    
    // Show loading state
    showLoadingState();
    
    // Simulate API call delay
    setTimeout(() => {
        hideLoadingState();
        // No data to update since everything is 0
    }, 1000);
}

function showLoadingState() {
    const kpiCards = document.querySelectorAll('.admin-kpi-card');
    kpiCards.forEach(card => card.classList.add('loading'));
}

function hideLoadingState() {
    const kpiCards = document.querySelectorAll('.admin-kpi-card');
    kpiCards.forEach(card => card.classList.remove('loading'));
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
                data: [0, 0, 0, 0],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'No registration data available yet',
                    font: { size: 14 },
                    color: '#6b7280'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    charts.revenue = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['No Events Available'],
            datasets: [{
                label: 'Revenue (IDR)',
                data: [0],
                backgroundColor: '#10b981',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'No revenue data available yet',
                    font: { size: 14 },
                    color: '#6b7280'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Demographics Chart
    const demographicsCtx = document.getElementById('demographicsChart').getContext('2d');
    charts.demographics = new Chart(demographicsCtx, {
        type: 'doughnut',
        data: {
            labels: ['No Participants'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'No participant data available yet',
                    font: { size: 14 },
                    color: '#6b7280'
                },
                legend: {
                    display: false
                }
            }
        }
    });

    // Format Chart
    const formatCtx = document.getElementById('formatChart').getContext('2d');
    charts.format = new Chart(formatCtx, {
        type: 'pie',
        data: {
            labels: ['No Events'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'No event format data available yet',
                    font: { size: 14 },
                    color: '#6b7280'
                },
                legend: {
                    display: false
                }
            }
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

    // Report type change handler
    document.getElementById('reportType').addEventListener('change', function() {
        updateReportOptions(this.value);
    });
}

function updateRegistrationChart(type) {
    charts.registration.config.type = type;
    charts.registration.update();
}

function loadEventsForReport() {
    // TODO: Load events from server
    const eventsSelect = document.getElementById('reportEvents');
    // Since no events exist, keep placeholder
    console.log('No events to load for report selection');
}

function setDefaultDates() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
}

function updateReportOptions(reportType) {
    // Update form based on selected report type
    console.log('Updating options for report type:', reportType);
}

function exportReport() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showNotification('Export completed! (No data available)', 'info');
    }, 2000);
}

function generateCustomReport() {
    const formData = new FormData(document.getElementById('customReportForm'));
    const btn = document.querySelector('.btn-generate');
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Generating...</span>';
    btn.disabled = true;
    
    // Simulate report generation
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showNotification('Report generated successfully! (No data available)', 'info');
    }, 3000);
}

function resetReportForm() {
    document.getElementById('customReportForm').reset();
    setDefaultDates();
    showNotification('Form reset successfully', 'success');
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

function formatPercentage(value) {
    return `${value > 0 ? '+' : ''}${value}%`;
}
</script>

<style>
/* Base Styles */
.admin-analytics-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.admin-kpi-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #f3f4f6;
}

.admin-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.admin-kpi-card.loading {
    opacity: 0.6;
    pointer-events: none;
}

.admin-kpi-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.participants-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.events-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.revenue-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.abstracts-icon {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.admin-kpi-content h3 {
    font-size: 2.25rem;
    font-weight: 700;
    margin: 0;
    color: #1f2937;
    line-height: 1;
}

.admin-kpi-content p {
    margin: 0.5rem 0 0.25rem 0;
    color: #6b7280;
    font-weight: 500;
    font-size: 0.875rem;
}

.admin-kpi-change {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    display: inline-block;
}

.admin-kpi-change.positive {
    background: #d1fae5;
    color: #065f46;
}

.admin-kpi-change.negative {
    background: #fee2e2;
    color: #991b1b;
}

/* Charts */
.admin-analytics-charts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.admin-chart-container .admin-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}

.admin-chart-container .admin-card:hover {
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.admin-card-header {
    background: #f9fafb;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-card-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.admin-card-header h3 i {
    color: #6b7280;
    font-size: 0.875rem;
}

.admin-card-body {
    padding: 1.5rem;
    background: white;
}

.admin-chart-container canvas {
    height: 300px !important;
}

.admin-chart-controls {
    display: flex;
    gap: 0.5rem;
}

.admin-select-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    color: #374151;
}

/* Tables */
.admin-analytics-tables {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.admin-table-container {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    background: #f9fafb;
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    border-bottom: 1px solid #e5e7eb;
}

.admin-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
    color: #6b7280;
    font-size: 0.875rem;
}

.empty-state {
    background: #f9fafb;
}

.empty-state-content {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.empty-state-content i {
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state-content p {
    font-weight: 500;
    margin: 0.5rem 0 0.25rem 0;
    color: #374151;
}

.empty-state-content small {
    color: #9ca3af;
}

/* Activity Feed */
.admin-activity-feed {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    gap: 0.75rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    flex-shrink: 0;
}

.empty-activity .activity-icon {
    background: #e5e7eb;
}

.activity-content p {
    margin: 0;
    color: #374151;
    font-size: 0.875rem;
}

.activity-content small {
    color: #9ca3af;
    font-size: 0.75rem;
}

/* Custom Report Form */
.custom-report-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}

.custom-report-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
}

.report-header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.report-header-icon {
    font-size: 1.5rem;
    opacity: 0.9;
}

.report-header-content h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.report-header-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.875rem;
}

.custom-report-form {
    padding: 2rem;
    background: white;
}

.report-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f3f4f6;
}

.report-section:last-of-type {
    border-bottom: none;
    margin-bottom: 1rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.section-header i {
    color: #667eea;
    font-size: 1rem;
}

.section-header h4 {
    margin: 0;
    color: #1f2937;
    font-size: 1rem;
    font-weight: 600;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label i {
    color: #6b7280;
    font-size: 0.75rem;
}

.form-input, .form-select {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: white;
    color: #374151;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.25em 1.25em;
    padding-right: 3rem;
}

.form-select[multiple] {
    background-image: none;
    padding-right: 1rem;
    min-height: 120px;
}

.form-select option.placeholder-option {
    color: #9ca3af;
    font-style: italic;
}

.form-help {
    margin-top: 0.25rem;
    color: #6b7280;
    font-size: 0.75rem;
    line-height: 1.4;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
    background: white;
}

.checkbox-item:hover {
    border-color: #667eea;
    background: #f8faff;
}

.checkbox-item input[type="checkbox"] {
    display: none;
}

.checkbox-mark {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.checkbox-item input[type="checkbox"]:checked + .checkbox-mark {
    background: #667eea;
    border-color: #667eea;
}

.checkbox-item input[type="checkbox"]:checked + .checkbox-mark::after {
    content: '✓';
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
}

.checkbox-label {
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
}

.report-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
    margin-top: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    justify-content: center;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover:not(:disabled) {
    background: #e5e7eb;
    border-color: #9ca3af;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-generate {
    min-width: 160px;
}

/* Notifications */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    max-width: 400px;
    z-index: 1000;
    animation: slideInRight 0.3s ease;
}

.notification-success {
    border-left: 4px solid #10b981;
}

.notification-error {
    border-left: 4px solid #ef4444;
}

.notification-info {
    border-left: 4px solid #3b82f6;
}

.notification i {
    color: inherit;
}

.notification-success i {
    color: #10b981;
}

.notification-error i {
    color: #ef4444;
}

.notification-info i {
    color: #3b82f6;
}

.notification span {
    flex: 1;
    color: #374151;
    font-size: 0.875rem;
}

.notification-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: color 0.2s ease;
}

.notification-close:hover {
    color: #6b7280;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .admin-analytics-charts {
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    }
    
    .admin-analytics-tables {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .admin-analytics-kpis {
        grid-template-columns: 1fr;
    }
    
    .admin-analytics-charts {
        grid-template-columns: 1fr;
    }
    
    .admin-kpi-card {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .admin-kpi-icon {
        margin: 0 auto;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .checkbox-grid {
        grid-template-columns: 1fr;
    }
    
    .report-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn {
        justify-content: center;
    }
    
    .admin-header-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .notification {
        left: 20px;
        right: 20px;
        max-width: none;
    }
}

@media (max-width: 480px) {
    .custom-report-form {
        padding: 1rem;
    }
    
    .report-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
    }
    
    .admin-card-body {
        padding: 1rem;
    }
    
    .admin-card-header {
        padding: 0.75rem 1rem;
    }
    
    .admin-kpi-card {
        padding: 1rem;
    }
    
    .admin-kpi-content h3 {
        font-size: 1.75rem;
    }
}
</style>
<?= $this->endSection() ?>