<?php

namespace Esvlad\BsauApi\Models;

class FTP {	
	private $conn_id;
	private $login_result;

	function __construct(string $host, string $login, string $passw) : void {
		$this->conn_id = ftp_connect($host);
		$this->login_result = ftp_login($this->conn_id, $login, $passw);
		ftp_pasv($conn_id, true);
	}

	function __destruct() : void{
		ftp_close($this->conn_id);
	}
}