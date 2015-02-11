<?php

class msgHandle
{
    private $postObj;

	/**
     * 构造函数
     * @param $postObj
     */
    public function msgHandle($postObj)
    {
        $this->postObj = $postObj;
    }


	/**
     * Event受信处理函数
	 * @return int
     */
    public function receiveEvent()
    {
        $event = trim($this->postObj->Event);
        switch ($event) {
            case 'CLICK':   //点击按钮事件处理
                $this->receiveEvent_Click();
                break;
            case 'subscribe':   //用户关注事件处理
                //$this->handleEvent_Subscribe($this->postObj);
                break;
            default:
                break;
        }
        return ErrorCode_Handle::$OK;
    }

	/**
     * 点击按钮事件处理函数
     */
    private function receiveEvent_Click()
    {
        $eventKey = trim($this->postObj->EventKey);
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

                $fromUsername = $this->postObj->FromUserName;
                $toUsername = $this->postObj->ToUserName;
                $time = time();
                $textTpl = $this->getTpl($this->postObj);
                $contentStr = $strtemp;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                echo $resultStr;
                break;
            case 'myInfo':
                $fromUsername = $this->postObj->FromUserName;
                $toUsername = $this->postObj->ToUserName;
                $time = time();
                $textTpl = $this->getTpl($this->postObj);
                $contentStr = "myINFO";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                echo $resultStr;
                break;
            case 'memberCharge':
                $fromUsername = $this->postObj->FromUserName;
                $toUsername = $this->postObj->ToUserName;
                $time = time();
                $textTpl = $this->getTpl($this->postObj);
                $contentStr = "myINFO";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                echo $resultStr;
                break;
            default:
                # code...
                break;
        }
    }

	/**
     * Text受信处理函数
     * @return int
     */
    public function receiveText()
    {
        $fromUsername = $this->postObj->FromUserName;
        $toUsername = $this->postObj->ToUserName;
        $time = time();
        $textTpl = $this->getTpl($this->postObj);
        $contentStr = $this->getReplyText($this->postObj);
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
        echo $resultStr;
        return ErrorCode_Handle::$OK;
    }


    //Common Funcs
    private function getTpl($postObj)
    {
        $msgType = trim($this->postObj->MsgType);
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

    private function getReplyText()
    {
        $keyword = trim($this->postObj->Content);
        if($keyword == "women"){
            $contentStr = "E-young【产后上门护理】\n".'<a href="http://sekikou.oicp.net:48446/wordpress/"> 我们的站点</a>';
        }else{
            $contentStr = "test OK: ".$keyword;
        }
        return $contentStr;
    }
}
?>