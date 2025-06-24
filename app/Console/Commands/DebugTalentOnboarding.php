<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\TalentRequest;
use App\Models\ProjectAssignment;
use Illuminate\Support\Facades\DB;

class DebugTalentOnboarding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:talent-onboarding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug talent onboarding inconsistencies between talent requests and project assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TALENT ONBOARDING DEBUG ===');
        $this->newLine();

        $this->checkOnboardedRequests();
        $this->checkActiveProjects();
        $this->checkInconsistencies();

        $this->newLine();
        $this->info('=== END DEBUG ===');
    }

    private function checkOnboardedRequests()
    {
        $this->info('1. Recent onboarded talent requests:');

        $requests = TalentRequest::where('status', 'onboarded')
            ->whereNotNull('project_id')
            ->with(['project', 'talent.user', 'talentUser'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        if ($requests->isEmpty()) {
            $this->warn('   No onboarded talent requests found.');
            return;
        }

        foreach ($requests as $request) {
            $this->line("   Request ID: {$request->id}, Project ID: {$request->project_id}");
            $this->line("   Talent ID: {$request->talent_id}, User ID: {$request->talent_user_id}");
            $this->line("   Title: {$request->project_title}, Status: {$request->status}");
            $this->line("   Updated: {$request->updated_at}");

            // Check corresponding project assignments
            $assignments = ProjectAssignment::where('project_id', $request->project_id)
                ->where('talent_id', $request->talent_id)
                ->get();

            $this->line("   Corresponding assignments: " . $assignments->count());
            foreach ($assignments as $assignment) {
                $this->line("     Assignment ID: {$assignment->id}, Status: {$assignment->status}");
                $this->line("     Accepted: {$assignment->talent_accepted_at}");
            }
            $this->newLine();
        }
    }

    private function checkActiveProjects()
    {
        $this->info('2. Active projects with their assignments:');

        $projects = Project::where('status', 'active')
            ->with(['assignments', 'talentRequests'])
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        if ($projects->isEmpty()) {
            $this->warn('   No active projects found.');
            return;
        }

        foreach ($projects as $project) {
            $this->line("   Project ID: {$project->id}, Title: {$project->title}, Status: {$project->status}");
            $this->line("     Assignments: {$project->assignments->count()}, Talent Requests: {$project->talentRequests->count()}");
            $this->newLine();
        }
    }

    private function checkInconsistencies()
    {
        $this->info('3. Inconsistency check - Projects with onboarded talent requests but no accepted assignments:');

        $inconsistencies = DB::select("
            SELECT DISTINCT p.id as project_id, p.title,
                   COUNT(tr.id) as onboarded_requests,
                   COUNT(pa.id) as accepted_assignments
            FROM projects p
            LEFT JOIN talent_requests tr ON p.id = tr.project_id AND tr.status = 'onboarded'
            LEFT JOIN project_assignments pa ON p.id = pa.project_id AND pa.status = 'accepted'
            WHERE p.status IN ('approved', 'active')
            GROUP BY p.id, p.title
            HAVING onboarded_requests > 0 AND accepted_assignments = 0
        ");

        if (empty($inconsistencies)) {
            $this->info('   No inconsistencies found - all onboarded requests have corresponding accepted assignments.');
            return;
        }

        foreach ($inconsistencies as $project) {
            $this->warn("   INCONSISTENCY FOUND:");
            $this->line("   Project ID: {$project->project_id}, Title: {$project->title}");
            $this->line("   Onboarded Requests: {$project->onboarded_requests}, Accepted Assignments: {$project->accepted_assignments}");

            // Get detailed talent request info for this project
            $requests = TalentRequest::where('project_id', $project->project_id)
                ->where('status', 'onboarded')
                ->get();

            foreach ($requests as $request) {
                $this->line("     Request ID: {$request->id}, Talent ID: {$request->talent_id}, User ID: {$request->talent_user_id}");
                $this->line("     Onboarded: {$request->onboarded_at}");
            }
            $this->newLine();
        }
    }
}
