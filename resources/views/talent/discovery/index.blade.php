@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-search mr-3 text-purple-600"></i>
            Talent Discovery
        </h1>
        <p class="text-gray-600">Find the perfect talent for your project based on skills and experience from our LMS platform.</p>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Skill Search with Auto-complete -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tags mr-2"></i>Skills
                </label>
                <div class="relative">
                    <input type="text" id="skillSearch"
                           placeholder="e.g., JavaScript, Python, React"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           autocomplete="off"
                           data-debounce="true">
                    <!-- Auto-complete dropdown -->
                    <div id="skillSuggestions" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-48 overflow-y-auto shadow-lg">
                        <!-- Suggestions will be populated here -->
                    </div>
                </div>
                <small class="text-gray-500">Separate multiple skills with commas</small>
                <!-- Real-time search indicator -->
                <div id="searchIndicator" class="hidden text-xs text-blue-600 mt-1">
                    <i class="fas fa-search fa-spin mr-1"></i>Searching...
                </div>
            </div>

            <!-- Experience Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-chart-line mr-2"></i>Experience Level
                </label>
                <select id="experienceLevel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Any Level</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>

            <!-- Minimum Skills -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-trophy mr-2"></i>Minimum Skills
                </label>
                <select id="minExperience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Any</option>
                    <option value="1">1+ Skills</option>
                    <option value="3">3+ Skills</option>
                    <option value="5">5+ Skills</option>
                    <option value="10">10+ Skills</option>
                </select>
            </div>
        </div>

        <!-- Advanced Filters (Collapsible) -->
        <div id="advancedFilters" class="hidden mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Advanced Filters</h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>Location
                    </label>
                    <input type="text" id="locationFilter" placeholder="City, Country" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2"></i>Availability
                    </label>
                    <select id="availabilityFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Any</option>
                        <option value="available">Available Now</option>
                        <option value="busy">Currently Busy</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sort mr-2"></i>Sort By
                    </label>
                    <select id="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="updated_at">Last Active</option>
                        <option value="experience">Experience Level</option>
                        <option value="skills">Skill Count</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mt-6">
            <button onclick="performAdvancedSearch()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 relative"
                    id="primarySearchBtn">
                <span id="searchBtnText">
                    <i class="fas fa-search mr-2"></i>Smart Search
                </span>
                <div id="searchBtnLoading" class="hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Searching...
                </div>
            </button>
            <button onclick="getRecommendations()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-magic mr-2"></i>Get Recommendations
            </button>
            <button onclick="showAllTalents()"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-users mr-2"></i>Show All Available
            </button>
            <button onclick="toggleAdvancedFilters()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200"
                    id="advancedToggle">
                <i class="fas fa-cog mr-2"></i>Advanced Filters
            </button>
            <button onclick="clearAllFilters()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>Clear All
            </button>
        </div>
    </div>

    <!-- Welcome Message -->
    <div id="welcomeMessage" class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-8 mb-8 text-center">
        <div class="max-w-2xl mx-auto">
            <i class="fas fa-rocket text-4xl text-purple-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Welcome to Talent Discovery</h2>
            <p class="text-gray-600 mb-6">Find the perfect talent for your project from our extensive pool of skilled professionals who have completed courses on our LMS platform. Use advanced filters and smart search to find the perfect match.</p>
            <button onclick="showAllTalents()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                Discover Available Talents
            </button>
        </div>
    </div>

    <!-- Results Section with Virtual Scrolling -->
    <div id="resultsSection" class="hidden">
        <!-- Results Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <span id="resultsTitle">Search Results</span>
                <span id="resultsCount" class="text-sm text-gray-500 ml-2"></span>
            </h2>
            <div class="flex gap-2">
                <button onclick="toggleView('grid')" id="gridViewBtn"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-th-large"></i>
                </button>
                <button onclick="toggleView('list')" id="listViewBtn"
                        class="p-2 text-purple-600 transition-colors duration-200">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Results Container with Virtual Scrolling Support -->
        <div id="resultsContainer" class="space-y-4 min-h-96">
            <!-- Dynamic talent cards will be inserted here -->
        </div>

        <!-- Progressive Loading Indicator -->
        <div id="progressiveLoading" class="hidden text-center py-4">
            <div class="inline-flex items-center text-purple-600">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading more results...
            </div>
        </div>

        <!-- Load More Button -->
        <div id="loadMoreSection" class="hidden text-center py-6">
            <button onclick="loadMoreResults()" id="loadMoreBtn"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>Load More Talents
            </button>
            <p class="text-sm text-gray-500 mt-2">
                Showing <span id="currentlyShowing">0</span> of <span id="totalResults">0</span> results
            </p>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="hidden text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Searching for talents...</p>
            <div class="text-xs text-gray-500 mt-2">
                Using optimized database queries and caching...
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-900 mb-2">No talents found</h3>
            <p class="text-gray-600 mb-4">Try adjusting your search criteria or filters.</p>
            <button onclick="clearAllFilters()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Clear All Filters
            </button>
        </div>

        <!-- Network Error State -->
        <div id="errorState" class="hidden text-center py-12">
            <i class="fas fa-exclamation-triangle text-6xl text-red-300 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Search Failed</h3>
            <p class="text-gray-600 mb-4">There was an error loading talent data. Please try again.</p>
            <button onclick="retryLastSearch()"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-redo mr-2"></i>Retry Search
            </button>
        </div>
    </div>

    <!-- Enhanced Analytics Section -->
    <div id="analyticsSection" class="mt-8 hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar mr-2"></i>Search Analytics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="analyticsCards">
                <!-- Analytics cards will be dynamically loaded -->
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Talent Profile Modal -->
<div id="talentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeTalentModal()" aria-hidden="true"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Talent Profile</h3>
                <button onclick="closeTalentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="talentModalContent" class="p-6">
                <!-- Modal content will be dynamically loaded -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Enhanced performance-focused variables
