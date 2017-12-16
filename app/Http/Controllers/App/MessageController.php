<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\MessageRepository;
use App\Repositories\MessageReceiverRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Entities\MessageReceiver;

class MessageController extends Controller
{
    public function __construct(MessageReceiverRepository $messageReceiverRepository, MessageRepository
        $messageRepository
    )
    {
        $this->messageReceiverRepository = $messageReceiverRepository;
        $this->messageRepository        = $messageRepository;
    }
     /**
      * 拿到所有消息列表
      *
      * @SWG\Get(path="/api/v1/msgs",
      *   tags={"api1.1"},
      *   summary="拿到所有消息列表-可测试-zj",
      *   description="拿到所有消息列表",
      *   operationId="register",
      *   produces={"application/json"},
      * @SWG\Parameter(
      *     in="header",
      *     name="Authorization",
      *     type="string",
      *     description="用户旧的jwt-token, value以Bearer开头",
      *     required=true,
      *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbHNkLWFwaS5mZW5neGlhb2JhaS5jbi9hdXRoL2xvZ2luIiwiaWF0IjoxNDk1MDAwMjIxLCJleHAiOjE1MzEyODgyMjEsIm5iZiI6MTQ5NTAwMDIyMSwianRpIjoiNXNkZXlVa2t0TWZoa050VyIsInN1YiI6NTE4NDIxODk1MTQ2NjM2MjkyfQ.h4l2QZpJitwbIh63yh_ef_P7tXnm1R4XUgm8rnDv9Zg"
      *   ),
      * @SWG\Parameter(
      *     in="query",
      *     name="page",
      *     type="string",
      *     description="页码",
      *     default="1",
      *     required=true
      *   ),
      * @SWG\Parameter(
      *     in="query",
      *     name="msg_type",
      *     type="string",
      *     description="0 系统消息； 1个人信息",
      *     default="1",
      *     required=true
      *   ),
      * @SWG\Response(response="default", description="操作成功")
      * )
      */
    public function lists(Request $request)
    {
        $data    = $request->all();
        $context = [
            'data'   => $data,
            'msg'    => '消息列表',
            'method' => __METHOD__,
        ];
        Log::info('消息列表', $context);
        try {
            $msgType  = isset($data['msg_type']) ? $data['msg_type'] : 1;
            $page     = isset($data['page']) ? $data['page'] : 1;
            $limit    = config('paginate.limit');
            $user     = $this->getUser();
            $whereCod = $this->getUserMsgWhere($user, $msgType);
            $results  = $this->messageReceiverRepository->with(
                [
                        'messages' => function ($query) {
                                return $query->select(['id', 'content', 'order_id',  'short_url', 'push_type', 'title', 'created_at']);
                        }]
            )->scopeQuery(
                function ($query) use ($whereCod) {
                    foreach ($whereCod as $key => $val) {
                        if ($key == 'in') {
                            foreach ($val as $k => $v) {
                                return $query = $query->whereIn($k, $v);
                            }
                        }
                        if ($key == 'where') {
                            return $query = $query->where($val);
                        }
                    }
                }
            )->paginate($limit);
            $output = [];
            $redNotice = 0;
            foreach ($results as $key => $val) {
                $message         = $val->messages;
                $message->id     = $val->id;
                $message->state  = $val->state;
                $val->state == 0 ? $redNotice++ : '';
                $message->msg_id = $message->id;
                $message->state  = $val->state;
                $output[]        = $message->transform();
            }
            $output = [
                'list' => $output,
                'page' => $page,
            ];
            Log::info('msg', $output);
            return $this->response(0, config('error.0'), $output);
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(0, config('error.0'));
        }
    }
    //得到查询条件
    protected function getUserMsgWhere($user, $msgType)
    {
        $userType = '';
        switch ($user->billable_type) {
            case 'App\\Entities\\UserCompanies':
            case 'App\\Entities\\Companies':
                // code...
                $userType = 'company';
                break;
            case 'App\\Entities\\UserWorker':
                // code...
                $userType = 'worker';
                break;
            case 'App\\Entities\\UserSite':
                // code...
                $userType = 'site';
                break;
            default:
                // code...
                break;
        }
        $whereCod = [];
        switch ($msgType) {
            //系统消息
            case '0':
                $userMsgType = strtoupper('all_'.$userType);
                $userMsgType = constant(sprintf('%s::%s', MessageReceiver::class, $userMsgType));
                $whereCod['in'] = [
                            'sent_all' => [$userMsgType, MessageReceiver::ALL_USER],
                            ];
                $whereCod['where'] = [
                'msg_type'  => 0,
                ];
                // code...
                break;
            //系统消息
            case '1':
                $whereCod['where'] = [
                'receiver_id' => $user->id,
                'sent_all'   => 0,
                'msg_type'   => 1,
                ];
                // code...
                break;
            default:
                // code...
                break;
        }
        return $whereCod;
    }

     /**
      * 消息设置为已读
      *
      * @SWG\Post(path="/api/v1/msg/setread",
      *   tags={"api1.1"},
      *   summary="消息设置为已读--可测试-zj",
      *   description="消息设置为已读",
      *   operationId="register",
      *   produces={"application/json"},
      * @SWG\Parameter(
      *     in="header",
      *     name="Authorization",
      *     type="string",
      *     description="用户旧的jwt-token, value以Bearer开头",
      *     required=true,
      *     default="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbHNkLWFwaS5mZW5neGlhb2JhaS5jbi9hdXRoL2xvZ2luIiwiaWF0IjoxNDk1MDAwMjIxLCJleHAiOjE1MzEyODgyMjEsIm5iZiI6MTQ5NTAwMDIyMSwianRpIjoiNXNkZXlVa2t0TWZoa050VyIsInN1YiI6NTE4NDIxODk1MTQ2NjM2MjkyfQ.h4l2QZpJitwbIh63yh_ef_P7tXnm1R4XUgm8rnDv9Zg"
      *   ),
      * @SWG\Parameter(
      *     in="formData",
      *     name="id",
      *     type="string",
      *     description="发送的iD 消息列表返回数据的id】",
      *     required=false
      *   ),
      * @SWG\Parameter(
      *     in="formData",
      *     name="type",
      *     type="string",
      *     description="all 全部， 其他为单个",
      *     required=true
      *   ),
      * @SWG\Response(response="default",     description="操作成功")
      * )
      */
    public function setMsgReadFlag(Request $request)
    {
        $data    = $request->all();
        $context = [
            'data'   => $data,
            'msg'    => '消息列表',
            'method' => __METHOD__,
        ];
        Log::info('消息列表', $context);
        try {
            $msgType  = isset($data['type']) ? $data['type'] : 'all';
            $id     = isset($data['id']) ? $data['id'] : 1;
            $user     = $this->getUser();
            if ($msgType == 'all') {
                $where = [
                    'receiver_id' => $user->id,
                ];
            } else {
                $where = [
                    'id'          => $id,
                    'receiver_id' => $user->id,
                ];
            }
            $this->messageReceiverRepository->updateState($where);
            return $this->response(0, config('error.0'));
        } catch (\Exception $ex) {
            Log::error($ex);
            return $this->response(0, config('error.0'));
        }
    }
}
