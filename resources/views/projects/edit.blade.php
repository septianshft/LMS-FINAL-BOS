@extends('layout.template.mainTemplate')

@section('title', 'Edit Project')
@section('container')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Project</h1>
            <p class="text-gray-600 mt-2">Update your project details</p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Project Edit Form -->
    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <!-- Basic Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Project Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Project Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               required
                               value="{{ old('title', $project->title) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter your project title">
                    </div>

                    <!-- Industry -->
                    <div>
                        <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">
                            Industry
                        </label>
                        <input type="text"
                               id="industry"
                               name="industry"
                               value="{{ old('industry', $project->industry) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Technology, Healthcare, Finance">
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Project Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="5"
                                  required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Describe your project goals, scope, and expected deliverables...">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <!-- General Requirements -->
                    <div>
                        <label for="general_requirements" class="block text-sm font-medium text-gray-700 mb-2">
                            General Requirements
                        </label>
                        <textarea id="general_requirements"
                                  name="general_requirements"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="List general skills, experience, or qualifications needed...">{{ old('general_requirements', $project->general_requirements) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Timeline & Budget -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Timeline & Budget</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Expected Start Date -->
                    <div>
                        <label for="expected_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expected Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="expected_start_date"
                               name="expected_start_date"
                               required
                               value="{{ old('expected_start_date', $project->expected_start_date ? $project->expected_start_date->format('Y-m-d') : '') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Expected End Date -->
                    <div>
                        <label for="expected_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expected End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="expected_end_date"
                               name="expected_end_date"
                               required
                               value="{{ old('expected_end_date', $project->expected_end_date ? $project->expected_end_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Budget Min -->
                    <div>
                        <label for="overall_budget_min" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Budget ($)
                        </label>
                        <input type="number"
                               id="overall_budget_min"
                               name="overall_budget_min"
                               min="0"
                               step="100"
                               value="{{ old('overall_budget_min', $project->overall_budget_min) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="5000">
                    </div>

                    <!-- Budget Max -->
                    <div>
                        <label for="overall_budget_max" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Budget ($)
                        </label>
                        <input type="number"
                               id="overall_budget_max"
                               name="overall_budget_max"
                               min="0"
                               step="100"
                               value="{{ old('overall_budget_max', $project->overall_budget_max) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="15000">
                    </div>
                </div>

                <!-- Budget Notice -->
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Budget Guidelines:</strong> Provide a realistic budget range that covers talent costs, tools, and resources.
                                This helps talents understand the project scope and compensation expectations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        Changes will be subject to admin re-approval if the project status is pending.
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('projects.show', $project) }}"
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>Update Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('expected_start_date');
    const endDateInput = document.getElementById('expected_end_date');
    const budgetMinInput = document.getElementById('overall_budget_min');
    const budgetMaxInput = document.getElementById('overall_budget_max');

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = '';
        }
    });

    // Validate budget range
    function validateBudgetRange() {
        const minVal = parseFloat(budgetMinInput.value) || 0;
        const maxVal = parseFloat(budgetMaxInput.value) || 0;

        if (minVal > 0 && maxVal > 0 && minVal > maxVal) {
            budgetMaxInput.setCustomValidity('Maximum budget must be greater than or equal to minimum budget');
        } else {
            budgetMaxInput.setCustomValidity('');
        }
    }

    budgetMinInput.addEventListener('input', validateBudgetRange);
    budgetMaxInput.addEventListener('input', validateBudgetRange);

    // Set initial end date minimum
    if (startDateInput.value) {
        endDateInput.min = startDateInput.value;
    }
});
</script>
@endpush