let currentView = 'list';
let currentResults = [];
let allResults = [];
let searchTimeout = null;
let currentPage = 1;
let totalPages = 1;
let isLoading = false;
let cache = new Map();
let lastSearchParams = null;
let observerInitialized = false;
let intersectionObserver = null;

// Performance metrics tracking
let performanceMetrics = {
    searches: 0,
    cacheHits: 0,
    averageResponseTime: 0,
    totalResults: 0,
    startTime: 0
};

// Common skill suggestions for auto-complete
const commonSkills = [
    'JavaScript', 'Python', 'React', 'Node.js', 'PHP', 'Laravel', 'Vue.js', 'Angular',
    'Java', 'C++', 'C#', 'Go', 'Rust', 'Swift', 'Kotlin', 'Flutter', 'Django', 'Flask',
    'Express', 'MongoDB', 'MySQL', 'PostgreSQL', 'Redis', 'Docker', 'Kubernetes',
    'AWS', 'Azure', 'GCP', 'DevOps', 'CI/CD', 'Git', 'Linux', 'Machine Learning',
    'Data Science', 'UI/UX Design', 'Figma', 'Photoshop', 'WordPress', 'Shopify'
];

// Initialize enhanced features on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initializeEnhancedFeatures();
    initializeAutoComplete();
    initializePerformanceTracking();
    initializeIntersectionObserver();

    // Auto-trigger search on input changes with debouncing
    document.getElementById('skillSearch').addEventListener('input', debouncedAutoSearch);
    document.getElementById('experienceLevel').addEventListener('change', debouncedAutoSearch);
    document.getElementById('minExperience').addEventListener('change', debouncedAutoSearch);

    // Advanced filter listeners
    setupAdvancedFilterListeners();

    // Keyboard shortcuts
    setupKeyboardShortcuts();
});

// Initialize enhanced performance features
function initializeEnhancedFeatures() {
    // Setup intersection observer for lazy loading
    if ('IntersectionObserver' in window) {
        intersectionObserver = new IntersectionObserver(handleIntersection, {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        });
    }
}

// Initialize auto-complete functionality
function initializeAutoComplete() {
    const skillInput = document.getElementById('skillSearch');
    const suggestionsDiv = document.getElementById('skillSuggestions');

    skillInput.addEventListener('input', function(e) {
        const value = e.target.value.toLowerCase();
        const lastComma = value.lastIndexOf(',');
        const currentSkill = lastComma >= 0 ? value.substring(lastComma + 1).trim() : value.trim();

        if (currentSkill.length >= 2) {
            const suggestions = commonSkills.filter(skill =>
                skill.toLowerCase().includes(currentSkill)
            ).slice(0, 8);

            if (suggestions.length > 0) {
                showSuggestions(suggestions, currentSkill, lastComma);
            } else {
                hideSuggestions();
            }
        } else {
            hideSuggestions();
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#skillSearch') && !e.target.closest('#skillSuggestions')) {
            hideSuggestions();
        }
    });
}

