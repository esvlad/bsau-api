<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Snema extends Model{
	//protected $connection = "snema";
	protected $table = "users_session_data";

	/*public static function get(int $ELEMENT_ID) : object {
	}*/

	public static function mergeSessionData(){
		Capsule::connection('snema')->table('education_session')->where('last', 1)->distinct()->chunkById(100, function($sessions){
			$data = [];
			foreach($sessions as $session){
				$session_id = $session->id;

				$data[] = [
					'user_id' => $session->user_id,
					'session_id' => $session_id,
					'test_default' => $session->test_default,
					'test_line' => $session->test_line,
					'test_retraining' => $session->test_retraining,
					'test_additional' => $session->test_additional,
					'protocol' => Capsule::connection('snema')->table('test_history')->where('user_id', $session->user_id)->where('session_id', $session_id)->where('protocol', 1)->count()
				];
			}

			Capsule::connection('snema')->table('users_session_data')->insert($data);
		});

		return true;
	}
}