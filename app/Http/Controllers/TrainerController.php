<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrainerRequest;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::orderBy('id', 'desc')->get();
        return view('admin.trainers.index', compact('trainers'));
    }

    public function create()
    {
        return view('admin.trainers.create');
    }

    public function store(StoreTrainerRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Data tidak ditemukan'
            ]);
        }

        if ($user->hasRole('trainer')) {
            return back()->withErrors([
                'email' => 'Email tersebut telah menjadi guru'
            ]);
        }

        DB::transaction(function () use ($user, $validated) {
            $validated['user_id'] = $user->id;
            $validated['is_active'] = true;

            Trainer::create($validated);

            if ($user->hasRole('trainee')) {
                $user->removeRole('trainee');
            }

            $user->assignRole('trainer');
        });

        return redirect()->route('admin.trainers.index')->with('success', 'Trainer berhasil ditambahkan');
    }

    public function show(Trainer $trainer)
    {
        return view('admin.trainers.show', compact('trainer'));
    }

    public function edit(Trainer $trainer)
    {
        return view('admin.trainers.edit', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        //
    }

    public function destroy(Trainer $trainer)
    {
        DB::beginTransaction();

        try {
            $user = User::find($trainer->user_id);

            $trainer->delete();

            if ($user) {
                $user->removeRole('trainer');
                $user->assignRole('trainee');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Trainer berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            throw ValidationException::withMessages([
                'system_error' => ['System error: ' . $e->getMessage()],
            ]);
        }
    }
}
