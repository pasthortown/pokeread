<?php
include_once('./alfabeto.php');
include_once('./pokemon_gender_ratio.php');
include_once('./pokemon_aviable.php');

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

function process_trainer_data($data_readed) {
    $data = $data_readed['trainer_info']['data'];
    $rival_data = $data_readed['rival_info']['data'];
    $gender_data = substr($data, getCharCountByBytesCount(8), getCharCountByBytesCount(1));
    $gender = '';
    if (hexdec($gender_data) == 0) {
        $gender = 'CHICO';
    } else {
        $gender = 'CHICA';
    }
    $player_name = traduce_to_string(substr($data, getCharCountByBytesCount(0), getCharCountByBytesCount(7)));
    $player_gender = $gender;
    $player_trainer_id = orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(10), getCharCountByBytesCount(4)));
    $player_time_played = hexdec(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(14), getCharCountByBytesCount(2))))."H " .
                          hexdec(substr($data, getCharCountByBytesCount(16), getCharCountByBytesCount(1)))."m ".
                          hexdec(substr($data, getCharCountByBytesCount(17), getCharCountByBytesCount(1)))."s ".
                          hexdec(substr($data, getCharCountByBytesCount(17), getCharCountByBytesCount(1)))."FRAMES";
    $player_options = process_player_options(substr($data, getCharCountByBytesCount(19), getCharCountByBytesCount(3)));
    $player_game_code = substr($data, getCharCountByBytesCount(172), getCharCountByBytesCount(4));
    $player_security_key = orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(2808), getCharCountByBytesCount(4)));
    $version_game = process_game_code($player_game_code);
    $rival_name = 'N/A';
    if ($version_game == 'FireRed/LeafGreen') {
        $rival_name = traduce_to_string(substr($rival_data, getCharCountByBytesCount(3020), getCharCountByBytesCount(8)));
    }
    $toReturn = [
        "player_name"=>$player_name,
        "player_gender"=>$player_gender,
        "trainer_id"=>$player_trainer_id,
        "time_played"=>$player_time_played,
        "options"=>$player_options,
        "game_code"=>$version_game,
        "security_key"=>$player_security_key,
        "rival_name"=>$rival_name,
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
    $text_speed = bindec(substr($text_speed_frame, -3, 3));
    if ($text_speed == 0) {
        $text_speed_settings = 'NORMAL';
    }
    if ($text_speed == 1) {
        $text_speed_settings = 'MEDIUM';
    }
    if ($text_speed == 2) {
        $text_speed_settings = 'FAST';
    }
    $frame_style = 'TYPE '.(bindec(substr($text_speed_frame, 0, 5)) + 1);
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
    $language_aviable = [
        "0201"=>"JAPANESE",
        "0202"=>"ENGLISH",
        "0203"=>"FRENCH",
        "0204"=>"ITALIAN",
        "0205"=>"GERMAN",
        "0206"=>"KOREAN",
        "0207"=>"SPANISH",
    ];
    $personality_value = process_personality_value(orderBytesToReadAsNumber(substr($pokemon_data, 0, getCharCountByBytesCount(4))));
    $pokemon_subdata_bytes = substr($pokemon_data, getCharCountByBytesCount(32), getCharCountByBytesCount(48));
    $pokemon_data_bytes = str_split($pokemon_subdata_bytes, getCharCountByBytesCount(12));
    $personality_value_bytes = orderBytesToReadAsNumber(substr($pokemon_data, 0, getCharCountByBytesCount(4)));
    $original_trainer_id = orderBytesToReadAsNumber(substr($pokemon_data, getCharCountByBytesCount(4), getCharCountByBytesCount(4)));
    $toReturn = [
        'personality_value'=>$personality_value,
        'original_trainer_id'=>$original_trainer_id,
        'nickname'=>traduce_to_string(substr($pokemon_data, getCharCountByBytesCount(8), getCharCountByBytesCount(10))),
        'language'=>$language_aviable[substr($pokemon_data, getCharCountByBytesCount(18), getCharCountByBytesCount(2))],
        'original_trainer_name'=>traduce_to_string(substr($pokemon_data, getCharCountByBytesCount(20), getCharCountByBytesCount(7))),
        'markings'=>hexdec(substr($pokemon_data, getCharCountByBytesCount(27), getCharCountByBytesCount(1))),
        'checksum'=>substr($pokemon_data, getCharCountByBytesCount(28), getCharCountByBytesCount(2)),
        'wtf'=>substr($pokemon_data, getCharCountByBytesCount(30), getCharCountByBytesCount(2)),
        'data'=>process_pokemon_subdata($pokemon_data_bytes, $personality_value_bytes, $original_trainer_id),
    ];
    return $toReturn;
}

