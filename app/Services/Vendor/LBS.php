<?php
namespace App\Services\Vendor;

use App\Services\ApiService;
use Ixudra\Curl\Facades\Curl;
use Laravel\Lumen\Application;

/**
 *  LBS.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: LBS.php 2017-03-20 下午4:47 $
 */
class LBS
{
    /**
     * @var \Laravel\Lumen\Application
     */
    private $application;

    public function __construct(Application $application)
    {

        $this->application = $application;
    }

    public function getWorkers($lng, $lat, $dist = 20000)
    {
        $url = config('gis.api_endpoint') . 'lbs/search-worker';
        $data = [
            'worker_lng' => $lng,
            'worker_lat' => $lat,
            'dist'       => $dist,
        ];

        $response = ApiService::request($url, $data, 'shifu');

        return $response;
    }

    public function createOrder()
    {
    }

    public function syncWorker($workerId, $state)
    {
        $url = config('gis.api_endpoint') . 'lbs/save-worker';
        $data = [
            'id'        => $workerId,
            'state'     => $state,
        ];
        $response = ApiService::request($url, $data, 'shifu', 'post');

        return $response;
    }

    public function getOrders($lng, $lat, $page, $limit, $dist = 20000)
    {
        $url = config('gis.api_endpoint') . 'lbs/search-order';
        $data = [
            'user_lng' => $lng,
            'user_lat' => $lat,
            'dist'       => $dist,
            'size' => $limit,
            'page' => $page,
        ];

        $response = ApiService::request($url, $data, 'shifu');

        return $response;
    }

    public function syncOrder($orderId, $state)
    {
        $url = config('gis.api_endpoint') . 'lbs/save-order';
        $data = [
            'id' => $orderId,
            'state'     => $state,
        ];
        $response = ApiService::request($url, $data, 'shifu', 'post');

        return $response;
    }
}
