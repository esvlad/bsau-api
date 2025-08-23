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

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use Esvlad\BsauApi\Models\Database;
$db = new Database();

use Esvlad\BsauApi\Models\ElementDefenseDissertation;

ElementDefenseDissertation::getElementsForFileUploads();

print("Заполнение Базы данных сделок завершен!");