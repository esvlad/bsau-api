<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

use Esvlad\BsauApi\Models\IBlockSection;

class IBlockElement extends Model{
	protected $table = "iblock_element";

	/*public static function get(int $ELEMENT_ID) : object {
	}*/

	public static function getList($IBLOCK_ID){
		return self::where('IBLOCK_ID', $IBLOCK_ID)->get();
	}

	public static function listNew(int $IBLOCK_ID) {
		return Capsule::connection('new')->table('iblock_element')->where('IBLOCK_ID', $IBLOCK_ID)->get();
	}

	public static function getNewID($XML_ID){
		return Capsule::connection('new')->table('iblock_element')->where('XML_ID', $XML_ID)->value('ID');
	}

	public static function newEdit(int $ID, $DATA){
		Capsule::connection('new')->table('iblock_element')->where('ID', $ID)->update($DATA);
	}

	public static function getNewIDByOldID($ID){
		$XML_ID = self::where('ID', $ID);

		$NEW_ID = false;
		if($XML_ID->exists()){
			$NEW_ID = self::getNewID($XML_ID->value('XML_ID'));
		}

		return $NEW_ID;
	}

	public static function updateSection(){
		Capsule::connection('bsau3')->table('iblock_element')->orderBy('ID')->chunk(100, function($elements){
			foreach($elements as $element){
				if(!empty($element->IBLOCK_SECTION_ID) && $element->IBLOCK_SECTION_ID > 0){
					Capsule::connection('bsau3')->table('iblock_element')->where('ID', $element->ID)->update(['IBLOCK_SECTION_ID' => IBlockSection::getNewID($element->IBLOCK_SECTION_ID)]);
				}				
			}
		});
	}

	public static function setElementBsau3($data){
		Capsule::connection('bsau3')->table('iblock_element')->insert($data);
	}
}