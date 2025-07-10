<div class="flex flex-wrap" id="ajax-course-wrapper">
    @forelse($courses as $course)
    <div class="course-card w-1/3 px-3 pb-[70px] mt-[2px]">
        <div class="flex flex-col rounded-t-[12px] rounded-b-[24px] gap-[32px] bg-white w-full pb-[10px] overflow-hidden transition-all duration-300 hover:ring-2 hover:ring-[#FF6129]">
            <a href="{{ route('front.details', $course->slug) }}" class="thumbnail w-full h-[200px] shrink-0 rounded-[10px] overflow-hidden">
            <img src="{{ asset(path: 'storage/' . $course->thumbnail) }}" class="rounded-2xl object-cover w-[120px] h-[90px]" alt="thumbnail">
            </a>
            <div class="flex flex-col px-4 gap-[10px]">
                <a href="{{ route('front.details', $course->slug) }}" class="font-semibold text-lg line-clamp-2 hover:line-clamp-none min-h-[56px]">{{ $course->name }}</a>
                <div class="font-semibold text-lg">
                    {{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'FREE' }}
                </div>
                <p class="text-sm text-[#6D7786]">{{ $course->mode->name ?? '' }} - {{ $course->level->name ?? '' }}</p>
                @if($course->enrollment_start || $course->enrollment_end)
                <p class="text-xs text-gray-500">
                    Enrollment:
                    {{ $course->enrollment_start ? $course->enrollment_start->format('d M Y') : '-' }}
                    -
                    {{ $course->enrollment_end ? $course->enrollment_end->format('d M Y') : '-' }}
                </p>
                @endif

                <form action="{{ route('cart.store', $course->slug) }}" method="POST">
                    @csrf
                    <button class="mt-1 px-3 py-1 bg-[#FF6129] text-white rounded">Add to Cart</button>
                </form>

                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-[2px]">
                        @for ($i = 0; $i < 5; $i++)
                            <img src="assets/icon/star.svg" alt="star">
                        @endfor
                    </div>
                    <p class="text-right text-[#6D7786]">{{ $course->trainees->count() }}</p>
                </div>

                @php
                    $trainerUser = optional($course->trainer?->user);
                @endphp

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 flex shrink-0 rounded-full overflow-hidden">
                        <img src="{{ $trainerUser->avatar_url ?? asset('images/default-avatar.svg') }}" class="w-full h-full object-cover" alt="avatar">
                    </div>
                    <div class="flex flex-col">
                        <p class="font-semibold">{{ $trainerUser->name ?? 'Unknown Trainer' }}</p>
                        <p class="text-[#6D7786]">{{ $trainerUser->pekerjaan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <p>Belum ada data kelas terbaru</p>
    @endforelse
</div>
