<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class File extends Model{
	protected $table = "file";

	public static function getNewIDByXMLID(int $ID, $folder) {
		$has_old_file = self::where('ID', $ID);
		if(!$has_old_file->exists()) return false;
		
		$old_file = $has_old_file->first();

		$has_file = Capsule::connection('new')->table('file')->where('SUBDIR', $old_file->SUBDIR)->where('FILE_NAME', $old_file->FILE_NAME);

		if(!$has_file->exists()){
			$file_id = Capsule::connection('new')->table('file')->insertGetId([
				'TIMESTAMP_X' => $old_file->TIMESTAMP_X,
				'MODULE_ID' => $old_file->MODULE_ID,
				'HEIGHT' => $old_file->HEIGHT,
				'WIDTH' => $old_file->WIDTH,
				'FILE_SIZE' => $old_file->FILE_SIZE,
				'CONTENT_TYPE' => $old_file->CONTENT_TYPE,
				'SUBDIR' => $old_file->SUBDIR,
				'FILE_NAME' => $old_file->FILE_NAME,
				'ORIGINAL_NAME' => $old_file->ORIGINAL_NAME,
				'DESCRIPTION' => $old_file->DESCRIPTION,
				'HANDLER_ID' => $old_file->HANDLER_ID,
			]);
		} else {
			$file_id = $has_file->value('ID');
		}
		

		if(!file_exists('D:\\' . $folder . '\\' . $old_file->SUBDIR . '\\' . $old_file->FILE_NAME) && file_exists('D:\bitrix\iblock\\' . $old_file->SUBDIR . '\\' . $old_file->FILE_NAME)){

			if(!file_exists('D:\\' . $folder . '\\' . $old_file->SUBDIR)){
				mkdir('D:\\' . $folder . '\\' . $old_file->SUBDIR, 0777, True);
			}

			copy('D:\bitrix\iblock\\' . $old_file->SUBDIR . '\\' . $old_file->FILE_NAME, 'D:\\' . $folder . '\\' . $old_file->SUBDIR . '\\' . $old_file->FILE_NAME);
		}

		return $file_id;
	}
}