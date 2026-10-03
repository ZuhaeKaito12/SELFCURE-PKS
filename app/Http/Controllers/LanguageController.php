<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
            $request->session()->put('locale', $locale);
        }

        // Return to the page the toggle was clicked on. back() alone is not enough:
        // downloading a PDF report also counts as the "previous" page, so switching
        // language right after a download would just download the PDF again.
        $return = (string) $request->query('return', '');
        if ($return !== '' && $this->isSameSite($request, $return)) {
            return redirect()->to($return);
        }

        return back();
    }

    /**
     * Only allow redirects within this application (no open redirects).
     */
    private function isSameSite(Request $request, string $url): bool
    {
        // Browsers treat "/\host" like "//host", so backslashes are never allowed.
        if (str_contains($url, '\\')) {
            return false;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        $parts = parse_url($url);

        return isset($parts['host'])
            && strcasecmp($parts['host'], $request->getHost()) === 0
            && in_array($parts['scheme'] ?? '', ['http', 'https'], true);
    }
}
