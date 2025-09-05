<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {
	function __construct() {
		$capsule = new Capsule;
		
		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => env('DBNAME'),
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8",
		    "collation" => "utf8_unicode_ci",
		    "prefix" => "",
		], 'default');

		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => env('DB2NAME'),
		    "username" => env('DB2USER'),
		    "password" => env('DB2PASS'),
		    "charset" => "utf8",
		    "collation" => "utf8_unicode_ci",
		    "prefix" => "",
		], 'secondary');

		$capsule->setAsGlobal();
		$capsule->bootEloquent();
	}
}