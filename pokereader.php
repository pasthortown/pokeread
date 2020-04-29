<?php
include_once('./alfabeto.php');

function traduce_to_string($hex_code) {
    $toTraduce = str_split($hex_code,2);
    $toReturn = [];
    foreach ($toTraduce as $slice) {
        if ($slice == 'ff') {
            return join('',$toReturn);
        }
        array_push($toReturn, ALFABETO[hexdec($slice)]);
    }
    return join('',$toReturn);
}

function traduce_from_string($toTraduce) {
    $toTraduceArray = str_split($toTraduce);
    $toReturn = [];
    foreach($toTraduceArray as $letra_to_traduce) {
        $indice = 0;
        $encontrado = false;
        foreach(ALFABETO as $letra_en_alfabeto) {
            if (!$encontrado) {
                $indice++;
                if ($letra_en_alfabeto == $letra_to_traduce) {
                    $encontrado = true;
                    $value = dechex($indice - 1);
                    array_push($toReturn, $value);
                }
            }            
        }
    }
    return join('',$toReturn);
}

function read_pokesave($filename) {
    // ESTRUCTURA DE LOS DATOS EN EL ARCHIVO

    //  57344 B  Game save A
    //  57344 B  Game save B
    //   8192 B  Hall of Fame
    //   4096 B  Mystery Gift/e-Reader
    //   4096 B  Recorded Battle
    // --------
    // 131072 B  TOTAL DEL ARCHIVO
    // 1K = 1024 => 131072/1024 = 128 KB

    // 1 B son dos dígitos Hexadecimales

    $archivo = @fopen($filename, "r");
    $datos = [];
    if ($archivo) {
        while (!feof($archivo)) {
           $hex = bin2hex(fread($archivo, 4));
           if ($hex != "") {
            array_push($datos, $hex);
           }
        }
        fclose($archivo);
    }
    $contenido = join('', $datos);
    $estructura = [
        "game_save_a"=>get_sections(substr($contenido, 0, getCharCountByBytesCount(57344))), 
        "game_save_b"=>get_sections(substr($contenido, getCharCountByBytesCount(57344), getCharCountByBytesCount(57344))),
        "hall_of_fame"=>substr($contenido, getCharCountByBytesCount(57344) + getCharCountByBytesCount(57344), getCharCountByBytesCount(8192)),
        "mystery_gift_e_reader"=>substr($contenido, getCharCountByBytesCount(57344) + getCharCountByBytesCount(57344) + getCharCountByBytesCount(8192), getCharCountByBytesCount(4096)),
        "recorded_battle"=>substr($contenido, getCharCountByBytesCount(57344) + getCharCountByBytesCount(57344) + getCharCountByBytesCount(8192) + getCharCountByBytesCount(4096), getCharCountByBytesCount(4096)),
    ];
    return $estructura;    
}

function getCharCountByBytesCount($size) {
    // 1 B son dos dígitos Hexadecimales
    return $size * 2;
}

