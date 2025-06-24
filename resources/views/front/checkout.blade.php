<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{asset(path: 'css//output.css')}}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
</head>
<body class="text-black font-poppins pt-10 pb-[50px]">

    <!-- HERO SECTION -->
    <div style="background-image: url('{{ asset('assets/background/Hero-Banner.png') }}');"
        id="hero-section"
        class="max-w-[1200px] mx-auto w-full h-min-h-screen flex flex-col gap-10 pb-[50px] bg-center bg-no-repeat bg-cover rounded-[32px] overflow-hidden relative">

        <!-- Navigation Bar -->
@include('front.partials.nav')
        </nav>

        <div class="flex flex-col gap-[10px] items-center">
            <div class="gradient-badge w-fit p-[8px_16px] rounded-full border border-[#FED6AD] flex items-center gap-[6px]">
                <div>
                    <img src="{{ asset('assets/icon/medal-star.svg') }}" alt="icon">
                </div>
                <p class="font-medium text-sm text-[#FF6129]">Invest In Yourself Today</p>
            </div>
            <h2 class="font-bold text-[40px] leading-[60px] text-white">Checkout Subscription</h2>
        </div>

        <div class="flex gap-10 px-[100px] relative z-10">
            <div class="w-[400px] flex shrink-0 flex-col bg-white rounded-2xl p-5 gap-4 h-fit">
                <p class="font-bold text-lg">Package</p>
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <div class="w-[50px] h-[50px] flex shrink-0 rounded-full overflow-hidden">
                            <img src="{{ asset('assets/icon/Web Development 1.svg') }}" class="w-full h-full object-cover" alt="photo">
                        </div>
                        <div class="flex flex-col gap-[2px]">
                            <p class="font-semibold">{{ $course->name }}</p>
                            <p class="text-sm text-[#6D7786]">All Time Access</p>
                        </div>
                    </div>
                    <p class="p-[4px_12px] rounded-full bg-[#FF6129] font-semibold text-xs text-white text-center">Popular</p>
                </div>
                <hr>
                <div class="flex flex-col gap-5">
                    @foreach(['Access all course materials', 'Unlock all course badges for jobs', 'Receive premium rewards'] as $feature)
                    <div class="flex gap-3">
                        <div class="w-6 h-6 flex shrink-0">
                            <img src="{{ asset('assets/icon/tick-circle.svg') }}" class="w-full h-full object-cover" alt="icon">
                        </div>
                        <p class="text-[#475466]">{{ $feature }}</p>
                    </div>
                    @endforeach
                </div>
                <p class="font-semibold text-[28px] leading-[42px]">Rp {{ number_format($course->price, 0, ',', '.')}}</p>
            </div>

            <form action="{{ route('front.checkout.store', $course) }}" method="POST" enctype="multipart/form-data" class= " w-full flex flex-col bg-white rounded-2xl p-5 gap-5">
            @csrf    
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <p class="font-bold text-lg">Send Payment</p>
                @php
                    $bankInfo = [
                        'Bank Name' => 'XXXXXXXXXX',
                        'Account Number' => 'XXXXXXXXXXXX',
                        'Account Name' => 'XXXXXXXXXX',
                        
                    ];
                @endphp

                <div class="flex flex-col gap-5">
                    @foreach($bankInfo as $label => $value)
                    <div class="flex items-center justify-between">
                        <div class="flex gap-3">
                            <div class="w-6 h-6 flex shrink-0">
                                <img src="{{ asset('assets/icon/tick-circle.svg') }}" class="w-full h-full object-cover" alt="icon">
                            </div>
                            <p class="text-[#475466]">{{ $label }}</p>
                        </div>
                        <p class="font-semibold">{{ $value }}</p>
                        <input type="hidden" name="{{ Str::camel($label) }}" value="{{ $value }}">
                    </div>
                    @endforeach
                </div>

                <hr>
                <p class="font-bold text-lg">Confirm Your Payment</p>
                <div class="relative">
                    <button type="button" class="p-4 rounded-full flex gap-3 w-full ring-1 ring-black transition-all duration-300 hover:ring-2 hover:ring-[#FF6129]" onclick="document.getElementById('file').click()">
                        <div class="w-6 h-6 flex shrink-0">
                            <img src="{{ asset('assets/icon/note-add.svg') }}" alt="icon">
                        </div>
                        <p id="fileLabel">Add a file attachment</p>
                    </button>
                    <input id="file" type="file" name="proof" class="hidden" onchange="updateFileName(this)">
                </div>
                <button class="p-[20px_32px] bg-[#FF6129] text-white rounded-full text-center font-semibold transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980]">I've Made The Payment</button>
            </form>
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
        function updateFileName(input) {
            const fileLabel = document.getElementById('fileLabel');
            if (input.files.length > 0) {
                fileLabel.textContent = input.files[0].name;
            } else {
                fileLabel.textContent = 'Add a file attachment';
            }
        }
    </script>

</body>
</html>