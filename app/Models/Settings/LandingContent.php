<?php

namespace App\Models\Settings;

use App\Models\Officer;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Blocks of the public landing page (/welcome), edited in Settings › Landing page:
 * the hero (title, text, picture), feature cards (icon, headline, text) and live numbers (icon, label, which count).
 * Each block is written per language; a language with no blocks of a kind shows the default language's.
 * LandingContentSeeder adds a first version in Khmer and English.
 */
class LandingContent extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'landing_content';

    protected $guarded = [];

    public const HERO = 'hero';

    public const FEATURE = 'feature';

    public const STAT = 'stat';

    public const SECTIONS = [self::HERO, self::FEATURE, self::STAT];

    // Icons offered in the form (feather icons, https://feathericons.com)
    public const ICONS = [
        'users', 'user-check', 'home', 'briefcase', 'layers', 'grid', 'bar-chart-2', 'pie-chart', 'trending-up',
        'dollar-sign', 'credit-card', 'file-text', 'folder', 'clipboard', 'check-circle', 'shield', 'lock',
        'bell', 'clock', 'calendar', 'globe', 'zap', 'award', 'star', 'heart', 'smartphone', 'database', 'settings',
    ];

    private const CACHE_KEY = 'landing.blocks';

    protected static function booted()
    {
        $forget = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($forget);
        static::deleted($forget);
    }

    /** Live numbers the page may show: public totals only, never names or amounts */
    public static function stats(): array
    {
        return [
            'officers' => fn () => Officer::query()->whereNotDeleted()->count(),
            'entities' => fn () => Entity::query()->whereNotDeleted()->count(),
            'departments' => fn () => Department::query()->whereNotDeleted()->count(),
            'users' => fn () => User::query()->whereNotDeleted()->count(),
        ];
    }

    /**
     * The page in a language: { hero, features: [...], stats: [...] }.
     * Each kind of block falls back to the default language, then to any language that has it.
     */
    public static function page(string $locale): array
    {
        $blocks = Cache::rememberForever(self::CACHE_KEY, fn () => self::query()
            ->where('status', Status::ACTIVE)
            ->orderBy('order_level')->orderBy('id')
            ->get(['landing_section', 'language_code', 'headline', 'description', 'landing_icon', 'landing_stat', 'image'])
            ->toArray());

        $languages = array_unique(array_merge([$locale], Language::codes()));
        $pick = function (string $section) use ($blocks, $languages) {
            $rows = array_values(array_filter($blocks, fn ($row) => $row['landing_section'] === $section));
            foreach ($languages as $code) {
                $inLanguage = array_values(array_filter($rows, fn ($row) => $row['language_code'] === $code));
                if ($inLanguage) {
                    return $inLanguage;
                }
            }

            return $rows ? array_values(array_filter($rows, fn ($row) => $row['language_code'] === $rows[0]['language_code'])) : [];
        };

        $block = fn ($row) => [
            'title' => $row['headline'],
            'description' => $row['description'],
            'icon' => $row['landing_icon'],
            'image' => $row['image'],
        ];
        // Counted at most every 10 minutes: the page is public
        $counts = Cache::remember('landing.counts', 600, fn () => array_map(fn ($count) => $count(), self::stats()));

        return [
            'hero' => ($hero = $pick(self::HERO)[0] ?? null) ? $block($hero) : null,
            'features' => array_map($block, $pick(self::FEATURE)),
            'stats' => array_values(array_filter(array_map(
                fn ($row) => isset($counts[$row['landing_stat']]) ? $block($row) + ['value' => $counts[$row['landing_stat']]] : null,
                $pick(self::STAT)
            ))),
        ];
    }

    public static function checkQuery($filter)
    {
        $query = self::orderByFilter($filter);
        foreach ($filter['search_field'] ?: [] as $search) {
            $field = array_key_first($search);
            if (in_array($field, ['headline', 'description', 'landing_section', 'language_code', 'landing_stat'], true)) {
                $value = $search[$field]['value'] ?? $search[$field];
                $query->where($field, 'like', '%'.$value.'%');
            }
        }

        return $query->whereNotDeleted();
    }

    public static function getData($filter)
    {
        return self::checkQuery($filter)
            ->offset(($filter['page_number'] - 1) * $filter['limit'])
            ->limit($filter['limit'])
            ->get();
    }

    public static function getTotal($filter)
    {
        return self::checkQuery($filter)->count();
    }
}
