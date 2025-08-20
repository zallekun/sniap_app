<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>Event Schedule<?= $this->endSection() ?>

<?= $this->section('head') ?>
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
<div class="py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>" class="text-decoration-none">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active">Jadwal Acara</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="fas fa-calendar me-3"></i>
                    Jadwal Acara SNIA Conference
                </h1>
                <p class="page-subtitle text-muted">
                    Lihat jadwal lengkap acara, daftar event yang tersedia, dan kelola pendaftaran Anda
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="schedule-stats">
                    <div class="stat-item">
                        <span class="stat-label">Event Tersedia</span>
                        <span class="stat-value" id="totalEventsCount">-</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Terdaftar</span>
                        <span class="stat-value text-success" id="registeredEventsCount">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <div id="alertContainer"></div>

    <!-- View Mode Tabs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
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
                                    <i class="fas fa-calendar me-2 text-primary"></i>
                                    <span id="current-month-year">Loading...</span>
                                </h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary" id="prev-month">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="today-btn">Hari Ini</button>
                                    <button type="button" class="btn btn-outline-primary" id="next-month">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-primary ms-2" onclick="refreshSchedule()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="event-calendar">
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status"></div>
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
                            <div class="spinner-border text-primary" role="status"></div>
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
                            <div class="spinner-border text-primary" role="status"></div>
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
                        <span class="badge bg-success me-2">Terdaftar</span>
                        <span class="badge bg-primary me-2">Tersedia</span>
                        <span class="badge bg-secondary me-2">Berakhir</span>
                        Klik event untuk melihat detail dan mendaftar
                    </small>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                    <button class="btn btn-primary" onclick="refreshSchedule()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="eventDetailModalLabel">
                    <i class="fas fa-calendar-check me-2"></i>Detail Event
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetailContent">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="ms-3">Memuat detail event...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" id="registerEventBtn" style="display: none;">
                    <i class="fas fa-user-plus me-1"></i>Daftar Event
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
                    <i class="fas fa-user-plus me-2"></i>Konfirmasi Pendaftaran
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
    console.log('Event Schedule page initializing...');
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
        console.log('Loading event schedule data...');
        showLoading();
        
        const baseUrl = getBaseUrl();
        
        // Try multiple endpoints with different approaches
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
                url: baseUrl + 'audience/api/events',
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
            
            console.log('Loaded', allEvents.length, 'events');
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
            <div class="spinner-border text-primary" role="status"></div>
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

