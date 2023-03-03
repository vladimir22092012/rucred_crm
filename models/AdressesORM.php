<?php

class AdressesORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_addresses';
    protected $guarded = [];
    public $timestamps = false;

    public static function prepareData($address_array, $adressfull) {
        return [
            'zip' => $address_array['postal_code'],
            'okato' => $address_array['okato'],
            'oktmo' => $address_array['oktmo'],
            'adressfull' => $adressfull,
            'region' => $address_array['region'],
            'region_type' => $address_array['region_type'],
            'district' => $address_array['area'],
            'district_type' => $address_array['area_type'],
            'city' => $address_array['city'],
            'city_type' => $address_array['city_type'],
            'street' => $address_array['street'],
            'street_type' => $address_array['street_type'],
            'house' => $address_array['house'],
            'room' => $address_array['flat'],
        ];
    }
}
