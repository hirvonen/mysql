<?php
define("TOKEN", "phptestjbf");
define("AppID", "wxfad891501f5e751d");
define("EncodingAESKey", "xIb7PhJVeqgQvWaE774mCt7uwQgifSD6v99BAVNhlEH");

require (dirname(__FILE__).'/'.'encrypt/wxBizMsgCrypt.php');
require (dirname(__FILE__).'/'.'msgHandle/msgHandle.php');

$wechatObj = new wechatCallback();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
} 

class wechatCallback
{
	//验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"]; 
        $tmpArr = array(TOKEN, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr ); 
         if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

	//响应消息
    public function responseMsg()
    {
    	/*
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; 
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgtype_rcv = trim($postObj->MsgType);
            switch($msgtype_rcv){
                case "text":
                    //$this->handleText($postObj);
                    break;
                case "event":
                    $this->handleEvent($postObj);
                    break;
                default:
                    break;
            }
        }else{
            echo "";
            exit;
        }
        */
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $msg_signature = $_GET['msg_signature'];
        $encrypt_type = (isset($_GET['encrypt_type']) && ($_GET['encrypt_type']=='aes')) ? 'aes':'raw';

        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        if(!empty($postStr)){
        	//解密
	    	if ($encrypt_type == 'aes'){
	    		$pc = new WXBizMsgCrypt(TOKEN, EncodingAESKey, AppID);                
				$this->logger(" D \r\n".$postStr);
				$decryptMsg = "";  //解密后的明文
				$errCode = $pc->DecryptMsg($msg_signature, $timestamp, $nonce, $postStr, $decryptMsg);
				$postStr = $decryptMsg;
		    }
		    $this->logger(" R \r\n".$postStr);
		    $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		    $RX_TYPE = trim($postObj->MsgType);
        }

        //消息类型分离
        $msghdl = new msgHandle($postObj);
	    switch ($RX_TYPE)
	    {
	        case "event":
	            $result = $msghdl->receiveEvent();
	            break;
	        case "text":
	            $result = $msghdl->receiveText();
	            break;
	    }
	    $this->logger(" R \r\n".$result);
	    //加密
	    if ($encrypt_type == 'aes'){
	        $encryptMsg = ''; //加密后的密文
	        $errCode = $pc->encryptMsg($result, $timeStamp, $nonce, $encryptMsg);
	        $result = $encryptMsg;
	        $this->logger(" E \r\n".$result);
	    }
        echo $result;
    }

    //日志记录
	public function logger($log_content)
	{
	    if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
	        sae_set_display_errors(false);
	        sae_debug($log_content);
	        sae_set_display_errors(true);
	    }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
	        $max_size = 500000;
	        $log_filename = "log.xml";
	        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
	        file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
	    }
	}
}
?>