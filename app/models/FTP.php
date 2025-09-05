<?php

namespace Esvlad\BsauApi\Models;

class FTP {	
	private $conn_id;
	private $login_result;

	function __construct(string $host, string $port, string $login, string $passw) {
		$this->conn_id = ftp_connect($host, $port);
		$this->login_result = ftp_login($this->conn_id, $login, $passw);
		ftp_pasv($this->conn_id, true);
	}

	public function ftpFileExists($filename){
		$file_size = ftp_size($this->conn_id, $filename);

		if ($file_size != -1) {
			return true;
		}

		return false;
	}

	public function ftpFileDownload($folder, $filename, $file){
		$handle = fopen($folder . '/' . $filename, 'w');

		if (ftp_fget($this->conn_id, $handle, $file, FTP_ASCII, 0)){
			fclose($handle);
			return true;
		}

		return false;
	}

	public function ftpDirectoryExists($dir){
		$origin = ftp_pwd($this->conn_id);

		if (@ftp_chdir($this->conn_id, $dir)) {
			ftp_chdir($this->conn_id, $origin);

			return true;
		}
	 
		return false;
	}

	function __destruct(){
		ftp_close($this->conn_id);
	}
}