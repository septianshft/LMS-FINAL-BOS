<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock LMS Integration Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Mock LMS Integration Demo</h1>
                <p class="text-gray-600">Independent development with LMS-ready data structure</p>

                <!-- Integration Status Badge -->
                <div class="mt-4">
                    <span id="integration-status" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        🔄 Loading integration status...
                    </span>
                </div>
            </div>

            <!-- User Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select a User to Demo:</label>
                <select id="userSelect" class="border border-gray-300 rounded-md px-3 py-2 w-64">
                    <option value="">Choose a user...</option>
                    <option value="1">User ID: 1</option>
                    <option value="2">User ID: 2</option>
                    <option value="3">User ID: 3</option>
                    <option value="4">User ID: 4</option>
                    <option value="5">User ID: 5</option>
                </select>
                <button id="loadData" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Load Mock LMS Data
                </button>
            </div>

            <!-- Demo Sections -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Talent Profile -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3">📊 Talent Profile</h3>
                    <div id="talent-profile" class="text-sm text-gray-600">
                        Select a user to see their mock LMS profile
                    </div>
                </div>

                <!-- Skill Analysis -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3">🎯 Skill Analysis</h3>
                    <div id="skill-analysis" class="text-sm text-gray-600">
                        Select a user to see their skill breakdown
                    </div>
                </div>

                <!-- Score & Progress -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3">📈 Score & Progress</h3>
                    <div id="score-progress" class="text-sm text-gray-600">
                        Select a user to see their learning metrics
                    </div>
                </div>
            </div>

            <!-- API Response Details -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-3">🔧 Raw API Response (for developer reference)</h3>
                <pre id="api-response" class="bg-gray-900 text-green-400 p-4 rounded-lg text-xs overflow-x-auto">
// Select a user and click "Load Mock LMS Data" to see the API response structure
// This shows exactly what your friend's LMS should return when integrated
                </pre>
            </div>

            <!-- Integration Instructions -->
            <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 p-4">
                <h4 class="text-lg font-semibold text-blue-800 mb-2">🚀 Integration Instructions</h4>
                <div class="text-blue-700 text-sm">
                    <p class="mb-2"><strong>Current Status:</strong> Using mock data for independent development</p>
                    <p class="mb-2"><strong>When LMS is ready:</strong> Simply update the integration service to connect to real API</p>
                    <p class="mb-2"><strong>Zero Code Changes:</strong> Your application code stays the same</p>
                    <p><strong>Data Structure:</strong> Mock data matches expected LMS response format</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load integration status on page load
        loadIntegrationStatus();

        // Event listeners
        document.getElementById('loadData').addEventListener('click', loadMockLMSData);

        async function loadIntegrationStatus() {
            try {
                const response = await axios.get('/admin/lms-mock/integration-status');
                const status = response.data;

                const statusElement = document.getElementById('integration-status');
                if (status.connected) {
                    statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                    statusElement.textContent = '✅ LMS Connected';
                } else {
                    statusElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
                    statusElement.textContent = '🔄 Mock Data Mode';
                }
            } catch (error) {
                console.error('Failed to load integration status:', error);
            }
        }

        async function loadMockLMSData() {
            const userId = document.getElementById('userSelect').value;
            if (!userId) {
                alert('Please select a user first');
                return;
            }

            try {
                // Load all data in parallel
                const [profileResponse, skillsResponse, scoreResponse] = await Promise.all([
                    axios.get(`/admin/lms-mock/talent/${userId}/profile`),
                    axios.get(`/admin/lms-mock/talent/${userId}/skills`),
                    axios.get(`/admin/lms-mock/talent/${userId}/score`)
                ]);

                // Display talent profile
                displayTalentProfile(profileResponse.data.data);

                // Display skill analysis
                displaySkillAnalysis(skillsResponse.data.data);

                // Display score and progress
                displayScoreProgress(scoreResponse.data.data);

                // Show raw API response
                document.getElementById('api-response').textContent = JSON.stringify({
                    profile: profileResponse.data,
                    skills: skillsResponse.data,
                    score: scoreResponse.data
                }, null, 2);

            } catch (error) {
                console.error('Failed to load mock LMS data:', error);
                alert('Failed to load data. Check console for details.');
            }
        }

        function displayTalentProfile(profile) {
            const html = `
                <div class="space-y-2">
                    <div><strong>Overall Score:</strong> ${profile.overall_score}/100</div>
                    <div><strong>Readiness:</strong> ${profile.readiness_score}/100</div>
                    <div><strong>Market Alignment:</strong> ${profile.market_alignment}%</div>
                    <div><strong>Skills Count:</strong> ${profile.skills.length}</div>
                    <div><strong>Data Source:</strong> ${profile.data_source}</div>
                </div>
            `;
            document.getElementById('talent-profile').innerHTML = html;
        }

        function displaySkillAnalysis(skillData) {
            const categories = Object.keys(skillData.categories);
            const html = `
                <div class="space-y-2">
                    <div><strong>Total Skills:</strong> ${skillData.skills.length}</div>
                    <div><strong>Categories:</strong> ${categories.length}</div>
                    <div class="text-xs">
                        ${categories.map(cat =>
                            `<div class="mt-1"><strong>${cat}:</strong> ${skillData.categories[cat].join(', ')}</div>`
                        ).join('')}
                    </div>
                </div>
            `;
            document.getElementById('skill-analysis').innerHTML = html;
        }

        function displayScoreProgress(scoreData) {
            const progress = scoreData.learning_progress;
            const html = `
                <div class="space-y-2">
                    <div><strong>Overall Score:</strong> ${scoreData.overall_score}/100</div>
                    <div><strong>Completed Courses:</strong> ${progress.completed_courses}</div>
                    <div><strong>Total Hours:</strong> ${progress.total_hours}</div>
                    <div><strong>Certificates:</strong> ${progress.certificates}</div>
                    <div><strong>Avg Score:</strong> ${progress.avg_score}%</div>
                    <div><strong>Learning Velocity:</strong> ${progress.learning_velocity} kursus/bulan</div>
                </div>
            `;
            document.getElementById('score-progress').innerHTML = html;
        }
    </script>
</body>
</html>
