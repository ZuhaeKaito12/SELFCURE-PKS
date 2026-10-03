<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentDomainScore;
use App\Models\Domain;
use App\Models\User;
use App\Services\MaturityScoringService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, MaturityScoringService $scoring): View
    {
        $clientsQuery = User::where('role', 'sme_owner')->with('latestCompletedAssessment');

        if ($search = $request->string('search')->trim()->value()) {
            $clientsQuery->where(function ($query) use ($search) {
                $query->where('business_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('ssm_number', 'like', "%{$search}%");
            });
        }

        if ($industry = $request->string('industry')->trim()->value()) {
            $clientsQuery->where('business_type', $industry);
        }

        $allClients = (clone $clientsQuery)->get();
        $highRiskCount = $allClients->filter(function (User $client) use ($scoring) {
            $score = $client->latestCompletedAssessment?->overall_score;

            return $score !== null && $scoring->riskLevelForPercentage((float) $score) === 'High Risk';
        })->count();

        $clients = $clientsQuery->orderBy('business_name')->paginate(10)->withQueryString();

        // Aggregate views across every registered SME, independent of the
        // directory's search/industry filters above.
        $everyClient = User::where('role', 'sme_owner')->with('latestCompletedAssessment')->get();
        $riskDistribution = $everyClient
            ->groupBy(function (User $client) use ($scoring) {
                $score = $client->latestCompletedAssessment?->overall_score;

                return $score !== null ? $scoring->riskLevelForPercentage((float) $score) : 'Not Assessed';
            })
            ->map->count();

        $domainAverages = Domain::orderBy('order')->get()->map(function (Domain $domain) {
            return [
                'name' => $domain->translatedName(),
                'average' => round((float) AssessmentDomainScore::where('domain_id', $domain->id)->avg('score'), 2),
            ];
        });

        return view('admin.dashboard', [
            'totalClients' => User::where('role', 'sme_owner')->count(),
            'averageScore' => round((float) Assessment::where('status', 'completed')->avg('overall_score'), 2),
            'highRiskCount' => $highRiskCount,
            'totalAudits' => Assessment::where('status', 'completed')->count(),
            'clients' => $clients,
            'scoring' => $scoring,
            'businessTypes' => User::BUSINESS_TYPES,
            'riskDistribution' => $riskDistribution,
            'domainAverages' => $domainAverages,
            'hasSeenTour' => $request->user()->has_seen_tour,
        ]);
    }
}