// Show skill suggestions
function showSuggestions(suggestions, currentSkill, lastComma) {
    const suggestionsDiv = document.getElementById('skillSuggestions');
    suggestionsDiv.innerHTML = suggestions.map(skill =>
        `<div class="px-4 py-2 hover:bg-purple-50 cursor-pointer text-sm" onclick="selectSkill('${skill}', ${lastComma})">
            ${skill}
        </div>`
    ).join('');
    suggestionsDiv.classList.remove('hidden');
}

// Hide skill suggestions
function hideSuggestions() {
    document.getElementById('skillSuggestions').classList.add('hidden');
}

// Select skill from suggestions
function selectSkill(skill, lastComma) {
    const skillInput = document.getElementById('skillSearch');
    const currentValue = skillInput.value;

    if (lastComma >= 0) {
        skillInput.value = currentValue.substring(0, lastComma + 1) + ' ' + skill + ', ';
    } else {
        skillInput.value = skill + ', ';
    }

    hideSuggestions();
    skillInput.focus();
    debouncedAutoSearch();
}

// Initialize performance tracking
function initializePerformanceTracking() {
    // Track page load time
    if (performance.mark) {
        performance.mark('talent-discovery-loaded');
    }
}

// Setup advanced filter listeners
function setupAdvancedFilterListeners() {
    const advancedInputs = ['locationFilter', 'availabilityFilter', 'sortBy'];
    advancedInputs.forEach(inputId => {
        const element = document.getElementById(inputId);
        if (element) {
            element.addEventListener('change', debouncedAutoSearch);
        }
    });
}

// Setup keyboard shortcuts
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('skillSearch').focus();
        }

        // Escape to clear search
        if (e.key === 'Escape') {
            clearAllFilters();
        }

        // Enter to search
        if (e.key === 'Enter' && e.target.id === 'skillSearch') {
            e.preventDefault();
            performAdvancedSearch();
        }
    });
}

// Initialize intersection observer for lazy loading
function initializeIntersectionObserver() {
    if (!intersectionObserver) return;

    // Observe load more trigger element
    const loadMoreSection = document.getElementById('loadMoreSection');
    if (loadMoreSection) {
        intersectionObserver.observe(loadMoreSection);
    }
}

// Handle intersection observer events
function handleIntersection(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting && entry.target.id === 'loadMoreSection') {
            // Auto-load more results when user scrolls near the bottom
            if (currentPage < totalPages && !isLoading) {
                loadMoreResults();
            }
        }
    });
}

// Enhanced debounced search with auto-trigger
const debouncedAutoSearch = debounceFunction(function() {
    if (hasActiveFilters()) {
        performAdvancedSearch();
    }
}, 800);

// Performance-optimized debounce function
function debounceFunction(func, delay = 500) {
    return function(...args) {
        clearTimeout(searchTimeout);
        showSearchIndicator(true);
        searchTimeout = setTimeout(() => {
            showSearchIndicator(false);
            func.apply(this, args);
        }, delay);
    };
}

// Check if user has active filters
function hasActiveFilters() {
    const skills = document.getElementById('skillSearch').value.trim();
    const level = document.getElementById('experienceLevel').value;
    const minExperience = document.getElementById('minExperience').value;
    const location = document.getElementById('locationFilter')?.value;
    const availability = document.getElementById('availabilityFilter')?.value;

    return skills || level || minExperience || location || availability;
}

// Show/hide search indicator
function showSearchIndicator(show) {
    const indicator = document.getElementById('searchIndicator');
    if (show) {
        indicator.classList.remove('hidden');
    } else {
        indicator.classList.add('hidden');
    }
}

