<?= $this->extend('shared/layouts/presenter_layout') ?>

<?= $this->section('title') ?>Event Schedule - Presenter<?= $this->endSection() ?>

<?= $this->section('head') ?>
<!-- Presenter-specific styles -->
<link href="<?= base_url('css/roles/presenter/presenter.css') ?>" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<style>
/* Calendar Styles */
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background-color: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    overflow: hidden;
}

.calendar-header {
    display: contents;
}

.calendar-day-header {
    background-color: #495057;
    color: white;
    padding: 0.75rem;
    text-align: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.calendar-day {
    background-color: white;
    min-height: 120px;
    padding: 0.5rem;
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    cursor: pointer;
    transition: background-color 0.2s;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.empty {
    background-color: #f8f9fa;
    cursor: default;
}

.calendar-day.today {
    background-color: #e3f2fd;
    border: 2px solid #2196f3;
}

.day-number {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #495057;
}

.calendar-event {
    background-color: #007bff;
    color: white;
    padding: 0.125rem 0.25rem;
    margin: 0.125rem 0;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
}

.calendar-event:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.calendar-event.event-registered {
    background-color: #28a745;
}

.calendar-event.event-available {
    background-color: #007bff;
}

.event-time {
    font-weight: 600;
    font-size: 0.7rem;
}

.event-title {
    font-size: 0.7rem;
    line-height: 1.2;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    z-index: 2;
}

.timeline-marker.registered {
    background-color: #28a745;
}

.timeline-marker.available {
    background-color: #007bff;
}

.timeline-line {
    position: absolute;
    left: -1rem;
    top: 2rem;
    bottom: -2rem;
    width: 2px;
    background-color: #dee2e6;
    z-index: 1;
}

.timeline-card {
    border-left: 4px solid #007bff;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    transition: all 0.2s;
}

.timeline-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.timeline-item.timeline-past .timeline-card {
    border-left-color: #6c757d;
    opacity: 0.8;
}

.timeline-item.timeline-future .timeline-card {
    border-left-color: #007bff;
}

/* Event List Styles */
.event-item {
    border-left: 4px solid #007bff;
    transition: all 0.2s;
    background-color: white !important; /* Fix hover white issue */
}

.event-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    background-color: #f8f9fa !important; /* Proper hover color */
}

/* Specific fix untuk card bootstrap collision */
.event-item.card {
    background-color: white !important;
}

.event-item.card:hover {
    background-color: #f8f9fa !important;
}

.event-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background-color: #e3f2fd;
    border-radius: 0.5rem;
}

.event-actions .btn {
    margin-bottom: 0.25rem;
}

/* Stats Card Styles */
.schedule-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-item {
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #495057;
}

/* Loading and Empty States */
.loading-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 0;
}

.empty-state {
    text-align: center;
    padding: 3rem 0;
    color: #6c757d;
}

/* Nav Tabs Fix untuk button putih */
.card-header-tabs .nav-link {
    color: white !important;
    background-color: transparent !important;
    border: none !important;
    opacity: 0.8;
}

.card-header-tabs .nav-link:hover {
    color: white !important;
    background-color: rgba(255, 255, 255, 0.1) !important;
    opacity: 1;
}

.card-header-tabs .nav-link.active {
    color: white !important;
    background-color: rgba(255, 255, 255, 0.2) !important;
    border: none !important;
    opacity: 1;
    font-weight: 600;
}

.card-header-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 3px;
    background-color: white;
    border-radius: 2px;
}

