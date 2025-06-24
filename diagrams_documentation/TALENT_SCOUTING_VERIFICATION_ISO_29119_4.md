# Dokumen Verifikasi Sistem Talent Scouting
## Kepatuhan ISO/IEC/IEEE 29119-4:2015

**Informasi Dokumen:**
- **Sistem:** WebPelatihan Talent Scouting Platform
- **Versi:** 1.0
- **Tanggal:** 19 Juni 2025
- **Standar:** ISO/IEC/IEEE 29119-4:2015 (Software and systems engineering — Software testing — Part 4: Test techniques)
- **Ruang Lingkup Verifikasi:** Komponen Talent Scouting Flow

---

## 1. Ringkasan Eksekutif

Dokumen ini menyediakan bukti verifikasi untuk Sistem Talent Scouting yang diimplementasikan dalam platform WebPelatihan, menunjukkan kepatuhan terhadap test techniques dan standar verifikasi ISO/IEC/IEEE 29119-4:2015. Verifikasi mencakup alur kerja talent lengkap dari talent opt-in hingga project assignment dan penyelesaian.

---

## 2. Ikhtisar Arsitektur Sistem

### 2.1 Komponen Inti yang Diverifikasi
- **Talent Opt-In System** (`ProfileController`, `TalentScouting Service`)
- **Talent Discovery System** (`RecruiterController`, `TalentDiscovery Service`) 
- **Talent Request Workflow** (`TalentRequest Model`, notification system)
- **Project Management System** (`ProjectController`, assignment management)
- **Admin Management System** (`TalentAdminController`, request processing)

### 2.2 Technology Stack
- **Framework:** Laravel 10.x (PHP 8.2)
- **Database:** MySQL dengan Eloquent ORM
- **Frontend:** Blade Templates dengan Tailwind CSS
- **Architecture:** MVC Pattern dengan Service Layer
- **Authentication:** Spatie Laravel Permission (Role-based)

---

## 3. Metodologi Pengujian (Kepatuhan ISO 29119-4)

### 3.1 Test Techniques yang Diterapkan

Verifikasi ini menggunakan **metode black-box testing** yang berfokus pada pengujian fungsi sistem dari perspektif pengguna, tanpa melihat kode pemrograman internal.

#### 3.1.1 Input Category Testing (Equivalence Partitioning)
- **Pengujian Tipe Pengguna:** Menguji kategori pengguna yang berbeda (talent, recruiter, admin)
- **Pengujian Input Data:** Skenario entri data valid dan tidak valid untuk form
- **Pengujian Kategori Status:** Status request yang berbeda dan perilaku yang diharapkan

#### 3.1.2 Limit Value Testing (Boundary Value Analysis)  
- **Pengujian Rentang Tanggal:** Tanggal mulai/selesai proyek, batas timeline
- **Pengujian Rentang Budget:** Nilai budget minimum/maksimum dan edge cases
- **Pengujian Panjang Input:** Batas karakter field dan pembatasan

#### 3.1.3 Business Rule Testing (Decision Table Testing)
- **Pengujian Logika Workflow:** Titik keputusan persetujuan talent request
- **Pengujian Permission Akses:** Siapa yang dapat mengakses fitur apa
- **Pengujian Proses Bisnis:** Aturan bisnis kondisional yang kompleks

#### 3.1.4 Process Flow Testing (State Transition Testing)
- **Proses Talent Request:** Workflow lengkap dari awal hingga selesai
- **Perubahan Status Pengguna:** Proses aktivasi/deaktivasi talent
- **Lifecycle Proyek:** Perubahan status dan dependensi

#### 3.1.5 Complete User Journey Testing (Use Case Testing)
- **Pengujian Proses End-to-End:** Verifikasi workflow lengkap
- **Pengujian Interaksi Multi-User:** Pengujian antara tipe pengguna yang berbeda
- **Pengujian Skenario Real-World:** Pola penggunaan bisnis aktual

---

## 4. Hasil Pengujian Sistem (Alur Workflow End-to-End)

### 4.1 Fase 1: Talent Opt-In dan Pendaftaran

