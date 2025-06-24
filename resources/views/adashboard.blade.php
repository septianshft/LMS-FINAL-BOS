@extends('layouts.mainTemplate') {{-- Pastikan path ini benar, jika mainTemplate ada di resources/views/layouts/mainTemplate.blade.php --}}

@section('container')
    <h1>Dashboard</h1>
    <span class="small">Pemantauan Data</span>
    <hr>


    {{-- Konten Utama --}}
    <div class="bg-body-secondary rounded-4 mb-4">
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row  align-items-center g-5 py-5">

                <div class="text-center">
                    <h3 class=" text-primary fw-bold d-block">Informasi Sistem</h3>
                </div>
                {{-- Chart Section --}}
                <div class="col-12 p-4 border border-primary rounded-4">
                    <div class="row">
                        {{-- Materi chart (Contoh Penyesuaian) --}}
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Jumlah Kursus Dibuat (dalam 7 Hari Terakhir)</h6> {{-- Contoh perubahan judul --}}
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="courseChart" style="display: block; width: 558px; height: 320px;" class="chartjs-render-monitor w-100 "></canvas> {{-- ID diubah --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tugas Chart (Contoh Penyesuaian) --}}
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Jumlah Kuis Dibuat (dalam 7 Hari Terakhir)</h6> {{-- Contoh perubahan judul --}}
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="quizChart" style="display: block; width: 558px; height: 320px;" class="chartjs-render-monitor w-100 "></canvas> {{-- ID diubah --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Ujian Chart (Sudah menggunakan FinalQuiz, mungkin hanya perlu penyesuaian nama variabel) --}}
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Jumlah Ujian Dibuat (dalam 7 Hari Terakhir)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="finalQuizChart" style="display: block; width: 558px; height: 320px;" class="chartjs-render-monitor w-100 "></canvas> {{-- ID diubah --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Total Siswa --}}
                <div class="col-lg-3 col-6">
                    <div class="p-4 border border-primary rounded-2">
                        <div class="text-center">
                            <img src="{{ url('/assets/icon/user-graduate.svg') }}" alt="" class="img-fluid w-50" srcset="" width="100px" style="max-width: 100px;"> {{-- Path icon mungkin perlu disesuaikan --}}
                            <br>
                            <Strong class="fs-5 text-primary d-block">Total Trainee</Strong>
                            <span class="small d-block">Total data Trainee</span>
                            {{-- Ganti dengan variabel yang sesuai dari controller, contoh: --}}
                            <h1 class="display-3 text-primary fw-bold d-block">{{ $user->count()}}</h1> 
                        </div>
                    </div>
                </div>
                
                {{-- Total Pengajar --}}
                <div class="col-lg-3  col-6">
                    <div class="p-4 border border-primary rounded-2">
                        <div class="text-center">
                            <img src="{{ url('/assets/icon/chalkboard-user.svg') }}" alt="" class="img-fluid w-50" srcset="" width="100px" style="max-width: 100px;"> {{-- Path icon mungkin perlu disesuaikan --}}
                            <br>
                            <Strong class="fs-5 text-primary d-block">Total Pengajar</Strong>
                            <span class="small d-block">User pengajar</span>
                             {{-- Ganti dengan variabel yang sesuai dari controller, contoh: --}}
                            <h1 class="display-3 text-primary fw-bold d-block">{{ $totalTrainers ?? 'N/A' }}</h1>
                        </div>
                    </div>
                </div>

                {{-- Total Kelas/Kursus --}}
                <div class="col-lg-3 col-6">
                    <div class="p-4 border border-primary rounded-2">
                        <div class="text-center">
                            <img src="{{ url('/assets/icon/book-open-reader.svg') }}" alt="" class="img-fluid w-50" srcset="" width="100px" style="max-width: 100px;"> {{-- Path icon mungkin perlu disesuaikan --}}
                            <br>
                            <Strong class="fs-5 text-primary d-block">Total Kursus</Strong>
                            <span class="small d-block">Semua kursus</span>
                            {{-- Ganti dengan variabel yang sesuai dari controller, contoh: --}}
                            <h1 class="display-3 text-primary fw-bold d-block">{{ $totalCourses ?? 'N/A' }}</h1>
                        </div>
                    </div>
                </div>

                {{-- Total Kategori --}}
                <div class="col-lg-3 col-6">
                    <div class="p-4 border border-primary rounded-2">
                        <div class="text-center">
                            <img src="{{ url('/assets/icon/layer-group.svg') }}" alt="" class="img-fluid w-50" srcset="" width="100px" style="max-width: 100px;"> {{-- Path icon mungkin perlu disesuaikan --}}
                            <br>
                            <Strong class="fs-5 text-primary d-block">Total Kategori</Strong>
                            <span class="small d-block">Kategori yang terdaftar</span>
                            {{-- Ganti dengan variabel yang sesuai dari controller, contoh: --}}
                            <h1 class="display-3 text-primary fw-bold d-block">{{ $totalCategories ?? 'N/A' }}</h1>
                        </div>
                    </div>
                </div>

                {{-- Total Ujian --}}
                <div class="col-lg-3 col-6">
                    <div class="p-4 border border-primary rounded-2">
                        <div class="text-center">
                            <img src="{{ url('/assets/icon/file-signature.svg') }}" alt="" class="img-fluid w-50" srcset="" width="100px" style="max-width: 100px;"> {{-- Path icon mungkin perlu disesuaikan --}}
                            <br>
                            <Strong class="fs-5 text-primary d-block">Total Ujian</Strong>
                            <span class="small d-block">Ujian yang terdaftar</span>
                            {{-- Ganti dengan variabel yang sesuai dari controller, contoh: --}}
                            <h1 class="display-3 text-primary fw-bold d-block">{{ $totalQuizzes ?? 'N/A' }}</h1>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    
    {{-- Chart Kursus (Contoh Penyesuaian) --}}
    <script>
        var ctxCourse = document.getElementById('courseChart').getContext('2d');
        // Pastikan variabel $coursesLast7Days dikirim dari controller
        var courseData = <?php echo json_encode($coursesLast7Days ?? []); ?>; 
        var courseCount = [];
        var labelDatesCourse = [];

        for (var i = 0; i < 7; i++) {
            var date = moment().subtract(i, 'days').format('MMM-DD');
            labelDatesCourse.push(date);
            var count = courseData.filter(function(item) {
                return moment(item.created_at).format('MMM-DD') === date;
            }).length;
            courseCount.push(count);
        }

        new Chart(ctxCourse, {
            type: 'line',
            data: {
                labels: labelDatesCourse.reverse(),
                datasets: [{
                    label: 'Kursus dibuat',
                    data: courseCount.reverse(),
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                // ... opsi chart lainnya
                scales: {
                    y: { 
                        min: 0,
                        // max: 10, // Sesuaikan jika perlu
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    {{-- Chart Kuis (Contoh Penyesuaian) --}}
    <script>
        var ctxQuiz = document.getElementById('quizChart').getContext('2d');
        // Pastikan variabel $quizzesLast7Days dikirim dari controller
        var quizData = <?php echo json_encode($quizzesLast7Days ?? []); ?>; 
        var quizCount = [];
        var labelDatesQuiz = [];

        for (var i = 0; i < 7; i++) {
            var date = moment().subtract(i, 'days').format('MMM-DD');
            labelDatesQuiz.push(date);
            var count = quizData.filter(function(item) {
                return moment(item.created_at).format('MMM-DD') === date;
            }).length;
            quizCount.push(count);
        }

        new Chart(ctxQuiz, {
            type: 'line',
            data: {
                labels: labelDatesQuiz.reverse(),
                datasets: [{
                    label: 'Kuis dibuat',
                    data: quizCount.reverse(),
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                // ... opsi chart lainnya
                scales: {
                    y: { 
                        min: 0,
                        // max: 10, // Sesuaikan jika perlu
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    {{-- Chart Ujian (FinalQuiz) --}}
    <script>
        var ctxFinalQuiz = document.getElementById('finalQuizChart').getContext('2d');
        // Pastikan variabel $finalQuizzesLast7Days dikirim dari controller
        var finalQuizData = <?php echo json_encode($finalQuizzesLast7Days ?? []); ?>; 
        var finalQuizCount = [];
        var labelDatesFinalQuiz = [];

        for (var i = 0; i < 7; i++) {
            var date = moment().subtract(i, 'days').format('MMM-DD');
            labelDatesFinalQuiz.push(date);
            var count = finalQuizData.filter(function(item) {
                return moment(item.created_at).format('MMM-DD') === date;
            }).length;
            finalQuizCount.push(count);
        }

        new Chart(ctxFinalQuiz, {
            type: 'line',
            data: {
                labels: labelDatesFinalQuiz.reverse(),
                datasets: [{
                    label: 'Ujian dibuat',
                    data: finalQuizCount.reverse(),
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                // ... opsi chart lainnya
                scales: {
                    y: { 
                        min: 0,
                        // max: 10, // Sesuaikan jika perlu
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
