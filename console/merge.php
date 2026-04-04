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

use Esvlad\BsauApi\Models\MergeElement;

try {
    //MergeElement::setProperty(107, 59, 'matteh');
    //MergeElement::mergeElementProperty(25, 65, 'news_prop'); // 84 - образование  mergeXMLID
    //MergeElement::mergeElementsSections(118, 66, 'photo_sections');
    //MergeElement::mergeElementsPicture(65, 'news_picture');
    //MergeElement::mergeElements(118, 66, 'photo_smi');
    //MergeElement::editElements(66);
    //MergeElement::editSection(66);
} catch (Exception $e) {
    echo $e->getMessage();
}

//print("Заполнение Базы данных завершен!");