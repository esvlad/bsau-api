<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {
	function __construct() {
		$capsule = new Capsule;
		
		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => 'old_bsau',
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8",
		    "collation" => "utf8_unicode_ci",
		    "prefix" => "b_",
		], 'default');

		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => 'new_bsau',
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8mb4",
		    "collation" => "utf8mb4_unicode_ci",
		    "prefix" => "b_",
		], 'new');

		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => 'diff_bsau',
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8mb4",
		    "collation" => "utf8mb4_unicode_ci",
		    "prefix" => "b_",
		], 'diff');

		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => 'bsau',
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8mb4",
		    "collation" => "utf8mb4_unicode_ci",
		    "prefix" => "b_",
		], 'bsau');

		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => 'bsau2',
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8mb4",
		    "collation" => "utf8mb4_unicode_ci",
		    "prefix" => "b_",
		], 'bsau2');

		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => 'bsau3',
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8mb4",
		    "collation" => "utf8mb4_unicode_ci",
		    "prefix" => "b_",
		], 'bsau3');

		$capsule->addConnection([
		    "driver" => env('DBDRIVER'),
		    "host" => env('DBHOST'),
		    "database" => 'ucsnema',
		    "username" => env('DBUSER'),
		    "password" => env('DBPASS'),
		    "charset" => "utf8mb4",
		    "collation" => "utf8mb4_unicode_ci",
		    "prefix" => "",
		], 'snema');

		$capsule->setAsGlobal();
		$capsule->bootEloquent();
	}
}