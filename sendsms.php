<?php
/**
 * @author Lukin <my@lukin.cn>
 * @version $Id$
 * @datetime 
 */
// include global
include dirname(__FILE__) . '/global.php';
// define class
class sendsmsHandler{
    public $debug  = true;
    public $user;
    public $pass;
    public $sock;
    public $logs;

    function __before() {
        $this->user = '13641738806@139.com';
        $this->pass = 'Lukin262865';
    }

    function __after() {
    }

    function get() {
        $http = new Httplib();
        $resp = $http->get('http://html5.mail.10086.cn/');
        Logger::instance()->debug($resp);
    }
    /**
     * 报错
     *
     * @param string $str
     */
    function error($str){
        quit($str);
    }
}
// app run
App::instance()->run();
