<?php

namespace Tests\Unit;

use App\Services\MaturityScoringService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MaturityScoringServiceTest extends TestCase
{
    public static function averages(): array
    {
        return [
            'lowest answer' => [1.0, 0.0],
            'middle answer' => [3.0, 50.0],
            'highest answer' => [5.0, 100.0],
            'mixed answers' => [2.75, 43.75],
        ];
    }

    #[DataProvider('averages')]
    public function test_average_is_converted_to_a_percentage(float $average, float $expected): void
    {
        $this->assertSame($expected, (new MaturityScoringService)->toPercentage($average));
    }

    public static function levels(): array
    {
        return [
            [0.0, 'Initial'],
            [39.99, 'Initial'],
            [40.0, 'Developing'],
            [64.99, 'Developing'],
            [65.0, 'Managed'],
            [84.99, 'Managed'],
            [85.0, 'Optimized'],
            [100.0, 'Optimized'],
        ];
    }

    #[DataProvider('levels')]
    public function test_maturity_level_boundaries(float $percentage, string $expected): void
    {
        $this->assertSame($expected, (new MaturityScoringService)->levelForPercentage($percentage));
    }

    public static function risks(): array
    {
        return [
            [null, 'Not Assessed'],
            [39.99, 'High Risk'],
            [40.0, 'Medium Risk'],
            [64.99, 'Medium Risk'],
            [65.0, 'Low Risk'],
        ];
    }

    #[DataProvider('risks')]
    public function test_risk_level_boundaries(?float $percentage, string $expected): void
    {
        $this->assertSame($expected, (new MaturityScoringService)->riskLevelForPercentage($percentage));
    }
}