function get_sections($game_save) {
    $section_size = getCharCountByBytesCount(4096); 
    $secctions = str_split($game_save, $section_size);
    $toReturn = [
        "trainer_info"=>null,
        "team_items"=>null,
        "game_sate"=>null,
        "misc_data"=>null,
        "rival_info"=>null,
        "pc_buffer_a"=>null,
        "pc_buffer_b"=>null,
        "pc_buffer_c"=>null,
        "pc_buffer_d"=>null,
        "pc_buffer_e"=>null,
        "pc_buffer_f"=>null,
        "pc_buffer_g"=>null,
        "pc_buffer_h"=>null,
        "pc_buffer_i"=>null
    ];
    foreach($secctions as $section) {
        $data = substr($section, 0, getCharCountByBytesCount(3968));
        $footer_data = substr($section, getCharCountByBytesCount(3968), getCharCountByBytesCount(116));
        $id = hexdec(orderBytesToReadAsNumber(substr($section, getCharCountByBytesCount(4084), getCharCountByBytesCount(2))));
        $checksum = substr($section, getCharCountByBytesCount(4086), getCharCountByBytesCount(7));
        $save_index = substr($section, getCharCountByBytesCount(4092), getCharCountByBytesCount(4));
        if ($id == 0) {
            $toReturn["trainer_info"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 1) {
            $toReturn["team_items"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 2) {
            $toReturn["game_sate"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 3) {
            $toReturn["misc_data"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 4) {
            $toReturn["rival_info"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 5) {
            $toReturn["pc_buffer_a"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 6) {
            $toReturn["pc_buffer_b"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 7) {
            $toReturn["pc_buffer_c"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 8) {
            $toReturn["pc_buffer_d"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 9) {
            $toReturn["pc_buffer_e"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 10) {
            $toReturn["pc_buffer_f"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 11) {
            $toReturn["pc_buffer_g"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 12) {
            $toReturn["pc_buffer_h"] = ["data"=>$data, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
        if ($id == 13) {
            $newData = substr($data,0,getCharCountByBytesCount(2000));
            $toReturn["pc_buffer_i"] = ["data"=>$newData, "id"=>$id, "checksum"=>$checksum, "save_index"=>$save_index, "footer_data"=>$footer_data];
        }
    }
    return $toReturn;
}

function orderBytesToReadAsNumber($hex) {
    $hex_pieces = str_split($hex, 2);
    return join('', array_reverse($hex_pieces));
}

function process_trainner_data($data_readed) {
    $data = $data_readed['trainer_info']['data'];
    $gender_data = substr($data, getCharCountByBytesCount(8), getCharCountByBytesCount(1));
    $gender = '';
    if (hexdec($gender_data) == 0) {
        $gender = 'CHICO';
    } else {
        $gender = 'CHICA';
    }
    $player_name = traduce_to_string(substr($data, getCharCountByBytesCount(0), getCharCountByBytesCount(7)));
    $player_gender = $gender;
    $player_trainer_id = substr($data, getCharCountByBytesCount(10), getCharCountByBytesCount(4));
    $player_time_played = hexdec(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(14), getCharCountByBytesCount(2))))."H " .
                          hexdec(substr($data, getCharCountByBytesCount(16), getCharCountByBytesCount(1)))."m ".
                          hexdec(substr($data, getCharCountByBytesCount(17), getCharCountByBytesCount(1)))."s ".
                          hexdec(substr($data, getCharCountByBytesCount(17), getCharCountByBytesCount(1)))."FRAMES";
    $player_options = process_player_options(substr($data, getCharCountByBytesCount(19), getCharCountByBytesCount(3)));
    $player_game_code = substr($data, getCharCountByBytesCount(172), getCharCountByBytesCount(4));
    $player_security_key = substr($data, getCharCountByBytesCount(2808), getCharCountByBytesCount(4));
    $toReturn = [
        "player_name"=>$player_name,
        "player_gender"=>$player_gender,
        "trainer_id"=>$player_trainer_id,
        "time_played"=>$player_time_played,
        "options"=>$player_options,
        "game_code"=>process_game_code($player_game_code),
        "security_key"=>$player_security_key,
    ];
    return $toReturn;
}

function process_game_code($game_code) {
    $toProcess = hexdec(orderBytesToReadAsNumber($game_code));
    if ($toProcess == 0) {
        return 'Ruby/Sapphire';
    }
    if ($toProcess == 1) {
        return 'FireRed/LeafGreen';
    }
    return 'Emerald';
}

function process_player_options($options) {
    $buttons_settings = hexDec(substr($options, getCharCountByBytesCount(0), getCharCountByBytesCount(1)));
    if ($buttons_settings == 0) {
        $buttons = 'HELP';
    }
    if ($buttons_settings == 1) {
        $buttons = 'LR';
    }
    if ($buttons_settings == 2) {
        $buttons = 'L=A';
    }
    $text_speed_frame = convert_hex_to_bin(substr($options, getCharCountByBytesCount(1), getCharCountByBytesCount(1)));
    $text_speed = hexdec(substr($text_speed_frame, 0, 3));
    if ($text_speed == 0) {
        $text_speed_settings = 'NORMAL';
    }
    if ($text_speed == 1) {
        $text_speed_settings = 'MEDIUM';
    }
    if ($text_speed == 2) {
        $text_speed_settings = 'FAST';
    }
    $frame_style = substr($text_speed_frame, 3, 5);
    $sound = convert_hex_to_bin(substr($options, getCharCountByBytesCount(2), getCharCountByBytesCount(1)));
    $sound_settings = [
        "sound"=>'',
        "battle_style"=>'',
        "battle_scene"=>'',
    ];
    if (substr($sound, -1, 1) == '0') {
        $sound_settings['sound'] = 'MONO';
    } else {
        $sound_settings['sound'] = 'STEREO';
    }
    if (substr($sound, -2, 1) == '0') {
        $sound_settings['battle_style'] = 'SHIFT';
    } else {
        $sound_settings['battle_style'] = 'SET';
    }
    if (substr($sound, -3, 1) == '0') {
        $sound_settings['battle_scene'] = 'ON';
    } else {
        $sound_settings['battle_scene'] = 'OFF';
    }
    $toReturn = [
        "buttons"=>$buttons,
        "text_speed"=>$text_speed_settings,
        "sound_settings"=>$sound_settings,
        "frame_style"=>$frame_style,
    ];
    return $toReturn;
}

function convert_hex_to_bin($hex) {
    $hex_digits = str_split($hex, 1);
    $bin = [
        '0000',
        '0001',
        '0010',
        '0011',
        '0100',
        '0101',
        '0110',
        '0111',
        '1000',
        '1001',
        '1010',
        '1011',
        '1100',
        '1101',
        '1110',
        '1111',
    ];
    $toReturn = '';
    foreach($hex_digits as $hex_digit) {
        $toReturn .= $bin[hexdec($hex_digit)];
    }
    return $toReturn;
}

function proccess_pc_buffer($data_readed) {
    $data = $data_readed['pc_buffer_a']['data'] .
            $data_readed['pc_buffer_b']['data'] .
            $data_readed['pc_buffer_c']['data'] .
            $data_readed['pc_buffer_d']['data'] .
            $data_readed['pc_buffer_e']['data'] .
            $data_readed['pc_buffer_f']['data'] .
            $data_readed['pc_buffer_g']['data'] .
            $data_readed['pc_buffer_h']['data'] .
            $data_readed['pc_buffer_i']['data'];
    $current_pc_box = hexdec(substr($data, 0, getCharCountByBytesCount(1)));
    $box_wallpapers = str_split(substr($data, getCharCountByBytesCount(33730), getCharCountByBytesCount(14)), 2);
    $box_names_bytes = str_split(substr($data, getCharCountByBytesCount(33604), getCharCountByBytesCount(126)),18);
    $box_names = [];
    foreach($box_names_bytes as $box_name_bytes) {
        array_push($box_names, traduce_to_string($box_name_bytes));
    }
    $pc_boxes_pokemon_list_data = str_split(substr($data, getCharCountByBytesCount(4), getCharCountByBytesCount(33600)), getCharCountByBytesCount(80));
    $pc_boxes_pokemon_list = [];
    foreach($pc_boxes_pokemon_list_data as $pokemon_data) {
        array_push($pc_boxes_pokemon_list, build_pokemon_from_data($pokemon_data));
    }
    $toReturn = [
        "current_pc_box"=>$current_pc_box,
        "pc_boxes_pokemon_list"=>$pc_boxes_pokemon_list,
        "box_names"=>$box_names,
        "box_wallpapers"=>$box_wallpapers,
    ];
    return $toReturn;
}

function build_pokemon_from_data($pokemon_data) {
    $toReturn = [
        'personality_value'=>substr($pokemon_data, 0, getCharCountByBytesCount(4)),
        'original_trainer_id'=>substr($pokemon_data, getCharCountByBytesCount(4), getCharCountByBytesCount(4)),
        'nickname'=>traduce_to_string(substr($pokemon_data, getCharCountByBytesCount(8), getCharCountByBytesCount(10))),
        'language'=>substr($pokemon_data, getCharCountByBytesCount(18), getCharCountByBytesCount(2)),
        'original_trainer_name'=>traduce_to_string(substr($pokemon_data, getCharCountByBytesCount(20), getCharCountByBytesCount(7))),
        'markings'=>substr($pokemon_data, getCharCountByBytesCount(27), getCharCountByBytesCount(1)),
        'checksum'=>substr($pokemon_data, getCharCountByBytesCount(28), getCharCountByBytesCount(2)),
        'wtf'=>substr($pokemon_data, getCharCountByBytesCount(30), getCharCountByBytesCount(2)),
        'data'=>substr($pokemon_data, getCharCountByBytesCount(32), getCharCountByBytesCount(48)),
    ];
    return $toReturn;
}

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$filename = 'ANDRES.sav';
$result = read_pokesave($filename);
$trainner_data = process_trainner_data(($result['game_save_a']));
$pokemon_boxes = proccess_pc_buffer($result['game_save_a']);
echo json_encode($trainner_data);
//echo json_encode(traduce_from_string('Luis'));
//echo json_encode(traduce_to_string("bccfc6bcbbcdbbcfcc"));