// Update event statistics
function updateEventStats(events) {
    const totalCount = events.length;
    const registeredCount = events.filter(event => event.is_registered).length;
    
    document.getElementById('totalEventsCount').textContent = totalCount;
    document.getElementById('registeredEventsCount').textContent = registeredCount;
    
    console.log('Stats updated:', { total: totalCount, registered: registeredCount });
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

// Render Calendar View
function renderCalendarView(events) {
    console.log('Rendering calendar view with', events.length, 'events');
    
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
                const eventClass = event.is_registered ? 'event-registered' : 'event-available';
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
    
    console.log('Calendar rendered successfully');
}

// Render List View
function renderListView(events) {
    console.log('Rendering list view with', events.length, 'events');
    
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
                <button class="btn btn-primary" onclick="clearSearch()">
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
        
        const statusBadge = event.is_registered ? 
            '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Terdaftar</span>' : 
            '<span class="badge bg-primary"><i class="fas fa-calendar-plus me-1"></i>Tersedia</span>';
        
        const formatBadge = getFormatBadge(event.format);
        const categoryBadge = getCategoryBadge(event.category);
        
        const isPast = eventDate < new Date();
        const cardClass = isPast ? 'border-secondary' : (event.is_registered ? 'border-success' : 'border-primary');
        
        listHTML += `
            <div class="event-item card mb-3 ${cardClass}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="event-icon me-3">
                                    <i class="fas fa-${getEventIcon(event.category)} fa-2x text-primary"></i>
                                </div>
                                <div class="event-info flex-grow-1">
                                    <h6 class="event-title mb-2">
                                        ${event.title}
                                        ${isPast ? '<small class="text-muted">(Sudah Berakhir)</small>' : ''}
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
                                                ${event.registration_fee > 0 ? `Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : 'Gratis'}
                                            </div>
                                        </div>
                                    </div>
                                    <p class="event-description mb-2">${truncateText(event.description, 150)}</p>
                                    <div class="event-badges">
                                        ${statusBadge}
                                        ${formatBadge}
                                        ${categoryBadge}
                                        ${event.speaker ? `<span class="badge bg-info"><i class="fas fa-user me-1"></i>${truncateText(event.speaker, 30)}</span>` : ''}
                                    </div>
                                    ${event.current_participants && event.max_participants ? `
                                        <div class="mt-2">
                                            <small class="text-muted">Peserta: ${event.current_participants}/${event.max_participants}</small>
                                            <div class="progress mt-1" style="height: 4px;">
                                                <div class="progress-bar" style="width: ${(event.current_participants / event.max_participants) * 100}%"></div>
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="event-actions">
                                <button class="btn btn-outline-primary btn-sm d-block mb-2" onclick="showEventDetailModal('${event.id}')">
                                    <i class="fas fa-info-circle me-1"></i>Detail Lengkap
                                </button>
                                ${!event.is_registered && !isPast ? 
                                    `<button class="btn btn-success btn-sm d-block" onclick="showRegistrationModal('${event.id}')">
                                        <i class="fas fa-user-plus me-1"></i>Daftar Sekarang
                                    </button>` : 
                                    event.is_registered ? 
                                        `<button class="btn btn-success btn-sm d-block" disabled>
                                            <i class="fas fa-check me-1"></i>Sudah Terdaftar
                                        </button>
                                        <small class="text-muted d-block mt-1">Status: ${event.registration_status || 'Confirmed'}</small>` :
                                        `<button class="btn btn-secondary btn-sm d-block" disabled>
                                            <i class="fas fa-clock me-1"></i>Berakhir
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
    
    console.log('List view rendered successfully');
}

// Render Timeline View
function renderTimelineView(events) {
    console.log('Rendering timeline view with', events.length, 'events');
    
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
                <button class="btn btn-primary" onclick="resetTimelineFilter()">
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
        const timelineClass = isPast ? 'timeline-past' : 'timeline-future';
        
        const formattedDate = eventDate.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        timelineHTML += `
            <div class="timeline-item ${timelineClass}">
                <div class="timeline-marker ${event.is_registered ? 'registered' : 'available'}">
                    <i class="fas ${event.is_registered ? 'fa-check' : 'fa-calendar'}"></i>
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
                                                ${event.registration_fee > 0 ? `Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : 'Gratis'}
                                            </div>
                                        </div>
                                    </div>
                                    <p class="timeline-description mb-3">${truncateText(event.description, 200)}</p>
                                    ${event.speaker ? `
                                        <div class="mb-3">
                                            <strong><i class="fas fa-user me-1"></i>Speaker:</strong>
                                            <span class="text-muted">${event.speaker}</span>
                                        </div>
                                    ` : ''}
                                    <div class="timeline-badges">
                                        ${event.is_registered ? 
                                            '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Terdaftar</span>' : 
                                            '<span class="badge bg-primary"><i class="fas fa-calendar-plus me-1"></i>Tersedia</span>'
                                        }
                                        ${getFormatBadge(event.format)}
                                        ${getCategoryBadge(event.category)}
                                        ${isPast ? '<span class="badge bg-secondary"><i class="fas fa-history me-1"></i>Berakhir</span>' : ''}
                                    </div>
                                    ${event.current_participants && event.max_participants ? `
                                        <div class="mt-3">
                                            <small class="text-muted">Peserta: ${event.current_participants}/${event.max_participants}</small>
                                            <div class="progress mt-1" style="height: 6px;">
                                                <div class="progress-bar" style="width: ${(event.current_participants / event.max_participants) * 100}%"></div>
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="timeline-actions">
                                        <button class="btn btn-outline-primary btn-sm d-block mb-2" onclick="showEventDetailModal('${event.id}')">
                                            <i class="fas fa-info-circle me-1"></i>Detail
                                        </button>
                                        ${!event.is_registered && !isPast ? 
                                            `<button class="btn btn-success btn-sm d-block mb-2" onclick="showRegistrationModal('${event.id}')">
                                                <i class="fas fa-user-plus me-1"></i>Daftar
                                            </button>` : 
                                            event.is_registered ? 
                                                `<button class="btn btn-success btn-sm d-block mb-2" disabled>
                                                    <i class="fas fa-check me-1"></i>Terdaftar
                                                </button>
                                                ${event.payment_status === 'paid' ? 
                                                    '<small class="text-success d-block"><i class="fas fa-check-circle me-1"></i>Lunas</small>' :
                                                    '<small class="text-warning d-block"><i class="fas fa-clock me-1"></i>Pending</small>'
                                                }` :
                                                `<button class="btn btn-secondary btn-sm d-block mb-2" disabled>
                                                    <i class="fas fa-clock me-1"></i>Berakhir
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
    
    console.log('Timeline view rendered successfully');
}

// Show event detail in modal
function showEventDetailModal(eventId) {
    console.log('Showing event detail for ID:', eventId);
    
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
    
    const detailHTML = `
        <div class="event-detail-content">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <h4 class="mb-2">${event.title}</h4>
                        <div class="event-badges mb-3">
                            ${event.is_registered ? 
                                '<span class="badge bg-success fs-6"><i class="fas fa-check me-1"></i>Sudah Terdaftar</span>' : 
                                '<span class="badge bg-primary fs-6"><i class="fas fa-calendar-plus me-1"></i>Tersedia untuk Registrasi</span>'
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
                                    <strong><i class="fas fa-calendar text-primary me-2"></i>Tanggal:</strong><br>
                                    <span class="fs-6">${formattedDate}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <strong><i class="fas fa-clock text-primary me-2"></i>Waktu:</strong><br>
                                    <span class="fs-6">${event.time} WIB</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <strong><i class="fas fa-${event.format === 'online' ? 'video' : 'map-marker-alt'} text-primary me-2"></i>Lokasi:</strong><br>
                                    <span class="fs-6">${event.format === 'online' ? 'Online (Link akan dikirim)' : event.location || 'TBA'}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <strong><i class="fas fa-money-bill text-primary me-2"></i>Biaya Registrasi:</strong><br>
                                    <span class="fs-6 ${event.registration_fee > 0 ? 'text-success fw-bold' : 'text-success fw-bold'}">
                                        ${event.registration_fee > 0 ? `Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : 'GRATIS'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${event.speaker ? `
                        <div class="mb-4">
                            <h6 class="mb-2"><i class="fas fa-user text-primary me-2"></i>Speaker/Pemateri:</h6>
                            <p class="text-muted">${event.speaker}</p>
                        </div>
                    ` : ''}
                    
                    <div class="mb-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Deskripsi Event:</h6>
                        <p class="text-muted">${event.description}</p>
                    </div>
                    
                    ${event.current_participants && event.max_participants ? `
                        <div class="mb-4">
                            <h6 class="mb-2"><i class="fas fa-users text-primary me-2"></i>Kapasitas Peserta:</h6>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 me-3">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar ${(event.current_participants / event.max_participants) > 0.8 ? 'bg-warning' : 'bg-success'}" 
                                             style="width: ${(event.current_participants / event.max_participants) * 100}%">
                                        </div>
                                    </div>
                                </div>
                                <span class="text-muted">${event.current_participants}/${event.max_participants}</span>
                            </div>
                        </div>
                    ` : ''}
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <div class="mb-4">
                                ${event.is_registered ? 
                                    `<i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                     <h5 class="text-success">Sudah Terdaftar</h5>
                                     <div class="alert alert-success">
                                         <strong>Status:</strong> ${event.registration_status || 'Confirmed'}<br>
                                         ${event.payment_status ? `<strong>Pembayaran:</strong> ${event.payment_status === 'paid' ? 'Lunas' : 'Pending'}` : ''}
                                     </div>` :
                                    isPast ? 
                                        `<i class="fas fa-clock fa-4x text-secondary mb-3"></i>
                                         <h5 class="text-secondary">Event Sudah Berakhir</h5>
                                         <p class="text-muted">Event ini sudah tidak tersedia untuk registrasi</p>` :
                                        `<i class="fas fa-calendar-plus fa-4x text-primary mb-3"></i>
                                         <h5 class="text-primary">Tersedia untuk Registrasi</h5>
                                         <p class="text-muted">Klik tombol di bawah untuk mendaftar</p>`
                                }
                            </div>
                            
                            <div class="d-grid gap-2">
                                ${!event.is_registered && !isPast ? 
                                    `<button class="btn btn-success btn-lg" onclick="showRegistrationModal('${event.id}')">
                                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                    </button>
                                    <small class="text-muted">
                                        ${event.registration_fee > 0 ? 'Pembayaran setelah registrasi' : 'Registrasi gratis'}
                                    </small>` :
                                    event.is_registered ?
                                        `<button class="btn btn-outline-primary" onclick="viewRegistrationDetails('${event.id}')">
                                            <i class="fas fa-eye me-2"></i>Lihat Detail Registrasi
                                        </button>
                                        ${event.payment_status !== 'paid' && event.registration_fee > 0 ? 
                                            `<button class="btn btn-warning mt-2" onclick="processPayment('${event.id}')">
                                                <i class="fas fa-credit-card me-2"></i>Lanjutkan Pembayaran
                                            </button>` : ''
                                        }` : ''
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

// Show registration modal
function showRegistrationModal(eventId) {
    console.log('Showing registration modal for event ID:', eventId);
    
    const event = allEvents.find(e => e.id == eventId);
    if (!event) {
        showAlert('Event tidak ditemukan', 'danger');
        return;
    }
    
    if (event.is_registered) {
        showAlert('Anda sudah terdaftar untuk event ini', 'warning');
        return;
    }
    
    const registrationHTML = `
        <div class="registration-form">
            <div class="text-center mb-4">
                <h5 class="text-success">Konfirmasi Pendaftaran</h5>
                <p class="text-muted">Anda akan mendaftar untuk event berikut:</p>
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
                            <strong>Biaya:</strong><br>
                            <span class="text-success fw-bold">
                                ${event.registration_fee > 0 ? `Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : 'GRATIS'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="registrationForm">
                <div class="mb-3">
                    <label class="form-label">Tipe Registrasi:</label>
                    <select class="form-select" name="registration_type" required>
                        <option value="audience">Peserta/Audience</option>
                        <option value="presenter">Presenter (jika ada call for papers)</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Catatan Khusus (Opsional):</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Alergi makanan, kebutuhan khusus, dll."></textarea>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">
                        Saya setuju dengan syarat dan ketentuan event ini
                    </label>
                </div>
                
                ${event.registration_fee > 0 ? `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi Pembayaran:</strong><br>
                        Setelah registrasi berhasil, Anda akan diarahkan ke halaman pembayaran.
                        Event ticket akan dikirim setelah pembayaran dikonfirmasi.
                    </div>
                ` : `
                    <div class="alert alert-success">
                        <i class="fas fa-gift me-2"></i>
                        <strong>Event Gratis!</strong><br>
                        Event ticket akan langsung dikirim ke email Anda setelah registrasi.
                    </div>
                `}
            </form>
        </div>
    `;
    
    document.getElementById('registrationContent').innerHTML = registrationHTML;
    
    // Set up confirmation button
    const confirmBtn = document.getElementById('confirmRegistrationBtn');
    confirmBtn.onclick = () => registerForEvent(eventId);
    
    const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
    modal.show();
}

// Register for event - Fixed version with proper CSRF handling
async function registerForEvent(eventId) {
    try {
        console.log('Starting registration for event ID:', eventId);
        
        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const agreeTerms = document.getElementById('agreeTerms');
        if (!agreeTerms.checked) {
            showAlert('Anda harus menyetujui syarat dan ketentuan', 'warning');
            return;
        }
        
        // Show loading state
        const confirmBtn = document.getElementById('confirmRegistrationBtn');
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftar...';
        confirmBtn.disabled = true;
        
        const baseUrl = getBaseUrl();
        
        // Get CSRF token and name
        const csrfToken = getCsrfToken();
        const csrfTokenName = getCsrfTokenName();
        
        console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
        console.log('CSRF Token Name:', csrfTokenName);
        
        // Prepare registration data
        const registrationData = {
            event_id: parseInt(eventId),
            registration_type: formData.get('registration_type') || 'audience',
            notes: formData.get('notes') || ''
        };
        
        // Add CSRF token to data
        if (csrfToken) {
            registrationData[csrfTokenName] = csrfToken;
        }
        
        console.log('Registration data:', registrationData);
        
        // Try registration with form submission approach (most compatible with CodeIgniter)
        const registrationEndpoints = [
            // Method 1: Audience API endpoint (preferred)
            {
                url: baseUrl + 'audience/register-event',
                method: 'POST',
                useFormData: true
            },
            // Method 2: Direct form submission to index.php with audience route
            {
                url: baseUrl + 'index.php/audience/register-event',
                method: 'POST',
                useFormData: true
            },
            // Method 3: Standard CodeIgniter form submission with CSRF (fallback)
            {
                url: baseUrl + 'dashboard/register-event',
                method: 'POST',
                useFormData: true
            },
            // Method 4: API endpoint with JSON
            {
                url: baseUrl + 'api/v1/registrations/register',
                method: 'POST',
                useFormData: false
            }
        ];
        
        let success = false;
        let response, data;
        let lastError = '';
        
        for (const endpoint of registrationEndpoints) {
            try {
                console.log('Trying registration endpoint:', endpoint.url);
                
                let requestBody;
                let headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                };
                
                if (endpoint.useFormData) {
                    // Use FormData for traditional form submission
                    requestBody = new FormData();
                    Object.keys(registrationData).forEach(key => {
                        requestBody.append(key, registrationData[key]);
                    });
                    // Don't set Content-Type header when using FormData
                } else {
                    // Use JSON for API endpoints
                    requestBody = JSON.stringify(registrationData);
                    headers['Content-Type'] = 'application/json';
                    if (csrfToken) {
                        headers['X-CSRF-TOKEN'] = csrfToken;
                    }
                }
                
                console.log('Request headers:', headers);
                console.log('Request body type:', endpoint.useFormData ? 'FormData' : 'JSON');
                
                const fetchOptions = {
                    method: endpoint.method,
                    headers: headers,
                    body: requestBody,
                    credentials: 'same-origin'
                };
                
                response = await fetch(endpoint.url, fetchOptions);
                
                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));
                
                const responseText = await response.text();
                console.log('Response text (first 500 chars):', responseText.substring(0, 500));
                
                // Try to parse as JSON
                try {
                    data = JSON.parse(responseText);
                    console.log('Parsed JSON data:', data);
                } catch (parseError) {
                    console.log('Failed to parse JSON:', parseError.message);
                    
                    // Check if response contains success indicators
                    if (response.ok) {
                        if (responseText.includes('success') || 
                            responseText.includes('registered') || 
                            responseText.includes('berhasil')) {
                            data = { status: 'success', message: 'Registration successful' };
                        } else if (responseText.includes('error') || 
                                 responseText.includes('gagal') ||
                                 responseText.includes('fail')) {
                            data = { status: 'error', message: 'Registration failed' };
                        } else {
                            // If it's HTML but successful, treat as success
                            data = { status: 'success', message: 'Registration completed' };
                        }
                    } else {
                        data = { status: 'error', message: 'Registration failed' };
                    }
                }
                
                // Check for success
                if (response.ok && data && 
                    (data.status === 'success' || 
                     data.success === true || 
                     data.message === 'Registration successful' ||
                     data.message === 'Registration completed')) {
                    success = true;
                    console.log('Registration successful via:', endpoint.url);
                    break;
                } else if (response.status === 403 || 
                          (data && (data.message || '').includes('not allowed'))) {
                    lastError = 'CSRF token error or permission denied';
                    console.log('CSRF/Permission error, trying next endpoint');
                    continue;
                } else {
                    lastError = data?.message || data?.error || `HTTP ${response.status}: ${responseText.substring(0, 100)}`;
                    console.log('Registration failed:', lastError);
                    continue;
                }
                
            } catch (error) {
                console.log('Registration attempt failed:', endpoint.url, error.message);
                lastError = error.message;
                continue;
            }
        }
        
        if (success) {
            console.log('Registration completed successfully');
            
            // Update local event data
            const event = allEvents.find(e => e.id == eventId);
            if (event) {
                event.is_registered = true;
                event.registration_status = 'confirmed';
                event.payment_status = event.registration_fee > 0 ? 'pending' : 'paid';
                
                // Update participant count
                if (event.current_participants !== undefined) {
                    event.current_participants += 1;
                }
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
            const successMessage = event && event.registration_fee > 0 ? 
                'Berhasil mendaftar event! Silakan lanjutkan pembayaran untuk mengkonfirmasi pendaftaran Anda.' :
                'Berhasil mendaftar event! Ticket konfirmasi akan dikirim ke email Anda dalam beberapa menit.';
            
            showAlert(successMessage, 'success', 8000);
            
            // Refresh views
            updateEventStats(allEvents);
            renderCurrentView();
            
            // Handle payment redirection if needed
            if (event && event.registration_fee > 0) {
                setTimeout(() => {
                    if (data.data && data.data.payment_url) {
                        if (confirm('Event memerlukan pembayaran. Lanjutkan ke halaman pembayaran sekarang?')) {
                            window.open(data.data.payment_url, '_blank');
                        }
                    } else {
                        if (confirm('Event memerlukan pembayaran. Lanjutkan ke halaman pembayaran sekarang?')) {
                            window.location.href = baseUrl + 'audience/payments';
                        }
                    }
                }, 3000);
            }
            
        } else {
            throw new Error(lastError || 'Registration failed - all endpoints failed');
        }
        
    } catch (error) {
        console.error('Registration error:', error);
        
        let errorMessage = 'Gagal mendaftar event: ';
        if (error.message.includes('CSRF') || error.message.includes('not allowed')) {
            errorMessage += 'Terjadi masalah keamanan. Silakan refresh halaman dan coba lagi.';
        } else if (error.message.includes('network') || error.message.includes('fetch')) {
            errorMessage += 'Masalah koneksi internet. Silakan coba lagi.';
        } else if (error.message.includes('403') || error.message.includes('unauthorized')) {
            errorMessage += 'Sesi Anda telah habis. Silakan login kembali.';
        } else if (error.message.includes('400') || error.message.includes('validation')) {
            errorMessage += 'Data tidak valid. Silakan periksa form dan coba lagi.';
        } else if (error.message.includes('500')) {
            errorMessage += 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
        } else {
            errorMessage += error.message || 'Terjadi kesalahan tidak dikenal.';
        }
        
        showAlert(errorMessage, 'danger', 10000);
        
    } finally {
        // Reset button state
        const confirmBtn = document.getElementById('confirmRegistrationBtn');
        if (confirmBtn) {
            confirmBtn.innerHTML = '<i class="fas fa-check me-1"></i>Konfirmasi Pendaftaran';
            confirmBtn.disabled = false;
        }
    }
}

// View registration details
function viewRegistrationDetails(eventId) {
    console.log('Viewing registration details for event ID:', eventId);
    const event = allEvents.find(e => e.id == eventId);
    if (event && event.is_registered) {
        showAlert(`Detail registrasi untuk "${event.title}" akan segera tersedia di halaman My Events.`, 'info', 5000);
        // Optionally redirect to my events page
        setTimeout(() => {
            window.location.href = getBaseUrl() + 'audience/registrations';
        }, 2000);
    } else {
        showAlert('Data registrasi tidak ditemukan', 'warning');
    }
}

// Process payment
function processPayment(eventId) {
    console.log('Processing payment for event ID:', eventId);
    const event = allEvents.find(e => e.id == eventId);
    if (event) {
        showAlert(`Mengarahkan ke halaman pembayaran untuk ${event.title}...`, 'info', 3000);
        setTimeout(() => {
            window.location.href = getBaseUrl() + 'audience/payments?event_id=' + eventId;
        }, 1500);
    }
}

// Show day events (for calendar view when there are many events in one day)
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
        eventsHTML += `
            <div class="list-group-item list-group-item-action" onclick="showEventDetailModal('${event.id}')">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${event.title}</h6>
                    <small class="text-muted">${event.time}</small>
                </div>
                <p class="mb-1">${truncateText(event.description, 100)}</p>
                <small class="text-muted">
                    ${event.is_registered ? 
                        '<span class="badge bg-success">Terdaftar</span>' : 
                        '<span class="badge bg-primary">Tersedia</span>'
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

// Utility functions
function truncateText(text, maxLength) {
    if (!text) return '';
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
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
        'conference': { class: 'bg-primary', icon: 'users', text: 'Conference' },
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
            return events.filter(event => event.is_registered);
        case 'available':
            return events.filter(event => !event.is_registered);
        case 'upcoming':
            return events.filter(event => new Date(event.date) >= now);
        case 'past':
            return events.filter(event => new Date(event.date) < now);
        default:
            return events;
    }
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
    console.log('Refreshing schedule data...');
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
console.log('Event Schedule JavaScript loaded successfully');
console.log('Version: Fixed CSRF and Registration System');

// Export functions for global access (if needed)
window.EventSchedule = {
    refreshSchedule,
    showEventDetailModal,
    showRegistrationModal,
    registerForEvent,
    clearSearch,
    resetTimelineFilter
};
</script>

<?= $this->endSection() ?>