#### **Test Case TC-001: Talent Profile Opt-In Process**
```
Test ID: TC-001
Fitur Sistem: Talent Opt-In dan Profile Setup
Metode Pengujian: Input Category Testing
Prioritas: Tinggi

Kondisi Awal:
- User sudah memiliki akun di sistem WebPelatihan
- User belum terdaftar sebagai talent
- User ingin bergabung sebagai talent

Langkah Pengujian:
1. User login ke sistem dan masuk ke halaman profile
2. User mengaktifkan opsi "Available for Talent Scouting"
3. User mengisi data talent (bio, skills, hourly rate, availability)
4. Sistem memvalidasi kelengkapan data talent
5. User menyimpan perubahan profile talent

Hasil yang Diharapkan:
- User berhasil opt-in sebagai talent
- Status "available_for_scouting" diset ke true
- Data talent tersimpan dalam database
- User mendapat role "talent" dalam sistem
- Talent muncul dalam talent pool untuk discovery

Hasil Pengujian:
✓ Proses opt-in talent berhasil
✓ Data talent tersimpan dengan benar
✓ Role assignment berfungsi
✓ Talent masuk ke discovery pool
```

### 4.2 Fase 2: Project Creation dan Talent Request

#### **Test Case TC-002: Project Creation dan Talent Requirement**
```
Test ID: TC-002
Fitur Sistem: Project Management dan Talent Requirement Setup
Metode Pengujian: Use Case Testing
Prioritas: Tinggi

Kondisi Awal:
- Recruiter sudah login ke sistem
- Ada talent yang tersedia di talent pool
- Recruiter memiliki kebutuhan proyek baru

Langkah Pengujian:
1. Recruiter membuat proyek baru dengan detail lengkap
2. Recruiter menentukan talent requirements (skills, jumlah, timeline)
3. Sistem menyimpan proyek dengan status "planning"
4. Recruiter dapat melihat proyek dalam dashboard
5. Sistem siap untuk proses talent request

Hasil yang Diharapkan:
- Proyek berhasil dibuat dalam sistem
- Talent requirements terdefinisi dengan jelas
- Status proyek awal adalah "planning"
- Proyek muncul di dashboard recruiter
- Sistem siap untuk talent matching

Hasil Pengujian:
✓ Pembuatan proyek berhasil
✓ Requirements tersimpan dengan benar
✓ Status tracking berfungsi
✓ Dashboard integration aktif
```

#### **Test Case TC-003: Talent Request Creation (Per-Project)**
```
Test ID: TC-003
Fitur Sistem: Talent Request Management
Metode Pengujian: State Transition Testing
Prioritas: Kritis

Kondisi Awal:
- Proyek sudah dibuat dengan talent requirements
- Ada talent yang sesuai di talent pool
- Recruiter siap membuat talent request

Langkah Pengujian:
1. Recruiter membuka halaman talent discovery
2. Recruiter mencari talent berdasarkan kriteria proyek
3. Recruiter memilih talent yang sesuai
4. Recruiter membuat talent request untuk proyek tertentu
5. Sistem menyimpan request dengan status "pending"

Hasil yang Diharapkan:
- Talent request berhasil dibuat
- Request terkait dengan proyek dan talent spesifik
- Status awal adalah "pending"
- Notifikasi dikirim ke talent dan admin
- Request muncul di dashboard yang relevan

Hasil Pengujian:
✓ Request creation workflow berhasil
✓ Project-talent association benar
✓ Status management berfungsi
✓ Notification system aktif
```

### 4.3 Fase 3: Talent Admin Actions dan Approval Process

#### **Test Case TC-004: Talent Admin Review dan Decision Making**
```
Test ID: TC-004
Fitur Sistem: Talent Admin Management Dashboard
Metode Pengujian: Decision Table Testing
Prioritas: Kritis

Kondisi Awal:
- Talent request sudah dibuat oleh recruiter
- Talent Admin login ke sistem
- Request menunggu review dari admin

Langkah Pengujian:
1. Talent Admin membuka dashboard management
2. Admin melihat daftar pending talent requests
3. Admin review detail request dan talent profile
4. Admin memutuskan approve/reject request
5. Sistem memproses keputusan admin

Kondisi Pengujian:
| Talent Quality | Project Match | Admin Decision | Expected Result |
|---------------|---------------|----------------|-----------------|
| High          | Perfect       | Approve        | Status: Approved |
| Medium        | Good          | Approve        | Status: Approved |
| Low           | Poor          | Reject         | Status: Rejected |
| High          | No Match      | Reject         | Status: Rejected |

Hasil yang Diharapkan:
- Admin dapat melihat semua pending requests
- Detail talent dan project tersedia untuk review
- Keputusan admin tersimpan dengan alasan
- Status request berubah sesuai keputusan
- Notifikasi dikirim ke recruiter dan talent

Hasil Pengujian:
✓ Dashboard admin berfungsi dengan baik
✓ Review process dapat dilakukan
✓ Decision making workflow aktif
✓ Status update berjalan otomatis
```

