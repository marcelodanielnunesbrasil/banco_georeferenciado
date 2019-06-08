<?php
require_once("Database.php");

$db = new Database("mysql:host=localhost;dbname=qgis",'root','');

$d = $db->prepare('SELECT ST_AsGeoJson(SHAPE) FROM qgis LIMIT 1 ');

$d->execute();

$res = $d->fetch();

$data = [];
foreach($res as $k){
  $data[] = json_decode($k, true);
}

header('Content-type: application/json;');
echo json_encode($data);