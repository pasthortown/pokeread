<?php
include_once('./pokemon_aviable.php');

$index = 1;
while($index <= sizeof(POKEMON_AVIABLE)) {
    $pokemon_data = json_decode(httpGet('http://localhost:8000/?id='.$index));
    $sprites = $pokemon_data->sprites;
    $back_default = $sprites->back_default;
    $back_female = $sprites->back_female;
    $back_shiny = $sprites->back_shiny;
    $back_shiny_female = $sprites->back_shiny_female;
    $front_default = $sprites->front_default;
    $front_female = $sprites->front_female;
    $front_shiny = $sprites->front_shiny;
    $front_shiny_female = $sprites->front_shiny_female;
    $directory = './pokeapi/'.$index.'/sprites/';
    mkdir($directory, 0777, true);
    if ($back_default !== null) {
        get_poke_image($index, 'back_default', $back_default, $directory);
    }
    if ($back_female !== null) {
        get_poke_image($index, 'back_female', $back_female, $directory);
    }
    if ($back_shiny !== null) {
        get_poke_image($index, 'back_shiny', $back_shiny, $directory);
    }
    if ($back_shiny_female !== null) {
        get_poke_image($index, 'back_shiny_female', $back_shiny_female, $directory);
    }
    if ($front_default !== null) {
        get_poke_image($index, 'front_default', $front_default, $directory);
    }
    if ($front_female !== null) {
        get_poke_image($index, 'front_female', $front_female, $directory);
    }
    if ($front_shiny !== null) {
        get_poke_image($index, 'front_shiny', $front_shiny, $directory);
    }
    if ($front_shiny_female !== null) {
        get_poke_image($index, 'front_shiny_female', $front_shiny_female, $directory);
    }
    sleep(1);
    $index++;
}

function get_poke_image($index, $image_name, $url, $directory) {
    save_image_from_url($url, $directory.$image_name.'.png');
}

function save_image_from_url($url, $saveto){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, false);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // <-- don't forget this
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // <-- and this
    $result = curl_exec($ch);
    curl_close($ch);
    $fp = fopen($saveto,'wb');
    fwrite($fp, $result);
    fclose($fp);
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