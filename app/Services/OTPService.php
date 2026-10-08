<?php

    namespace App\Services;
    use App\Models\Mobile\OTP;
    class OTPService{

        // One-time sign-in code: 6 random digits from a secure generator, valid for config_otp_expire.
        public static function getOTP($request){
            $randomOTP = random_int(100000, 999999);
            $isSent = OTPService::sentOTP($randomOTP, $request);
            return $isSent
                ? array("success" => true, "optCode" => $randomOTP)
                : array("success" => false);
        }

        // Saves the code. Delivery: no SMS provider is connected yet; send the SMS here when one is added
        // (the code is never written to the log).
        public static function sentOTP($otpCode, $request){
            OTP::create([
                "device_token" => $request["device_token"] ?? null,
                "telephone" => $request["telephone"],
                "otp_code" => $otpCode,
                "expired_date" => OTPService::setExpiredOTP(),
                "remark" => "New Request",
                "created_at" => date("Y-m-d H:i:s")
            ]);
            return true;
        }

        // True when the newest unused code for this phone matches and has not expired; the code is then used up.
        // The user's password is not touched (signing in with an OTP issues a token directly).
        public static function checkOTP($request){
            $otp = OTP::where("telephone", $request["telephone"] ?? null)
                ->where("is_verify", 0)
                ->orderBy("id", "desc")
                ->first();
            if (!$otp || !hash_equals((string) $otp->otp_code, (string) ($request["optCode"] ?? ""))) {
                return false;
            }
            if (strtotime($otp->expired_date) <= time()) {
                return false;
            }
            OTP::where('id', $otp->id)->update(array("is_verify" => 1));
            return true;
        }

        public static function setExpiredOTP(){
            $expiredDate = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +" . setting('config_otp_expire')));
            return $expiredDate;
        }

    }
?>
