<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class IBlock extends Model {
	protected $table = "iblock";

	public static function get(int $iblock_id) : object {
		return self::find($iblock_id);
	}

	public static function has(string $name, $code = false) : object | bool {
		$iblock = Capsule::connection('new')->table('iblock')->where('NAME', $name);

		if($code !== false){
			$iblock->orWhere('CODE', $code);
		}

		if($iblock->exists()){
			return $iblock->first();
		}

		return false;
	}
}