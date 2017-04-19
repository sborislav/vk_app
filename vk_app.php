<?php

namespace sborislav\vk_app;


class vk_user
{
    public $id;
    public $auth_key;

    private $group;
    private $type;
    private $install;
    private $hash;
    private $is_secure;

    public function create_user($get)
    {
        $this->id = (int)$get['viewer_id']; // id пользователя
        $this->group = (int)$get['group_id']; // id группы
        $this->auth_key =(string)$get['auth_key']; //подлинности сессии на сервере
        $this->type = (int)$get['viewer_type']; // 4 - admin , 3 - редактор, 2 модератор, 1 участник, 0 - никто
        $this->install = (int)$get['is_app_user']; // приожение установлено в группу = 1, нет = 0
        $this->hash = (string)$get['hash']; // данные после символа # в строке адреса
        $this->is_secure = (int)$get['is_secure']; // данные после символа # в строке адреса
    }

}

class vk_app extends vk_user
{
    private $_id_app;
    private $_secret_app;

    public function __construct($id = false, $secret_app =  false)
    {
        if ( $id && $secret_app )
        {
            $this->_id_app = $id;
            $this->_secret_app = $secret_app;
        }

    }

    public function hash($get)
    {
        $sign = "";
        foreach ($get as $key => $param)
        {
            if ($key == 'hash' || $key == 'sign' || $key == 'api_result') continue;
            $sign .=$param;
        }
        $x = false;

        if ( isset($get['sign']) ) hash_hmac('sha256', $sign, $this->_secret_app) == $get['sign'] ? $x = true : $x = false;

        if ($x) $this->create_user($get);

        return $x;
    }

    public function check_session()
    {
        return ( $this->auth_key == md5($this->_id_app + '_' + $this->id + '_' + $this->_secret_app) );
    }

}


?>