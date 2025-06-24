<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\TalentRequest;
use App\Models\ProjectAssignment;
use App\Models\Talent;

class FixTalentOnboarding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:talent-onboarding {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing project assignments for onboarded talent requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('=== FIX TALENT ONBOARDING ===');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        $this->newLine();

        // Find onboarded talent requests without corresponding assignments
        $problemRequests = TalentRequest::where('status', 'onboarded')
            ->whereNotNull('project_id')
            ->whereNotNull('talent_id')
            ->get()
            ->filter(function($request) {
                // Check if assignment exists
                $assignment = ProjectAssignment::where('project_id', $request->project_id)
                    ->where('talent_id', $request->talent_id)
                    ->first();
                return !$assignment;
            });

        if ($problemRequests->isEmpty()) {
            $this->info('No issues found - all onboarded requests have corresponding assignments.');
            return;
        }

        $this->info("Found {$problemRequests->count()} onboarded talent requests without assignments:");
        $this->newLine();

        foreach ($problemRequests as $request) {
            $this->line("Processing Request ID: {$request->id}");
            $this->line("  Project ID: {$request->project_id}, Talent ID: {$request->talent_id}");
            $this->line("  Project Title: {$request->project_title}");
            $this->line("  Onboarded At: {$request->onboarded_at}");

            if (!$dryRun) {
                try {                    // Extract numeric budget value
                    $budgetValue = 0;
                    if ($request->budget_range) {
                        // Extract first number from budget range like "Rp 5.000.000 - Rp 15.000.000"
                        preg_match('/[\d,]+/', str_replace('.', '', $request->budget_range), $matches);
                        if (!empty($matches)) {
                            $budgetValue = intval(str_replace(',', '', $matches[0]));
                        }
                    }

                    // Create the missing assignment
                    $assignment = ProjectAssignment::create([
                        'project_id' => $request->project_id,
                        'talent_id' => $request->talent_id,
                        'specific_role' => $request->project_title ?? 'General Role',
                        'status' => ProjectAssignment::STATUS_ACCEPTED,
                        'talent_accepted_at' => $request->onboarded_at ?? now(),
                        'assignment_notes' => 'Auto-created from onboarded talent request during fix operation',
                        'individual_budget' => $budgetValue,
                        'priority_level' => 'medium',
                        'talent_start_date' => $request->project_start_date ?? now(),
                        'talent_end_date' => $request->project_end_date ?? now()->addDays(30)
                    ]);

                    $this->info("  ✓ Created assignment ID: {$assignment->id}");

                    // Check if project can be activated
                    $this->checkAndActivateProject($request->project_id);

                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to create assignment: " . $e->getMessage());
                }
            } else {
                $this->comment("  → Would create assignment with 'accepted' status");
            }
            $this->newLine();
        }

        if (!$dryRun) {
            $this->info('Fix operation completed!');
        } else {
            $this->comment('Run without --dry-run to apply fixes');
        }
    }

    private function checkAndActivateProject($projectId)
    {
        try {
            $project = Project::find($projectId);
            if (!$project) {
                $this->warn("    Project {$projectId} not found");
                return;
            }

            $this->line("    Checking project activation for: {$project->title}");
            $this->line("    Current status: {$project->status}");

            // Only transition from 'approved' to 'active'
            if ($project->status !== Project::STATUS_APPROVED) {
                $this->line("    → No activation needed (status is not 'approved')");
                return;
            }

            // Check if all project assignments are accepted
            $totalAssignments = $project->assignments()->count();
            $acceptedAssignments = $project->assignments()
                ->where('status', ProjectAssignment::STATUS_ACCEPTED)
                ->count();

            $this->line("    Assignments: {$acceptedAssignments}/{$totalAssignments} accepted");

            // If all assignments are accepted, transition project to active
            if ($totalAssignments > 0 && $acceptedAssignments === $totalAssignments) {
                $project->update([
                    'status' => Project::STATUS_ACTIVE
                ]);
                $this->info("    ✓ Project activated!");
            } else {
                $this->line("    → Not all assignments accepted yet");
            }
        } catch (\Exception $e) {
            $this->error("    ✗ Error checking project activation: " . $e->getMessage());
        }
    }
}
