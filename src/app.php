<?php
require_once __DIR__.'/../vendor/autoload.php';
## Load the libraries

use Dotenv\Dotenv;
use Simplon\Mysql\Mysql;
use Simplon\Mysql\PDOConnector;

# Load the configs
$loc = __DIR__.'/../';
$dotenv =  Dotenv::createImmutable($loc);
$dotenv->load();

## get json
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://dummyjson.com/todos');

$json = $response->getBody();
$jsondata = json_decode($json);


# load db config
try {
    $pdo = new PDOConnector($_ENV['DB_HOST'],$_ENV['DB_USERNAME'],$_ENV['DB_PASSWORD'],$_ENV['DB_DATABASE']);
    $pdoConn = $pdo->connect('utf8', []); // charset, options

    $dbConn = new Mysql($pdoConn);

    for ($i=0; $i < sizeof($jsondata->todos); $i++) {
        $tmp[$i]=(array)$jsondata->todos[$i];
    }

    $dbConn->insertMany("todos",$tmp);
    // clearing connection
    $dbConn = null;
    $pdoConn = null;
} catch (\PDOException $ex) {
    echo $ex->getMessage();
    die();
}