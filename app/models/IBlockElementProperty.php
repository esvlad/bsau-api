<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class IBlockElementProperty extends Model{
	protected $table = "iblock_element_property";

	public static function getProps(int $ELEMENT_ID) {
		return Capsule::connection('diff')->table('iblock_element_property')->where('IBLOCK_ELEMENT_ID', $ELEMENT_ID)->get();
	}

	public static function setElementPropertyBsau3($data){
		Capsule::connection('bsau3')->table('iblock_element_property')->insert($data);
	}
}