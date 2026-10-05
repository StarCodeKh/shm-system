<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\CompositionRequest;
use App\Models\Officer;
use App\Models\Settings\Entity;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;
class NotificationUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'notification_user';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'user_id',
                            'title',
                            'image',
                            'description',
                            'status',
                            'push_count',
                            'notification_type',
                            'remark'
                          ];
    public $timestamps = false;

    public static function getNotificationByUsers($userId, $filter){
        
        $query = DB::table("notification_user as n2u")
                ->Select('n2u.*')
                ->Where('n2u.user_id', $userId)
                ->OrderBy('id', 'desc')
                ->offset($filter["offset"])
                ->limit($filter["limit"])
                ->get();
        $notificationArr = array();
        $i = 1;
        foreach($query as $row){
          $notificationArr[] = array(
            // "id" => $row->id,
            // "user_id" => $userId,
            // "title" => $row->title,
            // "description" => $row->description,
            // "date" => $row->created_at,
            // "read" => false,
            // "image" => ""
            "id" => 1,//$row->id,
            "title" => $row->title,
            "description" => $row->description,
            // "image" =>
            // "http://162.214.126.207:8080/files/filer_public/2f/74/2f7412a8-eaa0-4e0b-a2bf-807734f34b82/sunday_event.jpg",
            "clubId" => 1,
            // "date" => $row->created_at,
            "date" => "Apr 20, 2020 | 17:46:04",
            "read" => true
          );
          $i++;
        }
        return $notificationArr;
    }
}
