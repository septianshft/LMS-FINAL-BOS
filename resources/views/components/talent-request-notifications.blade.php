{{-- Talent Request Notifications Component --}}

{{-- New Request Notification --}}
@if(session('talent_request_notification'))
    @php $notification = session('talent_request_notification'); @endphp
    <div class="fixed top-4 right-4 z-50 max-w-sm bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden animate-slide-in">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center text-white">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-3">
                        <i class="fas fa-handshake text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">{{ $notification['title'] }}</h3>
                        <p class="text-blue-100 text-sm">{{ $notification['created_at'] }}</p>
                    </div>
                </div>
                <button onclick="dismissNotification(this)" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="p-4">
            <p class="text-gray-700 text-sm mb-3">{{ $notification['message'] }}</p>
            <div class="flex space-x-2">
                <button onclick="viewRequestDetails({{ $notification['request_id'] }})"
                        class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-eye mr-1"></i> View Details
                </button>
                <button onclick="dismissNotification(this)"
                        class="px-3 py-2 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition-colors">
                    Later
                </button>
            </div>
        </div>
    </div>
@endif

{{-- Status Change Notification --}}
@if(session('talent_status_notification'))
    @php $notification = session('talent_status_notification'); @endphp
    <div class="fixed top-4 right-4 z-50 max-w-sm bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden animate-slide-in">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center text-white">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-3">
                        <i class="fas fa-bell text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">{{ $notification['title'] }}</h3>
                        <p class="text-green-100 text-sm">Status Update</p>
                    </div>
                </div>
                <button onclick="dismissNotification(this)" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="p-4">
            <p class="text-gray-700 text-sm mb-3">{{ $notification['message'] }}</p>
            <div class="bg-gray-50 p-3 rounded-lg mb-3">
                <div class="text-xs text-gray-600 mb-1">Project: {{ $notification['project_title'] }}</div>
                <div class="text-xs text-gray-600">Recruiter: {{ $notification['recruiter_name'] }}</div>
            </div>
            <button onclick="dismissNotification(this)"
                    class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-check mr-1"></i> Got it
            </button>
        </div>
    </div>
@endif

{{-- Admin Request Notification (for admin users) --}}
@if(session('admin_request_notification') && auth()->user()->hasRole('talent_admin'))
    @php $notification = session('admin_request_notification'); @endphp
    <div class="fixed top-4 right-4 z-50 max-w-sm bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden animate-slide-in">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center text-white">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-3">
                        <i class="fas fa-clipboard-list text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">{{ $notification['title'] }}</h3>
                        <p class="text-orange-100 text-sm">Admin Action Required</p>
                    </div>
                </div>
                <button onclick="dismissNotification(this)" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="p-4">
            <p class="text-gray-700 text-sm mb-3">{{ $notification['message'] }}</p>
            <div class="bg-gray-50 p-3 rounded-lg mb-3">
                <div class="text-xs text-gray-600 mb-1">Project: {{ $notification['project_title'] }}</div>
                <div class="text-xs text-gray-600 mb-1">Recruiter: {{ $notification['recruiter_name'] }}</div>
                <div class="text-xs text-gray-600">Talent: {{ $notification['talent_name'] }}</div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('talent_admin.manage_requests') }}"
                   class="flex-1 px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-colors text-center">
                    <i class="fas fa-tasks mr-1"></i> Review
                </a>
                <button onclick="dismissNotification(this)"
                        class="px-3 py-2 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition-colors">
                    Later
                </button>
            </div>
        </div>
    </div>
@endif

<style>
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

@keyframes slide-out {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slide-out {
    animation: slide-out 0.3s ease-in;
}
</style>

<script>
function dismissNotification(button) {
    const notification = button.closest('.fixed');
    if (notification) {
        notification.classList.remove('animate-slide-in');
        notification.classList.add('animate-slide-out');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

// Auto-dismiss notifications after 10 seconds
document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('.fixed.z-50');
    notifications.forEach(notification => {
        setTimeout(() => {
            if (notification.parentNode) {
                dismissNotification(notification.querySelector('button'));
            }
        }, 10000);
    });
});
</script>