#### **Test Case TC-005: Talent Acceptance Process**
```
Test ID: TC-005
Fitur Sistem: Talent Response Management
Metode Pengujian: State Transition Testing
Prioritas: Tinggi

Kondisi Awal:
- Admin telah approve talent request
- Talent menerima notifikasi untuk respond
- Request status adalah "admin_approved"

Langkah Pengujian:
1. Talent login dan melihat notifikasi request
2. Talent review detail proyek dan terms
3. Talent memutuskan accept/decline request
4. Sistem memproses response talent
5. Status request diupdate berdasarkan response

Hasil yang Diharapkan:
- Talent dapat melihat detail request yang approved
- Talent dapat accept atau decline request
- Jika accept: status menjadi "accepted", proses lanjut
- Jika decline: status menjadi "declined", request selesai
- Notifikasi dikirim ke admin dan recruiter

Hasil Pengujian:
✓ Talent notification system bekerja
✓ Response mechanism berfungsi
✓ Status transition handling akurat
✓ Cross-notification system aktif
```

### 4.4 Fase 4: Project Assignment dan Onboarding

#### **Test Case TC-006: Automatic Project Assignment Creation**
```
Test ID: TC-006
Fitur Sistem: Project Assignment Management
Metode Pengujian: Use Case Testing
Prioritas: Tinggi

Kondisi Awal:
- Talent request telah di-accept oleh semua pihak
- Proyek masih dalam status "planning" atau "active"
- Budget allocation sudah didefinisikan

Langkah Pengujian:
1. Sistem mendeteksi talent request dengan status "accepted"
2. Admin melakukan onboarding process untuk talent
3. Sistem otomatis membuat project assignment
4. Budget dialokasikan dari request ke assignment
5. Status proyek diupdate jika semua posisi terisi

Hasil yang Diharapkan:
- Project assignment otomatis dibuat
- Budget allocation berjalan dengan benar
- Talent status berubah menjadi "onboarded"
- Project status diupdate sesuai staffing level
- Timeline tracking dimulai

Hasil Pengujian:
✓ Auto-assignment creation berfungsi
✓ Budget management akurat
✓ Status progression benar
✓ Timeline tracking aktif
```

### 4.5 Fase 5: Project Execution dan Monitoring

#### **Test Case TC-007: Project Progress Monitoring**
```
Test ID: TC-007
Fitur Sistem: Project Execution Tracking
Metode Pengujian: Boundary Value Analysis
Prioritas: Sedang

Kondisi Awal:
- Talent sudah onboarded ke proyek
- Project assignment sudah aktif
- Project dalam status "active"

Langkah Pengujian:
1. Sistem melacak progress proyek dan talent performance
2. Admin dan recruiter monitor dashboard proyek
3. Sistem menampilkan talent information yang akurat
4. Performance metrics diupdate secara berkala
5. Timeline dan milestone tracking aktif

Hasil yang Diharapkan:
- Progress tracking berfungsi dengan akurat
- Talent information ditampilkan dengan benar (nama talent, bukan nama proyek)
- Performance metrics dapat diakses real-time
- Timeline milestone dimonitor dengan baik
- Dashboard memberikan insight yang akurat

Hasil Pengujian:
✓ Project monitoring system berfungsi
✓ Talent name display benar (bukan project name)
✓ Performance tracking akurat
✓ Timeline monitoring aktif
```

### 4.6 Fase 6: Red Flag Management System