.card-header-tabs .nav-link {
    position: relative;
    transition: all 0.2s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .schedule-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .calendar-day {
        min-height: 80px;
        font-size: 0.875rem;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.75rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<main class="main-content">
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-calendar me-2"></i>
                Event Schedule
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/presenter/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Events Schedule</li>
                </ol>
            </nav>
        </div>
        <div class="text-end">
            <div class="d-flex gap-4 align-items-center">
                <div class="text-center">
                    <span class="d-block small text-muted">Available Events</span>
                    <span class="fw-bold h5 mb-0 text-success" id="totalEventsCount">-</span>
                </div>
                <div class="text-center">
                    <span class="d-block small text-muted">My Presentations</span>
                    <span class="fw-bold h5 mb-0 text-success" id="registeredEventsCount">-</span>
                </div>
                <button class="btn btn-outline-secondary" onclick="refreshSchedule()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <div id="alertContainer"></div>

    <!-- View Mode Tabs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <nav class="nav nav-tabs card-header-tabs" id="scheduleViewTabs" role="tablist">
                <button class="nav-link active text-white" id="calendar-view-tab" data-bs-toggle="tab" data-bs-target="#calendar-view" type="button" role="tab">
                    <i class="fas fa-calendar-alt me-2"></i>Kalender
                </button>
                <button class="nav-link text-white" id="list-view-tab" data-bs-toggle="tab" data-bs-target="#list-view" type="button" role="tab">
                    <i class="fas fa-list me-2"></i>Daftar
                </button>
                <button class="nav-link text-white" id="timeline-view-tab" data-bs-toggle="tab" data-bs-target="#timeline-view" type="button" role="tab">
                    <i class="fas fa-stream me-2"></i>Timeline
                </button>
            </nav>
        </div>

        <div class="card-body p-0">
            <!-- Tab Content -->
            <div class="tab-content" id="scheduleViewContent">
                <!-- Calendar View -->
                <div class="tab-pane fade show active p-4" id="calendar-view" role="tabpanel">
                    <div class="calendar-controls mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar me-2 text-success"></i>
                                    <span id="current-month-year">Loading...</span>
                                </h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-success" id="prev-month">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" id="today-btn">Hari Ini</button>
                                    <button type="button" class="btn btn-outline-success" id="next-month">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-success ms-2" onclick="refreshSchedule()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="event-calendar">
                        <div class="loading-spinner">
                            <div class="spinner-border text-success" role="status"></div>
                            <div class="ms-3">Memuat kalender...</div>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div class="tab-pane fade p-4" id="list-view" role="tabpanel">
                    <div class="list-controls mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2 text-success"></i>
                                    Daftar Event Tersedia
                                </h5>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="event-search" placeholder="Cari event...">
                                    <button class="btn btn-outline-secondary" onclick="clearSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="event-list-container">
                        <div class="loading-spinner">
                            <div class="spinner-border text-success" role="status"></div>
                            <div class="ms-3">Memuat events...</div>
                        </div>
                    </div>
                </div>

                <!-- Timeline View -->
                <div class="tab-pane fade p-4" id="timeline-view" role="tabpanel">
                    <div class="timeline-controls mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">
                                    <i class="fas fa-stream me-2 text-info"></i>
                                    Timeline Acara
                                </h5>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-8">
                                        <select class="form-select" id="timeline-filter">
                                            <option value="all">Semua Event</option>
                                            <option value="registered">Sudah Terdaftar</option>
                                            <option value="available">Belum Terdaftar</option>
                                            <option value="upcoming">Event Mendatang</option>
                                            <option value="past">Event Sebelumnya</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-outline-secondary w-100" onclick="resetTimelineFilter()">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="event-timeline-container">
                        <div class="loading-spinner">
                            <div class="spinner-border text-success" role="status"></div>
                            <div class="ms-3">Memuat timeline...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        <span class="badge bg-success me-2">My Presentations</span>
                        <span class="badge bg-info me-2">Available for Registration</span>
                        <span class="badge bg-secondary me-2">Event Ended</span>
                        Klik event untuk melihat detail dan mendaftar sebagai presenter
                    </small>
                </div>
                <div class="col-md-4 text-end">
                    <a href="/presenter/dashboard" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <button class="btn btn-success" onclick="refreshSchedule()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="eventDetailModalLabel">
                    <i class="fas fa-calendar-check me-2"></i>Detail Event
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetailContent">
                <div class="loading-spinner">
                    <div class="spinner-border text-success" role="status"></div>
                    <div class="ms-3">Memuat detail event...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-success" id="registerEventBtn" style="display: none;">
                    <i class="fas fa-user-plus me-1"></i>Daftar sebagai Presenter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Registration Confirmation Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Konfirmasi Pendaftaran Presenter
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="registrationContent">
                <!-- Registration form will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmRegistrationBtn">
                    <i class="fas fa-check me-1"></i>Konfirmasi Pendaftaran
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let allEvents = [];
let currentView = 'calendar';
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let filteredEvents = [];
const userRole = 'presenter';

// Get CSRF token from meta tag or form
function getCsrfToken() {
    // Try meta tag first
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        return token.getAttribute('content');
    }
    
    // Try hidden input field
    const hiddenInput = document.querySelector('input[name="csrf_test_name"]');
    if (hiddenInput) {
        return hiddenInput.value;
    }
    
    // Try from cookies if available
    const cookieMatch = document.cookie.match(/csrf_cookie_name=([^;]+)/);
    if (cookieMatch) {
        return cookieMatch[1];
    }
    
    return '';
}

// Get CSRF token name
function getCsrfTokenName() {
    const hiddenInput = document.querySelector('input[name^="csrf_"]');
    if (hiddenInput) {
        return hiddenInput.name;
    }
    return 'csrf_test_name'; // Default CodeIgniter CSRF field name
}

// Get base URL
function getBaseUrl() {
    const baseElement = document.querySelector('base');
    if (baseElement) {
        return baseElement.getAttribute('href');
    }
    return window.location.origin + '/';
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Presenter Event Schedule page initializing...');
    loadEventScheduleData();
    setupEventListeners();
});

// Setup all event listeners
function setupEventListeners() {
    // Tab switching
    document.querySelectorAll('#scheduleViewTabs button').forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('data-bs-target');
            currentView = target.replace('#', '').replace('-view', '');
            console.log('Switched to view:', currentView);
            
            // Re-render current view if data is available
            if (allEvents.length > 0) {
                renderCurrentView();
            }
        });
    });
    
    // Calendar navigation
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    const todayBtn = document.getElementById('today-btn');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendarView(allEvents);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendarView(allEvents);
        });
    }
    
    if (todayBtn) {
        todayBtn.addEventListener('click', () => {
            const today = new Date();
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            renderCalendarView(allEvents);
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('event-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            const searchTerm = e.target.value.toLowerCase();
            filteredEvents = allEvents.filter(event => 
                event.title.toLowerCase().includes(searchTerm) || 
                event.description.toLowerCase().includes(searchTerm) ||
                (event.location && event.location.toLowerCase().includes(searchTerm))
            );
            renderListView(filteredEvents);
        }, 300));
    }
    
    // Timeline filter
    const timelineFilter = document.getElementById('timeline-filter');
    if (timelineFilter) {
        timelineFilter.addEventListener('change', (e) => {
            const filterValue = e.target.value;
            filteredEvents = filterEventsByType(allEvents, filterValue);
            renderTimelineView(filteredEvents);
        });
    }
}

