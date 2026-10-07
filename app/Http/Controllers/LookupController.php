<?php

namespace App\Http\Controllers;

use App\Models\Settings\Currency;

/**
 * Choice lists for form dropdowns: GET /api/v1/lookup/{field}.
 *
 * Forms build their fields from the table columns; a dropdown whose page didn't send its choices
 * asks here by field name. To give another field a dropdown everywhere, add it to lookups().
 * Each list is [{ label, value }], the same shape the pages already send.
 */
class LookupController extends Controller
{
    protected function lookups(): array
    {
        return [
            // Columns that store the currency itself as text ("Reil", "Dollar"): value is that text
            'currency' => fn () => Currency::whereNotDeleted()->get()
                ->map(fn ($currency) => [
                    'label' => $currency->label(),
                    'value' => $currency->abbreviation ?: $currency->name_en,
                ])->values()->all(),
            // Columns that link to the currency table
            'currency_id' => fn () => Currency::getSingleOpt(),
            'from_currency' => fn () => Currency::getSingleOpt(),
            'to_currency' => fn () => Currency::getSingleOpt(),
        ];
    }

    /** Whether a field has a choice list here (forms then show it as a dropdown) */
    public static function has(string $field): bool
    {
        return array_key_exists($field, (new static)->lookups());
    }

    public function show(string $field)
    {
        $lookup = $this->lookups()[$field] ?? null;

        return $lookup
            ? response()->json($lookup())
            : response()->json(['message' => 'No choices for this field.'], 404);
    }
}