// Toggle advanced filters
function toggleAdvancedFilters() {
    const filtersDiv = document.getElementById('advancedFilters');
    const toggleBtn = document.getElementById('advancedToggle');

    if (filtersDiv.classList.contains('hidden')) {
        filtersDiv.classList.remove('hidden');
        toggleBtn.innerHTML = '<i class="fas fa-cog mr-2"></i>Hide Advanced';
    } else {
        filtersDiv.classList.add('hidden');
        toggleBtn.innerHTML = '<i class="fas fa-cog mr-2"></i>Advanced Filters';
    }
}

function debounceSearch(func, delay = 500) {
    return function(...args) {
        clearTimeout(searchTimeout);
        showSearchIndicator(true);
        searchTimeout = setTimeout(() => {
            showSearchIndicator(false);
            func.apply(this, args);
        }, delay);
    };
}

// Search indicator management
function showSearchIndicator(show) {
    const indicator = document.getElementById('searchIndicator');
    if (show) {
        indicator.classList.remove('hidden');
    } else {
        indicator.classList.add('hidden');
    }
}

// Cache management for better performance
function getCacheKey(endpoint, data) {
    return endpoint + '_' + JSON.stringify(data);
}

function getCachedResult(key) {
    const cached = cache.get(key);
    if (cached && Date.now() - cached.timestamp < 300000) { // 5 minutes cache
        performanceMetrics.cacheHits++;
        return cached.data;
    }
    return null;
}

function setCachedResult(key, data) {
    cache.set(key, {
        data: data,
        timestamp: Date.now()
    });

    // Limit cache size to prevent memory issues
    if (cache.size > 50) {
        const firstKey = cache.keys().next().value;
        cache.delete(firstKey);
    }
}

// Enhanced search for talents with caching and debouncing
const debouncedSearchTalents = debounceSearch(async function() {
    const skills = document.getElementById('skillSearch').value.split(',').map(s => s.trim()).filter(s => s);
    const level = document.getElementById('experienceLevel').value;
    const minExperience = document.getElementById('minExperience').value;

    const filters = {
        skills: skills,
        level: level || undefined,
        min_experience: minExperience || undefined
    };

    await performSearch('/recruiter/discovery/search', filters, 'Search Results');
});

// Auto-search on input change
function searchTalents() {
    debouncedSearchTalents();
}

// Get recommendations
async function getRecommendations() {
    await performSearch('/recruiter/discovery/recommendations', {}, 'Recommended Talents');
}

// Show all available talents
async function showAllTalents() {
    await performSearch('/recruiter/discovery/search', {}, 'All Available Talents');
}

// Enhanced advanced search function
async function performAdvancedSearch() {
    if (isLoading) return;

    const filters = collectAllFilters();
    lastSearchParams = { ...filters };

    await performSearch('/recruiter/discovery/search', filters, 'Search Results');
}

// Collect all filter values
function collectAllFilters() {
    const skills = document.getElementById('skillSearch').value
        .split(',')
        .map(s => s.trim())
        .filter(s => s);

    const filters = {
        skills: skills,
        level: document.getElementById('experienceLevel').value || undefined,
        min_experience: document.getElementById('minExperience').value || undefined
    };

    // Advanced filters (if shown)
    if (!document.getElementById('advancedFilters').classList.contains('hidden')) {
        const location = document.getElementById('locationFilter')?.value;
        const availability = document.getElementById('availabilityFilter')?.value;
        const sortBy = document.getElementById('sortBy')?.value;

        if (location) filters.location = location;
        if (availability) filters.availability = availability;
        if (sortBy) filters.sort_by = sortBy;
    }

    return filters;
}

