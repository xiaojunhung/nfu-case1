<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
header("Content-Type: text/html; charset=utf-8"); //Hank 自編 《PHP專書P14-3》
$hostname_connection1 = "localhost";
$database_connection1 = "ai_customerservice";
$username_connection1 = "root";
$password_connection1 = "tncvs713041";
$connection1 = mysql_pconnect($hostname_connection1, $username_connection1, $password_connection1) or trigger_error(mysql_error(),E_USER_ERROR); 
if (!@mysql_select_db("ai_customerservice")) die("資料庫選擇失敗");
mysql_query("SET NAMES 'utf8'"); //Hank 自編 《PHP專書P14-8》
?>