<?php

namespace App\Http\Controllers;

use App\Services\DataIntegrityService;
use App\Services\LMSIntegrationService;
use App\Services\TalentScoutingService;
use Illuminate\Http\Request;

class DataIntegrityController extends Controller
{
    protected $dataIntegrityService;

    public function __construct(
        LMSIntegrationService $lmsIntegrationService,
        TalentScoutingService $talentScoutingService
    ) {
        $this->dataIntegrityService = new DataIntegrityService($lmsIntegrationService, $talentScoutingService);
    }

    /**
     * Display data integrity dashboard
     */
    public function dashboard()
    {
        $report = $this->dataIntegrityService->generateDetailedReport();

        return view('admin.data_integrity.dashboard', [
            'report' => $report,
            'title' => 'Data Integrity Dashboard',
            'roles' => 'System Administrator'
        ]);
    }

    /**
     * Run data integrity checks via AJAX
     */
    public function runChecks(Request $request)
    {
        try {
            $results = $this->dataIntegrityService->runDataIntegrityChecks();

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download detailed report
     */
    public function downloadReport()
    {
        $report = $this->dataIntegrityService->generateDetailedReport();

        $filename = 'data_integrity_report_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($report)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
