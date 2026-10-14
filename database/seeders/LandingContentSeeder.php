<?php

namespace Database\Seeders;

use App\Models\Settings\LandingContent;
use App\Models\Settings\Status;
use Illuminate\Database\Seeder;

/**
 * First version of the public landing page (/welcome) in Khmer and English: the hero, six feature cards
 * and four live numbers. Change it later in Settings › Landing page.
 *
 * Safe to run more than once: php artisan db:seed --class=LandingContentSeeder
 * Only adds content when a language has none yet; content already on a server is never changed.
 * Runs StatusSeeder first.
 */
class LandingContentSeeder extends Seeder
{
    private const CONTENT = [
        'kh' => [
            'hero' => ['ប្រព័ន្ធគ្រប់គ្រងសមាជិក និងហិរញ្ញវត្ថុ', 'គ្រប់គ្រងសមាជិក ការបង់ប្រាក់ ប្រាក់ឧបត្ថម្ភ និងរបាយការណ៍ ក្នុងកន្លែងតែមួយ — រហ័ស សុវត្ថិភាព និងងាយស្រួលប្រើ។'],
            'features' => [
                ['users', 'សមាជិក', 'ចុះឈ្មោះ ឯកសារ សំណើ និងនាំចូល / នាំចេញ Excel។'],
                ['credit-card', 'ការបង់ប្រាក់', 'ប្រាក់ត្រូវបង់ប្រចាំខែ ការបង់ប្រាក់ទៀងទាត់ និងប្រវត្តិ។'],
                ['award', 'ប្រាក់ឧបត្ថម្ភ', 'កំណត់ត្រាប្រាក់ឧបត្ថម្ភតាមសមាជិក អង្គភាព និងធនាគារ។'],
                ['pie-chart', 'ហិរញ្ញវត្ថុ', 'ចំណូល ចំណាយ និងតារាងគណនី។'],
                ['file-text', 'របាយការណ៍', 'របាយការណ៍ PDF ជាភាសាខ្មែរ ជាមួយតម្រងងាយៗ។'],
                ['shield', 'សុវត្ថិភាព', 'តួនាទី សិទ្ធិប្រើប្រាស់ និងកំណត់ត្រាសាវនកម្ម។'],
            ],
            'stats' => [
                ['users', 'សមាជិក', 'officers'],
                ['home', 'អង្គភាព', 'entities'],
                ['layers', 'នាយកដ្ឋាន', 'departments'],
                ['user-check', 'អ្នកប្រើប្រាស់', 'users'],
            ],
        ],
        'en' => [
            'hero' => ['Membership and finance, in one place', 'Manage members, payments, incentives and reports in one system — fast, secure and easy to use.'],
            'features' => [
                ['users', 'Members', 'Registration, documents, requests and Excel import / export.'],
                ['credit-card', 'Payments', 'Monthly payables, recurring payments and full history.'],
                ['award', 'Incentives', 'Incentive records per member, entity and bank.'],
                ['pie-chart', 'Finance', 'Income, expenses and the chart of accounts.'],
                ['file-text', 'Reports', 'Khmer PDF reports with simple filters.'],
                ['shield', 'Secure access', 'Roles, permissions and a full audit log.'],
            ],
            'stats' => [
                ['users', 'Members', 'officers'],
                ['home', 'Entities', 'entities'],
                ['layers', 'Departments', 'departments'],
                ['user-check', 'Users', 'users'],
            ],
        ],
    ];

    public function run()
    {
        $this->call(StatusSeeder::class, true);
        foreach (self::CONTENT as $code => $content) {
            if (LandingContent::where('language_code', $code)->where('status', '!=', Status::DELETED)->exists()) {
                continue;
            }
            $add = fn (array $row) => LandingContent::create($row + ['language_code' => $code, 'status' => Status::ACTIVE]);

            $add(['landing_section' => LandingContent::HERO, 'headline' => $content['hero'][0], 'description' => $content['hero'][1], 'order_level' => 1]);
            foreach ($content['features'] as $i => [$icon, $title, $description]) {
                $add(['landing_section' => LandingContent::FEATURE, 'landing_icon' => $icon, 'headline' => $title, 'description' => $description, 'order_level' => $i + 1]);
            }
            foreach ($content['stats'] as $i => [$icon, $title, $stat]) {
                $add(['landing_section' => LandingContent::STAT, 'landing_icon' => $icon, 'headline' => $title, 'landing_stat' => $stat, 'order_level' => $i + 1]);
            }
        }
    }
}
