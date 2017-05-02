<?php
include "funcs.php";
if(!isset($_SESSION['thisID'])){
    $_SESSION['thisID'] = "";
}
if(!isset($_SESSION['fname'])){
    $_SESSION['fname'] = "";
}
if(!isset($_SESSION['lname'])){
    $_SESSION['lname'] = "";
}
$now = time();
Connect();
if(!isset($_POST['theDate'])){
	$_POST['theDate']="";
}
if(!isset($_POST['time'])){
	$_POST['time']="";
}
if(!isset($_GET['login'])){
	$_GET['login']=0;
}
if(!isset($_GET['logout'])){
	$_GET['logout']=0;
}
if(!isset($_GET['del'])){
	$_GET['del']=0;
}
if(!isset($_GET['id'])){
	$_GET['id']=0;
}
if(!isset($_GET['ctID'])){
	$_GET['ctID']=0;
}
if(!isset($_GET['view'])){
	$_GET['view']=0;
}

if($_GET['logout'] == 1){
	LogOut();
}
if(!isset($_SESSION['loggedIn'])){
	$_SESSION['loggedIn'] = false;
}
if(!isset($_SESSION['logStatus'])){
	$_SESSION['logStatus'] = "";
}
$logStatus = $_SESSION['logStatus'];
$status = $_SESSION['loggedIn'];
//check if trying to login
if(isset($_POST['firstName']) or isset($_POST['lastName'])){
	//validate login
}
if(!isset($_GET['sighting'])){
	$_GET['sighting'] = 0;
}
if(!isset($_SESSION['ol'])){
			$_SESSION['ol'] = 0;
}
if(!isset($_SESSION['tot'])){
			$_SESSION['tot'] = 0;
}
//user is active, update their timestamp

//print $_SESSION['thisID'];
$q = "update users set timestamp = $now where user_id = '".$_SESSION['thisID']."'";
Query($q);
//set all users to inactive
$q = "update users set active = 0";
Query($q);
//set all users to "active" where timestamp is > $now - 10
$then = $now - 300;
$q = "update users set active = 1 where timestamp > '$then'";
Query($q);
?>
<!DOCTYPE html>
<html>
<head>
	<title>TrainTrack 1.0 <?php print $_SESSION['fname']." ".$_SESSION['lname']?></title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/bfa5ac74be.js"></script>

	<link rel = "stylesheet" type="text/css" href="style.css">

</head>
<body>
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">Train Track 1.0</a>
      </div>
      <ul class="nav navbar-nav">
      	<li ><a href="index.php"> <i class="fa fa-home"></i> Home</span></a></li>
  		<li><a href="contributors.php" > <i class="fa fa-user-circle"></i> Contributors</a></li>
      <li><a target = "blank" href="https://www.facebook.com/groups/VictorianRailwayEnthusiasts/1234249243277762/?notif_t=like&notif_id=1484865710034242"><i class="fa fa-facebook-official"></i> Forum
          </a></li>
  		<li class="active"><a href="help.php"><i class="fa fa-book"> Help </i></a></li>
  		<li><a href="account.php"><i class="fa fa-cog"></i> Account </a></li>

        <li><?php print date("dS M",time());?></li>
      </ul>
      <ul class = "nav navbar-nav navbar-right"><li><?php print $logStatus;?></li></ul>
    </div>
  </nav>
<div class="container">
	<h2>User Guide</h2>

</div>
<div class="container" style = "color:black">
	<div class="row"><!-- THREE COLUMNS left column for data entry, right for data display -->
		<div class="col-md-3">	</div><!-- END COL -->

		<div class="panel panel-info">
		<div class="panel-heading">ABOUT</div>
		<div class="panel-body">
		<p>TrainTrack is a simple logger for recording information train operations. It is a website, designed to be functional
		on any browser and any device.</p>
		</div>
		</div>

		<div class="panel panel-info">
		<div class="panel-heading">USAGE</div>
		<div class="panel-body">
		<p><strong>Login:</strong></p>
		<p>When you are first added to the users list, you will be given a default password. To login, you will need
		to enter your first and last name, plus the default password. Once you've succesfully logged in, you will be able to
		enter a new password by clicking on the "Account" menu button. Simply enter your new password and click <kbd>update password</kbd></p>
		<p><strong>Adding a new sighting:</strong></p>
		<p>To begin logging your sightings, first you'll need to login to your account. Once logged in succesfully, you'll be presented with
		a basic data entry form on the left of the screen. After entering the details of your sighting
		information into the form, click on the <kbd>ENTER</kbd> button and your sighting will be logged and should appear in
		the listings in the center of the screen.<br />
		If you made an error or wish to remove the sighting from the log, simply click on the <kbd>DEL</kbd> button that corresponds
		to your entry and that entry will be removed permanenty. Or, click on <kbd>Account</kbd> in the top menu to edit any sightings you've made.
		</p>
		</div>
		</div>

		<div class="panel panel-info">
		<div class="panel-heading">UPDATES</div>
		<div class="panel-body">
		<p>The software that drives the application is currently being developed on a regular basis, so
		expect more features and functionality to be added. These additions will be aimed at making the application
		as useful and as easy to use as possible.</p>
		</div>
		</div>
		</div>
		<div class="col-md-6 displayPanel"><!-- DATA DISPLAY -->
		</div><!-- END DATA DISPLAY -->
		<div class="col-md-3 usersListPanel">
		</div><!-- USER LIST -->
	</div><!-- END ROW -->
</div>
</body>
</html>
