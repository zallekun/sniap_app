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
}

.event-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
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
        
        // Try multiple endpoints
        const endpoints = [
            '<?= base_url('dashboard/event-schedule') ?>',
            '<?= base_url('audience/api/events') ?>',
            '<?= base_url('api/v1/events') ?>'
        ];
        
        let data = null;
        let success = false;
        
        for (const endpoint of endpoints) {
            try {
                console.log('Trying endpoint:', endpoint);
                const response = await fetch(endpoint, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    data = await response.json();
                    if (data.status === 'success' && data.data) {
                        success = true;
                        break;
                    }
                }
            } catch (e) {
                console.log('Endpoint failed:', endpoint, e.message);
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
        
        // Load demo data for development
        loadDemoData();
        showAlert('Menggunakan data demo untuk keperluan development', 'warning', 5000);
    }
}

// Load demo data for development purposes
function loadDemoData() {
    console.log('Loading demo data...');
    
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    const nextWeek = new Date(today);
    nextWeek.setDate(today.getDate() + 7);
    
    allEvents = [
        {
            id: 1,
            title: 'International Tech Conference 2024',
            description: 'Konferensi teknologi internasional yang membahas tren terbaru dalam dunia teknologi, AI, dan transformasi digital. Dihadiri oleh para ahli dan praktisi dari seluruh dunia.',
            date: formatDate(tomorrow),
            time: '09:00',
            location: 'Jakarta Convention Center',
            format: 'hybrid',
            registration_fee: 500000,
            max_participants: 500,
            current_participants: 234,
            is_registered: false,
            registration_status: 'open',
            speaker: 'Dr. John Smith, Prof. Maria Garcia',
            category: 'conference'
        },
        {
            id: 2,
            title: 'Web Development Workshop',
            description: 'Workshop intensif pengembangan web modern menggunakan teknologi terbaru seperti React, Vue.js, dan Node.js. Cocok untuk developer pemula hingga intermediate.',
            date: formatDate(nextWeek),
            time: '13:00',
            location: 'Online via Zoom',
            format: 'online',
            registration_fee: 250000,
            max_participants: 100,
            current_participants: 45,
            is_registered: true,
            registration_status: 'confirmed',
            payment_status: 'paid',
            speaker: 'Ahmad Fadil, S.Kom',
            category: 'workshop'
        },
        {
            id: 3,
            title: 'AI & Machine Learning Seminar',
            description: 'Seminar tentang perkembangan terbaru dalam bidang Artificial Intelligence dan Machine Learning. Membahas aplikasi praktis dan implementasi dalam berbagai industri.',
            date: formatDate(new Date(today.getTime() + 14 * 24 * 60 * 60 * 1000)), // 2 weeks from now
            time: '14:30',
            location: 'Universitas Indonesia, Auditorium',
            format: 'offline',
            registration_fee: 150000,
            max_participants: 200,
            current_participants: 89,
            is_registered: false,
            registration_status: 'open',
            speaker: 'Dr. Sarah Wilson, Dr. Michael Chen',
            category: 'seminar'
        }
    ];
    
    filteredEvents = [...allEvents];
    updateEventStats(allEvents);
    renderCurrentView();
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
    // Loading will be replaced by actual content
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
            dayEvents.slice(0, 3).forEach(event => { // Show max 3 events per day
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
    
    // Sort events by date and time
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

// Register for event
async function registerForEvent(eventId) {
    try {
        console.log('Registering for event ID:', eventId);
        
        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Show loading state
        const confirmBtn = document.getElementById('confirmRegistrationBtn');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftar...';
        confirmBtn.disabled = true;
        
        // Prepare data
        const registrationData = {
            event_id: eventId,
            registration_type: formData.get('registration_type') || 'audience',
            notes: formData.get('notes') || ''
        };
        
        // Try multiple endpoints
        const endpoints = [
            '<?= base_url('dashboard/register-event') ?>',
            '<?= base_url('audience/api/register-event') ?>',
            '<?= base_url('api/v1/registrations/register') ?>'
        ];
        
        let success = false;
        let response, data;
        
        for (const endpoint of endpoints) {
            try {
                console.log('Trying registration endpoint:', endpoint);
                
                response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(registrationData)
                });
                
                if (response.ok) {
                    data = await response.json();
                    if (data.status === 'success') {
                        success = true;
                        break;
                    }
                }
            } catch (e) {
                console.log('Registration endpoint failed:', endpoint, e.message);
                continue;
            }
        }
        
        if (success) {
            // Update local event data
            const event = allEvents.find(e => e.id == eventId);
            if (event) {
                event.is_registered = true;
                event.registration_status = 'confirmed';
                if (event.registration_fee === 0) {
                    event.payment_status = 'paid';
                } else {
                    event.payment_status = 'pending';
                }
            }
            
            // Close modal and show success
            bootstrap.Modal.getInstance(document.getElementById('registrationModal')).hide();
            bootstrap.Modal.getInstance(document.getElementById('eventDetailModal')).hide();
            
            showAlert('Berhasil mendaftar event! ' + (event.registration_fee > 0 ? 'Silakan lanjutkan pembayaran.' : 'Ticket sudah dikirim ke email Anda.'), 'success', 5000);
            
            // Refresh views
            updateEventStats(allEvents);
            renderCurrentView();
            
            // Redirect to payment if needed
            if (event.registration_fee > 0 && data.data && data.data.payment_url) {
                setTimeout(() => {
                    if (confirm('Event memerlukan pembayaran. Lanjutkan ke halaman pembayaran sekarang?')) {
                        window.open(data.data.payment_url, '_blank');
                    }
                }, 2000);
            }
            
        } else {
            throw new Error(data?.message || 'Registration failed');
        }
        
    } catch (error) {
        console.error('Error registering for event:', error);
        showAlert('Gagal mendaftar event: ' + error.message, 'danger');
    } finally {
        // Reset button
        const confirmBtn = document.getElementById('confirmRegistrationBtn');
        if (confirmBtn) {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    }
}

// View registration details
function viewRegistrationDetails(eventId) {
    console.log('Viewing registration details for event ID:', eventId);
    showAlert('Fitur detail registrasi akan segera tersedia', 'info');
}

// Process payment
function processPayment(eventId) {
    console.log('Processing payment for event ID:', eventId);
    const event = allEvents.find(e => e.id == eventId);
    if (event) {
        showAlert(`Mengarahkan ke halaman pembayaran untuk ${event.title}...`, 'info');
        // Redirect to payment page
        setTimeout(() => {
            window.location.href = `<?= base_url('audience/payments') ?>`;
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
    const alertId = 'alert-' + Date.now();
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-dismiss after duration
    if (duration > 0) {
        setTimeout(() => {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                const bsAlert = new bootstrap.Alert(alertElement);
                bsAlert.close();
            }
        }, duration);
    }
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

// Add some custom styles
document.head.insertAdjacentHTML('beforeend', `
<style>
.bg-purple {
    background-color: #6f42c1 !important;
}

.calendar-day:hover .calendar-event {
    transform: scale(1.05);
}

.event-item:hover {
    border-left-width: 6px !important;
}

.timeline-marker {
    box-shadow: 0 0 0 4px #fff, 0 0 0 6px #dee2e6;
}

.timeline-item:hover .timeline-marker {
    transform: scale(1.1);
}

.loading-spinner {
    min-height: 200px;
}

.empty-state {
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.modal-xl {
    max-width: 1200px;
}

.event-badges .badge {
    margin-right: 0.5rem;
    margin-bottom: 0.25rem;
}

.progress {
    border-radius: 10px;
}

.card-body .row.text-sm {
    font-size: 0.875rem;
}

@media (max-width: 576px) {
    .schedule-stats {
        text-align: center;
    }
    
    .calendar-day {
        min-height: 60px;
        padding: 0.25rem;
    }
    
    .calendar-event {
        font-size: 0.6rem;
        padding: 0.1rem;
    }
    
    .event-actions .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Animation for state changes */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Loading animation */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.loading-spinner .spinner-border {
    animation: pulse 1s infinite;
}
</style>
`);

// Console log for debugging
console.log('Event Schedule View loaded successfully');
console.log('Available endpoints will be tried in order:');
console.log('1. <?= base_url('dashboard/event-schedule') ?>');
console.log('2. <?= base_url('audience/api/events') ?>');
console.log('3. <?= base_url('api/v1/events') ?>');
</script>

<?= $this->endSection() ?>