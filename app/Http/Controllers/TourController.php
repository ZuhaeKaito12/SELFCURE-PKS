<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Record that the logged-in user has finished (or skipped) the guided
     * tour, so it does not auto-start again on their next login. The tour
     * can still always be replayed manually regardless of this flag.
     */
    public function complete(Request $request): JsonResponse
    {
        $request->user()->update(['has_seen_tour' => true]);

        return response()->json(['saved' => true]);
    }
}
