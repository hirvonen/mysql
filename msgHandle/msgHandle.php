<?php
define("DBHOST", "121.41.104.220");
define("DBUSER", "root");
define("DBPASS", "Cccc1111");
define("DBNAME", "eyoungdb");

define("REDIRECT_URI", "http://hirvonen.sinaapp.com/oauth2.php");
define("SCOPE", "snsapi_userinfo");
define("OAUTH2_URL", "https://open.weixin.qq.com/connect/oauth2/authorize?");

class msgHandle
{
    private $postObj;
    private $db_conn;
    private $send_fromUsername;
    private $send_toUsername;
    private $send_time;
    private $send_textTpl;

    /**
     * 构造函数
     * @param $postObj
     */
    public function __construct($postObj)
    {
        $this->postObj = $postObj;
        $this->send_fromUsername = $this->postObj->ToUserName;
        $this->send_toUsername = $this->postObj->FromUserName;
        $this->send_time = time();
        $this->send_textTpl = $this->getTemplate($this->postObj);
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
                $result = $this->receiveEvent_Click();
                break;
            case 'subscribe':   //用户关注事件处理
                $result = $this->receiveEvent_Subscribe();
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

    /**
     * Text受信处理函数
     * @return int
     */
    public function receiveText()
    {
        $contentStr = $this->getReplyText($this->postObj);
        $result = $this->sentText($contentStr);
        return $result;
    }


    //内部函数
    /**
     * 点击按钮事件处理函数
     */
    private function receiveEvent_Click()
    {
        $this->connectDB();
        $this->selectDB();
        $eventKey = trim($this->postObj->EventKey);
        switch ($eventKey) {
            case 'viewdb'://查看数据
                //$sql = "SHOW TABLES FROM eyoungdb ";
                $sql = "SELECT * FROM tbl_user ";
                $retval = mysql_query($sql, $this->db_conn);

                $strtemp = '';
                while($row = mysql_fetch_row($retval)){
                    //echo "<tr><td>$row[0]</td></tr>";
                    $strtemp = $strtemp.$row[0]." ".
                        $row[1]." ".
                        $row[2]." ".
                        $row[3]." ".
                        $row[4]." ".
                        $row[5]." ".
                        "\n";
                }
                //    $row = mysql_fetch_row($retval);
                $contentStr = $strtemp;
                break;
            case 'oauth2'://授权测试
                $contentStr = "授权测试".
                    "<a href='https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx03cccee44426ee51&secret=80f8942c040ff31e6f631038b85e7763&code=031727092636d6725cdfc0e74a3f11fn&grant_type=authorization_code'>点击这里获得openid</a>";
                break;
            case 'key_bargan'://注册用户
                $contentStr = "优惠活动准备中！敬请期待！";
                break;
            default:
                $contentStr = "哎呦出错啦！请联系我们！021-XXXXXXXX";
                break;
        }
        $result = $this->sentText($contentStr);
        $this->disconnectDB();
        return $result;
    }

    /**
     * 关注事件处理函数
     * @return int
     */
    private function receiveEvent_Subscribe()
    {
        $contentStr = "";
        $title = "欢迎关注Eyoung！\n我们将为您提供最完美的产后护理上门服务！";
        $description = "点击此处立即免费体验我们的优质上门服务！";
        $picurl = "http://www.animal-wallpaper.org/backgrounds/butterfly/Pink_butterfly_at_pink_flower.jpg";
        $url = "http://www.eyoungbeauty.com/e-young/index.php?r=commodity/trial";
        $result = $this->sentPicText($contentStr,$title,$description,$picurl,$url);
        return $result;
    }

    private function connectDB()
    {
        $this->db_conn = mysql_connect(DBHOST, DBUSER, DBPASS);
    }

    private function disconnectDB()
    {
        mysql_close($this->db_conn);
    }

    private function selectDB()
    {
        if( $this->db_conn ) {
            mysql_select_db( DBNAME );
        }
    }

    private function getTemplate()
    {
        $msgType = trim($this->postObj->MsgType);
        $replyTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        switch ($msgType) {
            case 'text':
                break;
            case 'event':
                $event = trim($this->postObj->Event);
                switch($event) {
                    case 'subscribe':
                        $replyTpl = "<xml>
                                    <ToUserName><![CDATA[%s]]></ToUserName>
                                    <FromUserName><![CDATA[%s]]></FromUserName>
                                    <CreateTime>%s</CreateTime>
                                    <MsgType><![CDATA[%s]]></MsgType>
                                    <Content><![CDATA[%s]]></Content>
                                    <ArticleCount>1</ArticleCount>
                                    <Articles>
                                        <item>
                                            <Title><![CDATA[%s]]></Title>
                                            <Description><![CDATA[%s]]></Description>
                                            <PicUrl><![CDATA[%s]]></PicUrl>
                                            <Url><![CDATA[%s]]></Url>
                                        </item>
                                    </Articles>
                                    <FuncFlag>0</FuncFlag>
                                    </xml>";
                        break;
                    case 'CLICK':
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
        return $replyTpl;
    }

    private function sentText($contentStr)
    {
        $resultStr = sprintf($this->send_textTpl,
            $this->send_toUsername,
            $this->send_fromUsername,
            $this->send_time,
            "text",
            $contentStr);
        return $resultStr;
    }

    private function sentPicText($contentStr,$title,$description,$picurl,$url)
    {
        $resultStr = sprintf($this->send_textTpl,
            $this->send_toUsername,
            $this->send_fromUsername,
            $this->send_time,
            "news",
            $contentStr,
            $title,
            $description,
            $picurl,
            $url);
        return $resultStr;
    }

    private function getReplyText()
    {
        $keyword = trim($this->postObj->Content);
        if($keyword == "women"){
            $contentStr = "E-young【产后恢复美疗】\n".'<a href="http://www.eyoungbeauty.com/e-young"> 我们的站点</a>';
        }else{
            $contentStr = "程序员玩儿命施工中，敬请期待。 ".$keyword;
        }
        return $contentStr;
    }
}
?>