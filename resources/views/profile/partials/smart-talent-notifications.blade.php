@php
use Illuminate\Support\Facades\Cache;
@endphp

{{-- Smart Talent Conversion Notifications --}}

{{-- Smart Talent Suggestion --}}
@php
    // Check session first, then fall back to cache for persistent display
    $smartSuggestion = session('smart_talent_suggestion') ?? Cache::get("conversion_suggestion_" . auth()->id());

    // If user already has talent role, clear any existing suggestion data
    if (auth()->user()->hasRole('talent') && $smartSuggestion) {
        // Clear cache and session since user already converted
        Cache::forget("conversion_suggestion_" . auth()->id());
        session()->forget('smart_talent_suggestion');
        $smartSuggestion = null; // Don't show notification
    }
@endphp

@if($smartSuggestion && !auth()->user()->hasRole('talent'))
    @php $suggestion = $smartSuggestion; @endphp
    <div class="smart-talent-notification bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-lg mb-6 shadow-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-300 text-blue-600 rounded-full flex items-center justify-center text-xl font-bold">
                    ⭐
                </div>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-lg font-semibold mb-1">🎉 You're Ready for the Next Level!</h3>
                <p class="mb-3">{{ $suggestion['message'] }}</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-3">
                    <p class="text-sm font-medium">{{ $suggestion['reason'] }}</p>
                    <div class="mt-2 flex items-center text-sm">
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded mr-2">
                            ⚙️ {{ $suggestion['skill_count'] }} Skills
                        </span>
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded">
                            📈 High Market Value
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ $suggestion['action_url'] }}"
                       class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200 text-center">
                        🚀 Join Talent Platform
                    </a>
                    <button onclick="dismissSuggestion('smart_talent_suggestion')" type="button"
                            class="inline-flex items-center px-4 py-2 bg-transparent border border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition-colors duration-200">
                        ❌ Maybe Later
                    </button>
                </div>
            </div>
            <button onclick="dismissSuggestion('smart_talent_suggestion')" type="button"
                    class="flex-shrink-0 ml-3 text-white hover:text-gray-200 transition-colors duration-200 w-6 h-6 flex items-center justify-center">
                ✕
            </button>
        </div>
    </div>
@endif

{{-- Certificate Talent Suggestion --}}
@php
    // Check session first, then fall back to cache for persistent display
    $certificateSuggestion = session('certificate_talent_suggestion') ?? Cache::get("certificate_suggestion_" . auth()->id());

    // If user already has talent role, clear any existing certificate suggestion data
    if (auth()->user()->hasRole('talent') && $certificateSuggestion) {
        // Clear cache and session since user already converted
        Cache::forget("certificate_suggestion_" . auth()->id());
        session()->forget('certificate_talent_suggestion');
        $certificateSuggestion = null; // Don't show notification
    }
@endphp

@if($certificateSuggestion && !auth()->user()->hasRole('talent'))
    @php $suggestion = $certificateSuggestion; @endphp
    <div class="certificate-talent-notification bg-gradient-to-r from-green-500 to-teal-600 text-white p-4 rounded-lg mb-6 shadow-lg" style="display: none;">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-300 text-green-600 rounded-full flex items-center justify-center text-xl font-bold">
                    🏆
                </div>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-lg font-semibold mb-1">🏆 Course Completed & Certificate Earned!</h3>
                <p class="mb-3">{{ $suggestion['message'] }}</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-3">
                    <div class="flex items-center text-sm">
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded mr-2">
                            🏅 Certificate Ready
                        </span>
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded">
                            💼 Employer Ready
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ $suggestion['action_url'] }}"
                       class="inline-flex items-center px-4 py-2 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors duration-200 text-center">
                        👔 Become Discoverable
                    </a>
                    <button onclick="dismissSuggestion('certificate_talent_suggestion')" type="button"
                            class="inline-flex items-center px-4 py-2 bg-transparent border border-white text-white font-semibold rounded-lg hover:bg-white hover:text-green-600 transition-colors duration-200">
                        🕐 Not Now
                    </button>
                </div>
            </div>
            <button onclick="dismissSuggestion('certificate_talent_suggestion')" type="button"
                    class="flex-shrink-0 ml-3 text-white hover:text-gray-200 transition-colors duration-200 w-6 h-6 flex items-center justify-center">
                ✕
            </button>
        </div>
    </div>
@endif

