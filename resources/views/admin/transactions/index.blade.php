<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Transactions') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">

                @forelse($transactions as $transaction)
                    <div class="item-card flex flex-row justify-between items-center p-6 border rounded-lg hover:shadow transition">
                        <svg ... class="w-12 h-12">...</svg>

                        <div>
                            <p class="text-slate-500 text-sm">Total Amount</p>
                            <h3 class="text-indigo-950 text-xl font-bold">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </h3>
                        </div>

                        <div>
                            <p class="text-slate-500 text-sm">Date</p>
                            <h3 class="text-indigo-950 text-xl font-bold">
                                {{ $transaction->created_at->format('d M Y, H:i') }}
                            </h3>
                        </div>

                        <div>
                            <p class="text-slate-500 text-sm mb-2">Status</p>
                            @if($transaction->is_paid)
                                <span class="text-sm font-bold py-2 px-3 rounded-full bg-green-500 text-white">
                                    ACTIVE
                                </span>
                            @else
                                <span class="text-sm font-bold py-2 px-3 rounded-full bg-orange-500 text-white">
                                    PENDING
                                </span>
                            @endif
                        </div>

                        <div class="hidden md:flex flex-col">
                            <p class="text-slate-500 text-sm">Trainee</p>
                            <h3 class="text-indigo-950 text-xl font-bold">{{ $transaction->user->name }}</h3>
                        </div>

                        <div class="hidden md:flex flex-row items-center gap-x-3">
                            <a href="{{ route('admin.subscribe_transactions.show', $transactions) }}" class="font-bold py-3 px-5 bg-indigo-700 text-white rounded-full">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-500">Belum ada transaksi terbaru</p>
                @endforelse

            </div>
        </div>
    </div>
</x-app-layout>
