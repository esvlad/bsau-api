<?php
ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit', '100M');

function preprint($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

require "../vendor/autoload.php";

function getStorage($path = false){
    $dir = dirname(__DIR__);

    if($path === false){
        return $dir  . '/uploads/';
    }

    if(!file_exists($dir . '/uploads/' . $path)){
        mkdir($dir . '/uploads/' . $path, 0777, True);
    }

    return $dir  . '/uploads/' . $path . '/';
}

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use Esvlad\BsauApi\Models\Database;
$db = new Database();

//use Esvlad\BsauApi\Models\ElementDefenseDissertation;
//ElementDefenseDissertation::getOldElementsForFileUploads();

$fname = 'Отзыв ФГБОУ ВО Ульяновский ГАУ_Курдюмов В.И.,Курушин В.В.pdf';
$directory = '/upload/test';
$file_path = $directory . '/' . $fname;

$path = getStorage('test');
$new_file = $path . $fname;
$url = 'https://old-site.bsau.ru/upload/iblock/009/Отзыв ФГБОУ ВО Ульяновский ГАУ_Курдюмов В.И.,Курушин В.В.pdf';


$fp = fopen($new_file, 'w');
$ftp_server = 'ftp://92.50.185.11:50221/upload/iblock/009/Отзыв ФГБОУ ВО Ульяновский ГАУ_Курдюмов В.И.,Курушин В.В.pdf';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ftp_server);
curl_setopt($ch, CURLOPT_USERPWD,'site:55bUcPtFlNw1Lgah');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
curl_setopt($ch, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
curl_setopt($ch, CURLOPT_UPLOAD, 1);
curl_setopt($ch, CURLOPT_INFILE, $fp);
$output = curl_exec($ch);
$error_no = curl_errno($ch);
//var_dump(curl_error($ch));
curl_close($ch);
fclose($fp);

print("Заполнение Базы данных сделок завершен!");