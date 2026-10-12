<?php

namespace App\Models\Settings;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Languages the system can be shown in (Settings › Language). Active ones fill the language switch
 * in the top bar and on the sign-in page, in order_level order; the first one is the default.
 * LanguageSeeder adds Khmer (kh) and English (en).
 */
class Language extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'language';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = true;

    private const CACHE_KEY = 'languages.active';

    protected static function booted()
    {
        // The switch and SetLocale read the cached list: refresh it on any change
        $forget = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($forget);
        static::deleted($forget);
    }

    /** Active languages, default first: [{ code, name, image }] (cached, read on every request by SetLocale) */
    public static function active(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => self::query()
            ->where('status', Status::ACTIVE)
            ->whereNotNull('code')
            ->orderBy('order_level')->orderBy('id')
            ->get(['code', 'name', 'image'])
            ->map(fn ($row) => ['code' => strtolower(trim($row->code)), 'name' => $row->name, 'image' => $row->image ?: null])
            ->all());
    }

    /** Codes of the active languages, default first, e.g. ['kh', 'en'] */
    public static function codes(): array
    {
        return array_column(self::active(), 'code');
    }

    public static function dataFields()
    {
        return collect(DB::select('show columns from '.DB::getTablePrefix().(new Language)->getTable()))
            ->filter(fn ($column) => $column->Null == 'NO' && $column->Field != 'id')
            ->pluck('Field')->values()->all();
    }

    public static function checkQuery($filter)
    {
        $query = Language::orderByFilter($filter);
        foreach ($filter['search_field'] ?: [] as $search) {
            $field = array_key_first($search);
            if (in_array($field, ['name', 'code'], true)) {
                $value = $search[$field]['value'] ?? $search[$field];
                $query->where($field, 'like', '%'.$value.'%');
            }
        }

        return $query->whereNotDeleted();
    }

    public static function getData($filter)
    {
        return Language::checkQuery($filter)
            ->offset(($filter['page_number'] - 1) * $filter['limit'])
            ->limit($filter['limit'])
            ->get();
    }

    public static function getTotal($filter)
    {
        return Language::checkQuery($filter)->count();
    }
}
