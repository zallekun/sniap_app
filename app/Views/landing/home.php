<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/shared/landing.css') ?>">
<style>
.hero-section {
    background:
        linear-gradient(135deg, rgba(44, 62, 80, 0.75), rgba(52, 152, 219, 0.75)),
        url('<?= base_url('assets/img/Background.jpg') ?>') center/cover no-repeat;
    min-height: 100vh;
    color: white;
    display: flex;
    align-items: center;
    padding-top: 100px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section d-flex align-items-start">
    <div class="container hero-content">
       <div class="row">
            <div class="col-lg-8 col-xl-7 d-flex flex-column justify-content-center">
                <h1 class="display-custom mb-4">
                    Seminar Nasional<br>
                    Informatika dan Aplikasinya<br>
                    <span class="text-warning">(SNIA) 2025</span>
                </h1>
                <p class="lead-custom mb-5" style="max-width: 600px;">
                    Diselenggarakan oleh Jurusan Informatika Universitas Jenderal Achmad Yani 
                    (UNJANI), acara dua tahunan yang mempertemukan akademisi, peneliti, dan praktisi 
                    untuk berbagi pengetahuan dan inovasi terdepan di bidang teknologi informasi.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#daftar" class="btn btn-outline-light-custom btn-custom">DAFTAR SEKARANG</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tentang Section -->
<section id="tentang" class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <span class="badge-custom mb-4">Tentang SNIA</span>
                <h2 class="section-title">Ajang Ilmiah Dua Tahunan UNJANI</h2>
                <p class="section-subtitle">
                    SNIA adalah singkatan dari <strong>Seminar Nasional Informatika dan Aplikasinya</strong>, 
                    acara ilmiah bergengsi yang diselenggarakan oleh Jurusan Informatika <strong>UNJANI</strong> 
                    setiap dua tahun sekali. Program Studi Teknik Informatika UNJANI telah terakreditasi A 
                    oleh BAN-PT dan aktif menghadirkan forum berbagi ilmu serta hasil penelitian terbaru 
                    di bidang informatika dan aplikasinya.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Highlight Section -->
<section id="highlight" class="section-padding bg-light-custom">
    <div class="container">
        <div class="text-center mb-custom">
            <span class="badge-custom mb-4">Highlight Acara</span>
            <h2 class="section-title">Rangkaian Kegiatan</h2>
            <p class="section-subtitle">
                Bergabunglah dalam serangkaian kegiatan menarik yang dirancang untuk memperluas 
                wawasan dan membangun koneksi profesional di bidang teknologi informasi.
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-custom text-center p-4 h-100">
                    <div class="feature-icon">
                        <i class="fas fa-microphone-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Keynote Speaker</h5>
                    <p class="text-muted">
                        Pemaparan materi inspiratif dari pakar teknologi nasional dan internasional 
                        yang berpengalaman di bidangnya.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card-custom text-center p-4 h-100">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Paper Presentation</h5>
                    <p class="text-muted">
                        Presentasi hasil penelitian terbaru oleh akademisi dan peneliti dari 
                        berbagai institusi terkemuka.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card-custom text-center p-4 h-100">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Networking</h5>
                    <p class="text-muted">
                        Kesempatan emas untuk membangun koneksi dan kolaborasi dengan 
                        peneliti, akademisi, dan praktisi industri.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card-custom text-center p-4 h-100">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Workshop</h5>
                    <p class="text-muted">
                        Kegiatan pelatihan praktis dan diskusi panel interaktif untuk 
                        memperdalam pemahaman teknologi terkini.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Harga Section -->
<section id="harga" class="section-padding">
    <div class="container">
        <div class="text-center mb-custom">
            <span class="badge-custom mb-4">Harga Tiket</span>
            <h2 class="section-title">Kategori Peserta & Biaya</h2>
            <p class="section-subtitle">
                Pilih kategori peserta yang sesuai dengan kebutuhan Anda. 
                Semua paket sudah termasuk materi dan sertifikat keikutsertaan.
            </p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="price-card p-4 text-center h-100">
                    <div class="mb-4">
                        <i class="fas fa-presentation fa-3x text-primary mb-3"></i>
                        <h4 class="fw-bold mb-3">Presenter</h4>
                    </div>
                    <div class="price">Rp 500.000</div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Publikasi di Prosiding</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sertifikat Presenter</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Akses Semua Sesi</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Coffee Break & Lunch</li>
                    </ul>
                    <a href="#daftar" class="btn btn-primary-custom btn-custom w-100">Daftar Sekarang</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="price-card p-4 text-center h-100">
                    <div class="mb-4">
                        <i class="fas fa-laptop fa-3x text-info mb-3"></i>
                        <h4 class="fw-bold mb-3">Penonton Online</h4>
                    </div>
                    <div class="price">Rp 50.000</div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Akses via Zoom</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>E-Sertifikat</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Materi Digital</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Recording Akses</li>
                    </ul>
                    <a href="#daftar" class="btn btn-primary-custom btn-custom w-100">Daftar Sekarang</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="price-card p-4 text-center h-100">
                    <div class="mb-4">
                        <i class="fas fa-user-friends fa-3x text-warning mb-3"></i>
                        <h4 class="fw-bold mb-3">Penonton Offline</h4>
                    </div>
                    <div class="price">Rp 100.000</div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Hadir Langsung</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sertifikat Fisik</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Networking Session</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Coffee Break & Lunch</li>
                    </ul>
                    <a href="#daftar" class="btn btn-primary-custom btn-custom w-100">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Jadwal Section -->
<section id="jadwal" class="section-padding bg-light-custom">
    <div class="container">
        <div class="text-center mb-custom">
            <span class="badge-custom mb-4">Jadwal Acara</span>
            <h2 class="section-title">12 Desember 2025</h2>
            <p class="section-subtitle">
                Rundown lengkap kegiatan SNIA 2025 yang telah dirancang untuk memberikan 
                pengalaman pembelajaran yang optimal bagi seluruh peserta.
            </p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="schedule-item">
                    <div class="schedule-time">08.00 - 09.00</div>
                    <div class="schedule-event">
                        <strong>Registrasi & Welcome Coffee</strong><br>
                        Penerimaan peserta, pembagian kit, dan sesi perkenalan informal
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">09.00 - 12.00</div>
                    <div class="schedule-event">
                        <strong>Opening Ceremony & Keynote Speaker</strong><br>
                        Pembukaan resmi dan presentasi dari pembicara utama nasional & internasional
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">12.00 - 13.00</div>
                    <div class="schedule-event">
                        <strong>Lunch Break & Networking</strong><br>
                        Istirahat makan siang dan sesi networking untuk membangun koneksi
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">13.00 - 17.00</div>
                    <div class="schedule-event">
                        <strong>Paper Presentation & Workshop</strong><br>
                        Presentasi hasil penelitian dalam sesi paralel dan workshop interaktif
                    </div>
                </div>
                
                <div class="schedule-item">
                    <div class="schedule-time">17.00 - 17.30</div>
                    <div class="schedule-event">
                        <strong>Closing Ceremony</strong><br>
                        Penutupan acara dan pembagian sertifikat untuk seluruh peserta
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section id="daftar" class="section-padding bg-primary-custom text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-4" style="font-size: 2.5rem;">
                    Siap Bergabung di SNIA 2025?
                </h2>
                <p class="lead mb-4" style="font-size: 1.25rem; opacity: 0.9;">
                    Jangan lewatkan kesempatan untuk menjadi bagian dari acara ilmiah teknologi informasi 
                    terbesar di UNJANI. Daftarkan diri Anda sekarang dan jadilah bagian dari 
                    komunitas inovator teknologi masa depan.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="/register" class="btn btn-warning-custom btn-custom" 
                       style="font-size: 1.1rem; padding: 15px 40px;">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </a>
                    <a href="#harga" class="btn btn-outline-light-custom btn-custom"
                       style="font-size: 1.1rem; padding: 15px 40px;">
                        <i class="fas fa-info-circle me-2"></i>Info Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>