<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{asset('css//output.css')}}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
</head>
<body class="text-black font-poppins pt-10 pb-[50px]">

    <!-- HERO SECTION -->
    <div style="background-image: url('{{ asset('assets/background/Hero-Banner.png') }}');"
        id="hero-section"
        class="max-w-[1200px] mx-auto w-full flex flex-col gap-10 bg-center bg-no-repeat bg-cover rounded-[32px] overflow-hidden">

        <!-- Navigation Bar -->
@include('front.partials.nav')
        </nav>
    </div>

        <!-- Page Title -->
        <div class="flex flex-col gap-[10px] items-center mt-8"> {{-- Added mt-8 for spacing --}}
            <h2 class="font-bold text-[40px] leading-[60px] text-gray-800">Course Quiz</h2> {{-- Changed text-white to text-gray-800 --}}
            @if(isset($course)) {{-- Jika $course juga dikirim ke view --}}
                <p class="text-xl text-gray-700">{{ $course->name }}</p> {{-- Changed text-white to text-gray-700 --}}
            @endif
        </div>

        <!-- Quiz Content Section -->
        <div class="flex flex-col items-center justify-center px-[50px] md:px-[100px] relative z-10 mt-8"> {{-- Removed text-white, Added mt-8 for spacing --}}
            <div class="w-full bg-white/20 backdrop-blur-md p-8 rounded-2xl">
                <h3 class="font-bold text-2xl mb-6 text-center text-gray-800">{{ $quiz->title ?? 'Judul Quiz' }}</h3> {{-- Changed to text-gray-800 --}}

                @if(session('result'))
                    <div class="mb-6 p-4 rounded-lg {{ session('result.passed') ? 'bg-green-500/70' : 'bg-red-500/70' }} text-white text-center">
                        <h4 class="font-bold text-xl">Hasil Kuis Anda</h4>
                        <p>Skor: {{ session('result.score') }}%</p>
                        <p>{{ session('result.passed') ? 'Selamat, Anda Lulus!' : 'Maaf, Anda belum lulus. Coba lagi nanti.' }}</p>
                        <a href="{{ route('front.details', $quiz->course->slug) }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            Kembali ke Kursus
                        </a>
                    </div>
                @endif

                @if(isset($quiz) && $quiz->questions->count() > 0 && !session('result'))
                    @php
                        $duration = $quiz->duration ?? 600; // default 10 minutes
                    @endphp
                    <div id="timer" class="text-right font-semibold text-lg mb-4" data-duration="{{ $duration }}"></div>
                    <form id="quiz-form" action="{{ route('learning.quiz.submit', ['quiz' => $quiz->id]) }}" method="POST">
                        @csrf
                        <div class="flex">
                            <div class="w-1/4 pr-4 space-y-2" id="sidebar">
                                @foreach($quiz->questions as $index => $question)
                                    <button type="button" class="sidebar-btn w-full py-2 rounded bg-gray-200" data-index="{{ $index }}">{{ $index + 1 }}</button>
                                @endforeach
                            </div>
                            <div class="w-3/4" id="questions-container">
                                @foreach($quiz->questions as $index => $question)
                                    <div class="question-item {{ $index == 0 ? '' : 'hidden' }} mb-8 p-6 bg-white/30 rounded-lg shadow" data-index="{{ $index }}">
                                        <p class="block text-xl font-semibold mb-3 text-gray-800">{{ $index + 1 }}. {{ $question->question }}</p>
                                        @if($question->options && $question->options->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($question->options as $option)
                                                    <label class="block">
                                                        <input type="radio" name="option_{{ $question->id }}" value="{{ $option->id }}" data-question="{{ $question->id }}" class="sr-only option-input peer" required>
                                                        <div class="p-4 rounded-md border border-gray-400 peer-checked:bg-[#FF6129] peer-checked:border-[#FF6129] peer-checked:text-white text-gray-700 bg-white/70 hover:bg-white/90 hover:border-gray-500 cursor-pointer transition-all duration-200 ease-in-out">
                                                            <span class="font-medium">{{ $option->option_text ?? 'Opsi tidak valid' }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500">Tidak ada opsi jawaban untuk pertanyaan ini.</p>
                                        @endif
                                        <input type="hidden" name="answers[{{ $question->id }}]" id="answer-{{ $question->id }}">
                                    </div>
                                @endforeach

                                <div class="flex justify-between mt-4">
                                    <button type="button" id="prev-btn" class="px-4 py-2 rounded bg-gray-200">Previous</button>
                                    <button type="button" id="next-btn" class="px-4 py-2 rounded bg-gray-200">Next</button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-8 p-[20px_32px] bg-[#FF6129] text-white rounded-full text-center font-semibold transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980]">
                            Kirim Jawaban
                        </button>
                    </form>
                @elseif(!session('result'))
                    <p class="text-center text-lg text-gray-700">Belum ada pertanyaan untuk quiz ini atau quiz tidak ditemukan.</p> {{-- Changed to text-gray-700 --}}
                @endif
            </div>
        </div>

        <div class="flex justify-center absolute transform -translate-x-1/2 left-1/2 bottom-0 w-full">
            <img src="{{ asset('assets/background/alqowy.svg') }}" alt="background">
        </div>
    </div>

    <!-- JavaScript -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const questions = document.querySelectorAll('.question-item');
            const sidebarButtons = document.querySelectorAll('.sidebar-btn');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            let currentIndex = 0;

            function showQuestion(index) {
                questions.forEach((q, i) => {
                    q.classList.toggle('hidden', i !== index);
                });
                sidebarButtons.forEach((btn, i) => {
                    if (i === index) {
                        btn.classList.add('bg-[#FF6129]', 'text-white');
                    } else {
                        btn.classList.remove('bg-[#FF6129]', 'text-white');
                    }
                });
                currentIndex = index;
                prevBtn.disabled = index === 0;
                nextBtn.disabled = index === questions.length - 1;
            }

            prevBtn.addEventListener('click', () => {
                if (currentIndex > 0) showQuestion(currentIndex - 1);
            });
            nextBtn.addEventListener('click', () => {
                if (currentIndex < questions.length - 1) showQuestion(currentIndex + 1);
            });

            sidebarButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const idx = parseInt(btn.dataset.index, 10);
                    showQuestion(idx);
                });
            });

            // Preserve answers
            document.querySelectorAll('.option-input').forEach((input) => {
                input.addEventListener('change', () => {
                    const qid = input.dataset.question;
                    const hidden = document.getElementById('answer-' + qid);
                    hidden.value = input.value;
                });
            });

            showQuestion(0);

            // Timer
            const timerEl = document.getElementById('timer');
            let remaining = parseInt(timerEl.dataset.duration, 10);

            function updateTimer() {
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                timerEl.textContent = minutes + ':' + String(seconds).padStart(2, '0');
                if (remaining <= 0) {
                    clearInterval(interval);
                    document.getElementById('quiz-form').submit();
                }
                remaining--;
            }
            updateTimer();
            const interval = setInterval(updateTimer, 1000);
        });
    </script>

</body>
</html>