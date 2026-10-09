<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Settings\Currency;
use App\Models\Settings\Planning;
use Illuminate\Http\Request;
use CommonService;

class ConfigurationController extends Controller
{
    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth');
        $this->db_table = new Configuration;
    }

    // Settings whose value never goes to the browser (the form shows them empty: blank on save = keep)
    const SECRET = '/password|secret|api_?key|token/i';

    // Notification switches (Settings › Notifications); every other key belongs to Settings › General
    const NOTIFICATION_PREFIX = 'config_noti_';

    /**
     * All settings, for Settings › General / Notifications. Needs either section's view permission;
     * a viewer of Notifications only gets the notification switches.
     */
    public function index(Request $request)
    {
        $general = CommonService::can('setting_view');
        if (!$general && !CommonService::can('setting_notifications_view')) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        $filter = CommonService::getFilter($request->all());
        $settings = collect($this->db_table::getConfigs($filter))
            ->filter(fn ($value, $key) => $general || str_starts_with($key, self::NOTIFICATION_PREFIX));
        $secret = $settings->keys()->filter(fn ($key) => preg_match(self::SECRET, $key))->values();

        $data = array(
            "data" => $settings->map(fn ($value, $key) => $secret->contains($key) ? '' : $value)->all(),
            // Keys that have a value on the server but are not sent (the form says "leave blank to keep")
            "secret_keys" => $secret->filter(fn ($key) => (string) $settings[$key] !== '')->values(),
            "can_update" => CommonService::can($general ? 'setting_update' : 'setting_notifications_update'),
            "config_language" => array(["label" => "En", "value" => "1"], ["label" => "Kh", "value" => "2"]),
            "config_currency" => CommonService::optVals(Currency::class, $filter, 1),
            "config_date_format" => array(["label" => "Y-m-d", "value" => "Y-m-d"], ["label" => "m-d-Y", "value" => "m-d-Y"]),
            "config_planning_year" => CommonService::optVals(Planning::class, $filter, 1),
        );
        return response()->json($data);
    }

    /**
     * The current planning year (config_planning_year), e.g. { id: 4, year: 2023, name_kh, name_en }.
     */
    public function planning()
    {
        $planning = Planning::find(setting('config_planning_year'));
        return response()->json([
            'success' => (bool) $planning,
            'data' => $planning ? [
                'id' => $planning->id,
                'year' => (int) $planning->year,
                'name_kh' => $planning->name_kh,
                'name_en' => $planning->name_en,
            ] : null,
        ]);
    }

    /**
     * Save settings: [{ key: value }, ...]. Each key needs its section's update permission
     * (notification switches: setting_notifications_update, the rest: setting_update).
     * A secret sent blank keeps its current value; keys that don't exist are ignored.
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $values = array_is_list($input) ? call_user_func_array('array_merge', array_filter($input, 'is_array') ?: [[]]) : $input;
        $canGeneral = CommonService::can('setting_update');
        $canNotifications = CommonService::can('setting_notifications_update');

        $refused = [];
        foreach ($values as $key => $value) {
            if (!is_string($key) || !str_starts_with($key, 'config')) continue;
            $allowed = str_starts_with($key, self::NOTIFICATION_PREFIX) ? ($canNotifications || $canGeneral) : $canGeneral;
            if (!$allowed) {
                $refused[] = $key;
                continue;
            }
            if (preg_match(self::SECRET, $key) && ($value === null || $value === '')) continue;
            $row = $this->db_table::where('key', $key)->first();
            if ($row) $row->update(["value" => is_array($value) ? ($value['value'] ?? json_encode($value)) : $value]);
        }

        if ($refused && count($refused) === count(array_filter(array_keys($values), fn ($key) => is_string($key) && str_starts_with($key, 'config')))) {
            return response()->json(["success" => false, "message" => trans('common.msg_no_permission_access')], 403);
        }
        return response()->json([
            "success" => true,
            "message" => trans('common.msg_update_successfully'),
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $table = Configuration::find($id);
        $notification = $table && str_starts_with($table->key, self::NOTIFICATION_PREFIX);
        if (!CommonService::can('setting_view') && !($notification && CommonService::can('setting_notifications_view'))) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        if ($table && preg_match(self::SECRET, $table->key)) {
            $table->value = '';
        }
        $data = array(
            "data" => $table
        );
        return response()->json($data);
    }
}
