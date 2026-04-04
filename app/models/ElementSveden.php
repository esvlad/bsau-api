<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Capsule\Manager as Capsule;

class ElementSveden {
	public static function getOldElementsSveden($start = 0) {
		print(date('d.m.Y H:i:s') . " Выполнено шагов - " . $start . "\r\n");

		$count = Capsule::table('b_iblock_element')->where('IBLOCK_ID', 82)->count();
		$elements = Capsule::table('b_iblock_element')->where('IBLOCK_ID', 82)->offset($start)->limit(100)->get();

		if($start < $count) $next = $start + 100;

		//$files_properties = self::getOldProperties(82, 'F');

		foreach($elements as $element){

		}

		unset($elements);

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
}