<?php
define("TOKEN", "phptestjbf");
define("AppID", "wxfad891501f5e751d");
define("EncodingAESKey", "xIb7PhJVeqgQvWaE774mCt7uwQgifSD6v99BAVNhlEH");

require('wxBizMsgCrypt.php');

$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
} 

class wechatCallbackapiTest
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
	    switch ($RX_TYPE)
	    {
	        case "event":
	            $result = $this->receiveEvent($postObj);
	            break;
	        case "text":
	            $result = $this->receiveText($postObj);
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

    //Common Funcs
    private function getTpl($postObj)
    {
        $msgType = trim($postObj->MsgType);
        switch ($msgType) {
            case 'text':
                $replyTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
                break;
            case 'event':
                $replyTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
                break;
            default:
                break;
        }
        return $replyTpl;
    }

    //Text Handle Funcs
    private function handleText($postObj)
    {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $time = time();
        $textTpl = $this->getTpl($postObj);
        $contentStr = $this->getReplyText($postObj);
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
        echo $resultStr;
    }
    private function getReplyText($postObj)
    {
        $keyword = trim($postObj->Content);
        if($keyword == "women"){
            $contentStr = "E-young【产后上门护理】\n".'<a href="http://sekikou.oicp.net:48446/wordpress/"> 我们的站点</a>';
        }else{
            $contentStr = "test OK: ".$keyword;
        }
        return $contentStr;
    }

    //Event Handle Funcs
    private function handleEvent($postObj)
    {
        $event = trim($postObj->Event);
        switch ($event) {
            case 'CLICK':
                $this->handleEvent_Click($postObj);
                break;
            case 'subscribe':
            //$this->handleEvent_Subscribe($postObj);
	            break;
            default:
                # code...
                break;
        }
    }
    private function handleEvent_Click($postObj)
    {
        $eventKey = trim($postObj->EventKey);
        switch ($eventKey) {
            case 'myOrder'://我的订单
                //connect to db
                $dbhost = '121.41.104.220';
                $dbuser = 'root';
                $dbpass = 'Cccc1111';
	            $db_name = 'eyoungdb';
                $conn = mysql_connect($dbhost, $dbuser, $dbpass);
                $retval = mysql_select_db( $db_name );
                $sql = "SHOW TABLES FROM eyoungdb ";
                $retval = mysql_query($sql, $conn);
            	
           		while($row = mysql_fetch_row($retval)){
          	          //echo "<tr><td>$row[0]</td></tr>";
                    $strtemp = $strtemp."$row[0]"."\n";
          		}
            //    $row = mysql_fetch_row($retval);

                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $time = time();
                $textTpl = $this->getTpl($postObj);
                $contentStr = $strtemp;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                echo $resultStr;
                break;
            case 'myInfo':
            	$fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $time = time();
                $textTpl = $this->getTpl($postObj);
                $contentStr = "myINFO";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                echo $resultStr;
                break;
            case 'memberCharge':
            	$fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $time = time();
                $textTpl = $this->getTpl($postObj);
                $contentStr = "myINFO";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                echo $resultStr;
                break;
            default:
                # code...
                break;
        }
    }
}
?>