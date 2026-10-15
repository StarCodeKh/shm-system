<?php

namespace App\Services;

use App\Models\Settings\Status;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filters a dashboard link adds to a list, so the list shows exactly the records behind the number that was clicked.
 * The dashboard puts them in the page URL (/incentive?from=2026-01-01&to=2026-12-31&type=...), the list sends that
 * as `link` with its search, and the list's query calls ListLink::apply() for its table.
 * Each filter is written the same way as the dashboard query it mirrors (App\Models\Dashboard).
 * Unknown keys and malformed values are ignored, never passed to SQL.
 *
 *   officer            status   status id                              member status chart
 *                      registered  year of register_date              new members by year
 *                      payment  paid | unpaid, with from / to          paid / unpaid members: active members whose
 *                                                                      payables due in the range are all paid / not all paid
 *   officer_payable    status=paid, from / to, entity                 income: paid by a payment recorded in the range
 *                                                                      (officer_payment.issue_date), of a member of that institution
 *                      status=unpaid, from / to                       unpaid amount: unpaid and due in the range
 *   officer_incentive  from / to, type (one or more type names)       expense, benefits by type: issue_date in the range
 */
class ListLink
{
    /** The link keys the lists understand (anything else in the page URL is not a filter) */
    const KEYS = ['from', 'to', 'status', 'type', 'entity', 'registered', 'payment'];

    public static function apply(Builder $query, string $table, $link): void
    {
        $link = is_array($link) ? array_intersect_key($link, array_flip(self::KEYS)) : [];
        if (! $link) {
            return;
        }
        $from = self::date($link['from'] ?? null);
        $to = self::date($link['to'] ?? null);
        $model = $query->getModel()->getTable();

        switch ($table) {
            case 'officer':
                if (self::id($link['status'] ?? null)) {
                    $query->where("$model.status", self::id($link['status']));
                }
                if (self::id($link['registered'] ?? null)) {
                    $query->whereYear("$model.register_date", self::id($link['registered']));
                }
                if (in_array($link['payment'] ?? null, ['paid', 'unpaid'], true) && $from && $to) {
                    $due = fn ($q) => $q->from('officer_payable')->whereColumn('officer_payable.officer_id', "$model.id")
                        ->whereBetween('officer_payable.due_date', [$from, $to]);
                    $query->where("$model.status", Status::ACTIVE);
                    if ($link['payment'] === 'paid') {
                        $query->whereExists($due)->whereNotExists(fn ($q) => $due($q)->where('officer_payable.status', Status::UNPAID));
                    } else {
                        $query->whereExists(fn ($q) => $due($q)->where('officer_payable.status', Status::UNPAID));
                    }
                }
                break;

            case 'officer_payable':
                if (($link['status'] ?? null) === 'paid') {
                    $query->where("$model.status", Status::PAID);
                    if ($from && $to) {
                        $query->whereExists(fn ($q) => $q->from('officer_payment')->whereColumn('officer_payment.ref_payable_id', "$model.id")
                            ->whereBetween('officer_payment.issue_date', [$from.' 00:00:00', $to.' 23:59:59']));
                    }
                } elseif (($link['status'] ?? null) === 'unpaid') {
                    $query->where("$model.status", Status::UNPAID);
                    if ($from && $to) {
                        $query->whereBetween("$model.due_date", [$from, $to]);
                    }
                } elseif ($from && $to) {
                    $query->whereBetween("$model.due_date", [$from, $to]);
                }
                if (self::id($link['entity'] ?? null)) {
                    $query->whereHas('officer', fn ($q) => $q->where('entity_id', self::id($link['entity'])));
                }
                break;

            case 'officer_incentive':
                if ($from && $to) {
                    $query->whereBetween("$model.issue_date", [$from.' 00:00:00', $to.' 23:59:59']);
                }
                $types = array_values(array_filter((array) ($link['type'] ?? []), fn ($type) => is_string($type) && $type !== ''));
                if ($types) {
                    $query->whereIn("$model.request_title", $types);
                }
                break;
        }
    }

    /** A Y-m-d date, or null */
    private static function date($value): ?string
    {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) ? $value : null;
    }

    /** A positive whole number, or null */
    private static function id($value): ?int
    {
        return is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0 ? (int) $value : null;
    }
}
