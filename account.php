<?php
// die("The site is currently down. Please check back later.");
include("funcs.php");
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
$userID = "";
Connect();
if(!isset($_POST['theDate'])){
	$_POST['theDate']="";
}
if(!isset($_GET['edit'])){
	$_GET['edit']=0;
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
if($_GET['login']==1){
	if(!isset($_POST['firstName'])){
	$_POST['firstName']=0;
	}
	if(!isset($_POST['lastName'])){
	$_POST['lastName']=0;
	}
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	Connect();
	CheckLogin($firstName, $lastName);
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
$status = $_SESSION['loggedIn'];
//check if trying to login
if(isset($_POST['firstName']) or isset($_POST['lastName'])){
	//validate login
}
if(!isset($_GET['sighting'])){
	$_GET['sighting'] = 0;
}
if(!isset($_GET['newPD'])){
	$_GET['newPD'] = "";
}
if(!isset($_SESSION['ol'])){
			$_SESSION['ol'] = 0;
}
if(!isset($_SESSION['tot'])){
			$_SESSION['tot'] = 0;
}

if($_SESSION['isAdmin'] == 0) {
	$q = "update users set timestamp = $now where user_id = '".$_SESSION['thisID']."'";
	Query($q);
	//set all users to inactive
	$q = "update users set active = 0";
	Query($q);
	//set all users to "active" where timestamp is > $now - 10
	$then = $now - 300;
	$q = "update users set active = 1 where timestamp > '$then'";
	Query($q);
}

if($_GET['edit'] == 1){
	if(!isset($_POST['motivePower'])){
	$_POST['motivePower'] = array("no ", "information", "entered");
	}
	if(!isset($_POST['location'])){
	$_POST['location'] = "missing";
	}
	if(!isset($_POST['direction'])){
	$_POST['direction'] = "missing";
	}
	if(!isset($_POST['trainNumber'])){
	$_POST['trainNumber'] = "missing";
	}
	if(!isset($_POST['motivePowerDescription'])){
	$_POST['motivePowerDescription'] = "missing";
	}
	if(!isset($_POST['notes'])){
	$_POST['notes'] = "";
	}
	if(!isset($_POST['ctID'])){
	$_POST['ctID'] = 100000;
	}
	if(!isset($locoString)){
	$locoString = "";
	}
	$mp[] = $_POST['motivePower'];
	//prepare data for storage
	$locoString = $_POST['motivePower'];
	$theDate = $_POST['theDate'];
	$time = $_POST['time'];
	//print "DATE: ".$theDate." TIME: ".$time;
	//set local vars
	$firstName = $_SESSION['fname'];
	$lastName = $_SESSION['lname'];
	$direction = $_POST['direction'];
	$location = $_POST['location'];
	$trainNumber = $_POST['trainNumber'];
	$firstName = trim($firstName);
	$lastName = trim($lastName);
	$motivePowerDescription = $_POST['motivePower'];
	$motivePowerDescription = ltrim($motivePowerDescription);
	$notes = $_POST['notes'];
	$firstName = filter_var($firstName, FILTER_SANITIZE_STRING);
	$lastName = filter_var($lastName, FILTER_SANITIZE_STRING);
	$direction = filter_var($direction, FILTER_SANITIZE_STRING);
	$location = filter_var($location, FILTER_SANITIZE_STRING);
	$trainNumber = filter_var($trainNumber, FILTER_SANITIZE_STRING);
	$notes = filter_var($notes, FILTER_SANITIZE_STRING);
	//$motivePowerDescription = filter_var($motivePowerDescription, FILTER_SANITIZE_STRING);
	if($time == "" or $theDate == ""){
		$date = OneVal("select date from sighting where ct_id = ".$_POST['ctID']."");
	}else	$date = strtotime($theDate." ".$time);
	$userID = OneVal("select user_id from users where first_name = '".$firstName."' AND last_name = '".$lastName."'");
	//INSERT DATA
	$q = "update sighting set
		date = ".$date.",
		location = '".$location."',
		direction = '".$direction."',
		train_number = '".$trainNumber."',
		motive_power = '".$motivePowerDescription."',
		notes = '".$notes."' where ct_id = ".$_POST['ctID']."
		";
	print $_POST['ctID'];
	Connect();
	Query($q);

}
if($_GET['newPD'] == 1){
	if(!isset($_POST['pwd'])){
	$_POST['pwd'] = "21232f297a57a5a743894a0e4a801fc3";
	}
	$pwd = $_POST['pwd'];
	//$pwd = filter_var($pwd, FILTER_SANITIZE_STRING);
	$pwd = md5($pwd);
	$userID = $_SESSION['thisID'];
	$q = "update users set pwd = '".$pwd."' where user_id = ".$userID."";
	Query($q);
}
Connect();
$logStatus = ($status == true?"<a href = \"index.php?logout=1\">LOGOUT [".$_SESSION['fname']."]</a>":"<a>You aren't logged in</a>");
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
        <li><a href="index.php"> <i class="fa fa-home"></i> Home</span></a></li>
      <li><a href="contributors.php" > <i class="fa fa-user-circle"></i> Contributors</a></li>
      <li><a target = "blank" href="https://www.facebook.com/groups/VictorianRailwayEnthusiasts/1234249243277762/?notif_t=like&notif_id=1484865710034242"><i class="fa fa-facebook-official"></i> Forum
          </a></li>
      <li><a href="help.php"><i class="fa fa-book"> Help </i></a></li>
      <li class="active"><a href="account.php"><i class="fa fa-cog"></i> Account </a></li>

        <li><?php print date("dS M",time());?></li>
      </ul>
      <ul class = "nav navbar-nav navbar-right"><li><?php print $logStatus;?></li></ul>
    </div>
  </nav>
<?php
if($_SESSION['loggedIn']!=1){
?>
<div class="container">
	<h2>Please enter your name to login:</h2>
  	<form action = "index.php?login=1" method ="post">
    <div class="form-group">
    	<label for="input">First Name:</label>
      	<input type="input" class="form-control" id="input" placeholder="first name" name="firstName" required="">
    </div>
    <div class="form-group">
      	<label for="input">Last Name:</label>
     	<input type="input" class="form-control" id="pwd" placeholder="last name" name="lastName" required="">
    </div>
    <div class="form-group">
      	<label for="input">Password:</label>
     	<input type="password" class="form-control" id="pwd" placeholder="password (default = admin)" name="pwd" required="">
    </div>
       	<button type="submit" class="btn btn-default" >enter</button>
 	</form>
</div>
<?php }
if($status == false)die("
	<div class = \"row\">
	<div class = \"col-sm-4\"></div>
	<div class = \"col-sm-4\">
	<div class = \"alert alert-warning\">Please enter your details to view or edit sightings.
	</div>
	<div class = \"col-sm-4\"></div>");?>
	<div class="row">
		<div class="col-md-2 col-lg-2"></div>
		<div class = "container">
		<div style = "color:black" class="panel panel-info">
		<div class = "panel-body">
		In this section you can scroll through your sightings and correct any mistakes or update
		information where needed. You can also update your password if required.<br />
		No sighting information will be altered unless you make a change manually.
		</div>
		</div>
		</div>
		<div class="col-md-2 col-lg-2"></div>
	</div>
	<div class="row">
		<div class="col-md-2 col-lg-2"></div>
		<div class="col-md-8 col-lg-8 accountPanel"><!-- DATA DISPLAY -->

		      	<?php
		if($_GET['edit']==1){
		$firstName = $_SESSION['fname'];
		$lastName = $_SESSION['lname'];
		$userID = OneVal("select user_id from users where first_name = '".$firstName."' AND last_name = '".$lastName."'");
		Connect();
		$ctId = $_GET['id'];
		$res = Query("select * from sighting where ct_id = ".$ctId." and user_id = ".$userID."");

		if(mysqli_num_rows($res)>0){
			Query("update users set sighting_count = sighting_count - 1 where user_id = ".$userID."");
		}
		$res = mysqli_query("delete from sighting where ct_id = ".$ctId." and user_id = '".$userID."'");
		/*if(gettype($res)!="boolean"){
			Query("update users set sighting_count = sighting_count - 1 where user_id = ".$userID."");
		}*/
}
			$counter = 1;
			Connect();
			$userID = $_SESSION['thisID'];
			$q = "select * from sighting where user_id = ".$userID."";
			$res = Query($q);

			while($arr = mysqli_fetch_array($res)){
				print"<class =\"row\">
					<div class = \"col-md-12 accountEdit\"><h4><strong>Edit sighting #".$counter++."?</strong></h4></div>

				<form action=\"account.php?edit=1\" method=\"post\">
				<input type = \"hidden\" name = \"ctID\" value = \"$arr[0]\">
					<div class = \"form-group row\">
							<label for=\"input\" class=\"col-2 col-form-label\">Date</label>
							<div class=\"col-10\">
   								".date("dS M",$arr[2])."<input class=\"form-control\" type=\"date\" placeholder=\"date\" id=\"date\" name = \"theDate\">
  							</div><!-- END COL -->
  							<label for=\"input\" class=\"col-2 col-form-label\">Time <sup>(24hr)</sup></label>
							<div class=\"col-10\">
   								".date("g iH",$arr[2])."<input class=\"form-control\" type=\"time\" placeholder=\"time\" id=\"time\" name = \"time\">
  							</div><!-- END COL -->
							<label for=\"input\" class=\"col-2 col-form-label\">Location</label>
							<div class=\"col-10\">
   	<input class=\"form-control\" type=\"input\" id=\"input\" name = \"location\" value = \"$arr[5]\" required=\"\">
  							</div><!-- END COL -->
							<label for=\"input\" class=\"col-2 col-form-label\">Direction</label>
							<div class=\"col-10\">
   	<input class=\"form-control\" type=\"input\" id=\"input\" name = \"direction\" value = \"$arr[4]\" required=\"\">
  							</div><!-- END COL -->
							<label for=\"text-input\" class=\"col-2 col-form-label\">Train Number</label>
							<div class=\"col-10\">
   	<input class=\"form-control\" type=\"input\" placeholder=\"train number\" id=\"text-input\" name = \"trainNumber\" value = \"$arr[6]\">
  							</div><!-- END COL -->
  							<label for=\"date-input\" class=\"col-2 col-form-label\">Motive Power</label>
							<div class=\"col-10\">
   	<input class=\"form-control\" type=\"input\"  size=\"7\" multiple=\"multiple\" id=\"motive_power\" name = \"motivePower\" value = \"$arr[3]\">
  							</div><!-- END COL -->
  							<hr />
  							<label for=\"comment\">Notes</label>
  								<textarea class=\"form-control\" rows=\"5\" id=\"comment\" name=\"notes\">$arr[8]</textarea>
  							<label for=\"submit-input\" class=\"col-2 col-form-label\">Log Sighting</label>
							<div class=\"col-1\">
							<div class = \"col-1\">
								<div class = \"col-2-md\">
									<input style = \"background-color:skyblue;\" class=\"form-control\" type=\"submit\" value = \"UPDATE\" id=\"date-input\">
								</div>
							</div>
					</div><!-- END FORM GROUP ROW -->
					</div>
				</form><hr /><hr />	";
			}
			?>
		</div><!-- END DATA DISPLAY -->
		<div class="col-md-2 col-lg-2 displayPanel"></div>
	</div><!-- END ROW -->
	<div class="row">
		<div class="col-md-3 col-lg-3 formPanel"></div><!-- LEFT SPACER -->
		<div class="col-md-6 col-lg-6 displayPanel"><!-- Main Panel -->

				<h4>To change your password, please enter the new one here:</h4>
  				<form action = "account.php?newPD=1" method ="post">
    			<div class="form-group">
      			<label for="password">Password:</label>
     				<input width = "15" type="password" class="form-control" id="password" placeholder="new password" name="pwd">
    			</div>
       		<button type="submit" class="btn btn-default" >update password</button>
 				</form>

		</div><!-- END MAIN PANEL-->
	<div class="col-md-3"></div>

	</div><!-- END ROW -->

</body>
</html>
