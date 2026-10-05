<?php

namespace App\Helper\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesCommands;

abstract class HelperController extends BaseController
{
	use DispatchesCommands, ValidatesRequests;
	
    public function convertToKhmerNumbersss($number){
		$khmerNumber = [
		   '1' => '១',
		   '2' => '២',
		   '3' => '៣',
		   '4' => '៤',
		   '5' => '៥',
		   '6' => '៦',
		   '7' => '៧',
		   '8' => '៨',
		   '9' => '៩',
		   '0' => '០',
		   '' => '០',
		   'null' => '០'
		 ];
		 $arrNumbers = str_split($number);
		 $newKhmerNumber = '';
		 foreach ($arrNumbers as $number){
			$newKhmerNumber .= $khmerNumber[$number];
		 }
		 return $newKhmerNumber;
	}

	public function test(){
		$data = "testing";
		return $data;
	}
}
