@extends('layout.template.mainTemplate')

@section('title', 'Assignment Details - Talent')
@section('container')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center space-x-4 mb-2">
                <a href="{{ route('talent.assignments.index') }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $assignment->project->title }}</h1>
                <span class="@if($assignment->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($assignment->status === 'accepted') bg-green-100 text-green-800 @elseif($assignment->status === 'declined') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif px-3 py-1 rounded-full text-sm font-medium">
                    {{ ucfirst($assignment->status) }}
                </span>
            </div>
            <p class="text-gray-600">{{ $assignment->project->recruiter->company_name ?? 'Unknown Company' }}</p>
        </div>

        @if($assignment->status === 'pending')
            <div class="flex space-x-3">
                <button onclick="respondToAssignment('accepted')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Accept Assignment
                </button>
                <button onclick="respondToAssignment('declined')" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Decline Assignment
                </button>
            </div>
        @elseif($assignment->status === 'accepted' && $assignment->project->status === 'active')
            <div class="flex space-x-3">
                <button onclick="openProgressModal()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                    Update Progress
                </button>
                @if($assignment->project->canRequestExtension())
                    <button onclick="openExtensionModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        Request Extension
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Assignment Overview -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Assignment Details</h2>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Assignment Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Estimated Hours:</span>
                                <span class="font-medium">{{ $assignment->project->duration_weeks * 40 }} hours</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Timeline</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-medium">{{ $assignment->project->duration_weeks }} weeks</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Start Date:</span>
                                <span class="font-medium">{{ $assignment->project->start_date ? $assignment->project->start_date->format('M j, Y') : 'TBD' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">End Date:</span>
                                <span class="font-medium">{{ $assignment->project->end_date ? $assignment->project->end_date->format('M j, Y') : 'TBD' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($assignment->project->start_date && $assignment->project->end_date)
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Project Progress</span>
                            <span>{{ $assignment->project->getProgressPercentage() }}% Complete</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $assignment->project->getProgressPercentage() }}%"></div>
                        </div>
                    </div>
                @endif

                @if($assignment->notes)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-2">Assignment Notes</h3>
                        <p class="text-gray-700">{{ $assignment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Project Description -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Project Description</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $assignment->project->description }}</p>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Required Skills</h3>
                        <p class="text-gray-700">{{ $assignment->project->required_skills }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Experience Level</h3>
                        <p class="text-gray-700">{{ ucfirst($assignment->project->experience_level) }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            @if($assignment->project->assignments->count() > 1)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Team Members</h2>
                    <div class="space-y-3">
                        @foreach($assignment->project->assignments->where('status', 'accepted') as $teamAssignment)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-gray-600 font-medium">{{ substr($teamAssignment->talent->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">
                                            @if($teamAssignment->id === $assignment->id)
                                                {{ $teamAssignment->talent->user->name }} (You)
                                            @else
                                                {{ $teamAssignment->talent->user->name }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600">Team Member</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500">
                                    Joined {{ $teamAssignment->responded_at ? $teamAssignment->responded_at->format('M j') : $teamAssignment->created_at->format('M j') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Progress Updates -->
            @if($assignment->status === 'accepted' && $progressUpdates->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Progress Updates</h2>
                    <div class="space-y-4">
                        @foreach($progressUpdates as $update)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-900">Progress Update</h3>
                                    <span class="text-sm text-gray-500">{{ $update->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <p class="text-gray-700 mb-2">{{ $update->description }}</p>
                                @if(isset($update->completion_percentage))
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">Completion:</span>
                                        <span class="text-sm font-medium">{{ $update->completion_percentage }}%</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Recruiter Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recruiter Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600">Company:</span>
                        <p class="font-medium">{{ $assignment->project->recruiter->company_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Contact:</span>
                        <p class="font-medium">{{ $assignment->project->recruiter->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $assignment->project->recruiter->user->email }}</p>
                    </div>
                    @if($assignment->project->recruiter->phone)
                        <div>
                            <span class="text-gray-600">Phone:</span>
                            <p class="font-medium">{{ $assignment->project->recruiter->phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assignment Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium @if($assignment->status === 'pending') text-yellow-600 @elseif($assignment->status === 'accepted') text-green-600 @elseif($assignment->status === 'declined') text-red-600 @endif">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Assigned:</span>
                        <span class="font-medium">{{ $assignment->created_at->format('M j, Y') }}</span>
                    </div>
                    @if($assignment->responded_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Responded:</span>
                            <span class="font-medium">{{ $assignment->responded_at->format('M j, Y') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Project Status:</span>
                        <span class="font-medium">{{ ucfirst($assignment->project->status) }}</span>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            @if($assignment->project->timelineEvents->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        @foreach($assignment->project->timelineEvents->take(5) as $event)
                            <div class="flex">
                                <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $event->event_type }}</p>
                                    <p class="text-xs text-gray-600">{{ $event->created_at->format('M j, Y g:i A') }}</p>
                                    @if($event->description)
                                        <p class="text-xs text-gray-700 mt-1">{{ $event->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Response Modal -->
<div id="responseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 id="responseModalTitle" class="text-lg font-medium text-gray-900 mb-4"></h3>
            <form method="POST" action="{{ route('talent.assignments.respond', $assignment) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" id="responseStatus">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Response Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Add any comments about your decision..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeResponseModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="responseSubmitBtn"
                            class="px-4 py-2 rounded-lg transition-colors">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Progress Update Modal -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Update Progress</h3>
            <form method="POST" action="{{ route('talent.assignments.progress', $assignment) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Progress Update</label>
                    <textarea name="progress_update" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe your current progress, completed tasks, and next steps..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Completion Percentage</label>
                    <input type="range" name="completion_percentage" min="0" max="100" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateProgressValue(this.value)">
                    <div class="flex justify-between text-xs text-gray-600 mt-1">
                        <span>0%</span>
                        <span id="progressValue">0%</span>
                        <span>100%</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeProgressModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Progress
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extension Request Modal -->
<div id="extensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Request Project Extension</h3>
            <form method="POST" action="{{ route('talent.assignments.request-extension', $assignment) }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Weeks</label>
                    <input type="number" name="additional_weeks" min="1" max="12" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Budget ($)</label>
                    <input type="number" name="additional_budget" min="0" step="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Extension</label>
                    <textarea name="reason" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Explain why you need this extension..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeExtensionModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function respondToAssignment(status) {
    const modal = document.getElementById('responseModal');
    const title = document.getElementById('responseModalTitle');
    const statusInput = document.getElementById('responseStatus');
    const submitBtn = document.getElementById('responseSubmitBtn');

    statusInput.value = status;

    if (status === 'accepted') {
        title.textContent = 'Accept Assignment';
        submitBtn.textContent = 'Accept Assignment';
        submitBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
    } else {
        title.textContent = 'Decline Assignment';
        submitBtn.textContent = 'Decline Assignment';
        submitBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors';
    }

    modal.classList.remove('hidden');
}

function closeResponseModal() {
    document.getElementById('responseModal').classList.add('hidden');
}

function openProgressModal() {
    document.getElementById('progressModal').classList.remove('hidden');
}

function closeProgressModal() {
    document.getElementById('progressModal').classList.add('hidden');
}

function updateProgressValue(value) {
    document.getElementById('progressValue').textContent = value + '%';
}

function openExtensionModal() {
    document.getElementById('extensionModal').classList.remove('hidden');
}

function closeExtensionModal() {
    document.getElementById('extensionModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const responseModal = document.getElementById('responseModal');
    const progressModal = document.getElementById('progressModal');
    const extensionModal = document.getElementById('extensionModal');

    if (event.target === responseModal) {
        closeResponseModal();
    }
    if (event.target === progressModal) {
        closeProgressModal();
    }
    if (event.target === extensionModal) {
        closeExtensionModal();
    }
}
</script>
@endsection
