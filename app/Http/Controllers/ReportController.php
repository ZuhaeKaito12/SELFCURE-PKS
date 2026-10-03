<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Services\ComplianceReportService;
use App\Services\MaturityScoringService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Generate and stream a downloadable PDF report for a completed
     * assessment, containing the maturity scores, identified gaps, and
     * plain-language, regulation-mapped remediation recommendations.
     */
    public function download(Assessment $assessment, ComplianceReportService $reportService, MaturityScoringService $scoring): Response
    {
        abort_unless(
            $assessment->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );

        abort_unless($assessment->isCompleted(), 404);

        $assessment->load(['domainScores.domain', 'user']);

        $pdf = Pdf::loadView('reports.assessment', [
            'assessment' => $assessment,
            'recommendations' => $reportService->prioritisedRecommendations($assessment),
            'riskLevel' => $scoring->riskLevelForPercentage((float) $assessment->overall_score),
        ])->setPaper('a4');

        $filename = 'SELFCURE-PKS-Report-'.$assessment->id.'-'.$assessment->completed_at->format('Ymd').'.pdf';

        self::addPageNumbers($pdf);

        return $pdf->download($filename);
    }

    /**
     * "Page X of Y" at the bottom right of every page. Added after rendering
     * because the total page count is only known once layout is finished.
     */
    public static function addPageNumbers(\Barryvdh\DomPDF\PDF $pdf): void
    {
        $pdf->render(); // marks the wrapper as rendered, so download() does not lay out again
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica');
        $label = __('Page :current of :total', ['current' => '{PAGE_NUM}', 'total' => '{PAGE_COUNT}']);
        // Right-aligned to the 18 mm page margin (A4 is 595 x 842 pt), on the footer line.
        $sample = str_replace(['{PAGE_NUM}', '{PAGE_COUNT}'], ['88', '88'], $label);
        $width = $dompdf->getFontMetrics()->getTextWidth($sample, $font, 7.5);
        $canvas->page_text(595 - 51 - $width, 842 - 41.5, $label, $font, 7.5, [0.52, 0.56, 0.64]);
    }
}
