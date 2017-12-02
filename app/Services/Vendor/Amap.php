<?php
namespace App\Services\Vendor;

// use Illuminate\Foundation\Application;
use Laravel\Lumen\Application;
use Ixudra\Curl\Facades\Curl;

class Amap
{
    private $_appKey;

    public function __construct(Application $application)
    {
        $this->_appKey = config('amap.key');
    }

    public function getAddress($longitude, $latitude)
    {
        $params = [
            'output'     => 'json',
            'key'        => $this->_appKey,
            'location'   => $longitude . ',' . $latitude,
            'radius'     => 1000,
            'extensions' => 'base',
        ];
        $url = config('amap.regeo_api');

        $response = Curl::to($url)->withData($params)->asJson()->get();

        if ($response->status == 1) {
            $regeoCode = $response->regeocode;
            $addressComponent = $regeoCode->addressComponent;
            $province = $addressComponent->province;
            $city = $addressComponent->city;
            if (empty($city)) {
                $city = $province;
            }
            $district = $addressComponent->district;

            return [
                '',
                $province,
                $city,
                $district,
            ];
        } else {
            return false;
        }
    }

    public function getLocation($address, $city = '')
    {
        $params = [
            'output'  => 'json',
            'key'     => $this->_appKey,
            'address' => $address,
        ];

        if ($city !== '') {
            $params['city'] = $city;
        }

        $url = config('amap.geo_api');

        $response = Curl::to($url)->withData($params)->asJson()->get();

        if ($response->status == 1) {
            return $response->geocodes[0];
        } else {
            return false;
        }
    }
}
