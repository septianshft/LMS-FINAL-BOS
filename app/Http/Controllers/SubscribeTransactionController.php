<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscribeTransactionRequest;
use App\Models\SubscribeTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubscribeTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaction = SubscribeTransaction::with(['user', 'course'])->orderByDesc('id')->get();
        return view('admin.transactions.index', [
            'transactions' => $transaction, // meskipun namanya tunggal, isinya array
        ]);
            }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscribeTransactionRequest $request)
{
    $user = auth()->user();
    $courseId = $request->input('course_id');
    $course = \App\Models\Course::find($courseId); // Assuming you have a Course model

    if (!$course) {
        return redirect()->back()->with('error', 'Selected course not found.');
    }

    if ($course->mode && $course->mode->name === 'onsite') {
        $today = now()->toDateString();
        if (($course->enrollment_start && $today < $course->enrollment_start) ||
            ($course->enrollment_end && $today > $course->enrollment_end)) {
            return redirect()->back()->with('error', 'Enrollment period is closed.');
        }
    }

    // Cek apakah user sudah pernah beli course ini dan sudah lunas
    $alreadyExists = SubscribeTransaction::where('user_id', $user->id)
                    ->where('course_id', $courseId)
                    ->where('is_paid', true)
                    ->exists();

    if ($alreadyExists) {
        return redirect()->back()->with('error', 'Kamu sudah membeli course ini.');
    }

    // Simpan file bukti bayar
    $proofFile = $request->file('proof');
    if (!$proofFile) {
        return redirect()->back()->with('error', 'No proof file uploaded.');
    }
    
    $proofPath = $proofFile->store('proofs', 'public');
    if (!$proofPath) {
        return redirect()->back()->with('error', 'Failed to store proof file.');
    }
    // Simpan transaksi ke database
    SubscribeTransaction::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'total_amount' => $course->price, // Add this line
        'proof' => $proofPath,
        'is_paid' => false,
        'subscription_start_date' => null,
    ]);

    return redirect()->route('front.checkout.store')->with('success', 'Transaksi berhasil dikirim, tunggu persetujuan admin.');
}


    /**
     * Display the specified resource.
     */
    public function show(SubscribeTransaction $subscribeTransaction)
    {
        return view('admin.transactions.show', compact('subscribeTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscribeTransaction $subscribeTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscribeTransaction $subscribeTransaction)
    {
        DB::transaction(function () use ($subscribeTransaction) {
            $subscribeTransaction->update([
                'is_paid' => true,
                'subscription_start_date' => Carbon::now()
            ]);
        });

        return redirect()->route('admin.subscribe_transactions.show', $subscribeTransaction)
                         ->with('success', 'Transaksi berhasil disetujui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscribeTransaction $subscribeTransaction)
    {
        //
    }
}
