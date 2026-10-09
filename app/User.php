<?php

namespace App;

use App\Models\Officer;
use App\Models\Settings\Entity;
use App\Models\Settings\Status;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
    // class Officer extends Model implements Auditable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;
    use \OwenIt\Auditing\Auditable;

    public $guard_name = 'api';

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $fillable = [];

    // `role` is the user's one Spatie role (tbl_model_has_roles), not a column
    protected $appends = ['role'];

    public function getRoleAttribute()
    {
        return $this->roles->first()?->name;
    }

    public $timestamps = true;

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }

    // public static function
    // Columns of the users table (Settings > Users), in display order
    public static function dataFields()
    {
        return ['fullname', 'latin', 'username', 'email', 'telephone_1', 'role', 'entity_id', 'status'];
    }

    // Add / edit form of the users table. Choices for the selects come with the list response (role, gender, status).
    // Password is checked on the server: required when creating, optional when editing.
    public static function formAttributes()
    {
        return [
            ['name' => 'fullname', 'type' => 'text', 'required' => true],
            ['name' => 'latin', 'type' => 'text', 'required' => true],
            ['name' => 'role', 'type' => 'select', 'required' => true],
            // Data scope of the role: "own unit" uses entity_id, "my member record" uses officer_id
            ['name' => 'entity_id', 'type' => 'select', 'required' => false],
            ['name' => 'officer_id', 'type' => 'select', 'required' => false],
            ['name' => 'gender', 'type' => 'select', 'required' => false],
            ['name' => 'email', 'type' => 'text', 'required' => true],
            ['name' => 'telephone_1', 'type' => 'text', 'required' => true],
            ['name' => 'username', 'type' => 'text', 'required' => true],
            ['name' => 'password', 'type' => 'password', 'required' => false],
            ['name' => 'password_confirmation', 'type' => 'password', 'required' => false],
            ['name' => 'status', 'type' => 'select', 'required' => true],
            ['name' => 'receive_daily_notifications', 'type' => 'checkbox', 'required' => false],
            ['name' => 'receive_all_notification', 'type' => 'checkbox', 'required' => false],
        ];
    }

    public static function checkQuery($filter)
    {
        $data = [];
        if (isset($filter['data']['officer_id'])) {
            $query = User::with('roles', 'entity', 'status', 'officer')->where('officer_id', (int) $filter['data']['officer_id']);
        } else {
            $query = User::with('roles', 'entity', 'status')->orderByFilter($filter);
        }
        $whereClause = $query;

        if (Auth::user()->entity_id != 0 || Auth::user()->entity_id != null) {
            $whereClause->where('entity_id', Auth::user()->entity_id);
        }
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);

        if ($filter['search_field']) {
            $arraySingle = call_user_func_array('array_merge', $filter['search_field']);
            $dataFields = $arraySingle;
            if (isset($filter['search_field'])) {
                for ($i = 0; $i < count($filter['search_field']); $i++) {
                    $field = array_key_first($filter['search_field'][$i]); // array('key1', 'key2', 'key3');
                    if (array_key_exists($field, $dataFields)) {
                        $value = $filter['search_field'][$i][$field]['value'] ?? $filter['search_field'][$i][$field];
                        if ($field == 'role') {
                            $whereClause->whereHas('roles', fn ($q) => $q->where('name', 'Like', '%'.$value.'%'));
                        } else {
                            $whereClause->Where($field, 'Like', '%'.$value.'%');
                        }
                    }
                }
            }
        }

        $whereClause->whereNotDeleted();
        if (! isset($filter['data']['officer_id'])) {
        }

        return $whereClause;
    }

    public static function getData($filter)
    {
        $whereClause = User::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $whereClause = User::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }

    public static function getProfile($id)
    {
        $query = User::with('officer')->with('entity')->where('id', (int) $id);
        $whereClause = $query;
        if (Auth::user()->entity_id != 0 || Auth::user()->entity_id != null) {
            $whereClause->where('entity_id', Auth::user()->entity_id);
        }

        $data = collect($whereClause->first());

        return $data;
    }

    public static function checkHasUser($username)
    {
        $query = User::Where('username', $username)->first();
        $boolen = true;
        if ($query) {
            $boolen = true;
        } else {
            $boolen = false;
        }

        return $boolen;
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
