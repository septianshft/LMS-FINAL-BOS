<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Final Quiz for Course') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-10 shadow-sm sm:rounded-lg">

                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-indigo-900">{{ $course->name }}</h3>
                    <p class="text-slate-500">{{ $course->category->name }}</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 border border-green-400 p-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 border border-red-400 p-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif
                 @if ($errors->any())
                    <div class="mb-4">
                        <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
                        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form method="POST" action="{{ route('admin.course_quiz.update', $course) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="title" value="Quiz Title" />
                        <x-text-input id="title" name="title" type="text" class="w-full" 
                                      value="{{ old('title', $quiz->title) }}" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="passing_score" value="Passing Score (%)" />
                        <x-text-input id="passing_score" name="passing_score" type="number" class="w-full" 
                                      min="0" max="100" value="{{ old('passing_score', $quiz->passing_score) }}" required />
                        <x-input-error :messages="$errors->get('passing_score')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="is_hidden_from_trainee" class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                        <div class="flex items-center">
                            <input id="is_hidden_from_trainee" name="is_hidden_from_trainee" type="checkbox" value="1"
                                   {{ (isset($quiz) && $quiz->is_hidden_from_trainee) || old('is_hidden_from_trainee') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_hidden_from_trainee" class="ml-2 block text-sm text-gray-900">
                                Hide this quiz from trainees
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">If checked, trainees will not see this quiz on the learning page.</p>
                        @error('is_hidden_from_trainee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="my-6">

                    <h4 class="text-lg font-semibold mb-2">Questions</h4>
                    <div id="questions-container">
                        @forelse (old('questions', $quiz->questions->toArray()) as $q_idx => $question)
                            <div class="question-item mb-6 p-4 border rounded-md">
                                <input type="hidden" name="questions[{{ $q_idx }}][id]" value="{{ $question['id'] ?? '' }}">
                                <div class="mb-2">
                                    <x-input-label for="questions_{{ $q_idx }}_text" value="Question Text" />
                                    <x-text-input id="questions_{{ $q_idx }}_text" name="questions[{{ $q_idx }}][text]" 
                                                  type="text" class="w-full question-text-input" 
                                                  value="{{ $question['question'] ?? ($question['text'] ?? '') }}" required />
                                    <x-input-error :messages="$errors->get('questions.'.$q_idx.'.text')" class="mt-2" />
                                </div>
                                <h5 class="text-md font-semibold my-2">Options (Choose one correct answer)</h5>
                                <div class="options-container grid grid-cols-2 gap-4">
                                    @php 
                                        $options = $question['options'] ?? [];
                                        $correctOptionIndex = -1;
                                        if (!empty($options) && isset($question['correct_option'])) {
                                            $correctOptionIndex = $question['correct_option'];
                                        } elseif (!empty($options)) {
                                            foreach ($options as $opt_idx => $opt) {
                                                if (isset($opt['is_correct']) && $opt['is_correct']) {
                                                    $correctOptionIndex = $opt_idx;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @for ($o_idx = 0; $o_idx < 4; $o_idx++)
                                        @php
                                            $option = $options[$o_idx] ?? null;
                                        @endphp
                                        <div class="option-item">
                                            <input type="hidden" name="questions[{{ $q_idx }}][options][{{ $o_idx }}][id]" value="{{ $option['id'] ?? '' }}">
                                            <x-input-label for="questions_{{ $q_idx }}_options_{{ $o_idx }}_text" value="Option {{ $o_idx + 1 }}" />
                                            <div class="flex items-center">
                                                <x-text-input id="questions_{{ $q_idx }}_options_{{ $o_idx }}_text" 
                                                              name="questions[{{ $q_idx }}][options][{{ $o_idx }}][text]" 
                                                              type="text" class="w-full mr-2 option-text-input" 
                                                              value="{{ $option['option_text'] ?? ($option['text'] ?? '') }}" required />
                                                <input type="radio" name="questions[{{ $q_idx }}][correct_option]" 
                                                       value="{{ $o_idx }}" class="correct-option-radio" 
                                                       id="questions_{{ $q_idx }}_correct_option_{{ $o_idx }}"
                                                       {{ $correctOptionIndex == $o_idx ? 'checked' : '' }} required>
                                            </div>
                                            <x-input-error :messages="$errors->get('questions.'.$q_idx.'.options.'.$o_idx.'.text')" class="mt-2" />
                                        </div>
                                    @endfor
                                </div>
                                <x-input-error :messages="$errors->get('questions.'.$q_idx.'.correct_option')" class="mt-2 text-red-500 font-semibold" />
                                <button type="button" class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm remove-question-button">
                                    Remove Question
                                </button>
                            </div>
                        @empty
                            <!-- Template for JS if no questions exist or for adding new ones -->
                             <div class="question-item mb-6 p-4 border rounded-md" style="display: none;" id="question-template">
                                <input type="hidden" name="questions[0][id]" value="">
                                <div class="mb-2">
                                    <x-input-label for="questions_0_text_tpl" value="Question Text" />
                                    <x-text-input id="questions_0_text_tpl" name="questions[0][text]" 
                                                  type="text" class="w-full question-text-input" value="" required />
                                </div>
                                <h5 class="text-md font-semibold my-2">Options (Choose one correct answer)</h5>
                                <div class="options-container grid grid-cols-2 gap-4">
                                    @for ($o_idx = 0; $o_idx < 4; $o_idx++)
                                        <div class="option-item">
                                            <input type="hidden" name="questions[0][options][{{ $o_idx }}][id]" value="">
                                            <x-input-label for="questions_0_options_{{ $o_idx }}_text_tpl" value="Option {{ $o_idx + 1 }}" />
                                            <div class="flex items-center">
                                                <x-text-input id="questions_0_options_{{ $o_idx }}_text_tpl" 
                                                              name="questions[0][options][{{ $o_idx }}][text]" 
                                                              type="text" class="w-full mr-2 option-text-input" value="" required />
                                                <input type="radio" name="questions[0][correct_option]" 
                                                       value="{{ $o_idx }}" class="correct-option-radio" 
                                                       id="questions_0_correct_option_{{ $o_idx }}_tpl" required>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                 <x-input-error data-error-key="questions.0.correct_option" class="mt-2 text-red-500 font-semibold" />
                                <button type="button" class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm remove-question-button">
                                    Remove Question
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <button type="button" id="add-question" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Question
                    </button>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>
                            {{ __('Save Quiz') }}
                        </x-primary-button>
                        <a href="{{ route('admin.course_quiz.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const questionsContainer = document.getElementById('questions-container');
        const addQuestionButton = document.getElementById('add-question');
        // Use the template if it exists, otherwise clone the first existing item (if any)
        const questionTemplate = document.getElementById('question-template') || (questionsContainer.children.length > 0 ? questionsContainer.children[0].cloneNode(true) : null);
        
        if (questionTemplate && questionTemplate.id === 'question-template') {
            questionTemplate.style.display = 'block'; // Make template visible if it was hidden
        }
        
        let questionIndex = {{ count(old('questions', $quiz->questions->toArray())) }};
        if (questionsContainer.children.length === 0 && questionTemplate) { // If no questions rendered, prepare to add first from template
             questionIndex = 0;
        }


        function initializeRemoveButtons() {
            questionsContainer.querySelectorAll('.question-item').forEach(item => {
                let removeButton = item.querySelector('.remove-question-button');
                if (removeButton) {
                    removeButton.addEventListener('click', function () {
                        if (questionsContainer.querySelectorAll('.question-item').length > 1) {
                            item.remove();
                        } else {
                            alert('You must have at least one question.');
                        }
                    });
                }
            });
        }
        initializeRemoveButtons(); // Call for existing items

        addQuestionButton.addEventListener('click', function () {
            if (!questionTemplate) {
                console.error("Question template not found!");
                return;
            }
            const newQuestion = questionTemplate.cloneNode(true);
            newQuestion.removeAttribute('id'); // Remove ID from cloned template
            
            newQuestion.querySelectorAll('input[type="text"], input[type="hidden"]').forEach(input => input.value = '');
            newQuestion.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);
            newQuestion.querySelectorAll('input[name$="[id]"]').forEach(input => input.value = ''); // Clear hidden IDs

            // Clear error messages from the clone
            newQuestion.querySelectorAll('.text-red-500').forEach(errorMsg => errorMsg.textContent = '');

            updateNamesAndIds(newQuestion, questionIndex);
            
            let removeButton = newQuestion.querySelector('.remove-question-button');
            if (removeButton) {
                 removeButton.addEventListener('click', function () {
                    if (questionsContainer.querySelectorAll('.question-item').length > 1) {
                        newQuestion.remove();
                    } else {
                        alert('You must have at least one question.');
                    }
                });
            } else { // Add remove button if template didn't have one visible
                const rb = document.createElement('button');
                rb.type = 'button';
                rb.textContent = 'Remove Question';
                rb.className = 'mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm remove-question-button';
                rb.addEventListener('click', function () { /* ... remove logic ... */ });
                newQuestion.appendChild(rb); // Adjust append location as needed
            }

            questionsContainer.appendChild(newQuestion);
            questionIndex++;
        });

        function updateNamesAndIds(questionItem, index) {
            // Update question ID hidden input if it exists
            const questionIdInput = questionItem.querySelector('input[name^="questions["][name$="[id]"]');
            if (questionIdInput) questionIdInput.name = `questions[${index}][id]`;

            const questionTextInput = questionItem.querySelector('.question-text-input');
            questionTextInput.name = `questions[${index}][text]`;
            questionTextInput.id = `questions_${index}_text`;
            
            const correctOptionError = questionItem.querySelector('[data-error-key^="questions."][data-error-key$=".correct_option"]');
            if (correctOptionError) {
                 correctOptionError.setAttribute('data-error-key', `questions.${index}.correct_option`);
            }


            questionItem.querySelectorAll('.option-item').forEach((option, optionIdx) => {
                const optionIdInput = option.querySelector('input[name^="questions["][name$="[id]"]');
                 if (optionIdInput) optionIdInput.name = `questions[${index}][options][${optionIdx}][id]`;

                const optionTextInput = option.querySelector('.option-text-input');
                optionTextInput.name = `questions[${index}][options][${optionIdx}][text]`;
                optionTextInput.id = `questions_${index}_options_${optionIdx}_text`;

                const radioInput = option.querySelector('.correct-option-radio');
                radioInput.name = `questions[${index}][correct_option]`;
                radioInput.id = `questions_${index}_correct_option_${optionIdx}`;
            });
        }
         // If there are no questions initially, add one from the template
        if (questionsContainer.children.length === 0 && questionTemplate && {{ count(old('questions', $quiz->questions->toArray())) }} === 0) {
            addQuestionButton.click();
        } else if (questionsContainer.children.length === 1 && questionsContainer.children[0].id === 'question-template' && {{ count(old('questions', $quiz->questions->toArray())) }} === 0) {
            // If only the template is there and it's hidden, trigger add to make the first usable one
             questionsContainer.children[0].remove(); // remove the template itself
             addQuestionButton.click(); // add a fresh one
        }


    });
    </script>
</x-app-layout>