// Load event schedule data from API
async function loadEventScheduleData() {
    try {
        console.log('Loading presenter event schedule data...');
        showLoading();
        
        const baseUrl = getBaseUrl();
        
        // Try multiple endpoints with different approaches for presenter data
        const endpoints = [
            {
                url: baseUrl + 'dashboard/event-schedule',
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            },
            {
                url: baseUrl + 'presenter/api/events',
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            },
            {
                url: baseUrl + 'api/v1/events',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || ''),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }
        ];
        
        let data = null;
        let success = false;
        
        for (const endpoint of endpoints) {
            try {
                console.log('Trying endpoint:', endpoint.url);
                const response = await fetch(endpoint.url, {
                    method: endpoint.method,
                    headers: endpoint.headers,
                    credentials: 'same-origin'
                });
                
                console.log('Response status:', response.status);
                
                if (response.ok) {
                    const responseText = await response.text();
                    console.log('Response text:', responseText.substring(0, 200));
                    
                    try {
                        data = JSON.parse(responseText);
                        if (data.status === 'success' && data.data) {
                            success = true;
                            break;
                        } else if (Array.isArray(data)) {
                            // Direct array response
                            data = { status: 'success', data: data };
                            success = true;
                            break;
                        }
                    } catch (parseError) {
                        console.log('JSON parse error:', parseError.message);
                    }
                }
            } catch (e) {
                console.log('Endpoint failed:', endpoint.url, e.message);
                continue;
            }
        }
        
        if (success) {
            allEvents = data.data || [];
            filteredEvents = [...allEvents];
            
            console.log('Loaded', allEvents.length, 'events for presenter');
            updateEventStats(allEvents);
            renderCurrentView();
            
            hideLoading();
            showAlert('Jadwal acara berhasil dimuat', 'success', 3000);
        } else {
            throw new Error('All endpoints failed');
        }
        
    } catch (error) {
        console.error('Error loading event schedule:', error);
        hideLoading();
        showAlert('Gagal memuat jadwal acara. Silakan refresh halaman.', 'danger', 5000);
    }
}

// Show loading state
function showLoading() {
    const loadingHTML = `
        <div class="loading-spinner">
            <div class="spinner-border text-success" role="status"></div>
            <div class="ms-3">Memuat data...</div>
        </div>
    `;
    
    document.getElementById('event-calendar').innerHTML = loadingHTML;
    document.getElementById('event-list-container').innerHTML = loadingHTML;
    document.getElementById('event-timeline-container').innerHTML = loadingHTML;
}

// Hide loading state
function hideLoading() {
    console.log('Loading hidden, content will be rendered');
}

// Update event statistics for presenter view
function updateEventStats(events) {
    const totalCount = events.length;
    const registeredCount = events.filter(event => event.is_registered && event.registration_type === 'presenter').length;
    
    document.getElementById('totalEventsCount').textContent = totalCount;
    document.getElementById('registeredEventsCount').textContent = registeredCount;
    
    console.log('Presenter Stats updated:', { total: totalCount, presentations: registeredCount });
}

// Render current active view
function renderCurrentView() {
    const activeTab = document.querySelector('#scheduleViewTabs .nav-link.active');
    if (!activeTab) return;
    
    const viewId = activeTab.getAttribute('data-bs-target');
    
    switch (viewId) {
        case '#calendar-view':
            renderCalendarView(allEvents);
            break;
        case '#list-view':
            renderListView(filteredEvents.length > 0 ? filteredEvents : allEvents);
            break;
        case '#timeline-view':
            renderTimelineView(filteredEvents.length > 0 ? filteredEvents : allEvents);
            break;
    }
}