{{-- Legacy Talent Suggestion (fallback) --}}
@if(session('talent_suggestion') && !auth()->user()->hasRole('talent'))
    @php $suggestion = session('talent_suggestion'); @endphp
    <div class="talent-notification bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-lightbulb text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-blue-700">{{ $suggestion['message'] }}</p>
                <div class="mt-2">
                    <a href="{{ $suggestion['action_url'] }}"
                       class="text-blue-600 hover:text-blue-500 font-medium">
                        Enable Talent Scouting →
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Talent Notification Scripts -->
<script>
function dismissSuggestion(suggestionType) {
    console.log('Dismissing suggestion:', suggestionType);
    console.log('Available notifications on page:', document.querySelectorAll('[class*="notification"]'));

    // Map suggestion types to their corresponding CSS classes
    const selectorMap = {
        'smart_talent_suggestion': '.smart-talent-notification',
        'certificate_talent_suggestion': '.certificate-talent-notification',
        'talent_suggestion': '.talent-notification'
    };

    const notificationSelector = selectorMap[suggestionType] || `.${suggestionType.replace('_', '-')}-notification`;
    console.log('Looking for notification with selector:', notificationSelector);

    const notification = document.querySelector(notificationSelector);
    console.log('Found notification element:', notification);

    if (notification) {
        // Add a temporary "dismissing" message
        const originalContent = notification.innerHTML;
        notification.innerHTML = `
            <div class="flex items-center justify-center p-4">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white mr-3"></div>
                <span>Hiding notification for 24 hours...</span>
            </div>
        `;

        // Animate out after showing the message briefly
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }, 1000);

        // Store dismissal in localStorage with timestamp for 24-hour hiding
        const dismissalTime = Date.now();
        localStorage.setItem(`dismissed_${suggestionType}`, dismissalTime);
        console.log(`Stored dismissal timestamp for ${suggestionType}:`, dismissalTime);

        // Send AJAX request to server to set dismissal flag (24-hour server-side tracking)
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found in page');
            return;
        }

        fetch('/dismiss-suggestion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({
                suggestion_type: suggestionType
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Suggestion dismissed successfully:', data);
            // Show a subtle confirmation
            if (window.location.pathname === '/dashboard') {
                const message = document.createElement('div');
                message.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                message.textContent = 'Notification hidden for 24 hours';
                document.body.appendChild(message);
                setTimeout(() => {
                    message.remove();
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Failed to dismiss suggestion on server:', error);
            // Restore original content on error
            notification.innerHTML = originalContent;
            notification.style.transform = '';
            notification.style.opacity = '';
            notification.style.display = '';

            // Show error message
            alert('Failed to save dismissal. The notification may reappear immediately.');
        });
    } else {
        console.error('Notification element not found for type:', suggestionType);
        console.error('Expected selector:', notificationSelector);
        console.error('Available elements with "notification" in class:');
        document.querySelectorAll('[class*="notification"]').forEach((el, index) => {
            console.error(`  ${index + 1}. Element:`, el, 'Classes:', el.className);
        });
    }
}

// Add smooth animation on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Loading talent notifications...');

    const notifications = document.querySelectorAll('.smart-talent-notification, .certificate-talent-notification');
    console.log('Found notifications:', notifications.length);

    notifications.forEach((notification, index) => {
        // Get notification type
        const notificationType = notification.classList.contains('smart-talent-notification') ? 'smart_talent_suggestion' : 'certificate_talent_suggestion';

        // Check localStorage for recent dismissal (within last hour only - for immediate feedback)
        const dismissedTime = localStorage.getItem(`dismissed_${notificationType}`);
        const currentTime = Date.now();
        const minutesAgo = dismissedTime ? Math.floor((currentTime - parseInt(dismissedTime)) / (60 * 1000)) : null;

        console.log(`Checking notification ${notificationType}:`);
        console.log(`  - Dismissed timestamp: ${dismissedTime}`);
        console.log(`  - Minutes since dismissal: ${minutesAgo}`);

        // Only hide if dismissed very recently (within 5 minutes) - for immediate UI feedback
        if (dismissedTime && minutesAgo < 5) {
            console.log(`Hiding notification ${notificationType} - dismissed ${minutesAgo} minutes ago`);
            notification.style.display = 'none';
            return;
        }

        // Clear old dismissal data if more than 5 minutes have passed
        if (dismissedTime && minutesAgo >= 5) {
            console.log(`Clearing old dismissal data for ${notificationType} - ${minutesAgo} minutes old`);
            localStorage.removeItem(`dismissed_${notificationType}`);
        }

        // Show notification with animation
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            notification.style.transition = 'all 0.5s ease';
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, index * 200);
    });
});
</script>

<style>
.smart-talent-notification,
.certificate-talent-notification {
    transition: all 0.3s ease;
}

.smart-talent-notification:hover,
.certificate-talent-notification:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}
</style>
