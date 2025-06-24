<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Final Quiz for Course') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-10 shadow-sm sm:rounded-lg">

                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-indigo-900">{{ $course->name }}</h3>
                    <p class="text-slate-500">{{ $course->category->name }}</p>
                </div>

                <form method="POST" action="{{ route('admin.course_quiz.store', $course) }}">
                    @csrf

                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <div class="mb-4">
                        <x-input-label for="title" value="Quiz Title" />
                        <x-text-input id="title" name="title" type="text" class="w-full" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="passing_score" value="Passing Score (%)" />
                        <x-text-input id="passing_score" name="passing_score" type="number" class="w-full" min="0" max="100" required />
                    </div>

                    <div class="mb-4">
                        <label for="is_hidden_from_trainee" class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                        <div class="flex items-center">
                            <input id="is_hidden_from_trainee" name="is_hidden_from_trainee" type="checkbox" value="1" 
                                   {{ old('is_hidden_from_trainee') ? 'checked' : '' }}
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

                    <hr class="my-6" />

                    <h4 class="text-lg font-semibold mb-2">Questions</h4>
                    <div id="questions-container">
                        <!-- Initial Question -->
                        <div class="question-item mb-6 p-4 border rounded-md">
                            <div class="mb-2">
                                <x-input-label for="questions[0][text]" value="Question Text" />
                                <x-text-input name="questions[0][text]" type="text" class="w-full question-text-input" required />
                                <x-input-error :messages="$errors->get('questions.0.text')" class="mt-2" />
                            </div>

                            <h5 class="text-md font-semibold my-2">Options (Choose one correct answer)</h5>
                            <div class="options-container grid grid-cols-2 gap-4">
                                @for ($i = 0; $i < 4; $i++)
                                    <div class="option-item">
                                        <x-input-label for="questions[0][options][{{ $i }}][text]" value="Option {{ $i + 1 }}" />
                                        <div class="flex items-center">
                                            <x-text-input name="questions[0][options][{{ $i }}][text]" type="text" class="w-full mr-2 option-text-input" required />
                                            <input type="radio" name="questions[0][correct_option]" value="{{ $i }}" class="correct-option-radio" required>
                                        </div>
                                        <x-input-error :messages="$errors->get('questions.0.options.'.$i.'.text')" class="mt-2" />
                                    </div>
                                @endfor
                            </div>

                            <x-input-error :messages="$errors->get('questions.0.correct_option')" class="mt-2 text-red-500 font-semibold" />
                        </div>
                    </div>

                    <button type="button" id="add-question" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Question
                    </button>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-indigo-700 text-white px-6 py-3 rounded-full font-semibold">
                            Save Quiz
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const questionsContainer = document.getElementById('questions-container');
            const addQuestionButton = document.getElementById('add-question');
            let questionIndex = {{ old('questions') ? count(old('questions')) : 1 }};

            // Initialize existing question items
            document.querySelectorAll('.question-item').forEach((item, idx) => {
                if (idx > 0) addRemoveButton(item, idx);
                updateNamesAndIds(item, idx);
            });

            addQuestionButton.addEventListener('click', function () {
                const newQuestion = questionsContainer.children[0].cloneNode(true);

                newQuestion.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
                newQuestion.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);
                newQuestion.querySelectorAll('.mt-2').forEach(errorMsg => {
                    if (errorMsg.classList.contains('text-red-500')) errorMsg.innerHTML = '';
                });

                updateNamesAndIds(newQuestion, questionIndex);
                addRemoveButton(newQuestion, questionIndex);
                questionsContainer.appendChild(newQuestion);
                questionIndex++;
            });

            function updateNamesAndIds(questionItem, index) {
                const questionInput = questionItem.querySelector('.question-text-input');
                questionInput.name = `questions[${index}][text]`;
                questionInput.id = `questions_${index}_text`;

                const correctOptionError = questionItem.querySelector('input[name^="questions["][name$="[correct_option]"]')
                    .closest('.question-item')
                    .querySelector('.text-red-500.font-semibold');

                if (correctOptionError) {
                    correctOptionError.setAttribute('data-error-key', `questions.${index}.correct_option`);
                }

                questionItem.querySelectorAll('.option-item').forEach((option, optionIdx) => {
                    const optionInput = option.querySelector('.option-text-input');
                    optionInput.name = `questions[${index}][options][${optionIdx}][text]`;
                    optionInput.id = `questions_${index}_options_${optionIdx}_text`;

                    const radioInput = option.querySelector('.correct-option-radio');
                    radioInput.name = `questions[${index}][correct_option]`;
                    radioInput.id = `questions_${index}_correct_option_${optionIdx}`;
                });
            }

            function addRemoveButton(questionItem, index) {
                if (questionsContainer.children.length > 1 || index > 0) {
                    let removeButton = questionItem.querySelector('.remove-question-button');

                    if (!removeButton) {
                        removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.textContent = 'Remove Question';
                        removeButton.className = 'mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm remove-question-button';

                        removeButton.addEventListener('click', function () {
                            if (questionsContainer.children.length > 1) {
                                questionItem.remove();
                            } else {
                                alert('You must have at least one question.');
                            }
                        });

                        questionItem.querySelector('.options-container').insertAdjacentElement('afterend', removeButton);
                    }
                }
            }
        });
    </script>
</x-app-layout>