#### **Test Case TC-008: Project-Level Red Flag Detection dan Management**
```
Test ID: TC-008
Fitur Sistem: Red Flag Management (Project-Level)
Metode Pengujian: Decision Table Testing
Prioritas: Kritis

Kondisi Awal:
- Proyek sedang berjalan dengan talent assignments
- Ada indikasi masalah dalam project execution
- Admin perlu menandai red flag untuk proyek/talent tertentu

Langkah Pengujian:
1. Admin mendeteksi issue dalam proyek (performance, behavior, dll)
2. Admin mengakses red flag management untuk proyek
3. Admin menambahkan red flag dengan kategori dan alasan
4. Sistem menyimpan red flag data pada level project/request
5. Red flag information dapat diakses sesuai permission

Kondisi Pengujian - Red Flag Access Control:
| User Role | Project Scope | Red Flag Action | Expected Result |
|-----------|---------------|-----------------|-----------------|
| Admin     | Any Project   | Create/View/Edit| Full Access     |
| Recruiter | Own Project   | View Only       | Read Access     |
| Recruiter | Other Project | View Only       | Read Access     |
| Talent    | Any Project   | No Access       | Restricted      |

Hasil yang Diharapkan:
- Red flag dapat ditambahkan pada level proyek/request
- Access control bekerja sesuai role pengguna
- Admin memiliki full access untuk manage red flags
- Recruiter dapat melihat red flags di semua proyek
- Talent tidak dapat mengakses red flag information
- Red flag history tercatat untuk audit trail

Hasil Pengujian:
✓ Red flag creation system berfungsi
✓ Project-level red flag association benar
✓ Role-based access control aktif
✓ Cross-recruiter visibility terkonfirmasi
✓ Audit trail lengkap dan akurat
```

#### **Test Case TC-009: Red Flag Impact pada Future Requests**
```
Test ID: TC-009
Fitur Sistem: Red Flag History dan Future Request Impact
Metode Pengujian: Use Case Testing
Prioritas: Tinggi

Kondisi Awal:
- Talent memiliki red flag history dari proyek sebelumnya
- Ada talent request baru untuk talent yang sama
- Admin perlu mempertimbangkan red flag history

Langkah Pengujian:
1. Recruiter membuat request untuk talent dengan red flag history
2. Sistem menampilkan red flag warning pada talent profile
3. Admin review red flag history saat approve/reject
4. Keputusan dibuat berdasarkan red flag severity
5. New request decision recorded dengan referensi ke history

Hasil yang Diharapkan:
- Red flag history visible saat talent selection
- Warning indicators muncul untuk flagged talents
- Admin dapat membuat informed decision
- Red flag tidak otomatis block talent (admin discretion)
- Decision rationale tercatat untuk future reference

Hasil Pengujian:
✓ Red flag history display berfungsi
✓ Warning system aktif
✓ Admin decision support tersedia
✓ Decision tracking akurat
```

---

## 5. Pengujian Performa dan Usability Sistem

### 5.1 Pengujian Performa (Perspektif Pengguna)
```
Fitur Sistem: Talent Discovery Performance
Metode Pengujian: Load and Stress Testing
Pendekatan Pengukuran: User Response Time

Skenario Pengujian:
- Beban normal: 20 pengguna bersamaan mencari talent
- Beban berat: 100+ pengguna bersamaan dengan filter
- Query kompleks: Pencarian talent multi-kriteria
- Loading dashboard: Multiple data widgets

Hasil Pengujian:
✓ Response time memenuhi ekspektasi pengguna
✓ Sistem stabil di bawah beban berat
✓ Query database dioptimalkan untuk performa
✓ User interface tetap responsif
```

### 5.2 Pengujian Usability (User Experience)
```
Fitur Sistem: User Interface Usability
Metode Pengujian: Task-Based User Testing
Pendekatan Pengukuran: Task Completion dan Satisfaction

Skenario Pengujian:
- Pengguna baru menyelesaikan pendaftaran talent
- Recruiter mencari skill spesifik
- Admin mengelola talent request dan persetujuan
- Navigasi sistem di berbagai role pengguna

Hasil Pengujian:
✓ Standar user experience berhasil dipenuhi
✓ Alur navigasi intuitif dan efisien
✓ Mekanisme pemulihan error bekerja dengan baik
✓ Kompatibilitas cross-device dikonfirmasi
```

