<?php

class Helpers extends Core
{

    public function get_regional_time($region)
    {
        $region_times = array(
            "адыгея" => 0,
            "башкортостан" => 2,
            "бурятия" => 5,
            "алтай" => 4,
            "дагестан" => 0,
            "ингушетия" => 0,
            "кабардино-балкарская" => 0,
            "калмыкия" => 0,
            "карачаево-черкесская" => 0,
            "карелия" => 0,
            "коми" => 0,
            "марий эл" => 0,
            "мордовия" => 0,
            "саха /якутия/" => 6,
            "северная осетия - алания" => 0,
            "татарстан",
            "тыва" => 4,
            "удмуртская" => 1,
            "хакасия" => 4,
            "чеченская",
            "чувашская" => 0,
            "алтайский" => 4,
            "краснодарский" => 0,
            "красноярский" => 4,
            "приморский" => 7,
            "ставропольский",
            "хабаровский" => 7,
            "амурская" => 6,
            "архангельская" => 0,
            "астраханская" => 1,
            "белгородская" => 0,
            "брянская" => 0,
            "владимирская" => 0,
            "волгоградская" => 0,
            "вологодская" => 0,
            "воронежская" => 0,
            "ивановская" => 0,
            "иркутская" => 5,
            "калининградская" => -1,
            "калужская" => 0,
            "камчатский" => 9,
            "кемеровская" => 4,
            "кировская" => 0,
            "костромская" => 0,
            "курганская" => 2,
            "курская" => 0,
            "ленинградская" => 0,
            "липецкая" => 0,
            "магаданская" => 8,
            "московская" => 0,
            "мурманская" => 0,
            "нижегородская" => 0,
            "новгородская" => 0,
            "новосибирская" => 4,
            "омская" => 3,
            "оренбургская" => 2,
            "орловская" => 0,
            "пензенская" => 0,
            "пермский" => 2,
            "псковская" => 0,
            "ростовская" => 0,
            "рязанская" => 0,
            "самарская" => 1,
            "саратовская" => 1,
            "сахалинская" => 8,
            "свердловская" => 2,
            "смоленская" => 0,
            "тамбовская" => 0,
            "тверская" => 0,
            "томская" => 4,
            "тульская" => 0,
            "тюменская" => 2,
            "ульяновская" => 1,
            "челябинская" => 2,
            "забайкальский" => 6,
            "ярославская" => 0,
            "москва" => 0,
            "санкт-петербург" => 0,
            "крым" => 0,
            "ханты-мансийский автономный округ - югра" => 2,
            "чукотский" => 9,
            "ямало-ненецкий" => 2,
            "севастополь" => 0,

        );
        
        $region = trim(mb_strtolower($region));
        
        $shift = 0;
        if (isset($region_times[$region])) {
            $shift = $region_times[$region];
        }
        
        return date('Y-m-d H:i:s', time() + $shift * 3600);
    }
    


    private $c2o_codes = array(
        array('z', 'x', 'c', 'V', 'B', 'N', 'm', 'A', 's', '4'),
        array('Q', 'W', 'r', 'S', '6', 'Y', 'k', 'n', 'G', 'i'),
        array('T', '2', 'H', 'e', 'D', '1', '8', 'P', 'o', 'g'),
        array('O', 'u', 'Z', 'h', '0', 'I', 'J', '7', 'a', 'L'),
        array('v', 'w', 'p', 'E', 't', '5', 'b', '9', 'l', 'R'),
        array('d', '3', 'q', 'C', 'U', 'M', 'y', 'X', 'K', 'j'),
    );
    
    public function c2o_encode($id)
    {
        $code = '';
        
        $chars = str_split($id);
        
        if (count($chars) != 6) {
            return false;
        }
        
        $code .= $this->c2o_codes[5][$chars[5]];
        $code .= $this->c2o_codes[4][$chars[4]];
        $code .= $this->c2o_codes[3][$chars[3]];
        $code .= $this->c2o_codes[2][$chars[2]];
        $code .= $this->c2o_codes[1][$chars[1]];
        $code .= $this->c2o_codes[0][$chars[0]];
        return $code;
    }
    
    public function c2o_decode($code)
    {
        $id = '';
        
        $chars = str_split($code);
        
        if (count($chars) != 6) {
            return false;
        }

        $id .= array_search($chars[5], $this->c2o_codes[0]);
        $id .= array_search($chars[4], $this->c2o_codes[1]);
        $id .= array_search($chars[3], $this->c2o_codes[2]);
        $id .= array_search($chars[2], $this->c2o_codes[3]);
        $id .= array_search($chars[1], $this->c2o_codes[4]);
        $id .= array_search($chars[0], $this->c2o_codes[5]);
        
        return $id;
    }
}
