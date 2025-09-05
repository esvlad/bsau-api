<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use Esvlad\BsauApi\Models\FTP;

class ElementDefenseDissertation {
	public static function getOldElementsForFileUploads($start = 0) {
		print(date('d.m.Y H:i:s') . " Выполнено шагов - " . $start . "\r\n");

		$count = Capsule::table('b_iblock_element')->where('IBLOCK_ID', 82)->count();
		$elements = Capsule::table('b_iblock_element')->where('IBLOCK_ID', 82)->offset($start)->limit(100)->get();

		if($start < $count) $next = $start + 100;

		$files_properties = self::getOldProperties(82, 'F');

		$files_property_multiple_true = [];
		$files_property_multiple_false = [];

		foreach($files_properties as $files_property){
			if($files_property->MULTIPLE == 'Y'){
				$files_property_multiple_true[] = $files_property->ID;
			} else {
				$files_property_multiple_false[] = 'PROPERTY_' . $files_property->ID;
			}
		}

		$upload = getStorage();

		foreach($elements as $element){

			$array_files_id = [];

			$element_property_multiple_files_id = Capsule::table('b_iblock_element_prop_m82')->select('ID', 'VALUE')->where('IBLOCK_ELEMENT_ID', $element->ID)->whereIn('IBLOCK_PROPERTY_ID', $files_property_multiple_true)->whereNotNull('VALUE')->get();

			$files_property_not_multiple_files_id = Capsule::table('b_iblock_element_prop_s82')->selectRaw(implode(',', $files_property_multiple_false))->where('IBLOCK_ELEMENT_ID', $element->ID)->first();

			foreach($element_property_multiple_files_id as $multiple_files_id){
				if(!empty($multiple_files_id->VALUE)){
					$array_files_id[] = $multiple_files_id->VALUE;
				}				
			}

			foreach($files_property_multiple_false as $key => $value){
				if(!empty($files_property_not_multiple_files_id->$value)){
					$array_files_id[] = $files_property_not_multiple_files_id->$value;
				}
			}

			$files = Capsule::table('b_file')->select('ID', 'SUBDIR', 'FILE_NAME')->whereIn('ID', $array_files_id);

			if($files->exists()){
				foreach($files->get() as $file){
					$directory = '/upload/' . $file->SUBDIR;
					$file_path = $directory . '/' . $file->FILE_NAME;

					$str = 'https://old-site.bsau.ru' . $file_path;
					file_put_contents($upload . 'links.txt', PHP_EOL . $str, FILE_APPEND);
				}

				unset($files);
			}
		}

		unset($elements);
		unset($files_properties);
		unset($files_property_multiple_true);
		unset($files_property_multiple_false);

		if(empty($next)){
			print("Загрузка файлов завершена\r\n");
			return true;
		}

		self::getOldElementsForFileUploads($next);
	}

	public static function getOldProperties(int $IBLOCK_ID, string $PROPERTY_TYPE) : object {
		$iblock_properties = Capsule::table('b_iblock_property')
		->select('ID', 'CODE', 'MULTIPLE')
		->where('IBLOCK_ID', $IBLOCK_ID)
		->where('PROPERTY_TYPE', $PROPERTY_TYPE)
		->get();

		if(empty($iblock_properties)){
			throw new \Exception('Свойства отсутствуют');
		}

		return $iblock_properties;
	}

	public static function downloadFiles(array $FILES_ID) : void {
		$files = Capsule::table('b_file')->select('ID', 'SUBDIR', 'FILE_NAME')->whereIn('ID', $FILES_ID);

		//$ftp = new FTP('92.50.185.11', '50221', 'site', '55bUcPtFlNw1Lgah');



		if($files->exists()){
			foreach($files->get() as $file){
				$directory = '/upload/' . $file->SUBDIR;
				$file_path = $directory . '/' . $file->FILE_NAME;
				//if(!$ftp->ftpDirectoryExists($directory)) continue;
				//if(!$ftp->ftpFileExists($file_path)) continue;

				$path = getStorage($file->SUBDIR);
				$ftp->ftpFileDownload($path, $file->FILE_NAME, $file_path);
			}
		}
	}
}