### 5.3 Pengujian Keamanan (Perspektif Eksternal)
```
Fitur Sistem: Access Control dan Security
Metode Pengujian: External Security Assessment
Pendekatan Pengukuran: Boundary dan Permission Testing

Skenario Pengujian:
- Autentikasi pengguna tanpa akses sistem
- Verifikasi boundary authorization
- Validasi input dari user interface
- Manajemen session dan timeout testing

Hasil Pengujian:
✓ Kontrol keamanan diverifikasi dari perspektif pengguna
✓ Pembatasan akses bekerja sesuai yang diinginkan
✓ Validasi input mencegah masalah keamanan
✓ Manajemen session berfungsi dengan baik
```

---

## 6. Ringkasan Hasil Pengujian

| Persyaratan Sistem | Test Cases | Status Verifikasi | Sumber Bukti |
|-------------------|------------|-------------------|---------------|
| Talent Opt-In Process | TC-001 | ✓ TERVERIFIKASI | User Profile System |
| Project & Talent Request Creation | TC-002, TC-003 | ✓ TERVERIFIKASI | Project Request System |
| Admin Review & Approval Process | TC-004, TC-005 | ✓ TERVERIFIKASI | Admin Management System |
| Project Assignment & Onboarding | TC-006 | ✓ TERVERIFIKASI | Assignment Management System |
| Project Execution Monitoring | TC-007 | ✓ TERVERIFIKASI | Project Tracking System |
| Red Flag Management (Project-Level) | TC-008, TC-009 | ✓ TERVERIFIKASI | Red Flag System |
| Database Integrity | Semua TC | ✓ TERVERIFIKASI | Data Management System |
| End-to-End Workflow | TC-001 sampai TC-009 | ✓ TERVERIFIKASI | Complete System |

---

## 7. Traceability Persyaratan

### 7.1 Pemetaan Persyaratan ke Test Case (End-to-End Workflow)
- **REQ-001** (Talent Opt-In) → TC-001
- **REQ-002** (Project Creation & Talent Request) → TC-002, TC-003
- **REQ-003** (Admin Review & Approval) → TC-004, TC-005
- **REQ-004** (Project Assignment & Onboarding) → TC-006
- **REQ-005** (Project Execution Monitoring) → TC-007
- **REQ-006** (Red Flag Management) → TC-008, TC-009
- **REQ-007** (Performance & Security) → Pengujian performa dan keamanan

### 7.2 Alur Workflow yang Diverifikasi
```
Alur End-to-End Talent Scouting System:
1. Talent Opt-In (TC-001) → User menjadi talent
2. Project Creation (TC-002) → Recruiter buat proyek  
3. Talent Request (TC-003) → Request talent untuk proyek
4. Admin Review (TC-004) → Admin review dan putuskan
5. Talent Response (TC-005) → Talent terima/tolak request
6. Auto Assignment (TC-006) → Sistem buat assignment otomatis
7. Project Monitoring (TC-007) → Monitor progress dan performance
8. Red Flag Management (TC-008, TC-009) → Kelola masalah dan history

Cakupan Workflow: 100% dari proses bisnis utama
```

---

## 8. Masalah yang Diidentifikasi dan Diselesaikan

### 8.1 Masalah Kritis yang Ditemukan dan Diperbaiki (Berdasarkan End-to-End Testing)

1. **Masalah:** Nama talent menampilkan nama proyek alih-alih nama sebenarnya (TC-007)
   - **Fase:** Project Execution Monitoring
   - **Dampak:** Informasi yang salah ditampilkan dalam project monitoring
   - **Solusi:** Memperbaiki relationship loading untuk memastikan data talent yang tepat
   - **Verifikasi:** TC-007 mengkonfirmasi tampilan yang benar bekerja

2. **Masalah:** Dashboard statistics menampilkan hitungan yang salah (TC-004)
   - **Fase:** Admin Review Process
   - **Dampak:** Metrik yang tidak akurat untuk pengambilan keputusan admin
   - **Solusi:** Memperbarui query database untuk menyertakan filtering yang tepat
   - **Verifikasi:** Akurasi statistik diverifikasi melalui pengujian TC-004

3. **Masalah:** Red flag tidak tersimpan pada level project/request (TC-008)
   - **Fase:** Red Flag Management
   - **Dampak:** Red flag tidak dapat dikaitkan dengan proyek spesifik
   - **Solusi:** Implementasi project-level red flag dengan proper relationship
   - **Verifikasi:** TC-008 dan TC-009 mengkonfirmasi red flag system bekerja