function process_pokemon_subdata($pokemon_subdata, $personality_value, $original_trainer_id) {
    $data_order_index = hexdec($personality_value)%24;
    $data_order_table = [
        'GAEM', 'GAME', 'GEAM', 'GEMA', 'GMAE', 'GMEA',
        'AGEM', 'AGME', 'AEGM', 'AEMG', 'AMGE', 'AMEG',
        'EGAM', 'EGMA', 'EAGM', 'EAMG', 'EMGA', 'EMAG',
        'MGAE', 'MGEA', 'MAGE', 'MAEG', 'MEGA', 'MEAG',
    ];
    $data_order = str_split($data_order_table[$data_order_index], 1);
    $decrypt_key = XORALLBYTES($original_trainer_id, $personality_value);
    $pokemon_data_in_order = [
        $data_order[0]=>convertBinToHex(decodeXOR(orderBytesToReadAsNumber($pokemon_subdata[0]), $decrypt_key)),
        $data_order[1]=>convertBinToHex(decodeXOR(orderBytesToReadAsNumber($pokemon_subdata[1]), $decrypt_key)),
        $data_order[2]=>convertBinToHex(decodeXOR(orderBytesToReadAsNumber($pokemon_subdata[2]), $decrypt_key)),
        $data_order[3]=>convertBinToHex(decodeXOR(orderBytesToReadAsNumber($pokemon_subdata[3]), $decrypt_key)),
    ];
    $growth = process_growth(orderBytesToReadAsNumber($pokemon_data_in_order['G']));
    $attacks = process_attacks(orderBytesToReadAsNumber($pokemon_data_in_order['A']));
    $evs = process_evs(orderBytesToReadAsNumber($pokemon_data_in_order['E']));
    $miscellaneous = process_miscellaneous(orderBytesToReadAsNumber($pokemon_data_in_order['M']));
    $toReturn = [
        'Growth'=>$growth,
        'Attacks'=>$attacks,
        'EVs'=>$evs,
        'Miscellaneous'=>$miscellaneous,
    ];
    return $toReturn;
}

function process_growth($bytes) {
    $specie = substr($bytes, getCharCountByBytesCount(0), getCharCountByBytesCount(2));
    $item_held = substr($bytes, getCharCountByBytesCount(2), getCharCountByBytesCount(2));
    $experience = substr($bytes, getCharCountByBytesCount(4), getCharCountByBytesCount(4));
    $pp_bonuses = substr($bytes, getCharCountByBytesCount(8), getCharCountByBytesCount(1));
    $friendship = substr($bytes, getCharCountByBytesCount(9), getCharCountByBytesCount(1));
    $unknown = substr($bytes, getCharCountByBytesCount(10), getCharCountByBytesCount(2));
    $toReturn = [
        'specie'=>hexdec(orderBytesToReadAsNumber($specie)),
        'item_held'=>$item_held,
        'experience'=>hexdec(orderBytesToReadAsNumber($experience)),
        'pp_bonuses'=>$pp_bonuses,
        'friendship'=>$friendship,
        'unknown'=>$unknown,
        'bytes'=>$bytes,
    ];
    return $toReturn;
}

