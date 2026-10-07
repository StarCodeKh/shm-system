<?php

namespace App\Models\Settings;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $table = 'audits';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_type',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags',
        'created_at',
        'updated_at',
    ];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public static function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from ' . DB::getTablePrefix() . (new Audit)->getTable());
        $str = "";
        for ($i = 0; $i < count($data["tables"]); $i++) {
            $str .= $data["tables"][$i]->Field . ",";
        }
        $newArr = explode(",", $str);
        array_pop($newArr);
        $dataFields = $newArr;
        return $dataFields;
    }

    public static function getSingleOpt()
    {
        $query = Audit::orderBy("updated_at")->get();
        $data = array();
        foreach ($query as $row) {
            $data[] = array(
                "label" => $row->name_kh,
                "value" => $row->name_kh,
            );
        }
        return $data;
    }

    public static function checkQuery($filter)
    {
        $order = strtolower((string) $filter["order"]) === "asc" ? "asc" : "desc"; // newest first unless asked
        $query = Audit::with("user")->orderByFilter(["sort" => $filter["sort"] ?: "id", "order" => $order]);
        $whereClause = $query;
        if ($filter["search_field"]) {
            $fields = call_user_func_array('array_merge', $filter["search_field"]);
            $value = function ($key) use ($fields) {
                if (!array_key_exists($key, $fields)) return null;
                $v = is_array($fields[$key]) ? ($fields[$key]["value"] ?? null) : $fields[$key];
                return ($v === null || $v === "") ? null : $v;
            };

            if ($keyword = $value("keyword")) {
                $whereClause->where(function ($q) use ($keyword) {
                    $like = '%' . $keyword . '%';
                    $q->where("auditable_type", "like", $like)
                        ->orWhere("event", "like", $like)
                        ->orWhere("ip_address", "like", $like)
                        ->orWhere("url", "like", $like)
                        ->orWhereHas("user", function ($u) use ($like) {
                            $u->where("username", "like", $like);
                        });
                    if (ctype_digit((string) $keyword)) {
                        $q->orWhere("auditable_id", (int) $keyword);
                    }
                });
            }
            if ($event = $value("event")) {
                $whereClause->where("event", $event);
            }
            if ($type = $value("auditable_type")) {
                $whereClause->where("auditable_type", "like", '%' . $type . '%');
            }
            if ($user = $value("user")) {
                $whereClause->whereHas("user", function ($u) use ($user) {
                    $u->where("username", "like", '%' . $user . '%');
                });
            }
            if ($from = $value("date_from")) {
                $whereClause->whereDate("created_at", ">=", $from);
            }
            if ($to = $value("date_to")) {
                $whereClause->whereDate("created_at", "<=", $to);
            }
        }
        return $whereClause;
    }

    public static function getData($filter)
    {
        $whereClause = Audit::checkQuery($filter);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);
        $data = collect($whereClause->get());
        return $data;
    }

    public static function getTotal($filter)
    {
        $whereClause = Audit::checkQuery($filter);
        $total = $whereClause->count();
        return $total;
    }
}
