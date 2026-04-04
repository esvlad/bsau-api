<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class IBlockSectionElement extends Model{
	protected $table = "iblock_section_element";

	public static function setSections($NEW_SECTIONS) {
		Capsule::connection('bsau3')->table('iblock_section_element')->insert($NEW_SECTIONS);
	}
}