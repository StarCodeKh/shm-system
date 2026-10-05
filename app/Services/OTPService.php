<?php 	
	
	namespace App\Services;
	use App\Models\Mobile\UserDevice;
	use App\Models\Mobile\OTP;
	use Carbon\Carbon;
	use DB;
	use Validator;
	use Auth;
	use App\User;
	use Session;
	class OTPService{
	
	    public static function getOTP($request){
			$randomOTP = mt_rand(OTP_config_limit, config_otp_start);
			$isSent = OTPService::sentOTP($randomOTP, $request);
			$data = array();
			if($isSent){
				$data = array(
					"success" => true,
					"optCode" => $randomOTP
				);
			}else{
				$data = array(
					"success" => false
				);
			}
			return $data;
		}
		
		public static function sentOTP($otpCode, $request){
			$saveOTP = OTP::create([
				"device_token" => $request["device_token"],
				"telephone" => $request["telephone"],
				"otp_code" => $otpCode,
				"expired_date" => OTPService::setExpiredOTP(),
				"remark" => "New Request",
				"created_at" => date("Y-m-d H:i:s")
			]);
			return true;
		}

		public static function checkOTP($request){
			$checkHasOTP = OTP::Where("otp_code", "=", $request["optCode"])->Where("telephone", "=", $request["telephone"])->Where("is_verify", 0)->OrderBy("id", "desc")->first();
			if($checkHasOTP){
				$checkIfExpiredOTP = OTPService::checkExpiredOTP($request["optCode"]);
				if($checkIfExpiredOTP){
					$fieldable = array(
						"is_verify" => 1
					);
					OTP::where('id', $checkHasOTP->id)->update($fieldable);
					User::where('username', $request["telephone"])->update(array("password" => bcrypt($request["optCode"])));
					return true;
				}else{
					return false;		
				}
			}else{
				return false;
			}
		}
		
		public static function setExpiredOTP(){
			$expiredDate = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +" . config_otp_expire));
			return $expiredDate;
		}

		public static function checkExpiredOTP($optCode){
			$expiredDate = OTP::Select('expired_date')->where("otp_code", $optCode)->OrderBy('id', 'desc')->first();
			$strExpiredDate = strtotime($expiredDate->expired_date);
			$strCurrentDate = strtotime(date("Y-m-d H:i:s"));
			if($strExpiredDate > $strCurrentDate){
				return true;
			}else{
				return false;
			}
		}
	
	}
?>