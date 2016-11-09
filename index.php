<?php
//session_start();
header("Content-Type:text/html; charset=utf-8");
require_once('Connections/connection1.php');

//啟動session
if (!isset($_SESSION)) {session_start();}
//$a = iconv("UTF-8","big5",$_POST["a"]);

//system("python QAsocket.py");
//echo "<script>";
//echo "window.open('sim.php','','width=750,height=450');";
//echo "<//script>";

//header('Location: sim.php');

//system('python QAsocket.py');

/*
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'tncvs713041';
$dbname = 'facebookdb';
*/
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue) 
{
  if (PHP_VERSION < 6) {
	 // 跳出字元設定
	 // get_magic_quotes_gpc - http://www.cnblogs.com/lsk/archive/2008/05/05/1184117.html
	 // get_magic_quotes_gpc - http://spcedu.tkblind.tku.edu.tw/~peterpan/php4/function.php-get_magic_quotes_gpc.htm
	 // stripslashes -         http://www.dk101.com/Discuz/viewthread.php?tid=133577#.U1I8MlWSx2I
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

//此為防止輸入攻擊 EX:http://soldierzx0705.blogspot.tw/2012/05/phpsql-mysqlrealescapestring.html
  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

     $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
     return $theValue;
}
}


//執行登出動作
if(isset($_GET['logout']) && ($_GET['logout']="true")) {
	$_SESSION['customerservice_loginUsername'] = NULL;
	$_SESSION['customerservice_loginName'] = NULL;
	unset($_SESSION['customerservice_loginUsername']);		
	unset($_SESSION['customerservice_loginName']);	
	
	header('location:index.php');
	exit();
}
//會員登入
if(isset($_POST['username']) && isset($_POST['password'])) {
	if($_POST['username']==""||$_POST['password']==""){
		header("location:?login=wrong");
	}else{
		//尋找會員資料
		$query_FindUsername = sprintf("SELECT * FROM `user_data` WHERE username=%s",GetSQLValueString($_POST['username']));
		$FindUsername = mysql_query($query_FindUsername);
		//取出帳號密碼的值
		$row_FindUsername = mysql_fetch_assoc($FindUsername);
		$sqlPassword = $row_FindUsername['password'];
		$sqlName = $row_FindUsername['name'];
		//比對密碼，若登入成功則呈現登入狀態
		if($_POST['password']==$sqlPassword) {
			$_SESSION['customerservice_loginUsername']=$_POST['username'];
			$_SESSION['customerservice_loginName']=$row_FindUsername['name'];
			//紀錄登入時間
			//$nowtime = date("Y/m/d H:i:s");
			//$query_update = sprintf("Update member SET lastlogin=%s WHERE username=%s",GetSQLValueString($nowtime),
			// GetSQLValueString($_POST['username']));
			//mysql_query($query_update);
			
			header("location:index.php");
		}else{
			header("location:?login=wrong");
		}
	}
}else{ //<---沒有傳送登入資料
	$conn = @mysql_pconnect('localhost', 'root', 'tncvs713041') ;
	$select_db = @mysql_select_db("room");
	mysql_select_db( "ai_customerservice" ) or die( 'Error'. mysql_error() );
	$sql="SELECT * FROM `room` ";
	$temp=mysql_query($sql) or die (mysql_error());
	$values = mysql_fetch_array($temp);
	if ($values[0] ==65000) {
	  $room_id=9000;
	}
	else{
		$room_id=$values[0]+1;
	}
	$sql="UPDATE `room` SET ";
	$sql .= " `id` = '".$room_id."' ";
	$sql .= "WHERE `id`=".$values[0];
	$temp=mysql_query($sql) or die (mysql_error());
	
	$login_state = "not login";
	if(isset($_SESSION['customerservice_loginUsername'])){
		$login_state = "have login";
		//pclose(popen('start C:\\xampp\\htdocs\\CustomerService\\QAsocket.py '.$room_id.' '.$_SESSION['customerservice_loginUsername'], 'r'));
		
		$sql="INSERT INTO `room_user`(`room_port`,`user`,`datetime`) VALUES ('".$room_id."','".$_SESSION['customerservice_loginUsername']."','".date("Y:m:d H:i:s")."')"; //<--- 目前這個似乎無用
		$temp=mysql_query($sql) or die (mysql_error());
	}else{
		//pclose(popen('start C:\\xampp\\htdocs\\CustomerService\\QAsocket.py '.$room_id, 'r'));
	}
	
	//$_SESSION['room_id']=$values[0];
	echo "<script>\r\n"; 
	echo "value=\"$room_id\";\r\n"; 
	echo "</script>\r\n"; 
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>智能文字客服機器人</title>
<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
<link rel="stylesheet" type="text/css" href="css/index.css"/>
<link rel="stylesheet" type="text/css" href="css/indexrwd.css">
<link rel="stylesheet" href="malihu-custom-scrollbar-plugin-master/jquery.mCustomScrollbar.css" />
<!--<script src="./dist/Chart.bundle.js"></script>-->
<script src="js/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="layer-v1.8.5/layer/layer.min.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function () {

	//如果頭像沒出來就重整
	setTimeout(function(){
		if($('div.server').length){
			login_state = '<?=$login_state?>';
			if(login_state=="not login"){
				$.layer({
				  shade: [0],
				  area: ['auto','auto'],
				  dialog: {
					msg: '請問您是本行的會員嗎？<br/>　',
					btns: 2,          
					type: 4,
					btn: ['是','否'],
					yes: function(){
					  login_show();
					}, no: function(){
					  layer.closeAll();
					}
				  }
				});
			}
		}else{
			history.go(0);
		}
	},3000);
	

	//----接收問題
    var socket = new WebSocket("ws://127.0.0.1:"+value);
    socket.onmessage = function(event) {
		info = event.data;
		info_array = new Array();
		info_array = info.split("|-|-|-|-|");
		show_info = "";
		if (info_array[0]=="0"){
			show_info += '<div class="server">';
			show_info += '<div class="head_img" style="width: 90%;">';
			show_info += '	<div class="info" style="margin-left: 80px;">';
		}else{
			show_info += '<div class="client">';
			show_info += '<div class="head_img" style="background-position: right;width: 90%;">';
			show_info += '	<div class="info" style="margin-right: 70px;">';
		}
		show_info += '		<div class="info_box">'+info_array[2]+'</div>';
		show_info += '		<div class="info_time">'+info_array[1]+'</div>';
		show_info += '	</div>';
		show_info += '</div>';
		show_info += '</div>';
		show_info += '<div class="clear" style="clear:both"></div>';
        $("div#message").append(show_info);		
		$("div.server").first().css('margin-top','30px');
		$("div.server").removeAttr('id'); //於最後一個訊息框加入id=last <--目前這個沒有用
		$("div.client").removeAttr('id');
		$("div#message").children().last().prev().attr("id","last");
		$(".content").mCustomScrollbar("scrollTo","bottom",0); //捲置最底部
		
    }
    
	//----傳送問題
	$("#send").click(function() {
		socket.send($("#text").val());
		$("#text").val("").select();
	});	
	$("input#text").keydown(function(ev){ //按Enter送出
		var ev=ev||window.event;
		if(ev.keyCode==13) {
			socket.send($("#text").val());
			$("#text").val("").select();
		}
	});
	
	//----調整樣式
	//調整輸入框的寬度
	$('input#text').width($('div#input_area').outerWidth()-64-24); //64是送出按鈕的寬度
	$(window).resize(function() {
        $('input#text').width($('div#input_area').outerWidth()-64-24);
    });	
	//調整輸入局域置
	margin_left = ($('div#content').innerWidth()-$('div#input_area').outerWidth())/2; 
	$('div#input_area').css("margin-left",margin_left.toString()); 
	$(window).resize(function() {
		margin_left = ($('div#content').innerWidth()-$('div#input_area').outerWidth())/2; 
		$('div#input_area').css("margin-left",margin_left.toString()); 
    });	
	
	//調整訊息視窗的高度
	if($('div#explan_area').is(':hidden')) {
		$('div#maincontent').height($('div#content').outerHeight()-$('div#title').outerHeight(true)-$('div#input_area').outerHeight(true)-$('.login_span').outerHeight(true));
	}else{
		$('div#maincontent').height($('div#content').outerHeight()-$('div#title').outerHeight(true)-$('div#input_area').outerHeight(true)-$('div#explan_area').outerHeight(true)-$('.login_span').outerHeight(true));
	}
	$(window).resize(function() {
		if($('div#explan_area').is(':hidden')) {
			$('div#maincontent').height($('div#content').outerHeight()-$('div#title').outerHeight(true)-$('div#input_area').outerHeight(true)-$('.login_span').outerHeight(true));
		}else{
			$('div#maincontent').height($('div#content').outerHeight()-$('div#title').outerHeight(true)-$('div#input_area').outerHeight(true)-$('div#explan_area').outerHeight(true)-$('.login_span').outerHeight(true));
		}
    });
	
	//---直接focus在TEXT上
	$('input#text').focus();
	
	if($('input#get_loginmessage').val()=="wrong"){
		login_show();
		layer.alert("帳號密碼錯誤！",5);
	}
	
});
</script>
<script>
function send_Number(theNumber){
	$('input#text').val(theNumber);
	$('span#send').click();

}
function wrong_answer(){
	$('input#text').val("這個回答不符合我問題的描述");
	$('span#send').click();

}
function explan_show(){ //----開關說明
	$('div#explan_area').slideToggle(function() {
		if($('div#explan_area').is(':hidden')) {
			$('span#explan').html('說明 ▲');
			$('div#maincontent').height($('div#content').outerHeight()-$('div#title').outerHeight(true)-$('div#input_area').outerHeight(true)-$('.login_span').outerHeight(true));
		}else{
			$('span#explan').html('說明 ▼');
			$('div#maincontent').height($('div#content').outerHeight()-$('div#title').outerHeight(true)-$('div#input_area').outerHeight(true)-$('div#explan_area').outerHeight(true)-$('.login_span').outerHeight(true));
		}
	});
}
</script>
<!-- 卷軸 http://tt5.org/%E5%A6%82%E4%BD%95%E8%87%AA%E8%A8%82-scrollbar-%E7%9A%84%E6%8D%B2%E8%BB%B8%E6%A8%A3%E5%BC%8F/ -->
<script src="malihu-custom-scrollbar-plugin-master/jquery.mCustomScrollbar.concat.min.js"></script>
<script>
(function($){
	$(window).on("load",function(){
		$(".content").mCustomScrollbar({
                theme:"light-thick", // 設定捲軸樣式
                 // setWidth: 350,設定寬度
                 // setHeight: 150, 設定高度
        });
	});
})(jQuery);
</script>
<script>
function test(){
	 $(".content").mCustomScrollbar("scrollTo","bottom");
}
function login_show(){
	$.layer({
	  type: 1,
	  shade: [0],
	  area: ['auto', 'auto'],
	  title: false,
	  closeBtn: [0, true], //去掉默认关闭按钮
	  border: [0],
	  page: {
		  html: '<div class="login-block" style="width: 320px;padding: 20px;background: #fff;border-radius: 5px;border-top: 5px solid #0061c0;margin: 0 auto;font-family: Montserrat;-moz-box-shadow:4px 4px 3px rgba(20%,20%,40%,0.5);-webkit-box-shadow:4px 4px 3px rgba(20%,20%,40%,0.5);box-shadow:4px 4px 3px rgba(20%,20%,40%,0.5);"><h1>會員登入</h1><input type="text" value="" placeholder="帳號" id="username" name="username" class="username1" /><input type="password" value="" placeholder="密碼" id="password" name="password" class="password1" /><button type="submit" name="submit" id="submit_btn" onclick="$(\'input.username\').val($(\'input.username1\').val());$(\'input.password\').val($(\'input.password1\').val());$(\'form#login\').removeAttr(\'onsubmit\');$(\'form\').submit()">登入</button><button id="pagebtn" class="btns" onclick="layer.closeAll();">取消</button></div>'
		  }
	});
	$('div.xubox_main').css('background','none');
	$('div.xubox_main').css('box-shadow','4px 4px 3px rgba(20%,20%,40%,0.5);');
	
}
</script>
</head>
<body>
<form name="login" id="login" method="post" onsubmit="return false">
    <div id="content">
    	<div id="test"></div>
        <div id="framecontent">
            <div id="title" style="text-align:center;">
            	<span>智能文字客服機器人-即問即答</span>
                <span id="explan" onclick="explan_show()">說明 ▼</span>
                <span id="explanrwd" class="rwd">說明</span>
                <input id="test_btn" type="button" value="test" style="display:none" onclick='test()'/>
                <input id="get_loginmessage" name="get_loginmessage" type="hidden" value="<?php if(isset($_GET['login'])&&$_GET['login']=="wrong"){echo "wrong";}?>" />
                <input type="hidden" value="" id="username" name="username" class="username" />
                <input type="hidden" value="" id="password" name="password" class="password" />
            </div>
            <?php
			if(isset($_SESSION['customerservice_loginUsername'])){
				echo '<a href=\'?logout=true\' class="login_span">登出</a>';
			}else{
				echo '<span class="login_span" onclick="login_show()">會員登入</span>';
			}
			?>
        </div>
        <div id="explan_area">
        	<div id="explan_area_text">
            ▪　為保護您的權益，請詳細閱讀相關「<a href="#" style="font-weight: bold;color: #1676cc;">個人資料運用告知聲明</a>」。當您開始填寫資料時，視同您已充分了解並同意本行將開始蒐集與處理您的個人資料。<br/>
            ▪　智能文字交談服務時間：7*24全年無休。<br/>
            ▪　轉接真人服務時間：週一至週日07:30~23:00。<br/>
            ▪　服務範圍：本服務提供產品諮詢(含商品服務、行銷活動、信用卡優惠…等)，及真人文字客服輕帳務查詢(如：繳款金額、繳款期限、點數查詢)，請多加利用。<br/>
            ▪　請勿傳遞與本行業務無關或違法訊息，本行有權終止非服務範圍內之訊息傳遞。<br/>
            ▪　為避免有心人士追蹤、竊取資料，若您使用公用電腦，請勿輸入私人機密資料(如：身分證字號、信用卡號、理財密碼…等)。<br/>
            </div>
            <div id="explan_area_close"><img src="images/cross.png" width="20" height="20" style="cursor: pointer;margin-top: 7px;" onclick="explan_show()"/></div>
        </div>
        <div id="maincontent" class="content">
			<div style="text-align:left;" id="message">
            	<div class="server" style="margin-top: 30px;">
                	<div class="head_img" style="width: 90%;">
                    	<div class="info" style="margin-left: 80px;">
                    		<div class="info_box">Hello~您好！我是智能文字客服機器人小Y，很高興為您服務，請問有什麼問題可以為您解答的呢？</div>
                        	<div class="info_time">2016-10-29 10:27:06</div>
                        </div>
                     </div>
                </div>
                <div class="clear" style="clear:both"></div>
                <div class="client">
                	<div class="head_img" style="background-position: right;width: 90%;">
                    	<div class="info" style="margin-right: 70px;">
                        	<div class="info_box">你好</div>
                            <div class="info_time">2016-10-29 10:28:07</div>
                        </div>
                    </div>
                </div>
                <div class="clear" style="clear:both"></div>
                <div class="server">
               		<div class="head_img" style="width: 90%;">
                        <div class="info" style="margin-left: 80px;">
                            <div class="info_box">
                                <div style="margin-top:5px">哈囉，請問有甚麼問題呢?</div>
                                <div style="margin-top:5px">
                                    <span class="line"></span>
                                    <span class="options" onclick="wrong_answer()">#這個回答不符合我問題的描述</span>
                                </div>
                            </div>
                            <div class="info_time">2016-10-29 10:28:07</div>
                        </div>
                	</div>
                </div>
                <div class="clear" style="clear:both"></div>
                <div class="client">
                	<div class="head_img" style="background-position: right;width: 90%;">
                    	<div class="info" style="margin-right: 70px;">
                        	<div class="info_box">想問信用卡</div>
                            <div class="info_time">2016-10-29 10:28:13</div>
                        </div>
                    </div>
                </div>
                <div class="clear" style="clear:both"></div>
                <div class="server" id="last">
                	<div class="head_img" style="width: 90%;">
                    	<div class="info" style="margin-left: 80px;">
                        	<div class="info_box">您好，下列是關於「信用卡」服務，請問您想瞭解的是：
                            	<div style="margin-top:5px">輸入1. <span class="options" onclick="send_Number(1)">信用卡申請</span><br>輸入2. <span class="options" onclick="send_Number(2)">信用卡停卡</span><br>輸入3. <span class="options" onclick="send_Number(3)">信用卡優惠回饋項目</span><br>輸入4. <span class="options" onclick="send_Number(4)">卡友權益</span><br>輸入5. <span class="options" onclick="send_Number(5)">信用卡產品介紹</span><br>輸入6. <span class="options" onclick="send_Number(6)">用卡須知</span><br></div><div style="margin-top:5px"><span class="line"></span><span class="options" onclick="wrong_answer()">#這個回答不符合我問題的描述</span>
                                </div>
                            </div>
                            <div class="info_time">2016-10-29 10:28:13</div>
                        </div>
                    </div>
                </div>
                <div class="clear" style="clear:both"></div>
            </div>
            
        <!-- 顯示占用的PORT
        <script type="text/javascript"> 
        document.write(value); 
        </script> 
        -->
        </div>
        <div id="input_area">
        	<input type="text" id="text" placeholder="請輸入您的問題，建議精簡扼要的描述（例如：信用卡申辦）">
            <span id="send">送出</span>
        </div>
    </div>
</form>
</body>

</html>