function process_attacks($bytes) {
    $move_1 = substr($bytes, getCharCountByBytesCount(0), getCharCountByBytesCount(2));
    $move_2 = substr($bytes, getCharCountByBytesCount(2), getCharCountByBytesCount(2));
    $move_3 = substr($bytes, getCharCountByBytesCount(4), getCharCountByBytesCount(2));
    $move_4 = substr($bytes, getCharCountByBytesCount(6), getCharCountByBytesCount(2));
    $pp_1 = substr($bytes, getCharCountByBytesCount(8), getCharCountByBytesCount(1));
    $pp_2 = substr($bytes, getCharCountByBytesCount(9), getCharCountByBytesCount(1));
    $pp_3 = substr($bytes, getCharCountByBytesCount(10), getCharCountByBytesCount(1));
    $pp_4 = substr($bytes, getCharCountByBytesCount(11), getCharCountByBytesCount(1));
    $toReturn = [
        'move_1'=>$move_1,
        'move_2'=>$move_2,
        'move_3'=>$move_3,
        'move_4'=>$move_4,
        'pp_1'=>$pp_1,
        'pp_2'=>$pp_2,
        'pp_3'=>$pp_3,
        'pp_4'=>$pp_4,
    ];
    return $toReturn;
}

function process_evs($bytes) {
    $hp = substr($bytes, getCharCountByBytesCount(0), getCharCountByBytesCount(1));
    $attack = substr($bytes, getCharCountByBytesCount(1), getCharCountByBytesCount(1));
    $defense = substr($bytes, getCharCountByBytesCount(2), getCharCountByBytesCount(1));
    $speed = substr($bytes, getCharCountByBytesCount(3), getCharCountByBytesCount(1));
    $special_attack = substr($bytes, getCharCountByBytesCount(4), getCharCountByBytesCount(1));
    $special_defense = substr($bytes, getCharCountByBytesCount(5), getCharCountByBytesCount(1));
    $coolness = substr($bytes, getCharCountByBytesCount(6), getCharCountByBytesCount(1));
    $beauty = substr($bytes, getCharCountByBytesCount(7), getCharCountByBytesCount(1));
    $cuteness = substr($bytes, getCharCountByBytesCount(8), getCharCountByBytesCount(1));
    $smartness = substr($bytes, getCharCountByBytesCount(9), getCharCountByBytesCount(1));
    $toughness = substr($bytes, getCharCountByBytesCount(10), getCharCountByBytesCount(1));
    $feel = substr($bytes, getCharCountByBytesCount(11), getCharCountByBytesCount(1));
    $toReturn = [
        'hp'=>$hp,
        'attack'=>$attack,
        'defense'=>$defense,
        'speed'=>$speed,
        'special_attack'=>$special_attack,
        'special_defense'=>$special_defense,
        'coolness'=>$coolness,
        'beauty'=>$beauty,
        'cuteness'=>$cuteness,
        'smartness'=>$smartness,
        'toughness'=>$toughness,
        'feel'=>$feel
    ];
    return $toReturn;
}

function process_miscellaneous($bytes) {
    $pokerus_status = substr($bytes, getCharCountByBytesCount(0), getCharCountByBytesCount(1));
    $met_location = substr($bytes, getCharCountByBytesCount(1), getCharCountByBytesCount(1));
    $origins_info = substr($bytes, getCharCountByBytesCount(2), getCharCountByBytesCount(2));
    $iv_egg_ability = substr($bytes, getCharCountByBytesCount(4), getCharCountByBytesCount(4));
    $ribbons_obedience = substr($bytes, getCharCountByBytesCount(8), getCharCountByBytesCount(4));
    $toReturn = [
        'pokerus_status'=>$pokerus_status,
        'met_location'=>$met_location,
        'origins_info'=>$origins_info,
        'iv_egg_ability'=>$iv_egg_ability,
        'ribbons_obedience'=>$ribbons_obedience
    ];
    return $toReturn;
}

