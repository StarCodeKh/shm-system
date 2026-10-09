<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class NotificationUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'notification_user';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'title',
        'image',
        'description',
        'status',
        'push_count',
        'notification_type',
        'remark',
    ];

    public $timestamps = false;
}
