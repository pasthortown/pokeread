<?php

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$vars = $_REQUEST;

$pokemon_id = $vars['id'];

$filename = './pokeapi/'.$pokemon_id.'.json';
$file = fopen($filename,'r');
$content = fread($file, filesize($filename));
fclose($file);
echo $content;