function convertBinToHex($input) {
    $toConvert = str_split($input,4);
    $toReturn = '';
    $bin2hex = [
        '0000'=>'0',
        '0001'=>'1',
        '0010'=>'2',
        '0011'=>'3',
        '0100'=>'4',
        '0101'=>'5',
        '0110'=>'6',
        '0111'=>'7',
        '1000'=>'8',
        '1001'=>'9',
        '1010'=>'a',
        '1011'=>'b',
        '1100'=>'c',
        '1101'=>'d',
        '1110'=>'e',
        '1111'=>'f',
    ];
    foreach($toConvert as $byte) {
        $toReturn .= $bin2hex[$byte];
    }
    return $toReturn;
}

function process_personality_value($value) {
    $gender = hexdec(substr($value, getCharCountByBytesCount(3), getCharCountByBytesCount(1)));
    $value_bin = convert_hex_to_bin($value);
    $ability = substr($value_bin, -1, 1);
    $ability_to_show = '';
    if ($ability == '0') {
        $ability_to_show = 'FIRST';
    }
    if ($ability == '1') {
        $ability_to_show = 'SECOND';
    }
    $nature_index = hexdec(substr($value, getCharCountByBytesCount(0), getCharCountByBytesCount(4))) % 25;
    $nature_dictionary = [
        ['name'=>'Hardy', 'stat_1'=>'-Attack', 'stat_2'=>'+Attack'],
        ['name'=>'Lonely', 'stat_1'=>'-Defense', 'stat_2'=>'+Attack'],
        ['name'=>'Brave', 'stat_1'=>'-Speed', 'stat_2'=>'+Attack'],
        ['name'=>'Adamant', 'stat_1'=>'-Sp.Atk', 'stat_2'=>'+Attack'],
        ['name'=>'Naughty', 'stat_1'=>'-Sp.Def', 'stat_2'=>'+Attack'],
        ['name'=>'Bold', 'stat_1'=>'-Attack', 'stat_2'=>'+Defense'],
        ['name'=>'Docile', 'stat_1'=>'-Defense', 'stat_2'=>'+Defense'],
        ['name'=>'Relaxed', 'stat_1'=>'-Seed', 'stat_2'=>'+Defense'],
        ['name'=>'Impish', 'stat_1'=>'-Sp.Atk', 'stat_2'=>'+Defense'],
        ['name'=>'Lax', 'stat_1'=>'-Sp.Def', 'stat_2'=>'+Defense'],
        ['name'=>'Timid', 'stat_1'=>'-Attack', 'stat_2'=>'+Speed'],
        ['name'=>'Hasty', 'stat_1'=>'-Defense', 'stat_2'=>'+Speed'],
        ['name'=>'Serious', 'stat_1'=>'-Speed', 'stat_2'=>'+Speed'],
        ['name'=>'Jolly', 'stat_1'=>'-Sp.Atk', 'stat_2'=>'+Speed'],
        ['name'=>'Naive', 'stat_1'=>'-Sp.Def', 'stat_2'=>'+Speed'],
        ['name'=>'Modest', 'stat_1'=>'-Attack', 'stat_2'=>'+Sp.Atk'],
        ['name'=>'Mild', 'stat_1'=>'-Defense', 'stat_2'=>'+Sp.Atk'],
        ['name'=>'Quiet', 'stat_1'=>'-Speed', 'stat_2'=>'+Sp.Atk'],
        ['name'=>'Bashful', 'stat_1'=>'-Sp.Atk', 'stat_2'=>'+Sp.Atk'],
        ['name'=>'Rash', 'stat_1'=>'-Sp.Def', 'stat_2'=>'+Sp.Atk'],
        ['name'=>'Calm', 'stat_1'=>'-Attack', 'stat_2'=>'+Sp.Def'],
        ['name'=>'Gentle', 'stat_1'=>'-Defense', 'stat_2'=>'+Sp.Def'],
        ['name'=>'Sassy', 'stat_1'=>'-Speed', 'stat_2'=>'+Sp.Def'],
        ['name'=>'Careful', 'stat_1'=>'-Sp.Atk', 'stat_2'=>'+Sp.Def'],
        ['name'=>'Quirky', 'stat_1'=>'-Sp.Def', 'stat_2'=>'+Sp.Def'],
    ];
    $unown_letter_index = bindec(substr($value_bin, 6, 2) . substr($value_bin, 14, 2) . substr($value_bin, 22, 2) . substr($value_bin, 30, 2)) % 28;
    $unown_letter_map = ['A','B','C','D','E','F','G','H','I','J',
                         'K','L','M','N','O','P','Q','R','S','T',
                         'U','V','W','X','Y','Z','?','!'];
    $toReturn = [
        'unown_letter'=>$unown_letter_map[$unown_letter_index],
        'p_gender'=>$gender,
        'ability'=>$ability_to_show,
        'nature'=>$nature_dictionary[$nature_index],
    ];
    return $toReturn;
}

