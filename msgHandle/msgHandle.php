<?php

class msgHandle
{
    private $postObj;

    public function msgHandle($postObj)
    {
        $this->postObj = $postObj;
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