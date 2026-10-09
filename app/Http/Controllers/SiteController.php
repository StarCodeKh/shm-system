<?php

namespace App\Http\Controllers;

use App\Services\Settings;

/**
 * Public branding of the system, from Settings › General: GET /api/v1/site.
 * Read by the footer (and any screen that shows the system's name), also before sign-in.
 * Only public values: never passwords, keys or other settings.
 */
class SiteController extends Controller
{
    // Setting key => name in the response
    private const PUBLIC = [
        'config_name' => 'name',
        'config_owner' => 'owner',
        'config_website' => 'website',
        'config_footer' => 'footer',
        'config_logo' => 'logo',
        'config_telephone' => 'telephone',
        'config_email' => 'email',
    ];

    public function show()
    {
        $settings = Settings::all();
        $site = [];
        foreach (self::PUBLIC as $key => $name) {
            $value = trim((string) ($settings[$key] ?? ''));
            $site[$name] = $value !== '' ? $value : null;
        }
        // A website without a scheme ("www.mef.gov.kh") still opens as a link
        if ($site['website'] && ! preg_match('#^https?://#i', $site['website'])) {
            $site['website'] = 'https://'.$site['website'];
        }

        return response()->json(['data' => $site]);
    }
}
