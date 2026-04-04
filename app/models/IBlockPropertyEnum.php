<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class IBlockPropertyEnum extends Model{
	protected $table = "iblock_property_enum";

	public static function getNewIDByXMLID(int $PROPERTY_ID, $ID) {
		$XML_ID = self::where('PROPERTY_ID', $PROPERTY_ID)->where('ID', $ID)->value('XML_ID');

		return Capsule::connection('new')->table('iblock_property_enum')->where('XML_ID', $XML_ID)->value('ID');
	}

	public static function setNewPropertyEnum($data){
		Capsule::connection('bsau')->table('iblock_property_enum')->insert($data);
	}
}