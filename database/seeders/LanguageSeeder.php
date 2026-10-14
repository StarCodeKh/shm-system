<?php

namespace Database\Seeders;

use App\Models\Settings\Language;
use App\Models\Settings\Status;
use Illuminate\Database\Seeder;

/**
 * Default languages of the language switch: Khmer (kh, the default) and English (en).
 * Their flags come with the app; a language added later in Settings › Language can have its own image.
 *
 * Safe to run more than once: php artisan db:seed --class=LanguageSeeder
 * A language is only added when its code is missing; ones already on a server are never changed.
 * Runs StatusSeeder first.
 */
class LanguageSeeder extends Seeder
{
    private const LANGUAGES = [
        ['code' => 'kh', 'name' => 'ខ្មែរ', 'order_level' => 1],
        ['code' => 'en', 'name' => 'English', 'order_level' => 2],
    ];

    public function run()
    {
        $this->call(StatusSeeder::class, true);
        foreach (self::LANGUAGES as $language) {
            if (Language::where('code', $language['code'])->where('status', '!=', Status::DELETED)->doesntExist()) {
                Language::create($language + ['status' => Status::ACTIVE]);
            }
        }
    }
}