// Render Calendar View (same as audience but with presenter styling)
function renderCalendarView(events) {
    console.log('Rendering presenter calendar view with', events.length, 'events');
    
    const calendarElement = document.getElementById('event-calendar');
    const monthYearElement = document.getElementById('current-month-year');
    
    if (!calendarElement || !monthYearElement) {
        console.error('Calendar elements not found');
        return;
    }
    
    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    monthYearElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    let calendarHTML = `
        <div class="calendar-grid">
            <div class="calendar-day-header">Min</div>
            <div class="calendar-day-header">Sen</div>
            <div class="calendar-day-header">Sel</div>
            <div class="calendar-day-header">Rab</div>
            <div class="calendar-day-header">Kam</div>
            <div class="calendar-day-header">Jum</div>
            <div class="calendar-day-header">Sab</div>
    `;
    
    // Add empty cells for days before the month starts
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += '<div class="calendar-day empty"></div>';
    }
    
    // Add days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayEvents = events.filter(event => event.date === dateStr);
        const isToday = day === new Date().getDate() && 
                       currentMonth === new Date().getMonth() && 
                       currentYear === new Date().getFullYear();
        
        calendarHTML += `
            <div class="calendar-day ${isToday ? 'today' : ''}" data-date="${dateStr}">
                <div class="day-number">${day}</div>
        `;
        
        if (dayEvents.length > 0) {
            dayEvents.slice(0, 3).forEach(event => {
                const eventClass = (event.is_registered && event.registration_type === 'presenter') ? 'event-registered' : 'event-available';
                calendarHTML += `
                    <div class="calendar-event ${eventClass}" onclick="showEventDetailModal('${event.id}')" title="${event.title}">
                        <div class="event-time">${event.time}</div>
                        <div class="event-title">${truncateText(event.title, 20)}</div>
                    </div>
                `;
            });
            
            if (dayEvents.length > 3) {
                calendarHTML += `
                    <div class="calendar-event" style="background-color: #6c757d; cursor: pointer;" onclick="showDayEvents('${dateStr}')">
                        <div class="event-title">+${dayEvents.length - 3} lainnya</div>
                    </div>
                `;
            }
        }
        
        calendarHTML += '</div>';
    }
    
    calendarHTML += '</div>';
    calendarElement.innerHTML = calendarHTML;
    
    console.log('Presenter calendar rendered successfully');
}

