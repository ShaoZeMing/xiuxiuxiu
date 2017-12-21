<?php

namespace App\Http\Controllers\Api;

use App\Entities\Area;
use App\Http\Controllers\Controller;
use App\Repositories\AreaRepository;
use App\Repositories\AreaRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class AreaController
 * @package App\Http\Controllers\Api
 */
class AreaController extends Controller
{

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @param AreaRepositoryEloquent $areaRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function area(Request $request, AreaRepositoryEloquent $areaRepository)
    {
        $context = [
            'method' => __METHOD__,
        ];
        Log::info('获取地址', [$context, $request->all()]);
        $parentId = $request->input('parent_id') ? $request->input('parent_id') : 0;
        try {
            $data = $areaRepository->getAreaTree($parentId);
            return $this->response(0, '成功', $data->toArray());
        } catch (\Exception $e) {
            Log::error('获取地域失败' . $e->getMessage(), $context);
            return $this->response(1009, '获取地址失败');

        }
    }



    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function city(Request $request)
    {
        $provinceId = $request->get('q')?:0;
        return Area::where('parent_id', $provinceId)->get(['id', DB::raw('name as text')]);
    }


}