### 8.2 Penilaian Risiko (Berdasarkan Workflow End-to-End)
- **Risiko Rendah:** Masalah UI dalam monitoring dashboard (diselesaikan TC-007)
- **Risiko Sedang:** Performance saat banyak concurrent talent requests (ditangani TC-003)
- **Risiko Tinggi:** Data integrity selama proses approval yang simultan (dilindungi TC-004, TC-005)
- **Risiko Kritis:** Red flag history tidak tersedia saat decision making (diselesaikan TC-009)

---

## 9. Hasil Verifikasi Akhir

### 9.1 Ringkasan Kepatuhan
Sistem Talent Scouting menunjukkan **KEPATUHAN PENUH** terhadap standar verifikasi ISO/IEC/IEEE 29119-4:2015 menggunakan pendekatan pengujian yang berfokus pada bisnis:

✓ **Penerapan Metode Pengujian:** Semua teknik pengujian berorientasi bisnis utama diterapkan secara sistematis
✓ **Cakupan Fungsional:** Verifikasi lengkap dari fungsionalitas yang menghadap pengguna
✓ **Bukti Verifikasi:** Dokumentasi komprehensif dan traceability dipertahankan
✓ **Quality Assurance:** Proses verifikasi sistematis dengan skenario pengujian yang dapat diulang

### 9.2 Penilaian Kesiapan Sistem (End-to-End Workflow Validation)
Sistem diverifikasi sebagai **SIAP UNTUK PRODUKSI** dengan workflow lengkap:

**✓ Fase 1 - Talent Acquisition:** 
- Talent opt-in process berfungsi sempurna
- Profile management dan role assignment aktif

**✓ Fase 2 - Project & Request Management:**
- Project creation dan talent request workflow terintegrasi
- Status tracking dan notification system operasional

**✓ Fase 3 - Administrative Process:**
- Admin review dan approval mechanism robust
- Dual acceptance workflow (admin + talent) berfungsi

**✓ Fase 4 - Assignment & Execution:**
- Automatic project assignment creation reliable
- Budget allocation dan timeline tracking akurat

**✓ Fase 5 - Monitoring & Quality Control:**
- Project progress monitoring dan performance tracking aktif
- Talent information display akurat dan real-time

**✓ Fase 6 - Risk Management:**
- Project-level red flag system fully implemented
- Red flag history dan impact analysis tersedia
- Cross-recruiter visibility dan admin control verified

### 9.3 Rekomendasi Perbaikan Berkelanjutan (Workflow-Specific)
Untuk verifikasi dan perbaikan sistem berkelanjutan:

**Talent Pipeline:**
- Monitor conversion rate dari opt-in ke active assignments
- Track talent satisfaction scores sepanjang workflow

**Request & Approval Process:**
- Automated testing untuk approval workflow complexity
- Performance monitoring untuk concurrent request processing

**Red Flag Management:**
- Regular audit red flag decision impact pada future requests
- Machine learning integration untuk predictive red flag detection
- Automated reporting untuk red flag trends dan patterns

---

## 10. Lampiran Dokumentasi

### Lampiran A: Set Data Pengujian
[Spesifikasi data pengujian skenario bisnis yang digunakan dalam verifikasi]

### Lampiran B: Spesifikasi Environment
[Konfigurasi environment pengujian dan detail setup]

### Lampiran C: Konfigurasi Tool
[Setup testing tools, frameworks, dan automasi]

### Lampiran D: Prosedur Verifikasi
[Prosedur verifikasi langkah demi langkah dan checklist]

---

**Persetujuan Dokumen:**

| Peran | Nama | Tanda Tangan | Tanggal |
|-------|------|--------------|---------|
| Project Manager | [Nama] | [Tanda Tangan] | 19 Juni 2025 |
| Quality Assurance | [Nama] | [Tanda Tangan] | 19 Juni 2025 |
| System Analyst | [Nama] | [Tanda Tangan] | 19 Juni 2025 |

---

*Dokumen ini mengikuti standar ISO/IEC/IEEE 29119-4:2015 untuk verifikasi software menggunakan metodologi pengujian yang berfokus pada bisnis dan sesuai untuk aplikasi teknik industri.*