function process_team_items($data_readed, $trainer_info) {
    $game_code = $trainer_info['game_code'];
    $security_key = $trainer_info['security_key'];
    $data = $data_readed['team_items']['data'];
    $team_size = 0;
    $team_pokemon_list = 0;
    $money = 0;
    $coins = 0;
    $pc_items = 0;
    $item_pocket = 0;
    $key_item_pocket = 0;
    $ball_item_pocket = 0;
    $TM_case = 0;
    $berry_pocket = 0;
    if ($game_code == 'Ruby/Sapphire') {
        $team_size = hexdec(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(564), getCharCountByBytesCount(4))));
        $team_pokemon_list = substr($data, getCharCountByBytesCount(568), getCharCountByBytesCount(600));
        $money = 'N/A';
        $coins = 'N/A';
        $pc_items = 'N/A';
        $item_pocket = 'N/A';
        $key_item_pocket = 'N/A';
        $ball_item_pocket = 'N/A';
        $TM_case = 'N/A';
        $berry_pocket = 'N/A';
    }
    if ($game_code == 'FireRed/LeafGreen') {
        $team_size = hexdec(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(52), getCharCountByBytesCount(4))));
        $team_pokemon_list = substr($data, getCharCountByBytesCount(56), getCharCountByBytesCount(600));
        $money = bindec(decodeXOR(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(656), getCharCountByBytesCount(4))), $security_key));
        $coins = bindec(decodeXOR(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(660), getCharCountByBytesCount(2))), substr($security_key, getCharCountByBytesCount(2) * (-1), getCharCountByBytesCount(2))));
        $pc_items = substr($data, getCharCountByBytesCount(664), getCharCountByBytesCount(120));
        $item_pocket = substr($data, getCharCountByBytesCount(784), getCharCountByBytesCount(168));
        $key_item_pocket = substr($data, getCharCountByBytesCount(952), getCharCountByBytesCount(120));
        $ball_item_pocket = substr($data, getCharCountByBytesCount(1072), getCharCountByBytesCount(52));
        $TM_case = substr($data, getCharCountByBytesCount(1124), getCharCountByBytesCount(232));
        $berry_pocket = substr($data, getCharCountByBytesCount(1356), getCharCountByBytesCount(172));
    }
    if ($game_code == 'Emerald') {
        $team_size = hexdec(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(564), getCharCountByBytesCount(4))));
        $team_pokemon_list = substr($data, getCharCountByBytesCount(568), getCharCountByBytesCount(600));
        $money = bindec(decodeXOR(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(1168), getCharCountByBytesCount(4))), $security_key));
        $coins = bindec(decodeXOR(orderBytesToReadAsNumber(substr($data, getCharCountByBytesCount(1172), getCharCountByBytesCount(2))), substr($security_key, getCharCountByBytesCount(2) * (-1), getCharCountByBytesCount(2))));
        $pc_items = substr($data, getCharCountByBytesCount(1176), getCharCountByBytesCount(200));
        $item_pocket = substr($data, getCharCountByBytesCount(1376), getCharCountByBytesCount(80));
        $key_item_pocket = substr($data, getCharCountByBytesCount(1496), getCharCountByBytesCount(80));
        $ball_item_pocket = substr($data, getCharCountByBytesCount(1536), getCharCountByBytesCount(64));
        $TM_case = substr($data, getCharCountByBytesCount(1600), getCharCountByBytesCount(256));
        $berry_pocket = substr($data, getCharCountByBytesCount(1856), getCharCountByBytesCount(184));
    }
    $team_pokemon_list_as_array = str_split($team_pokemon_list, getCharCountByBytesCount(100));
    $pokemon_party = [];
    for($i = 0; $i < $team_size; $i++) {
        array_push($pokemon_party, build_pokemon_from_data($team_pokemon_list_as_array[$i]));
    }
    $toReturn = [
        'team_size' => $team_size,
        'team_pokemon_list' => $pokemon_party,
        'money' => $money,
        'coins' => $coins,
        'pc_items' => $pc_items,
        'item_pocket' => $item_pocket,
        'key_item_pocket' => $key_item_pocket,
        'ball_item_pocket' => $ball_item_pocket,
        'TM_case' => $TM_case,
        'berry_pocket' => $berry_pocket,
    ];
    return $toReturn;
}