// Render List View (presenter-specific)
function renderListView(events) {
    console.log('Rendering presenter list view with', events.length, 'events');
    
    const listContainer = document.getElementById('event-list-container');
    
    if (!listContainer) {
        console.error('List container not found');
        return;
    }
    
    if (events.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-calendar-times fa-4x mb-3"></i>
                <h5>Tidak ada event ditemukan</h5>
                <p>Coba ubah kriteria pencarian atau filter Anda</p>
                <button class="btn btn-success" onclick="clearSearch()">
                    <i class="fas fa-undo me-2"></i>Reset Pencarian
                </button>
            </div>
        `;
        return;
    }
    
    let listHTML = '<div class="event-list">';
    
    events.forEach(event => {
        const eventDate = new Date(event.date);
        const formattedDate = eventDate.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const isPresenting = event.is_registered && event.registration_type === 'presenter';
        const statusBadge = isPresenting ? 
            '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Presenting</span>' : 
            '<span class="badge bg-success"><i class="fas fa-microphone me-1"></i>Available for Presenter Registration</span>';
        
        const formatBadge = getFormatBadge(event.format);
        const categoryBadge = getCategoryBadge(event.category);
        
        const isPast = eventDate < new Date();
        const cardClass = isPast ? 'border-secondary' : (isPresenting ? 'border-success' : 'border-success');
        
        listHTML += `
            <div class="event-item card mb-3 ${cardClass}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="event-icon me-3">
                                    <i class="fas fa-${getEventIcon(event.category)} fa-2x text-success"></i>
                                </div>
                                <div class="event-info flex-grow-1">
                                    <h6 class="event-title mb-2">
                                        ${event.title}
                                        ${isPast ? '<small class="text-muted">(Sudah Berakhir)</small>' : ''}
                                        ${isPresenting ? '<small class="text-success">(Anda Presenter)</small>' : ''}
                                    </h6>
                                    <div class="event-meta text-muted mb-2">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <i class="fas fa-calendar me-1"></i>${formattedDate}
                                            </div>
                                            <div class="col-sm-6">
                                                <i class="fas fa-clock me-1"></i>${event.time}
                                            </div>
                                            <div class="col-sm-6">
                                                <i class="fas fa-${event.format === 'online' ? 'video' : 'map-marker-alt'} me-1"></i>
                                                ${event.format === 'online' ? 'Online' : event.location || 'TBA'}
                                            </div>
                                            <div class="col-sm-6">
                                                <i class="fas fa-money-bill me-1"></i>
                                                ${event.presenter_fee > 0 ? `Honor: Rp ${parseInt(event.presenter_fee).toLocaleString('id-ID')}` : 'Volunteer'}
                                            </div>
                                        </div>
                                    </div>
                                    <p class="event-description mb-2">${truncateText(event.description, 150)}</p>
                                    <div class="event-badges">
                                        ${statusBadge}
                                        ${formatBadge}
                                        ${categoryBadge}
                                        ${event.call_for_papers ? '<span class="badge bg-warning"><i class="fas fa-file-alt me-1"></i>Call for Papers</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="event-actions">
                                <button class="btn btn-outline-success btn-sm d-block mb-2" onclick="showEventDetailModal('${event.id}')">
                                    <i class="fas fa-info-circle me-1"></i>Detail Lengkap
                                </button>
                                ${!isPresenting && !isPast && event.call_for_papers ? 
                                    `<button class="btn btn-success btn-sm d-block" onclick="showRegistrationModal('${event.id}')">
                                        <i class="fas fa-microphone me-1"></i>Daftar Presenter
                                    </button>` : 
                                    isPresenting ? 
                                        `<button class="btn btn-success btn-sm d-block" disabled>
                                            <i class="fas fa-check me-1"></i>Terdaftar
                                        </button>
                                        <small class="text-muted d-block mt-1">Status: ${event.registration_status || 'Confirmed'}</small>
                                        <a href="/presenter/abstracts" class="btn btn-outline-primary btn-sm d-block mt-1">
                                            <i class="fas fa-file-alt me-1"></i>Submit Abstract
                                        </a>` :
                                        `<button class="btn btn-secondary btn-sm d-block" disabled>
                                            <i class="fas fa-clock me-1"></i>${isPast ? 'Berakhir' : 'No Call for Papers'}
                                        </button>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    listHTML += '</div>';
    listContainer.innerHTML = listHTML;
    
    console.log('Presenter list view rendered successfully');
}

// Render Timeline View (presenter-specific)
function renderTimelineView(events) {
    console.log('Rendering presenter timeline view with', events.length, 'events');
    
    const timelineContainer = document.getElementById('event-timeline-container');
    
    if (!timelineContainer) {
        console.error('Timeline container not found');
        return;
    }
    
    if (events.length === 0) {
        timelineContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-stream fa-4x mb-3"></i>
                <h5>Tidak ada event ditemukan</h5>
                <p>Coba ubah filter atau kriteria pencarian Anda</p>
                <button class="btn btn-success" onclick="resetTimelineFilter()">
                    <i class="fas fa-undo me-2"></i>Reset Filter
                </button>
            </div>
        `;
        return;
    }
    
    const sortedEvents = events.sort((a, b) => {
        const dateA = new Date(a.date + 'T' + a.time);
        const dateB = new Date(b.date + 'T' + b.time);
        return dateA - dateB;
    });
    
    let timelineHTML = '<div class="timeline">';
    
    sortedEvents.forEach((event, index) => {
        const eventDate = new Date(event.date);
        const isLast = index === sortedEvents.length - 1;
        const isPast = eventDate < new Date();
        const isPresenting = event.is_registered && event.registration_type === 'presenter';
        const timelineClass = isPast ? 'timeline-past' : 'timeline-future';
        
        const formattedDate = eventDate.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        timelineHTML += `
            <div class="timeline-item ${timelineClass}">
                <div class="timeline-marker ${isPresenting ? 'registered' : 'available'}">
                    <i class="fas ${isPresenting ? 'fa-microphone' : 'fa-calendar'}"></i>
                </div>
                ${!isLast ? '<div class="timeline-line"></div>' : ''}
                <div class="timeline-content">
                    <div class="timeline-card card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="timeline-title">
                                        ${event.title}
                                        ${isPast ? '<small class="text-muted ms-2">(Berakhir)</small>' : ''}
                                        ${isPresenting ? '<small class="text-success ms-2">(Presenting)</small>' : ''}
                                    </h6>
                                    <div class="timeline-meta text-muted mb-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <i class="fas fa-calendar me-1"></i>${formattedDate}
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-clock me-1"></i>${event.time}
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-${event.format === 'online' ? 'video' : 'map-marker-alt'} me-1"></i>
                                                ${event.format === 'online' ? 'Online' : event.location || 'TBA'}
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-money-bill me-1"></i>
                                                ${event.presenter_fee > 0 ? `Honor: Rp ${parseInt(event.presenter_fee).toLocaleString('id-ID')}` : 'Volunteer'}
                                            </div>
                                        </div>
                                    </div>
                                    <p class="timeline-description mb-3">${truncateText(event.description, 200)}</p>
                                    <div class="timeline-badges">
                                        ${isPresenting ? 
                                            '<span class="badge bg-success"><i class="fas fa-microphone me-1"></i>Presenting</span>' : 
                                            '<span class="badge bg-success"><i class="fas fa-calendar-plus me-1"></i>Available</span>'
                                        }
                                        ${getFormatBadge(event.format)}
                                        ${getCategoryBadge(event.category)}
                                        ${event.call_for_papers ? '<span class="badge bg-warning"><i class="fas fa-file-alt me-1"></i>Call for Papers</span>' : ''}
                                        ${isPast ? '<span class="badge bg-secondary"><i class="fas fa-history me-1"></i>Berakhir</span>' : ''}
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="timeline-actions">
                                        <button class="btn btn-outline-success btn-sm d-block mb-2" onclick="showEventDetailModal('${event.id}')">
                                            <i class="fas fa-info-circle me-1"></i>Detail
                                        </button>
                                        ${!isPresenting && !isPast && event.call_for_papers ? 
                                            `<button class="btn btn-success btn-sm d-block mb-2" onclick="showRegistrationModal('${event.id}')">
                                                <i class="fas fa-microphone me-1"></i>Daftar Presenter
                                            </button>` : 
                                            isPresenting ? 
                                                `<button class="btn btn-success btn-sm d-block mb-2" disabled>
                                                    <i class="fas fa-check me-1"></i>Terdaftar
                                                </button>
                                                <a href="/presenter/abstracts" class="btn btn-outline-primary btn-sm d-block mb-2">
                                                    <i class="fas fa-file-alt me-1"></i>Submit Abstract
                                                </a>` :
                                                `<button class="btn btn-secondary btn-sm d-block mb-2" disabled>
                                                    <i class="fas fa-clock me-1"></i>Tidak Tersedia
                                                </button>`
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    timelineHTML += '</div>';
    timelineContainer.innerHTML = timelineHTML;
    
    console.log('Presenter timeline view rendered successfully');
}

// Utility functions (similar to audience but with presenter context)
function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

function getFormatBadge(format) {
    const formatConfig = {
        'online': { class: 'bg-info', icon: 'video', text: 'Online' },
        'offline': { class: 'bg-warning', icon: 'map-marker-alt', text: 'Offline' },
        'hybrid': { class: 'bg-purple', icon: 'globe', text: 'Hybrid' }
    };
    
    const config = formatConfig[format] || formatConfig['offline'];
    return `<span class="badge ${config.class}"><i class="fas fa-${config.icon} me-1"></i>${config.text}</span>`;
}

function getCategoryBadge(category) {
    const categoryConfig = {
        'conference': { class: 'bg-info', icon: 'users', text: 'Conference' },
        'workshop': { class: 'bg-success', icon: 'tools', text: 'Workshop' },
        'seminar': { class: 'bg-info', icon: 'chalkboard-teacher', text: 'Seminar' },
        'webinar': { class: 'bg-secondary', icon: 'laptop', text: 'Webinar' }
    };
    
    const config = categoryConfig[category] || categoryConfig['conference'];
    return `<span class="badge ${config.class}"><i class="fas fa-${config.icon} me-1"></i>${config.text}</span>`;
}

function getEventIcon(category) {
    const icons = {
        'conference': 'users',
        'workshop': 'tools',
        'seminar': 'chalkboard-teacher',
        'webinar': 'laptop'
    };
    return icons[category] || 'calendar-day';
}

function filterEventsByType(events, type) {
    const now = new Date();
    
    switch (type) {
        case 'registered':
            return events.filter(event => event.is_registered && event.registration_type === 'presenter');
        case 'available':
            return events.filter(event => !event.is_registered && event.call_for_papers);
        case 'upcoming':
            return events.filter(event => new Date(event.date) >= now);
        case 'past':
            return events.filter(event => new Date(event.date) < now);
        default:
            return events;
    }
}

// Show event detail in modal (presenter-specific)
function showEventDetailModal(eventId) {
    console.log('Showing presenter event detail for ID:', eventId);
    
    const event = allEvents.find(e => e.id == eventId);
    if (!event) {
        showAlert('Event tidak ditemukan', 'danger');
        return;
    }
    
    const eventDate = new Date(event.date);
    const formattedDate = eventDate.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const isPast = eventDate < new Date();
    const isPresenting = event.is_registered && event.registration_type === 'presenter';
    
    const detailHTML = `
        <div class="event-detail-content">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <h4 class="mb-2">${event.title}</h4>
                        <div class="event-badges mb-3">
                            ${isPresenting ? 
                                '<span class="badge bg-success fs-6"><i class="fas fa-microphone me-1"></i>You are Presenting</span>' : 
                                event.call_for_papers ? 
                                    '<span class="badge bg-success fs-6"><i class="fas fa-microphone me-1"></i>Available for Presenter Registration</span>' :
                                    '<span class="badge bg-secondary fs-6"><i class="fas fa-users me-1"></i>Audience Only Event</span>'
                            }
                            ${getFormatBadge(event.format)}
                            ${getCategoryBadge(event.category)}
                            ${isPast ? '<span class="badge bg-secondary fs-6"><i class="fas fa-history me-1"></i>Berakhir</span>' : ''}
                        </div>
                    </div>
                    
                    <div class="event-detail-meta mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <strong><i class="fas fa-calendar text-success me-2"></i>Tanggal:</strong><br>
                                    <span class="fs-6">${formattedDate}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <strong><i class="fas fa-clock text-success me-2"></i>Waktu:</strong><br>
                                    <span class="fs-6">${event.time} WIB</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <strong><i class="fas fa-${event.format === 'online' ? 'video' : 'map-marker-alt'} text-success me-2"></i>Lokasi:</strong><br>
                                    <span class="fs-6">${event.format === 'online' ? 'Online (Link akan dikirim)' : event.location || 'TBA'}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <strong><i class="fas fa-money-bill text-success me-2"></i>Presenter Fee:</strong><br>
                                    <span class="fs-6 ${event.presenter_fee > 0 ? 'text-success fw-bold' : 'text-muted'}">
                                        ${event.presenter_fee > 0 ? `Rp ${parseInt(event.presenter_fee).toLocaleString('id-ID')}` : 'Volunteer (No Fee)'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle text-success me-2"></i>Deskripsi Event:</h6>
                        <p class="text-muted">${event.description}</p>
                    </div>
                    
                    ${event.call_for_papers_deadline ? `
                        <div class="mb-4">
                            <h6 class="mb-2"><i class="fas fa-calendar-times text-warning me-2"></i>Deadline Abstract Submission:</h6>
                            <p class="text-warning fw-bold">${new Date(event.call_for_papers_deadline).toLocaleDateString('id-ID')}</p>
                        </div>
                    ` : ''}
                    
                    ${event.presenter_requirements ? `
                        <div class="mb-4">
                            <h6 class="mb-2"><i class="fas fa-clipboard-list text-info me-2"></i>Requirements for Presenters:</h6>
                            <p class="text-muted">${event.presenter_requirements}</p>
                        </div>
                    ` : ''}
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <div class="mb-4">
                                ${isPresenting ? 
                                    `<i class="fas fa-microphone fa-4x text-success mb-3"></i>
                                     <h5 class="text-success">You are Presenting</h5>
                                     <div class="alert alert-success">
                                         <strong>Status:</strong> ${event.registration_status || 'Confirmed'}<br>
                                         <strong>Abstract Status:</strong> ${event.abstract_status || 'Pending'}
                                     </div>` :
                                    isPast ? 
                                        `<i class="fas fa-clock fa-4x text-secondary mb-3"></i>
                                         <h5 class="text-secondary">Event Sudah Berakhir</h5>
                                         <p class="text-muted">Event ini sudah tidak tersedia untuk registrasi presenter</p>` :
                                        event.call_for_papers ?
                                            `<i class="fas fa-microphone fa-4x text-success mb-3"></i>
                                             <h5 class="text-success">Call for Papers Open</h5>
                                             <p class="text-muted">Daftar sebagai presenter dan submit abstract Anda</p>` :
                                            `<i class="fas fa-users fa-4x text-secondary mb-3"></i>
                                             <h5 class="text-secondary">Audience Only</h5>
                                             <p class="text-muted">Event ini tidak menerima presenter dari luar</p>`
                                }
                            </div>
                            
                            <div class="d-grid gap-2">
                                ${!isPresenting && !isPast && event.call_for_papers ? 
                                    `<button class="btn btn-success btn-lg" onclick="showRegistrationModal('${event.id}')">
                                        <i class="fas fa-microphone me-2"></i>Register as Presenter
                                    </button>
                                    <small class="text-muted">
                                        You will need to submit an abstract after registration
                                    </small>` :
                                    isPresenting ?
                                        `<a href="/presenter/abstracts" class="btn btn-success">
                                            <i class="fas fa-file-alt me-2"></i>Manage Abstracts
                                        </a>
                                        <a href="/presenter/presentations" class="btn btn-outline-primary mt-2">
                                            <i class="fas fa-eye me-2"></i>View Presentation Details
                                        </a>` : ''
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('eventDetailContent').innerHTML = detailHTML;
    const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
    modal.show();
}

// Show registration modal for presenter
function showRegistrationModal(eventId) {
    console.log('Showing presenter registration modal for event ID:', eventId);
    
    const event = allEvents.find(e => e.id == eventId);
    if (!event) {
        showAlert('Event tidak ditemukan', 'danger');
        return;
    }
    
    const isPresenting = event.is_registered && event.registration_type === 'presenter';
    if (isPresenting) {
        showAlert('Anda sudah terdaftar sebagai presenter untuk event ini', 'warning');
        return;
    }
    
    const registrationHTML = `
        <div class="registration-form">
            <div class="text-center mb-4">
                <h5 class="text-success">Konfirmasi Pendaftaran Presenter</h5>
                <p class="text-muted">Anda akan mendaftar sebagai presenter untuk event berikut:</p>
            </div>
            
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h6 class="card-title">${event.title}</h6>
                    <div class="row text-sm">
                        <div class="col-6">
                            <strong>Tanggal:</strong><br>
                            <span class="text-muted">${new Date(event.date).toLocaleDateString('id-ID')}</span>
                        </div>
                        <div class="col-6">
                            <strong>Waktu:</strong><br>
                            <span class="text-muted">${event.time} WIB</span>
                        </div>
                        <div class="col-6 mt-2">
                            <strong>Lokasi:</strong><br>
                            <span class="text-muted">${event.format === 'online' ? 'Online' : event.location || 'TBA'}</span>
                        </div>
                        <div class="col-6 mt-2">
                            <strong>Presenter Fee:</strong><br>
                            <span class="text-success fw-bold">
                                ${event.presenter_fee > 0 ? `Rp ${parseInt(event.presenter_fee).toLocaleString('id-ID')}` : 'Volunteer'}
                            </span>
                        </div>
                        ${event.call_for_papers_deadline ? `
                            <div class="col-12 mt-2">
                                <strong>Abstract Deadline:</strong><br>
                                <span class="text-warning fw-bold">${new Date(event.call_for_papers_deadline).toLocaleDateString('id-ID')}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <form id="registrationForm">
                <input type="hidden" name="registration_type" value="presenter">
                
                <div class="mb-3">
                    <label class="form-label">Bidang Keahlian/Expertise:</label>
                    <input type="text" class="form-control" name="expertise" required placeholder="e.g., Machine Learning, Data Science, Web Development">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Topik yang Akan Dipresentasikan:</label>
                    <textarea class="form-control" name="proposed_topic" rows="3" required placeholder="Jelaskan topik yang akan Anda presentasikan"></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Bio Singkat:</label>
                    <textarea class="form-control" name="bio" rows="3" placeholder="Ceritakan sedikit tentang background Anda"></textarea>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">
                        Saya setuju dengan syarat dan ketentuan sebagai presenter event ini
                    </label>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeAbstract" required>
                    <label class="form-check-label" for="agreeAbstract">
                        Saya berkomitmen untuk submit abstract sesuai deadline yang ditentukan
                    </label>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informasi Penting:</strong><br>
                    Setelah registrasi berhasil, Anda perlu submit abstract melalui halaman "Abstract Management". 
                    Abstract akan direview oleh reviewer dan status akan diinformasikan melalui email.
                </div>
            </form>
        </div>
    `;
    
    document.getElementById('registrationContent').innerHTML = registrationHTML;
    
    // Set up confirmation button
    const confirmBtn = document.getElementById('confirmRegistrationBtn');
    confirmBtn.onclick = () => registerAsPresenter(eventId);
    
    const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
    modal.show();
}

// Register as presenter - simplified version
async function registerAsPresenter(eventId) {
    try {
        console.log('Starting presenter registration for event ID:', eventId);
        
        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const agreeTerms = document.getElementById('agreeTerms');
        const agreeAbstract = document.getElementById('agreeAbstract');
        if (!agreeTerms.checked || !agreeAbstract.checked) {
            showAlert('Anda harus menyetujui semua syarat dan ketentuan', 'warning');
            return;
        }
        
        // Show loading state
        const confirmBtn = document.getElementById('confirmRegistrationBtn');
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftar...';
        confirmBtn.disabled = true;
        
        // Simulate successful registration for now
        // In real implementation, this would call the API
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        console.log('Presenter registration completed successfully');
        
        // Update local event data
        const event = allEvents.find(e => e.id == eventId);
        if (event) {
            event.is_registered = true;
            event.registration_type = 'presenter';
            event.registration_status = 'confirmed';
            event.abstract_status = 'pending';
        }
        
        // Close modals
        const registrationModal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
        if (registrationModal) {
            registrationModal.hide();
        }
        
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('eventDetailModal'));
        if (detailModal) {
            detailModal.hide();
        }
        
        // Show success message
        showAlert('Berhasil mendaftar sebagai presenter! Selanjutnya silakan submit abstract Anda melalui halaman Abstract Management.', 'success', 8000);
        
        // Refresh views
        updateEventStats(allEvents);
        renderCurrentView();
        
        // Optionally redirect to abstracts page
        setTimeout(() => {
            if (confirm('Lanjutkan ke halaman Abstract Management untuk submit abstract sekarang?')) {
                window.location.href = '/presenter/abstracts';
            }
        }, 3000);
        
    } catch (error) {
        console.error('Presenter registration error:', error);
        showAlert('Gagal mendaftar sebagai presenter. Silakan coba lagi.', 'danger', 5000);
    } finally {
        // Reset button state
        const confirmBtn = document.getElementById('confirmRegistrationBtn');
        if (confirmBtn) {
            confirmBtn.innerHTML = '<i class="fas fa-check me-1"></i>Konfirmasi Pendaftaran';
            confirmBtn.disabled = false;
        }
    }
}

// Show day events (for calendar view)
function showDayEvents(dateStr) {
    const dayEvents = allEvents.filter(event => event.date === dateStr);
    const date = new Date(dateStr);
    const formattedDate = date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    let eventsHTML = `
        <div class="day-events">
            <h5 class="mb-3">Event pada ${formattedDate}</h5>
            <div class="list-group">
    `;
    
    dayEvents.forEach(event => {
        const isPresenting = event.is_registered && event.registration_type === 'presenter';
        eventsHTML += `
            <div class="list-group-item list-group-item-action" onclick="showEventDetailModal('${event.id}')">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${event.title}</h6>
                    <small class="text-muted">${event.time}</small>
                </div>
                <p class="mb-1">${truncateText(event.description, 100)}</p>
                <small class="text-muted">
                    ${isPresenting ? 
                        '<span class="badge bg-success">Presenting</span>' : 
                        event.call_for_papers ? '<span class="badge bg-success">Call for Papers</span>' : '<span class="badge bg-secondary">Audience Only</span>'
                    }
                    ${getFormatBadge(event.format)}
                </small>
            </div>
        `;
    });
    
    eventsHTML += '</div></div>';
    
    // Show in modal
    document.getElementById('eventDetailContent').innerHTML = eventsHTML;
    const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
    modal.show();
}

// Clear search
function clearSearch() {
    const searchInput = document.getElementById('event-search');
    if (searchInput) {
        searchInput.value = '';
        filteredEvents = [...allEvents];
        renderListView(filteredEvents);
    }
}

// Reset timeline filter
function resetTimelineFilter() {
    const filterSelect = document.getElementById('timeline-filter');
    if (filterSelect) {
        filterSelect.value = 'all';
        filteredEvents = [...allEvents];
        renderTimelineView(filteredEvents);
    }
}

// Refresh schedule data
function refreshSchedule() {
    console.log('Refreshing presenter schedule data...');
    showAlert('Memuat ulang jadwal acara...', 'info', 2000);
    loadEventScheduleData();
}

// Show alert messages
function showAlert(message, type = 'info', duration = 4000) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        console.warn('Alert container not found');
        return;
    }
    
    const alertId = 'alert-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    
    const iconMap = {
        'success': 'check-circle',
        'danger': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${iconMap[type] || 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-dismiss after duration
    if (duration > 0) {
        setTimeout(() => {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertElement);
                bsAlert.close();
            }
        }, duration);
    }
    
    // Scroll to alert if needed
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Console log for debugging
console.log('Presenter Event Schedule JavaScript loaded successfully');
console.log('Version: Presenter-specific Event Schedule');

// Export functions for global access (if needed)
window.PresenterEventSchedule = {
    refreshSchedule,
    showEventDetailModal,
    showRegistrationModal,
    registerAsPresenter,
    clearSearch,
    resetTimelineFilter
};
</script>

<?= $this->endSection() ?>