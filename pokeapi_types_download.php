<?php
include_once('./pokemon_aviable.php');

$types = [];
$directory = './pokeapi/types/';
mkdir($directory, 0777, true);
$index = 1;
while($index <= sizeof(POKEMON_AVIABLE)) {
    $pokemon_data = json_decode(httpGet('http://localhost:8000/?id='.$index));
    $types_pk = $pokemon_data->types;
    foreach($types_pk as $type_pk) {
        $existe = false;
        foreach($types as $type) {
            if ($type_pk->type->name == $type->name) {
                $existe = true;
            }
        }
        if (!$existe) {
            array_push($types, $type_pk->type);
        }
    }
    $index++;
    sleep(0.5);
}

$types_to_save = [];

foreach($types as $type) {
    $data_type = httpGet($type->url);
    $newPokeFile = fopen($directory.$type->name.".json", "w") or die("Unable to open file!");
    fwrite($newPokeFile, $data_type);
    fclose($newPokeFile);
    sleep(1);
}

function httpGet( $url )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $content;
}