// Enhanced search function with caching and error handling
async function performSearch(endpoint, data, title) {
    if (isLoading) return;

    const cacheKey = getCacheKey(endpoint, data);
    const cached = getCachedResult(cacheKey);

    if (cached) {
        displayResults(cached, title);
        updatePerformanceInfo('Cache', 0);
        return;
    }

    isLoading = true;
    showLoading();
    hideStates(['emptyState', 'errorState']);

    try {
        const startTime = performance.now();
        performanceMetrics.searches++;
        performanceMetrics.startTime = startTime;

        // Update button loading state
        updateSearchButtonState(true);

        const response = await fetch(endpoint, {
            method: data.skills || data.level ? 'POST' : 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: (data.skills && data.skills.length > 0) || data.level ? JSON.stringify({
                ...data,
                per_page: 15 // Optimized page size
            }) : null
        });

        const endTime = performance.now();
        const responseTime = endTime - startTime;

        // Update metrics
        performanceMetrics.averageResponseTime =
            (performanceMetrics.averageResponseTime * (performanceMetrics.searches - 1) + responseTime) / performanceMetrics.searches;

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();

        if (result.success) {
            allResults = result.data || [];
            currentPage = 1;
            totalPages = Math.ceil(allResults.length / 6); // 6 items per page for performance

            setCachedResult(cacheKey, allResults);
            displayResultsWithPagination(allResults, title);
            updatePerformanceInfo('Network', responseTime);

            // Show analytics if results found
            if (allResults.length > 0) {
                showAnalytics(allResults);
            }

        } else {
            showErrorState(result.message || 'Search failed');
        }
    } catch (error) {
        console.error('Search error:', error);
        showErrorState('Network error: ' + error.message);
    } finally {
        isLoading = false;
        updateSearchButtonState(false);
    }
}

// Update search button loading state
function updateSearchButtonState(loading) {
    const btn = document.getElementById('primarySearchBtn');
    const textSpan = document.getElementById('searchBtnText');
    const loadingSpan = document.getElementById('searchBtnLoading');

    if (loading) {
        textSpan.classList.add('hidden');
        loadingSpan.classList.remove('hidden');
        btn.disabled = true;
    } else {
        textSpan.classList.remove('hidden');
        loadingSpan.classList.add('hidden');
        btn.disabled = false;
    }
}

// Enhanced results display with pagination and virtual scrolling
function displayResultsWithPagination(talents, title) {
    // Update UI state
    document.getElementById('welcomeMessage').classList.add('hidden');
    document.getElementById('resultsSection').classList.remove('hidden');
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('resultsTitle').textContent = title;
    document.getElementById('resultsCount').textContent = `(${talents.length} found)`;

    performanceMetrics.totalResults = talents.length;

    const container = document.getElementById('resultsContainer');

    if (talents.length === 0) {
        container.innerHTML = '';
        document.getElementById('emptyState').classList.remove('hidden');
        hideStates(['loadMoreSection']);
        return;
    }

    hideStates(['emptyState', 'errorState']);

    // Display first page of results
    const firstPageResults = talents.slice(0, 6);
    displayTalentCards(firstPageResults, container, true);

    // Setup pagination
    updatePaginationState();
}

// Display talent cards with enhanced performance
function displayTalentCards(talents, container, clearFirst = false) {
    if (clearFirst) {
        container.innerHTML = '';
    }

    // Use document fragment for efficient DOM manipulation
    const fragment = document.createDocumentFragment();

    talents.forEach((talent, index) => {
        const card = createOptimizedTalentCard(talent, index);
        fragment.appendChild(card);

        // Setup intersection observer for lazy loading images
        if (intersectionObserver) {
            const images = card.querySelectorAll('.lazy-image');
            images.forEach(img => intersectionObserver.observe(img));
        }
    });

    // Set container class based on view mode
    if (currentView === 'grid') {
        container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
    } else {
        container.className = 'space-y-4';
    }

    container.appendChild(fragment);
}

// Create optimized talent card
function createOptimizedTalentCard(talent, index) {
    const card = document.createElement('div');
    card.setAttribute('data-talent-id', talent.id);

    // Lazy load images
    const avatarSrc = talent.avatar || '/images/default-avatar.svg';
    const lazyAvatar = `<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Crect width='64' height='64' fill='%23f3f4f6'/%3E%3C/svg%3E"
                                 data-src="${avatarSrc}"
                                 alt="${talent.name}"
                                 class="lazy-image w-16 h-16 rounded-full object-cover border-2 border-white shadow-lg transition-opacity duration-300 opacity-0">`;

    if (currentView === 'grid') {
        card.className = 'bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300 p-6 transform hover:-translate-y-1';
        card.innerHTML = createGridCardHTML(talent, lazyAvatar);
    } else {
        card.className = 'bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 p-6';
        card.innerHTML = createListCardHTML(talent, lazyAvatar);
    }

    return card;
}

