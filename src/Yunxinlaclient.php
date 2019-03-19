<?php

namespace Phpang\Yunxinlaclient;

use Illuminate\Support\ServiceProvider;

class Yunxinlaclient
{
    protected $config;
    protected $yunXinUrl = [
        "create.action" => "https://api.netease.im/nimserver/user/create.action",
        "updateUinfo.action" => "https://api.netease.im/nimserver/user/updateUinfo.action",
        "sendMsg.action" => "https://api.netease.im/nimserver/msg/sendMsg.action",
        "sendBatchMsg.action" => "https://api.netease.im/nimserver/msg/sendBatchMsg.action",
    ];
    /**
      * Packagetest constructor.
      * @param SessionManager $session
      * @param Repository $config
      */
     public function __construct($config=[])
     {
         $this->config = $config;
     }
     /**
     * @param string $msg
     * @return string
     */
    public function testaaa($msg = ''){
        // dd($this->config);
        $this->addUser();
        return "收到了" . $msg . "@" . $this->randCode(20,5);
    }
    public function SendBatchMessages($data){
        $dataMes = [];
        $dataMes['fromAccid'] = $data["from"];//发送人accid
        // $dataMes['ope'] = 0;//0：点对点个人消息，1：群消息（高级群），其他返回414
        $dataMes['toAccids'] = json_encode($data["to"]);//        $dataMes['to'] = $data["to"];//送人accid
        $dataMes['type'] = $data["type"];//100 自定义消息类型,0文本,1图片,2语音,3视频,4地址里位置,5,文件,
        $dataMes['body'] = json_encode($data["body"]);
        // $dataMes['ext'] = json_encode($data["ext"]);
        // $dataMes['ext'] = "";
        $result = $this->yunxin_send($this->yunXinUrl['sendBatchMsg.action'],$dataMes);
        $result = json_decode($result,true);
        // p($dataMes);
        // dd($result);
        if($result['code'] != 200){
            return false;
        }
        return true;
    }
    public function SendCommonMessages($data){
        $dataMes = [];
        $dataMes['from'] = $data["from"];//发送人accid
        $dataMes['ope'] = 0;//0：点对点个人消息，1：群消息（高级群），其他返回414
        $dataMes['to'] = $data["to"];//        $dataMes['to'] = $data["to"];//送人accid
        $dataMes['type'] = $data["type"];//100 自定义消息类型,0文本,1图片,2语音,3视频,4地址里位置,5,文件,
        $dataMes['body'] = json_encode($data["body"]);
        // $dataMes['ext'] = json_encode($data["ext"]);
        // $dataMes['ext'] = "";
        $result = $this->yunxin_send($this->yunXinUrl['sendMsg.action'],$dataMes);
        $result = json_decode($result,true);
        // p($dataMes);
        // dd($result);
        if($result['code'] != 200){
            return false;
        }
        return true;
    }
    // 注册用户
    public function addUser($data = []){
        $user_id = $data['user_id'];
        $user['accid'] = strtolower($this->randCode(15,5) . $user_id);
        // $user['accid'] = "assistant";
        //$user['name'] = "1aaaa11aaaa11aaaa1";
        // dd($user['accid']);
        $user['name'] = $data['user_name']??'';
        $user['icon'] = $data['head_url']??'';
        $user['gender'] = $data['user_sex']??0;
        $result = $this->yunxin_send($this->yunXinUrl['create.action'],$user);
        $user = json_decode($result,true);
        if($user['code'] != 200){
            return false;
        }
        // dd($user);
        return $user['info'] ? $user['info'] : [];
    }
    public function setUserInfo($yunxin_accid,$data){
        $data['accid'] = $yunxin_accid;
        $result = $this->yunxin_send($this->yunXinUrl['updateUinfo.action'],$data);
        $result_arr = json_decode($result,true);
        if(isset($result_arr['code']) && $result_arr['code'] == 200){
            return true;
        }else{
            return false;
        }
    }
    // 生成随机数
    public function randCode($length = 5, $type = 0) {
    	$arr = array(
                    1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 
                    4 => "~@#$%^&*(){}[]|",
                    5 => "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
                    );
    	if ($type == 0) {
    		array_pop($arr);
    		$string = implode("", $arr);
    	} else if ($type == "-1") {
    		$string = implode("", $arr);
    	} else {
    		$string = $arr[$type];
    	}
    	$count = strlen($string) - 1;
        $code = '';
    	for ($i = 0; $i < $length; $i++) {
    		$str[$i] = $string[rand(0, $count)];
    		$code .= $str[$i];
    	}
    	return $code;
    }
    // post 请求
    public function yunxin_send($url='',$data=array(),$type="POST") {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// post数据
		curl_setopt($ch, CURLOPT_POST, 1);
        if($type == 'json'){
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $header= array('Content-Type: application/json');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }else{
            $header = [
                "Content-Type" => 'application/x-www-form-urlencoded;charset=utf-8',
                "AppKey" => $this->config['AppKey'],
                "Nonce" => $this->randCode(32,5),
                "CurTime" => time() . "",
            ];
            $token_str = $this->config['AppSecret'] . $header['Nonce'] . $header['CurTime'];
            $header['CheckSum'] = sha1($token_str);
            // dd($header);
            // $header = [
            //     "Content-Type" => 'application/x-www-form-urlencoded;charset=utf-8',
            //     "AppKey" => '43dd8978fa6c02658dd2336fde395f24',
            //     "Nonce" => "5",
            //     "CurTime" => "1548572902",
            //     "CheckSum" => "d2e8f94957f15274233c198df8cd061d6f62c643",
            // ];
            $header_post = [
                'Content-Type: ' . $header['Content-Type'],
                'AppKey: ' . $header['AppKey'],
                'Nonce: ' . $header['Nonce'],
                'CurTime: ' . $header['CurTime'],
                'CheckSum: ' . $header['CheckSum'],
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_post);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
		$output = curl_exec($ch);        
		curl_close($ch);
		return $output;
   }
}
