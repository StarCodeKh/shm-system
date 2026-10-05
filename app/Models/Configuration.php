<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Configuration extends Model
{
    protected $table = 'config';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'group',
                            'name',
                            'keyword',
                            'value',
                          ];
    public $timestamps = false;

    public static function getConfigs($filter){
      $data = array();
      $whereClause = Configuration::orderBy("key","asc");
      $results = collect($whereClause->get());
      foreach($results as $row){
        $data[$row->key] = $row->value;
        // "config_address" = "test",
        // $data["config_allow_file_extension"] = $row["config_allow_file_extensio"];
        // $data["config_country_id"] = $row["config_country_id"];
        // $data["config_currency"] = $row["config_currency"];
        // $data["config_date_format"] = $row["config_date_format"];
        // $data["config_default_page"] = $row["config_default_page"];
        // $data["config_dir_image"] = $row["config_dir_image"];
        // $data["config_email"] = $row["config_email"];
        // $data["config_fax"] = $row["config_fax"];
        // $data["config_FIRE_BASE_SERVER_API_KEY"] = $row["config_FIRE_BASE_SERVER_API_KEY"];
        // $data["config_FIRE_BASE_SERVER_URL"] = $row["config_FIRE_BASE_SERVER_URL"];
        // $data["config_footer"] = $row["config_footer"];
        // $data["config_footer_template"] = $row["config_footer_template"];
        // $data["config_google_map"] = $row["config_google_map"];
        // $data["config_header_template"] = $row["config_header_template"];
        // $data["config_icon"] = $row["config_icon"];
        // $data["config_invoice_logo"] = $row["config_invoice_logo"];
        // $data["config_khmerdate"] = $row["config_khmerdate"];
        // $data["config_language"] = $row["config_language"];
        // $data["config_limit"] = $row["config_limit"];
        // $data["config_mail_engine"] = $row["config_mail_engine"];
        // $data["config_mail_parameter"] = $row["config_mail_parameter"];
        // $data["config_mail_smtp_hostname"] = $row["config_mail_smtp_hostname"];
        // $data["config_mail_smtp_password"] = $row["config_mail_smtp_password"];
        // $data["config_mail_smtp_port"] = $row["config_mail_smtp_port"];
        // $data["config_mail_smtp_time_out"] = $row["config_mail_smtp_time_out"];
        // $data["config_mail_smtp_username"] = $row["config_mail_smtp_username"];
        // $data["config_max_file_size"] = $row["config_max_file_size"];
        // $data["config_menu"] = $row["config_menu"];
        // $data["config_meta_tag_description"] = $row["config_meta_tag_description"];
        // $data["config_meta_tag_keyword"] = $row["config_meta_tag_keyword"];
        // $data["config_meta_title"] = $row["config_meta_title"];
        // $data["config_name"] = $row["config_name"];
        // $data["config_offset"] = $row["config_offset"];
        // $data["config_open_time"] = $row["config_open_time"];
      }
      return $data;
    }
}
