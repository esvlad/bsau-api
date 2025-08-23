<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Esvlad\BsauApi\Models\FTP;

class ElementDefenseDissertation {
	public static function getElementsForFileUploads($start = 0){
		print(date('d.m.Y H:i:s') . " Выполнено шагов - " . $start . "\r\n");

		$count = Capsule::table('b_iblock_element')->where('IBLOCK_ID', 53)->count();
		$elements = Capsule::table('b_iblock_element')->where('IBLOCK_ID', 53)->offset($start)->limit(100)->get();

		if($start < $count) $next = $start + 100;

		foreach($elements as $element){
			// handler
		}

		if(empty($next)){
			print("Загрузка файлов завершена\r\n");
			return true;
		}

		self::getElementsForFileUploads($next);
	}

	public static function downloadFiles(array $FILES_ID){
		//$ftp_old = new FTP(env('FTP_HOST_OLD'), env('FTP_LOGIN_OLD'), env('FTP_PASSW_OLD'));
		
		//$ftp_new = new FTP(env('FTP_HOST_NEW'), env('FTP_LOGIN_NEW'), env('FTP_PASSW_NEW'));
	}
}