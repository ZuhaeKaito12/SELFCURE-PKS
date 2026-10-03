<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportController;
use App\Models\Assessment;
use App\Models\AssessmentDomainScore;
use App\Models\Domain;
use App\Models\User;
use App\Services\MaturityScoringService;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class SummaryReportController extends Controller
{
    /**
     * Generate a national summary report (PDF) of assessment trends
     * across every registered SME, for the administrator to download.
     * Satisfies the "generate reports" functional requirement: the
     * administrator can generate summaries of assessment trends across
     * registered clients.
     */
    public function download(MaturityScoringService $scoring): Response
    {
        $clients = User::where('role', 'sme_owner')
            ->with('latestCompletedAssessment')
            ->orderBy('business_name')
            ->get()
            ->map(function (User $client) use ($scoring) {
                $latest = $client->latestCompletedAssessment;

                return [
                    'business_name' => $client->business_name ?? $client->name,
                    'business_type' => $client->business_type,
                    'state' => $client->state,
                    'score' => $latest?->overall_score,
                    'level' => $latest?->overall_level,
                    'risk' => $scoring->riskLevelForPercentage($latest?->overall_score !== null ? (float) $latest->overall_score : null),
                ];
            });

        $domainAverages = Domain::orderBy('order')->get()->map(fn (Domain $domain) => [
            'name' => $domain->translatedName(),
            'average' => round((float) AssessmentDomainScore::where('domain_id', $domain->id)->avg('score'), 2),
        ]);

        $riskDistribution = $clients->countBy('risk');

        $pdf = Pdf::loadView('reports.summary', [
            'totalClients' => $clients->count(),
            'averageScore' => round((float) Assessment::where('status', 'completed')->avg('overall_score'), 2),
            'totalAudits' => Assessment::where('status', 'completed')->count(),
            'clients' => $clients,
            'domainAverages' => $domainAverages,
            'riskDistribution' => $riskDistribution,
            'generatedAt' => now(),
        ])->setPaper('a4');

        ReportController::addPageNumbers($pdf);

        return $pdf->download('SELFCURE-PKS-National-Summary-'.now()->format('Ymd_His').'.pdf');
    }
}
