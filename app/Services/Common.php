<?php

    namespace App\Services;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    use DB;
    use Auth;
    use App\User;
    class Common{

        public static function setPermissions(){
            $permissions = [
                'create officer',
                'view officer',
                'update officer',
                'delete officer',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
        }

        /**
         * The signed-in user's permissions as [name => true] (empty array when none).
         * Cached for the request: controllers call this several times per action.
         */
        public static function permission($table = null){
            static $cache = [];
            $user = Auth::user();
            if (!$user) return [];
            if (!array_key_exists($user->id, $cache)) {
                // Super Admin passes every check, even on permission names that were never created
                $cache[$user->id] = self::isSuperAdmin($user)
                    ? new FullAccess(Permission::pluck('name')->all())
                    : User::find($user->id)->getPermissionsViaRoles()
                        ->mapWithKeys(fn ($permission) => [$permission->name => true])
                        ->all();
            }
            return $cache[$user->id];
        }

        /** Whether the user (default: signed-in) has the Super Admin role from config/access.php */
        public static function isSuperAdmin($user = null){
            $user = $user ?: Auth::user();
            $role = config('access.super_admin_role');
            return $user && $role && User::find($user->id)->hasRole($role);
        }

        /** Whether a role is the Super Admin role (its permissions and name are fixed) */
        public static function isSuperAdminRole($role){
            return $role && $role->name === config('access.super_admin_role');
        }

        /** Whether the signed-in user has a permission (by name, or by a feature key in config/access.php) */
        public static function can($permission){
            $name = config('access.'.$permission, $permission);
            return !empty(self::permission()[$name]);
        }

        public static function getPermissionsViaRoles(){
            $user = User::find(Auth::user()->id);
            return $user->getPermissionsViaRoles();
        }

        public static function getPermissionsViaRolesByUser($roleId){
            $assignPermissionData = "";
            $role = Role::findById($roleId);
            $assignPermissions = $role->permissions;
            foreach ($assignPermissions as $assignPermission) {
                $assignPermissionData .= $assignPermission->name . ",";
            }
            $convertAssignPermission = explode(",", $assignPermissionData);

            $data = array();
            $permissions = Permission::get();
            $permission_data = [];
            foreach($permissions as $permission){
                if(in_array($permission->name, $convertAssignPermission)){
                    $data[$permission->name] = true;
                }else{
                    $data[$permission->name] = false;
                }
            }
            return $data;
        }

        public static function getAllPermissionData(){
            $data = array();
            $query = DB::table("permission_group")->orderBy("name")->get();
            foreach($query as $row){
                $permissions = Permission::where("group", $row->id)->orderBy("name")->get();
                $permission_data = [];
                foreach($permissions as $permission){
                    $permission_data[] = array(
                        "id" => $permission->id,
                        "name" => $permission->name,
                    );
                }
                $data[] = array(
                    "id" => $row->id,
                    "name" => $row->name,
                    "permission" => $permission_data
                );
            }
            return $data;
        }

        public static function convertDataType($case){
            // Fields with a shared choice list are dropdowns (see LookupController)
            if (\App\Http\Controllers\LookupController::has(strtolower($case->Field))) {
                return "select";
            }
            if($case->Field=='attach' || $case->Field=='file' || $case->Field=='photo' || $case->Field=='image' || $case->Field=='thumbnail'){
                $type = "file";
            }elseif($case->Field=='request_title'){
                $type = "text";
            }else{
                switch ($case->Type) {
                    case 'bigint':
                        $type = "number";
                        break;
                    case 'varchar(255)':
                        $type = "select";
                        break;
                    case 'varchar(50)':
                        $type = "select";
                        break;
                    case 'varchar(25)':
                        $type = "hidden";
                        break;
                    case 'datetime':
                        $type = "date";
                        break;
                    case 'date':
                        $type = "date";
                        break;
                    case 'float(20,4)':
                        $type = "number";
                        break;
                    case 'int':
                        $type = "number";
                        break;
                    case 'text':
                        $type = "text";
                        break;
                    case 'longtext':
                        $type = "textarea";
                        break;
                    case 'tinyint':
                        $type = "checkbox";
                        break;
                    case 'tinytext':
                        $type = "checkbox";
                        break;
                    case 'binary(255)':
                        $type = "password";
                        break;
                    case 'mediumtext':
                        $type = "file";
                        break;
                    default:
                        $type = "text";
                        break;
                }
            }
            return $type;
        }

        public static function convertDataTypeWay1($case)
        {
            // Normalize field and type
            $field = strtolower($case->Field);
            $casetype = strtolower($case->Type);
            $type = preg_replace('/\(.+\)/', '', $casetype); // remove size/precision
            // 1. Special file fields
            $fileFields = ['attach', 'file', 'photo', 'image', 'thumbnail'];
            if (in_array($field, $fileFields)) {
                return "file";
            }
            // 2. Select/lookup fields by convention (choices: the page's own list, else /api/v1/lookup/{field})
            if (\App\Http\Controllers\LookupController::has($field)) {
                return "select";
            }
            if (preg_match('/_id$/', $field) && $field!="user_id"  || preg_match('/_type$/', $field) || preg_match('/_code$/', $field) && $field != "bank_code" || preg_match('/_method$/', $field) || preg_match('/status$/', $field) || preg_match('/department$/', $field) || preg_match('/title$/', $field) && $field != "request_title" || preg_match('/bank$/', $field)) {
                return "select";
            }

            // exceptional_case fields like request_title
            $excptFields = ["request_title", "title_en", "title_kh"];
            if(in_array($field, $excptFields)){
                return "text";
            }

            $fileFieldHiddens = ['user_id'];
            if (in_array($field, $fileFieldHiddens)) {
                return "hidden";
            }
            // 3. Datatype mapping
            switch ($type) {
                case 'bigint':
                case 'int':
                    return "number"; // but _id handled above as select
                case 'varchar':
                    return "text";   // keep simple, unless _id/_type/_code
                case 'datetime':
                case 'timestamp':
                case 'date':
                    return "date";
                case 'float':
                case 'double':
                case 'decimal':
                    return "number";
                case 'text':
                case 'longtext':
                case 'mediumtext':
                    return "textarea";
                case 'tinyint':
                    return "checkbox"; // often boolean
                case 'binary':
                case 'varbinary':
                    return "password";
                default:
                    return "text"; // safe fallback
            }
        }

        public static function convertDataTypeFromComlumnBinding($field,$type)
        {
            // Normalize field and type
            $field = $field;
            $casetype = $type;
            $type = preg_replace('/\(.+\)/', '', $casetype); // remove size/precision
            // 1. Special file fields
            $fileFields = ['attach', 'file', 'photo', 'image', 'thumbnail'];
            if (in_array($field, $fileFields)) {
                return "file";
            }
            // 2. Select/lookup fields by convention (choices: the page's own list, else /api/v1/lookup/{field})
            if (\App\Http\Controllers\LookupController::has($field)) {
                return "select";
            }
            if (preg_match('/_id$/', $field) && $field!="user_id"  || preg_match('/_type$/', $field) || preg_match('/_code$/', $field) && $field != "bank_code" || preg_match('/_method$/', $field) || preg_match('/status$/', $field) || preg_match('/department$/', $field) || preg_match('/title$/', $field) || preg_match('/bank$/', $field)) {
                return "select";
            }

            $fileFieldHiddens = ['user_id'];
            if (in_array($field, $fileFieldHiddens)) {
                return "hidden";
            }
            // 3. Datatype mapping
            switch ($type) {
                case 'bigint':
                case 'int':
                    return "number"; // but _id handled above as select
                case 'varchar':
                    return "text";   // keep simple, unless _id/_type/_code
                case 'datetime':
                case 'timestamp':
                case 'date':
                    return "date";
                case 'float':
                case 'double':
                case 'decimal':
                    return "number";
                case 'text':
                case 'longtext':
                case 'mediumtext':
                    return "textarea";
                case 'tinyint':
                    return "checkbox"; // often boolean
                case 'binary':
                case 'varbinary':
                    return "password";
                default:
                    return "text"; // safe fallback
            }
        }

        protected static $customFieldTypes = [
            'expenses' => [
                'currency' => 'select',
                'status'   => 'select',
            ],
        ];

        public static function convertDataTypeWay2($case, $tableName = null)
        {
            $field = strtolower($case->Field);
            $type = strtolower($case->Type);
            $type = preg_replace('/\(.+\)/', '', $type);

            // 1. Check if defined in config
            if ($tableName && isset(self::$customFieldTypes[$tableName][$field])) {
                return self::$customFieldTypes[$tableName][$field];
            }

            // 2. File fields
            $fileFields = ['attach', 'file', 'photo', 'image', 'thumbnail'];
            if (in_array($field, $fileFields)) {
                return "file";
            }

            // 3. Datatype fallback
            switch ($type) {
                case 'int':
                case 'bigint':
                case 'decimal':
                case 'float':
                case 'double':
                    return "number";
                case 'date':
                case 'datetime':
                case 'timestamp':
                    return "date";
                case 'text':
                case 'longtext':
                case 'mediumtext':
                    return "textarea";
                case 'tinyint':
                    return "checkbox";
                default:
                    return "text";
            }
        }

        public static function getSingleOpt($model, $field_name){
            $query = $model::orderByLevel()->whereNotDeleted()->get();
            $data = array();
            foreach($query as $row){
                $data[] = array(
                    "label" => (config_language == "en")?$row->name_en:$row->name_kh,
                    "value" => (config_language == "en")?$row->name_en:$row->name_kh,
                );
            }
            return $data;
        }

        public static function getLen($var){
            $tmp = explode('.', $var);
            if(count($tmp)>1){
                return strlen($tmp[1]);
            }
        }

        public static function formatDoubleNum($num){
            $n = number_format($num, Common::getLen($num));
            return $n;
        }

        public static function getReilFraction(){
            $Currency = Currency::Where('id',2)->Select('exchange_rate')->first();
            return $Currency->exchange_rate;
        }

        public static function getCurrencyDefault($table_field){
            $Currency = Currency::Where('is_default',1)->first();
            return $Currency->$table_field;
        }

        // formatCurrentcy
        public static function FormatCurrentcy($price){
            $Currency = Currency::Where('is_default',1)->first();
            $exchange_rate = $Currency->exchange_rate;
            $fraction_unit = $Currency->fraction_unit;
            $total = floatval($price) * ($exchange_rate * $fraction_unit);
            $decimals = number_format($total,2);
            $result = $Currency->currency_sign.' '.$decimals;
            return $result;
        }

        public static function getLanguage(){
            $lang = DB::table("language")->Where('is_delete',0)->Where('is_active',1)->OrderBy('order_level','ASC')->get();
            return $lang;
        }

        // substring formart
        public static function substrTime($time){
            $newTime = date(DEFAULT_TIME_FORMAT,strtotime($time));
            return $newTime;
        }
        public static function substrTimeInput($time){
            $replace = str_replace(':', '', $time);
            $newTime = substr($replace,0,4);
            return $newTime;
        }

        // substring formart
        public static function FormatDate($datetime){
            $newDate = date("d-M-Y", strtotime($datetime));
            return $newDate;
        }
        public static function FormatDateTime($datetime){
            $newDate = date(DATE_TIME_FORMAT, strtotime($datetime));
            return $newDate;
        }
        public static function FormatDateSql(){
            return config_date_format;
        }
        public static function GetNumberOfMonth($startDate, $endDate){
            $date1 = $startDate;
            $date2 = $endDate;
            $ts1 = strtotime($date1);
            $ts2 = strtotime($date2);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;
            return $diff;
        }

        public static function SumTime ($time1, $time2) {
            $t1=explode(":",$time1);
            $t2=explode(":",$time2);
            $hours=$t1[0]+$t2[0];
            $minutes=$t1[1]+$t2[1];
            if($minutes > 59){
                $minutes=$minutes-60;
                $hours++;
            }
            if($minutes < 10){
                $minutes = "0".$minutes;
            }
            if($minutes == 0){
                $minutes = "00";
            }
            if($hours < 10){
                $hours = "0".$hours;
            }
            if($hours == 0){
                $hours = "00";
            }
            $sum=$hours.':'.$minutes;
            return $sum;
        }
        public static function TotalTimeFormat($time) {
            if(TOTAL_TIME_FORMAT=='H:i') return $time;
            else return str_replace(':','',$time);

        }

        //////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        public static function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
        {
            $datetime1 = date_create($date_1);
            $datetime2 = date_create($date_2);

            $interval = date_diff($datetime1, $datetime2);

            return $interval->format($differenceFormat);

        }

        public static function RealtimeCheckDate($uploaded_date){
            $currently_date = date('Y-m-d H:i:s');

            $diff = abs(strtotime($currently_date) - strtotime($uploaded_date));
            $years   = floor($diff / (365*60*60*24));
            $months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            $hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
            $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
            $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
            if($years == 0 && $months == 0 && $days == 0 && $hours == 0 && $minutes == 0) {
                return $seconds." seconds ago.";
            }elseif($years == 0 && $months == 0 && $days == 0 && $hours == 0) {
                return $minutes." Minutes and ".$seconds." seconds ago.";
            }elseif($years == 0 && $months == 0 && $days == 0) {
                return $hours." hours and ".$minutes." minutes ago.";
            }elseif($years == 0 && $months == 0) {
                return $days." days and ".$hours." hours ago.";
            }elseif($years == 0) {
                return $months." months and ".$days." days ago.";
            }else{
                return $years." years and ".$months." months ago.";
            }
        }

    /*
         * strTime formate must be h:m AM
         */
        public static function getTime($strTime){
            $text = explode(":",$strTime);
            $subText = explode(" ", $text[1]);
            $hour = (int)$text[0];
            $minute = (int)$subText[0];
            $format = strtoupper($subText[1]);
            if($format == "PM" && $hour != 12){
                $hour += 12;
            }
            $time = array(
                'hour'=> $hour,
                'minute'=> $minute
            );
            return $time;
        }
        //utc Duration
        public static function utcTimeDuration($location_id)
        {

            $localTime = DB::table('flight_location as l')
                ->join('utctimezone as t','l.utctimezone_id','=','t.id')
                ->select('value')
                ->where('l.id',$location_id)
                ->get();
            foreach ($localTime as $d) {
                return $d->value;
            }

        }
        //convert time
        public static function ConvertTime($d='date',$contvert_to='LC', $location_id, $date,$time) {
            $duration = helpers::utcTimeDuration($location_id);
            $nowtime = $date.' '.$time;

            if(($d=='date')&&($date==''||$date=='0000-00-00')) return '';
            else if(($d=='time')&&($time==''||$time=='00:00:00')) return '';

            if((DEFAULT_UTC_TIME==1)&&($contvert_to=='UTC')){
                if($d=='date') return date(DEFAULT_DATE_FORMAT,strtotime($date));
                else return date(DEFAULT_TIME_FORMAT,strtotime($time));
            }else{
                $new_datetime = date('Y-m-d H:i:s', strtotime($nowtime . " $duration hours"));

                $new_date = date(DEFAULT_DATE_FORMAT,strtotime($new_datetime));
                $new_time = date(DEFAULT_TIME_FORMAT,strtotime($new_datetime));

                if($d=='date') return $new_date;
                else return $new_time;

            }
        }
        //check day or night time
        public static function CheckDayNight($time){
            $day_time = strtotime(DAY_TIME);
            $night_time = strtotime(NIGHT_TIME);

            $time = substr($time, 0,2).':'.substr($time, 2,2);
            $this_time = strtotime($time);

            if(($this_time>=$night_time)||($this_time<$day_time)) return '<i class="fa fa-moon-o"></i><span style="display:none;">0</span>';
            else return '<i class="fa fa-sun-o"></i><span style="display:none;">1</span>';
        }
        //convert time from second
        public static function ConvertTimeSecond($seconds){
            $h=0;
            $i=0;
            $s=0;
            if($seconds>=3600){
                $h = floor($seconds/3600);
                $seconds = $seconds - (3600*$h);

                if($seconds>=60){
                    $i = floor(($seconds/60));
                }
                if($h<10) $h='0'.$h;
                if($i<10) $i='0'.$i;
                $time = $h.':'.$i;
            }else if($seconds>=60){
                $i = floor(($seconds/60));
                if($i<10) $i='0'.$i;
                $time = '00:'.$i;
            }else{
                $time = '00:00';
            }

            return $time;
        }

        public function convertToKhmerNumber($number){
            return $data;
            }

            /**
             * List filter from request input. Column names, sort direction and paging come from the
             * client and end up in SQL, so only plain column names are accepted, secrets can never be
             * searched (a LIKE on password would leak hashes one character at a time), and page size is capped.
             */
            public static function getFilter($obj){
                $column = '/^[a-z][a-z0-9_]*$/i';
                $secret = '/pass|token|secret|remember/i';
                $searchFields = [];
                foreach ((array) ($obj["search_field"] ?? []) as $item) {
                    if (!is_array($item)) continue;
                    $item = array_filter($item, fn ($name) => is_string($name) && preg_match($column, $name) && !preg_match($secret, $name), ARRAY_FILTER_USE_KEY);
                    if ($item) $searchFields[] = $item;
                }
                $sort = isset($obj["sort"]) && is_string($obj["sort"]) && preg_match('/^[a-z][a-z0-9_.]*$/i', $obj["sort"]) ? $obj["sort"] : config_sort;
                $order = isset($obj["order"]) && in_array(strtolower((string) $obj["order"]), ['asc', 'desc'], true) ? $obj["order"] : config_order;
                $limit = isset($obj["limit"]) ? (int) $obj["limit"] : config_limit;
                $filter = array(
                    "data" => $obj,
                    "limit" => max(1, min((int) ($limit ?: config_limit), 1000)),
                    "sort" => $sort,
                    "order" => $order,
                    "page_number" => max(1, (int) ($obj["page_number"] ?? 1)),
                    "search_field" => $searchFields ?: "",
                );
                return $filter;
            }

            public static function queryTable($db_table, $filter, $flag){
                $query = $db_table::orderByFilter($filter);
                $whereClause = $query;
                $whereClause->whereNotDeleted();
                if($flag != 1 && $flag != 2){
                    $whereClause->limit($filter["limit"]);
                    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
                }
                if($flag != 2){
                    if($filter["search_field"]){
                        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
                        $dataFields = $arraySingle;
                        foreach($dataFields as $key => $field){
                            if (array_key_exists($key, $dataFields)) {
                            $whereClause->Where($key, $dataFields[$key]);
                            }
                        }
                    }
                }
                return $whereClause;
            }

            public static function optVals($db_table, $filter, $flag){
                $data = array();
                $whereClause = Common::queryTable($db_table, $filter, $flag); // Flag == 1 mean if query result limit by qty
                $queryResult = collect($whereClause->get());
                foreach($queryResult as $row){
                    $data[] = array(
                        "label" => (config_language=="en")?$row->name_en:$row->name_kh,
                        "value" => $row->id,
                    );
                }
                return $data;
            }
    }
?>