function decodeXOR($dataToDecode, $security_key) {
    $toReturn = '';
    $toDecode = convert_hex_to_bin($dataToDecode);
    $key = convert_hex_to_bin($security_key);
    for($i=0; $i<strlen($toDecode); )
    {
        for($j=0; ($j<strlen($key) && $i<strlen($toDecode)); $j++,$i++)
        {
            $toReturn .= xorByDigit($toDecode[$i], $key[$j]);
        }
    }
    return $toReturn;
}

function XORALLBYTES($array1, $array2) {
    $toReturn = '';
    $array_1_bits = str_split(convert_hex_to_bin($array1),1);
    $array_2_bits = str_split(convert_hex_to_bin($array2),1);
    for($i=0; $i<sizeof($array_1_bits); $i++)
    {
        $toReturn .= xorByDigit($array_1_bits[$i], $array_2_bits[$i]);
    }
    return dechex(bindec($toReturn));
}

function xorByDigit($A, $B) {
    if ($A == $B) {
        return '0';
    }
    return '1';
}

function process_hall_of_fame($hall_of_fame_data) {
    $teams_data = str_split(substr($hall_of_fame_data, 0, getCharCountByBytesCount(6000)),getCharCountByBytesCount(120));
    $teams = [];
    foreach($teams_data as $team_data) {
        $team_to_process = str_split($team_data, getCharCountByBytesCount(20));
        $team = [];
        foreach($team_to_process as $pokemon_data) {
            array_push($team, process_pokemon_hall_of_fame($pokemon_data));
        }
        array_push($teams, $team);
    }
    return $teams;
}

function process_pokemon_hall_of_fame($pokemon_data) {
    $personality_value = process_personality_value(orderBytesToReadAsNumber(substr($pokemon_data, getCharCountByBytesCount(4), getCharCountByBytesCount(4))));
    $toReturn = [
        "trainer_id" => substr($pokemon_data, 0, getCharCountByBytesCount(4)),
        "personality_value" => $personality_value,
        "spiece" => hexdec(substr($pokemon_data, getCharCountByBytesCount(8), getCharCountByBytesCount(1))),
        "level" => bindec(substr(convert_hex_to_bin(substr($pokemon_data, getCharCountByBytesCount(9), getCharCountByBytesCount(1))),0,7)),
        "nickname" => traduce_to_string(substr($pokemon_data, getCharCountByBytesCount(10), getCharCountByBytesCount(10))),
    ];
    return $toReturn;
}

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$filename = 'COMPLETO.sav';
$result = read_pokesave($filename);
$trainer_info = process_trainer_data($result['game_save_a']);
$pokemon_pc_boxes = proccess_pc_buffer($result['game_save_a']);
$team_items = process_team_items($result['game_save_a'], $trainer_info);
$hall_of_fame = process_hall_of_fame($result['hall_of_fame']);
$toReturn = [
    "trainer_info" => $trainer_info,
    "pokemon_pc_boxes" => $pokemon_pc_boxes,
    "team_items" => $team_items,
    "hall_of_fame" => $hall_of_fame,
];
echo json_encode($toReturn);
//echo json_encode(traduce_from_string('Luis'));
//echo json_encode(traduce_to_string("bccfc6bcbbcdbbcfcc"));