<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class IBlockProperty extends Model{
	protected $table = "iblock_property";

	public static function get(int $iblock_id) : object {
		return self::where('IBLOCK_ID', $iblock_id)->select('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'XML_ID')->orderBy('CODE')->get();
	}

	public static function getNew(int $iblock_id) : object {
		return Capsule::connection('new')->table('iblock_property')->where('IBLOCK_ID', $iblock_id)->select('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'XML_ID')->orderBy('CODE')->get();
	}

	public static function getNewID(int $iblock_id, $CODE){
		return Capsule::connection('new')->table('iblock_property')->where('IBLOCK_ID', $iblock_id)->where('CODE', $CODE)->value('ID');
	}

	public static function has(int $iblock_id) : object | bool {
		$properties = Capsule::connection('new')->table('iblock_property')->select('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'XML_ID')
		->orderBy('CODE')->where('IBLOCK_ID', $iblock_id);

		if($properties->exists()){
			return $properties->get();
		}

		return false;
	}

	public static function setNewProperty($data){
		Capsule::connection('bsau')->table('iblock_property')->insert($data);
	}
}