// Create grid view card HTML
function createGridCardHTML(talent, avatarHTML) {
    const skills = talent.skills || [];
    const displaySkills = skills.slice(0, 3);
    const remainingSkills = Math.max(0, skills.length - 3);

    return `
        <div class="text-center mb-4">
            ${avatarHTML}
            <h3 class="text-lg font-semibold text-gray-900 mt-3 mb-1">${talent.name}</h3>
            <p class="text-purple-600 font-medium">${talent.skills ? talent.skills.length + ' Skills' : 'Professional'}</p>
            ${talent.location ? `<p class="text-sm text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i>${talent.location}</p>` : ''}
        </div>

        <div class="space-y-3">
            ${talent.bio ? `<p class="text-gray-600 text-sm line-clamp-2">${talent.bio}</p>` : ''}

            <div class="flex flex-wrap gap-1">
                ${displaySkills.map(skill => `
                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">
                        ${skill.name || skill}
                    </span>
                `).join('')}
                ${remainingSkills > 0 ? `<span class="text-xs text-gray-500">+${remainingSkills} more</span>` : ''}
            </div>

            <div class="flex justify-between items-center text-sm text-gray-500">
                <span><i class="fas fa-trophy mr-1"></i>${skills.length} skills</span>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
            <button onclick="showTalentProfile(${talent.id})"
                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-user mr-2"></i>View Profile
            </button>
            <button onclick="requestTalent(${talent.id})"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-handshake mr-2"></i>Request
            </button>
        </div>
    `;
}

// Create list view card HTML
function createListCardHTML(talent, avatarHTML) {
    const skills = talent.skills || [];
    const displaySkills = skills.slice(0, 5);
    const remainingSkills = Math.max(0, skills.length - 5);

    return `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                ${avatarHTML}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">${talent.name}</h3>
                        <p class="text-purple-600 font-medium">${talent.skills ? talent.skills.length + ' Skills' : 'Professional'}</p>
                        ${talent.location ? `<p class="text-sm text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i>${talent.location}</p>` : ''}
                        ${talent.bio ? `<p class="text-gray-600 text-sm mt-2 line-clamp-2">${talent.bio}</p>` : ''}
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                        <div class="flex gap-2">
                            <button onclick="showTalentProfile(${talent.id})"
                                    class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-user mr-2"></i>Profile
                            </button>
                            <button onclick="requestTalent(${talent.id})"
                                    class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-handshake mr-2"></i>Request
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="flex flex-wrap gap-1">
                        ${displaySkills.map(skill => `
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">
                                ${skill.name || skill}
                            </span>
                        `).join('')}
                        ${remainingSkills > 0 ? `<span class="text-xs text-gray-500">+${remainingSkills} more</span>` : ''}
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                    <span><i class="fas fa-trophy mr-1"></i>${skills.length} skills total</span>
                    <span><i class="fas fa-clock mr-1"></i>Updated ${talent.last_activity || 'recently'}</span>
                </div>
            </div>
        </div>
    `;
}

// Load more results for pagination
function loadMoreResults() {
    if (isLoading || currentPage >= totalPages) return;

    currentPage++;
    const startIndex = (currentPage - 1) * 6;
    const endIndex = startIndex + 6;
    const nextPageResults = allResults.slice(startIndex, endIndex);

    document.getElementById('progressiveLoading').classList.remove('hidden');

    setTimeout(() => {
        const container = document.getElementById('resultsContainer');
        displayTalentCards(nextPageResults, container, false);
        updatePaginationState();
        document.getElementById('progressiveLoading').classList.add('hidden');
    }, 500); // Simulate loading time for better UX
}

// Update pagination state
function updatePaginationState() {
    const loadMoreSection = document.getElementById('loadMoreSection');
    const currentlyShowing = document.getElementById('currentlyShowing');
    const totalResults = document.getElementById('totalResults');

    const showingCount = Math.min(currentPage * 6, allResults.length);

    currentlyShowing.textContent = showingCount;
    totalResults.textContent = allResults.length;

    if (currentPage < totalPages) {
        loadMoreSection.classList.remove('hidden');
    } else {
        loadMoreSection.classList.add('hidden');
    }
}

// Retry last search
function retryLastSearch() {
    if (lastSearchParams) {
        performSearch('/recruiter/discovery/search', lastSearchParams, 'Search Results');
    } else {
        showAllTalents();
    }
}

// Cache management functions
function getCacheKey(endpoint, data) {
    return endpoint + '_' + JSON.stringify(data);
}

function getCachedResult(key) {
    const cached = cache.get(key);
    if (cached && Date.now() - cached.timestamp < 300000) { // 5 minutes cache
        performanceMetrics.cacheHits++;
        return cached.data;
    }
    return null;
}

function setCachedResult(key, data) {
    cache.set(key, {
        data: data,
        timestamp: Date.now()
    });

    // Limit cache size to prevent memory issues
    if (cache.size > 50) {
        const firstKey = cache.keys().next().value;
        cache.delete(firstKey);
    }
}

// UI State Management
function showLoading() {
    document.getElementById('resultsSection').classList.remove('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    hideStates(['emptyState', 'errorState', 'loadMoreSection']);
}

function hideStates(stateIds) {
    stateIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.classList.add('hidden');
        }
    });
}

function showErrorState(message) {
    const errorState = document.getElementById('errorState');
    if (errorState) {
        errorState.querySelector('p').textContent = message;
        errorState.classList.remove('hidden');
    }
    hideStates(['loadingState', 'emptyState', 'loadMoreSection']);
}

// View toggle functions
function toggleView(viewType) {
    if (currentView === viewType) return;

    currentView = viewType;

    // Update button states
    document.getElementById('gridViewBtn').className = viewType === 'grid'
        ? 'p-2 text-purple-600 transition-colors duration-200'
        : 'p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200';

    document.getElementById('listViewBtn').className = viewType === 'list'
        ? 'p-2 text-purple-600 transition-colors duration-200'
        : 'p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200';

    // Re-render current results with new view
    if (allResults.length > 0) {
        const container = document.getElementById('resultsContainer');
        const currentResults = allResults.slice(0, currentPage * 6);
        displayTalentCards(currentResults, container, true);
    }
}

// Legacy support functions (for backward compatibility)
function searchTalents() {
    performAdvancedSearch();
}

async function getRecommendations() {
    await performSearch('/recruiter/discovery/recommendations', {}, 'Recommended Talents');
}

async function showAllTalents() {
    await performSearch('/recruiter/discovery/search', {}, 'All Available Talents');
}

function clearFilters() {
    clearAllFilters();
}

function clearAllFilters() {
    // Clear basic filters
    document.getElementById('skillSearch').value = '';
    document.getElementById('experienceLevel').value = '';
    document.getElementById('minExperience').value = '';

    // Clear advanced filters
    const advancedInputs = ['minRate', 'maxRate', 'locationFilter', 'availabilityFilter'];
    advancedInputs.forEach(inputId => {
        const element = document.getElementById(inputId);
        if (element) {
            element.value = '';
        }
    });

    // Reset sort
    const sortBy = document.getElementById('sortBy');
    if (sortBy) {
        sortBy.value = 'updated_at';
    }

    // Clear results
    allResults = [];
    currentResults = [];
    currentPage = 1;
    totalPages = 1;

    // Hide results section
    document.getElementById('resultsSection').classList.add('hidden');
    document.getElementById('welcomeMessage').classList.remove('hidden');

    // Clear cache related to searches
    cache.clear();

    // Hide suggestions
    hideSuggestions();
}

// Analytics and reporting
function showAnalytics(results) {
    const analyticsSection = document.getElementById('analyticsSection');
    const analyticsCards = document.getElementById('analyticsCards');

    if (!results || results.length === 0) {
        analyticsSection.classList.add('hidden');
        return;
    }

    // Calculate analytics
    const totalTalents = results.length;
    const skillCounts = {};
    const experienceLevels = {};
    let totalHourlyRate = 0;
    let ratedTalents = 0;

    results.forEach(talent => {
        // Count skills
        if (talent.skills) {
            talent.skills.forEach(skill => {
                const skillName = skill.name || skill;
                skillCounts[skillName] = (skillCounts[skillName] || 0) + 1;
            });
        }

        // Count skill levels instead of experience levels
        const skillCount = talent.skills ? talent.skills.length : 0;
        const skillCategory = skillCount >= 10 ? 'Expert (10+ skills)' :
                             skillCount >= 5 ? 'Experienced (5-9 skills)' :
                             skillCount >= 2 ? 'Intermediate (2-4 skills)' :
                             'Beginner (0-1 skills)';
        experienceLevels[skillCategory] = (experienceLevels[skillCategory] || 0) + 1;
    });

    const topSkills = Object.entries(skillCounts)
        .sort(([,a], [,b]) => b - a)
        .slice(0, 3)
        .map(([skill]) => skill);

    analyticsCards.innerHTML = `
        <div class="text-center p-4 bg-purple-50 rounded-lg">
            <div class="text-2xl font-bold text-purple-600">${totalTalents}</div>
            <div class="text-sm text-gray-600">Total Talents</div>
        </div>
        <div class="text-center p-4 bg-blue-50 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">${topSkills.length > 0 ? topSkills[0] : 'N/A'}</div>
            <div class="text-sm text-gray-600">Top Skill</div>
        </div>
        <div class="text-center p-4 bg-orange-50 rounded-lg">
            <div class="text-2xl font-bold text-orange-600">${Math.round(performanceMetrics.averageResponseTime)}ms</div>
            <div class="text-sm text-gray-600">Search Time</div>
        </div>
    `;

    analyticsSection.classList.remove('hidden');
}

// Performance monitoring
function updatePerformanceInfo(source, responseTime) {
    performanceMetrics.totalResults = allResults.length;

}

// Modal functions for talent profiles
async function showTalentProfile(talentId) {
    try {
        const response = await fetch(`/recruiter/discovery/talent/${talentId}`);
        const result = await response.json();

        if (result.success) {
            const talent = result.data;
            document.getElementById('talentModalContent').innerHTML = createTalentProfileHTML(talent);
            document.getElementById('talentModal').classList.remove('hidden');
        } else {
            showError('Failed to load talent profile');
        }
    } catch (error) {
        showError('Error loading profile: ' + error.message);
    }
}

function closeTalentModal() {
    document.getElementById('talentModal').classList.add('hidden');
}

function createTalentProfileHTML(talent) {
    return `
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <img src="${talent.avatar || '/images/default-avatar.svg'}"
                     alt="${talent.name}"
                     class="w-20 h-20 rounded-full object-cover">
                <div>
                    <h4 class="text-xl font-semibold text-gray-900">${talent.name}</h4>
                    <p class="text-purple-600 font-medium">${talent.skills ? talent.skills.length + ' Skills' : 'Professional'}</p>
                    ${talent.location ? `<p class="text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>${talent.location}</p>` : ''}
                </div>
            </div>

            ${talent.bio ? `
                <div>
                    <h5 class="font-semibold text-gray-900 mb-2">About</h5>
                    <p class="text-gray-600">${talent.bio}</p>
                </div>
            ` : ''}

            ${talent.skills && talent.skills.length > 0 ? `
                <div>
                    <h5 class="font-semibold text-gray-900 mb-2">Skills</h5>
                    <div class="flex flex-wrap gap-2">
                        ${talent.skills.map(skill => `
                            <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">
                                ${skill.name || skill}
                            </span>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            <div class="flex gap-4 pt-4">
                <button onclick="requestTalent(${talent.id})"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                    <i class="fas fa-handshake mr-2"></i>Send Request
                </button>
                ${talent.portfolio_url ? `
                    <a href="${talent.portfolio_url}" target="_blank"
                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-lg font-medium transition-colors text-center">
                        <i class="fas fa-external-link-alt mr-2"></i>Portfolio
                    </a>
                ` : ''}
            </div>
        </div>
    `;
}

// Talent request function (placeholder)
function requestTalent(talentId) {
    // This would typically open a request modal or redirect to request page
    alert(`Request talent feature would be implemented here for talent ID: ${talentId}`);
    // Example: window.location.href = `/recruiter/request/talent/${talentId}`;
}

// Error handling
function showError(message) {
    console.error('Error:', message);
    alert('Error: ' + message);
}

// Intersection observer callback for lazy loading
function handleImageIntersection(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.classList.add('opacity-100');
                img.classList.remove('opacity-0');
                intersectionObserver.unobserve(img);
            }
        }
    });
}

// Setup image lazy loading observer
if (intersectionObserver) {
    intersectionObserver.disconnect();
    intersectionObserver = new IntersectionObserver(handleImageIntersection, {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
    });
}
</script>
@endpush
@endsection
