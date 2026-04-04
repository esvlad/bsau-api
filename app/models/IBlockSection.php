<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class IBlockSection extends Model{
	protected $table = "iblock_section";

	public static function getList($IBLOCK_ID) {
		return self::where('IBLOCK_ID', $IBLOCK_ID)->get();
	}

	public static function getXmlID($ID){
		return self::where('ID', $ID)->value('XML_ID');
	}

	public static function getNewID($XML_ID){
		$has_section = Capsule::connection('new')->table('iblock_section')->where('XML_ID', $XML_ID);

		$section_id = false;
		if($has_section->exists()){
			$section_id = $has_section->value('ID');
		}

		return $section_id;
	}

	public static function getNewIDByOldID($ID){
		$XML_ID = self::getXmlID($ID);

		return self::getNewID($XML_ID);
	}

	public static function getSql($section){
		return "INSERT INTO `b_iblock_section`(`ID`, `TIMESTAMP_X`, `MODIFIED_BY`, `DATE_CREATE`, `CREATED_BY`, `IBLOCK_ID`, `IBLOCK_SECTION_ID`, `ACTIVE`, `GLOBAL_ACTIVE`, `SORT`, `NAME`, `PICTURE`, `LEFT_MARGIN`, `RIGHT_MARGIN`, `DEPTH_LEVEL`, `DESCRIPTION`, `DESCRIPTION_TYPE`, `SEARCHABLE_CONTENT`, `CODE`, `XML_ID`, `TMP_ID`, `DETAIL_PICTURE`, `SOCNET_GROUP_ID`) VALUES (NULL, '{$section['TIMESTAMP_X']}', '{$section['MODIFIED_BY']}', '{$section['DATE_CREATE']}', '{$section['CREATED_BY']}', '{$section['IBLOCK_ID']}', '{$section['IBLOCK_SECTION_ID']}', '{$section['ACTIVE']}', '{$section['GLOBAL_ACTIVE']}', '{$section['SORT']}', '{$section['NAME']}', '{$section['PICTURE']}', '{$section['LEFT_MARGIN']}', '{$section['RIGHT_MARGIN']}', '{$section['DEPTH_LEVEL']}', '{$section['DESCRIPTION']}', '{$section['DESCRIPTION_TYPE']}', '{$section['SEARCHABLE_CONTENT']}', '{$section['CODE']}', '{$section['XML_ID']}', '{$section['TMP_ID']}', '{$section['DETAIL_PICTURE']}', '{$section['SOCNET_GROUP_ID']}');";
	}

	public static function updateSectionsBsau3($NEW_IBLOCK_ID){
		Capsule::connection('bsau3')->table('iblock_section')->where('IBLOCK_ID', $NEW_IBLOCK_ID)->orderBy('ID')->chunk(100, function($sections){
			foreach($sections as $section){
				if(!empty($section->IBLOCK_SECTION_ID) && $section->IBLOCK_SECTION_ID > 0){
					$NEW_SECTION_ID = Capsule::connection('bsau3')->table('iblock_section')->where('XML_ID', $section->IBLOCK_SECTION_ID)->value('ID');

					Capsule::connection('bsau3')->table('iblock_section')->where('ID', $section->ID)->update(['IBLOCK_SECTION_ID' => $NEW_SECTION_ID]);
				}
			}
		});
	}
}