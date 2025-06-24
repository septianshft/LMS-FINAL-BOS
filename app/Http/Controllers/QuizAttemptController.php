<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FinalQuiz;
use App\Models\QuizAttempt;
use App\Models\CourseProgress;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    public function show(Course $course) // Laravel akan otomatis mengambil objek Course berdasarkan ID dari URL
    {
        $quiz = FinalQuiz::where('course_id', $course->id)
            ->with(['questions' => function ($query) {
                $query->orderBy('id')->with('options');
            }]) // Pastikan pertanyaan terurut dan memuat opsi
            ->firstOrFail(); // Ambil kuis pertama yang terkait dengan kursus ini

        // Mengirim data ke view 'front.quiz.blade.php'
        return view('front.quiz', compact('quiz', 'course'));
    }

    public function submit(Request $request, $quizId) // $quizId akan diisi dengan nilai dari {quiz} di URL
    {
        $quiz = FinalQuiz::with('questions.options')->findOrFail($quizId);
        $correct = 0;
        $total = count($quiz->questions);

        foreach ($quiz->questions as $question) {
            $userAnswer = $request->answers[$question->id] ?? null;
            if ($question->options && $question->options->where('is_correct', true)->first() && $question->options->where('is_correct', true)->first()->id == $userAnswer) {
                $correct++;
            }
        }

        $score = ($total > 0) ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        QuizAttempt::create([
            'final_quiz_id' => $quiz->id,
            'user_id' => auth()->user()->id,
            'score' => $score,
            'is_passed' => $passed,
        ]);

        $user = auth()->user();
        $course = $quiz->course;

        $progress = CourseProgress::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'completed_videos' => [],
            'progress' => 0,
        ]);

        if ($passed) {
            $progress->update(['quiz_passed' => true]);
        }

        if ($progress->progress == 100 && $passed && !$course->certificates()->where('user_id', $user->id)->exists()) {
            $this->generateCertificate($course, $user);
        }

        // Auto-add skill to user if they passed the quiz (completed the course)
        if ($passed) {
            $user = auth()->user();
            $course = $quiz->course;

            // Add skill from course completion with enhanced analytics
            $user->addSkillFromCourse($course);

            // Enhanced talent suggestion is now handled automatically in addSkillFromCourse()
            // Check for certificate eligibility suggestion
            if ($progress->progress == 100 && !$user->available_for_scouting) {
                session()->flash('certificate_talent_suggestion', [
                    'message' => 'Congratulations on completing the course! You\'ve earned a certificate and valuable skills. Join our talent platform to showcase your achievements to employers.',
                    'action_url' => route('profile.edit') . '#talent-settings',
                    'certificate_earned' => true
                ]);
            }
        }

        // Redirect kembali dengan membawa hasil
        return redirect()->back()->with('result', compact('score', 'passed', 'quiz'));
    }

    private function generateCertificate(Course $course, $user): void
    {
        $pdf = Pdf::loadView('certificates.certificate', [
            'course' => $course,
            'user' => $user,
            'date' => now()->toDateString(),
        ]);

        $path = 'certificates/' . $user->id . '_' . $course->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'path' => $path,
            'generated_at' => now(),
        ]);
    }
}
