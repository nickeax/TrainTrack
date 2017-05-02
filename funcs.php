<?php //FUNCS_PHP
session_start();
if(!isset($_SESSION['thisID'])){
    $_SESSION['thisID'] = "";
}
if(!isset($_SESSION['fname'])){
    $_SESSION['fname'] = "";
}
if(!isset($_SESSION['lname'])){
    $_SESSION['lname'] = "";
}
function Error($message, $sqlError){
    print "<div class=\"alert alert-warning\">";
    print"<p>$message</p><hr />";
    print"<em>Messages</em>:[".$sqlError."] click <a href = \"index.php\">[here]</a> to go home</div>";
    die();
}//end Error
function Connect(){
    //print"The site is currently down...";
    $link = "";
    include("lsconfig.php");
    if(!$link = mysqli_connect($host, $user, $password, $dbname)){
      Error("Connection trouble!!",mysqli_info($link));
    }//end if !mysqli
    mysqli_select_db($link, $dbname);
    return $link;
}//end connect
function Query($q){
    //print"The site is currently down...";
    $link = Connect();
    if(!$res = mysqli_query($link, $q)){
        print mysqli_info($link);
        Error("Database Error.", mysqli_info($link));
    }
    mysqli_close ( $link );
    return $res;
}
function QueryArr($q){
    if(!$res = mysqli_query($link, $q))
        Error("Database Error.", mysqli_error());
	$arr = mysqli_fetch_array($res);
    return $arr;
}
function CheckLogin($firstName, $lastName, $pwd){
		$pwd = md5($pwd);
    $sql = "select * from users where first_name = '".$firstName."' and
         last_name = '".$lastName."' and pwd = '".$pwd."'";
    $link = Connect();
    if(!($res = mysqli_query($link, $sql)))Error("Database Error...", mysqli_report($link));
    $arr = mysqli_fetch_row($res);
    if(!$num = mysqli_num_rows($res)){
        $_SESSION['loggedIn'] = 0;
    }else{
         $_SESSION['loggedIn']= 1;
         $_SESSION['thisID']=$arr[0];
         $_SESSION['fname'] = $firstName;
         $_SESSION['lname'] = $lastName;
         $userID = OneVal("select user_id from users where first_name = '".$firstName."' AND last_name = '".$lastName."'");
         $_SESSION['isAdmin'] = OneVal("select admin from users where first_name = '".$firstName."' AND last_name = '".$lastName."'");
         Query("update users set logged_in = 1 where user_id = $userID");
         Query("update users set uip = '".$_SERVER['REMOTE_ADDR']."' where user_id = $userID");
         //Error("Login success!","You have logged in");
    }
}//end CheckLogin     style = \"text-align:left\"
function LogOut(){
    $userID = OneVal("select user_id from users where first_name = '".$_SESSION['fname']."' AND last_name = '".$_SESSION['lname']."'");
    Query("update users set logged_in = 0 where user_id = $userID");
    $_SESSION['loggedIn'] = false;
    $_SESSION['fname'] = "";
    $_SESSION['lname'] = "";
}
function OneVal($q){
    Connect();
    $res = Query($q);
    $arr = mysqli_fetch_row($res);
    $ret = $arr[0];
    return $ret;
}//end OneVal
?>
