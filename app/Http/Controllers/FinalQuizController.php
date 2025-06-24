<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FinalQuiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\Trainer; // Add this line
use Illuminate\Support\Facades\Auth; // Add this line
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class FinalQuizController extends Controller
{
    /**
     * Display a listing of courses to manage their quizzes.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Course::with(['category', 'trainer.user', 'finalQuizzes']);

        if ($user->hasRole('trainer')) {
            $loggedInTrainer = Trainer::where('user_id', $user->id)->first();
            if ($loggedInTrainer) {
                $query->where('trainer_id', $loggedInTrainer->id);
            } else {
                // If for some reason the trainer profile doesn't exist for this user,
                // return an empty set of courses.
                $query->whereRaw('1 = 0'); // Effectively returns no results
            }
        }
        // Admins or other roles will see all courses as per the original logic

        $courses = $query->orderByDesc('id')->paginate(10);

        return view('admin.course_quiz.index', compact('courses'));
    }

    /**
     * Show the form for creating a new final quiz for a specific course.
     */
    public function create(Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer')) {
            $loggedInTrainer = Trainer::where('user_id', $user->id)->first();
            if (!$loggedInTrainer || $course->trainer_id !== $loggedInTrainer->id) {
                abort(403, 'Unauthorized action. This course does not belong to you.');
            }
        }

        return view('admin.course_quiz.create', compact('course'));
    }

    /**
     * Store a newly created Final Quiz and its associated questions and options.
     */
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer')) {
            $loggedInTrainer = Trainer::where('user_id', $user->id)->first();
            // We also need to ensure the course_id from the request matches the $course from route model binding
            // and that the trainer owns this course.
            if (!$loggedInTrainer || $course->trainer_id !== $loggedInTrainer->id || $request->input('course_id') != $course->id) {
                abort(403, 'Unauthorized action or mismatched course data.');
            }
        }

        // Validasi data
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_hidden_from_trainee' => 'sometimes|boolean', // Add this validation
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|size:4', 
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_option' => 'required|integer|min:0|max:3', 
        ]);

        DB::beginTransaction();

        try {
            // 1. Simpan data Final Quiz
            $finalQuiz = FinalQuiz::create([
                'course_id'     => $validatedData['course_id'],
                'title'         => $validatedData['title'],
                'passing_score' => $validatedData['passing_score'],
                'is_hidden_from_trainee' => $request->has('is_hidden_from_trainee'), // Process checkbox
            ]);

            // 2. Simpan setiap pertanyaan beserta opsi-opsinya
            foreach ($validatedData['questions'] as $questionData) {
                $question = $finalQuiz->questions()->create([ 
                    'question' => $questionData['text'], 
                ]);

                foreach ($questionData['options'] as $index => $optionData) {
                    $question->options()->create([ 
                        'option_text' => $optionData['text'], 
                        'is_correct'  => ($index == $questionData['correct_option']),
                    ]);
                }
            }

            DB::commit();

            // Redirect to the edit page for the newly created quiz
            return redirect()
                ->route('admin.course_quiz.edit', ['course' => $course->id]) 
                ->with('success', 'Final quiz created successfully! You can now review and edit it.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating final quiz: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'There was an error creating the quiz. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified final quiz.
     */
    public function edit(Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer')) {
            $loggedInTrainer = Trainer::where('user_id', $user->id)->first();
            if (!$loggedInTrainer || $course->trainer_id !== $loggedInTrainer->id) {
                abort(403, 'Unauthorized action. This course does not belong to you.');
            }
        }

        $quiz = $course->finalQuizzes()->with(['questions.options'])->first();

        if (!$quiz) {
            return redirect()->route('admin.course_quiz.create', $course)->with('info', 'No quiz found for this course. You can create one.');
        }

        return view('admin.course_quiz.edit', compact('course', 'quiz'));
    }

    /**
     * Update the specified final quiz in storage.
     */
    public function update(Request $request, Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer')) {
            $loggedInTrainer = Trainer::where('user_id', $user->id)->first();
            if (!$loggedInTrainer || $course->trainer_id !== $loggedInTrainer->id) {
                abort(403, 'Unauthorized action. This course does not belong to you.');
            }
        }

        // Validasi data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_hidden_from_trainee' => 'sometimes|boolean', // Add this validation
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:quiz_questions,id', 
            'questions.*.text' => 'required|string', 
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*.id' => 'nullable|exists:quiz_options,id', 
            'questions.*.options.*.text' => 'required|string', 
            'questions.*.correct_option' => 'required|integer|min:0|max:3', 
        ]);

        DB::beginTransaction();

        try {
            $finalQuiz = $course->finalQuizzes()->firstOrFail(); 

            // 1. Update Final Quiz details
            $finalQuiz->update([
                'title'         => $validatedData['title'],
                'passing_score' => $validatedData['passing_score'],
                'is_hidden_from_trainee' => $request->has('is_hidden_from_trainee'), // Process checkbox
            ]);

            $submittedQuestionIds = [];
            foreach ($validatedData['questions'] as $questionData) {
                if (isset($questionData['id']) && !empty($questionData['id'])) {
                    $submittedQuestionIds[] = $questionData['id'];
                }
            }
            // Delete questions that were removed from the form
            $finalQuiz->questions()->whereNotIn('id', array_filter($submittedQuestionIds))->delete(); 

            foreach ($validatedData['questions'] as $q_idx => $questionData) {
                $questionPayload = ['question' => $questionData['text']]; 
                $question = null;

                if (isset($questionData['id']) && !empty($questionData['id'])) {
                    $question = QuizQuestion::find($questionData['id']); 
                    if ($question && $question->final_quiz_id == $finalQuiz->id) { 
                        $question->update($questionPayload);
                    } else {
                        // This case should ideally not happen if IDs are managed correctly
                        // Or it could be an attempt to assign a question from another quiz
                        $question = $finalQuiz->questions()->create($questionPayload);
                    }
                } else {
                    $question = $finalQuiz->questions()->create($questionPayload);
                }

                $submittedOptionIds = [];
                if (isset($questionData['options']) && is_array($questionData['options'])) {
                    foreach($questionData['options'] as $optionData) {
                        if (isset($optionData['id']) && !empty($optionData['id'])) {
                            $submittedOptionIds[] = $optionData['id'];
                        }
                    }
                }
                // Delete options that were removed from the form for this question
                $question->options()->whereNotIn('id', array_filter($submittedOptionIds))->delete();

                if (isset($questionData['options']) && is_array($questionData['options'])) {
                    foreach ($questionData['options'] as $index => $optionData) {
                        $optionPayload = [
                            'option_text' => $optionData['text'], 
                            'is_correct'  => ($index == $questionData['correct_option']),
                        ];
                        
                        if (isset($optionData['id']) && !empty($optionData['id'])) {
                            $option = QuizOption::find($optionData['id']); 
                            if ($option && $option->quiz_question_id == $question->id) { // Corrected foreign key check
                                $option->update($optionPayload);
                            } else {
                                // This case should ideally not happen
                                $question->options()->create($optionPayload);
                            }
                        } else {
                            $question->options()->create($optionPayload);
                        }
                    }
                }
            }
            DB::commit();

            return redirect()->route('admin.course_quiz.edit', $course)->with('success', 'Final quiz updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating final quiz: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(), // Keep this for debugging
            ]);
            return back()->withInput()->with('error', 'There was an error updating the quiz. Please try again. Error: ' . $e->getMessage());
        }
    }
}