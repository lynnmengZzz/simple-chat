<?php

class ws_server
{
    public $server;

    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server("0.0.0.0", 9501);

        $this->server->on('open', function (swoole_websocket_server $server, $request) {
            echo "有个帅哥/美女连接到了服务  他的id是{$request->fd}\n";
        });

        $this->server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
            echo "接受到来自 {$frame->fd}的数据 : {$frame->data}\n";
            $all_users =  $this->server->connections;
            foreach ($all_users as $user)
            {
                if ($user == $frame->fd)
                {
                    $this->server->push($user, json_encode(['message' => $frame->data,'type' => 2],true));
                }
                else
                {
                    $this->server->push($user, json_encode(['message' => $frame->data,'type' => 1],true));
                }
            }
        });

        $this->server->on('close', function ($ser, $fd) {
            echo "有个傻屌下线了  他的id是{$fd}\n";
        });

//$this->server->on('request', function ($request, $response) {
//// 接收http请求从get获取message参数的值，给用户推送
//// $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
//foreach ($this->server->connections as $fd) {
//$this->server->push($fd, $request->get['message']);
//}
//});
        $this->server->start();
    }
}

new ws_server();