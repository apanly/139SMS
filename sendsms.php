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
    private $user;
    private $pass;
    private $http;
    private $session_id;
    private $userdata = array();
    private $cookies = array();

    function __construct() {
        $this->user = '13641738806';
        $this->pass = 'xxxxxx';
        $this->http = new Httplib();
    }

    function get() {
        $this->session_id = $this->login($this->user, $this->pass);
        $r = $this->send('13641738806', '测试短信testing...');
        if ($r) {
            quit('Success!');
        } else {
            quit('Failure!');
        }
    }

    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    function login($username,$password) {
        $resp = $this->http->post('http://mail.10086.cn/Login/Login.ashx?&f=1&w=1&c=1&face=B&selStyle=4&_fv=4&sidtype=mail&atl=1', array(
            'UserName' => $username,
            'Password' => $password,
            'VerifyCode' => '',
            'auto' => 1,
        ), array(
            'redirection' => 0
        ));
        if ($resp['response']['code'] == 302) {
            // 保存cookies
            $this->save_cookies($resp['cookies']);
            // 保存用户信息
            $userdata = $this->cookies['UserData']['value'];
            $this->userdata['ssoSid'] = mid($userdata, "ssoSid:'","'");
            $this->userdata['provCode'] = mid($userdata, "provCode:",",");
            $this->userdata['serviceItem'] = mid($userdata, "serviceItem:'","'");
            $this->userdata['userNumber'] = mid($userdata, "userNumber:'","'");
            $this->userdata['loginname'] = mid($userdata, "loginname:'","'");
            // 获取 session id
            $location = $resp['headers']['location'];
            $session_id = mid($location, 'sid=', '&');
            return $session_id;
        }
        return null;
    }
    /**
     * 查询短信发送限额
     *
     * @param string $session_id
     * @return array
     */
    function query() {
        $resp = $this->http->post('http://html5.mail.10086.cn/mw2/sms/sms?'.http_build_query(array(
            'func' => 'sms:getSmsMainData',
            'sid' => $this->session_id,
            'userNumber' => $this->userdata['userNumber'],
            'provCode' => $this->userdata['provCode'],
            'serviceItem' => $this->userdata['serviceItem'],
            'serviceId' => 10,
            'behaviorData' => '',
            'rnd' => mt_rand(),
        ),null,'&'), '<object><int name="type">1</int></object>', array(
            'redirection' => 0,
            'cookies' => $this->cookies,
            'headers' => array(
                'Content-Type' => 'application/xml',
                'Referer' => 'http://html5.mail.10086.cn/html/sms.html?sid='.$this->session_id,
            ),
        ));
        if ($resp['response']['code'] == 200) {
            return json_decode($resp['body'], true);
        }
        return null;
    }

    /**
     * 发送短信
     *
     * @param $receiver
     * @param $content
     * @return bool
     */
    function send($receiver, $content) {
        $data = $this->query();
        // 发送短信
        $resp = $this->http->post('http://html5.mail.10086.cn/mw2/sms/sms?'.http_build_query(array(
            'func' => 'sms:sendSms',
            'sid' => $this->session_id,
            'userNumber' => $this->userdata['userNumber'],
            'provCode' => $this->userdata['provCode'],
            'serviceItem' => $this->userdata['serviceItem'],
            'serviceId' => 10,
            'behaviorData' => '',
            'rnd' => mt_rand(),
        ),null,'&'), '<object><int name="doubleMsg">0</int><int name="submitType">1</int><string name="smsContent">'.$content.'</string><string name="receiverNumber">'.$receiver.'</string><string name="comeFrom">2</string><int name="sendType">0</int><int name="smsType">'.$data['var']['smsType'].'</int><int name="serialId">-1</int><int name="isShareSms">0</int><string name="sendTime"></string><string name="validImg"></string><int name="groupLength">'.$data['var']['groupLength'].'</int></object>', array(
            'redirection' => 0,
            'cookies' => $this->cookies,
            'headers' => array(
                'Content-Type' => 'application/xml',
                'Referer' => 'http://html5.mail.10086.cn/html/sms.html?sid='.$this->session_id,
            ),
        ));
        if ($resp['response']['code'] == 200) {
            return true;
        }
        return false;
    }

    /**
     * 保存cookies
     *
     * @param $cookies
     */
    function save_cookies($cookies) {
        foreach($cookies as $k=>$v) {
            $this->cookies[$v['name']] = $v;
        }
    }
}
// app run
App::instance()->run();
