<?php
 //die("The site is currently down. Please check back later.");
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
Connect();
if(!isset($_POST['theDate'])){
	$_POST['theDate']="";
}
if(!isset($_POST['pwd'])){
	$_POST['pwd']="";
}
$pwd = $_POST['pwd'];
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
	CheckLogin($firstName, $lastName, $pwd);
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
if(!isset($_SESSION['ol'])){
			$_SESSION['ol'] = 0;
}
if(!isset($_SESSION['tot'])){
			$_SESSION['tot'] = 0;
}
if(!isset($_SESSION['isAdmin'])){
  $_SESSION['isAdmin'] = 0;
}
//user is active, update their timestamp

//print $_SESSION['thisID'];
if($_SESSION['isAdmin'] == 0){
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

if($_GET['sighting'] == 1){
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
	if(!isset($locoString)){
	$locoString = "";
	}
	$mp[] = $_POST['motivePower'];
	//prepare data for storage
	foreach($_POST['motivePower'] as $i){
		$locoString .= " ".$i;
	}
  $locoString = ltrim($locoString);
	$theDate = $_POST['theDate'];
	$time = $_POST['time'];
	//print "DATE: ".$theDate." TIME: ".$time;
	//set local vars
	$firstName = $_SESSION['fname'];
	$lastName = $_SESSION['lname'];
	$direction = $_POST['direction'];
	$location = $_POST['location'];
  $location = addslashes($location);
	$trainNumber = $_POST['trainNumber'];
  $trainNumber = addslashes($trainNumber);
	$firstName = trim($firstName);
	$lastName = trim($lastName);
	$motivePowerDescription = $_POST['motivePowerDescription'];
  $motivePowerDescription = addslashes($motivePowerDescription);
	$notes = $_POST['notes'];
  $notes = addslashes($notes);
	$firstName = filter_var($firstName, FILTER_SANITIZE_STRING);
	$lastName = filter_var($lastName, FILTER_SANITIZE_STRING);
	$direction = filter_var($direction, FILTER_SANITIZE_STRING);
	$location = filter_var($location, FILTER_SANITIZE_STRING);
	$trainNumber = filter_var($trainNumber, FILTER_SANITIZE_STRING);
	$motivePowerDescription = filter_var($motivePowerDescription, FILTER_SANITIZE_STRING);
  $motivePowerDescription = ltrim($motivePowerDescription);
	if($time == "" or $theDate == ""){
		$date = time();
	}else	$date = strtotime($theDate." ".$time);
	$userID = OneVal("select user_id from users where first_name = '".$firstName."' AND last_name = '".$lastName."'");
	//INSERT DATA
	$q = "insert into sighting (user_id, date, motive_power, location, direction, train_number, motive_power_description, notes)
		values('".$userID."',".$date.",'".$locoString."','".$location."','".$direction."','".$trainNumber."','".$motivePowerDescription."','".$notes."')";
	Connect();
	Query($q);
	Query("update users set sighting_count = sighting_count + 1 where user_id = ".$userID."");

	/*list($m,$d,$y) = explode("/",$_POST['date']);
	$timestamp = mktime(0,0,0,$m,$d,$y);
	$date = date("Y-m-d",$timestamp);*/
}
Connect();
$_SESSION['ol'] = mysqli_num_rows(Query("select * from users where logged_in = 1"));
$_SESSION['tot'] = mysqli_num_rows(Query("select * from users"));
$logStatus = ($status == true?"<a href = \"index.php?logout=1\">LOGOUT [".$_SESSION['fname']."]</a>":"<a>You aren't logged in</a>");
if($_GET['del']==1){
		$firstName = $_SESSION['fname'];
		$lastName = $_SESSION['lname'];
		$userID = OneVal("select user_id from users where first_name = '".$firstName."' AND last_name = '".$lastName."'");
		Connect();
		$ctId = $_GET['id'];
		$res = Query("select * from sighting where ct_id = ".$ctId." and user_id = ".$userID."");

		if(mysqli_num_rows($res)>0){
			Query("update users set sighting_count = sighting_count - 1 where user_id = ".$userID."");
		}
		$res = Query("delete from sighting where ct_id = ".$ctId." and user_id = '".$userID."'");
		/*if(gettype($res)!="boolean"){
			Query("update users set sighting_count = sighting_count - 1 where user_id = ".$userID."");
		}*/
}

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
    	<li class="active"><a href="#"> <i class="fa fa-home"></i> Home</span></a></li>
		<li><a href="contributors.php" > <i class="fa fa-user-circle"></i> Contributors</a></li>
    <li><a target = "blank" href="https://www.facebook.com/groups/VictorianRailwayEnthusiasts/1234249243277762/?notif_t=like&notif_id=1484865710034242"><i class="fa fa-facebook-official"></i> Forum
        </a></li>
		<li><a href="help.php"><i class="fa fa-info-circle"> Help </i></a></li>
		<li><a href="account.php"><i class="fa fa-cog fa-spin fa-1x fa-fw"></i> Account </a></li>

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
<div class="container">
	<div class="row"><!-- THREE COLUMNS left column for data entry, right for data display -->
		<div class="col-md-3 col-lg-3 formPanel"><!-- DATA ENTRY -->
			<h4>Sighting Entry</h4>
				<form action="index.php?sighting=1" method="post">
					<div class = "form-group row">
							<label for="input" class="col-2 col-form-label">Date</label>
							<div class="col-10">
   								<input class="form-control" type="date" placeholder="date" id="date" name = "theDate">
  							</div><!-- END COL -->
  							<label for="input" class="col-2 col-form-label">Time <sup>(24hr)</sup></label>
							<div class="col-10">
   								<input class="form-control" type="time" placeholder="time" id="time" name = "time">
  							</div><!-- END COL -->
							<label for="input" class="col-2 col-form-label">Location</label>
							<div class="col-10">
   								<input class="form-control" type="input" placeholder="location" id="input" name = "location" required="">
  							</div><!-- END COL -->
  							<h5><strong>Direction</strong></h5>
  							<label class="radio-inline">
      						<input type="radio" name="direction" value="UP">Up
    						</label>
    						<label class="radio-inline">
      						<input type="radio" name="direction" value="DOWN">Down
    						</label>
    						<label class="radio-inline">
      						<input type="radio" name="direction" value="STOPPED">Stopped
    						</label><hr />
  							<label for="text-input" class="col-2 col-form-label">Train Number</label>
							<div class="col-10">
   								<input class="form-control" type="input" placeholder="train number" id="text-input" name = "trainNumber">
  							</div><!-- END COL -->
  							<label for="date-input" class="col-2 col-form-label">Motive Power (Hold <strong>CTRL</strong> for multiple units)</label>
							<div class="col-10">
   								<select class="form-control" type="select"  size="7" multiple="multiple" id="motive_power" name = "motivePower[]">
							<?php //while through all locos
   								Connect();
   								$q = "select * from locomotives order by locomotive_class";
   								$res = Query($q);
   								while($arr = mysqli_fetch_array($res)){
   									print"<option>$arr[1]</option>";
   								}
   							?>
   							</select>
  							</div><!-- END COL -->
  							<hr />
  							<label for="comment">Notes</label>
  								<textarea class="form-control" rows="5" id="comment" name="notes"></textarea>
  							<label for="submit-input" class="col-2 col-form-label">Log Sighting</label>
							<div class="col-10">
   								<input class="form-control" type="submit" value = "ENTER" id="date-input">
  							</div><!-- END COL -->
					</div><!-- END FORM GROUP ROW -->
				</form>
		</div><!-- DATA ENTRY -->
		<div class="col-md-9 col-lg-9 displayPanel"><!-- DATA DISPLAY -->
		      	<a class="rButton" href = "index.php"><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
              </a><em class = "bestResults"> for best results, use this refresh button instead of the browsers</em>
		      	<?php
			//Connect();
			?>

			<?php
      if($_GET['view']==1) {
        $id = $_GET['id'];
      	print"
      	<div class=\"col-md-9 col-lg-9 \"><!-- DATA DISPLAY -->
      			<table class = \"table\">
      				<thead>
      					<tr>
      					<th>date</th>
      					<th>time</th>
      					<th>direction</th>
      					<th>location</th>
      					<th>train number</th>
      					<th>motive power</th>
      					</tr>
      				</thead>
      				<tbody>";
      			$link = Connect();
      			$q = "select * from sighting where ct_id = ".$id."";
      			$res = Query($q);
            $arr = mysqli_fetch_array($res);
      			print"<tr>
      			<td>".date("dS M",$arr[2])."</td>
      			<td>".date("g iA",$arr[2])."</td>
      			<td>$arr[4]</td>
      			<td>$arr[5]</td>
      			<td>$arr[6]</td>
      			<td>$arr[3]</td></tr>
      			<tr><td colspan = 6>$arr[8]</td>
      			</tr>";
      			print"</tbody>
      			</table>
      		</div>
      		</div>";
      		//die();
      }else{
        ?><table class = "table table-condensed">
  				<thead>
  					<tr>
              <th class = "arrow">date<br /><a href = "index.php?OB=DTA"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
              <a href = "index.php?OB=DTD"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a></th>
              <th class = "arrow">time<br /><a href = "index.php?OB=TMA"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
              <a href = "index.php?OB=TMD"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a></th>
              <th class = "arrow">dir<br /><a href = "index.php?OB=DRA"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
              <a href = "index.php?OB=DRD"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a></th>
              <th class = "arrow">loc (clickable)<br /><a href = "index.php?OB=LCA"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
              <a href = "index.php?OB=LCD"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a></th>
              <th class = "arrow">train# (clickable)<br /><a href = "index.php?OB=TNA"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
              <a href = "index.php?OB=TND"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a></th>
              <th class = "arrow">locos<br /><a href = "index.php?OB=MPA"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>
              <a href = "index.php?OB=MPD"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a></th>
  					<th>del</th>
  					<th>notes</th>
  					</tr>
  				</thead>
  				<tbody><?php
      if(!isset($_GET['OB'])){
        $_GET['OB'] = "";
      }else $_SESSION['OB'] = $_GET['OB'];
      if(!isset($_SESSION['OB'])){
        $_SESSION['OB'] = $_GET['OB'];
      }
      $ORB = "date";
      $OB = $_SESSION['OB'];
      switch($OB){
        case "DTA":
          $ORB = "date ASC";
        break;
        case "DTD":
          $ORB = "date DESC";
        break;
        case "TMA":
          $ORB = "date ASC";
        break;
        case "TMD":
          $ORB = "date DESC";
        break;
        case "DRA":
          $ORB = "direction ASC";
        break;
        case "DRM":
          $ORB = "direction DESC";
        break;
        case "LCA":
          $ORB = "location ASC";
        break;
        case "LCD":
          $ORB = "location DESC";
        break;
        case "TNA":
          $ORB = "train_number ASC";
        break;
        case "TND":
          $ORB = "train_number DESC";
        break;
        case "MPA":
          $ORB = "motive_power ASC";
        break;
        case "MPD":
          $ORB = "motive_power DESC";
        break;
        default:
          $ORB = "date";
      }

      if(!isset($_GET['trainNumSel'])){
        $_GET['trainNumSel']="";
      }
      if(!isset($_GET['getTrainNumber'])){
        $_GET['getTrainNumber']=0;
      }
      if(!isset($_GET['getLocation'])){
        $_GET['getLocation']=null;
      }
      if(!isset($_GET['theLocation'])){
        $_GET['theLocation']="";
      }
      if(!isset($_GET['getLocoNumber'])){
        $_GET['getLocoNumber']=null;
      }
      if(!isset($_GET['locoNumSel'])){
        $_GET['locoNumSel']="";
      }
      if($_GET['getTrainNumber']==1){
        $q = "select * from sighting where train_number = '".$_GET['trainNumSel']."' order by ".$ORB."";
      }elseif ($_GET['getLocation']==1){
        $q = "select * from sighting where location = '".$_GET['theLocation']."' order by ".$ORB."";
      }elseif ($_GET['getLocoNumber']==1){
        $q = "select * from sighting where motive_power like '%".$_GET['locoNumSel']."%' order by ".$ORB."";
      }else	$q = "select * from sighting order by ".$ORB."";
			$res = Query($q);
			while($arr = mysqli_fetch_array($res)){
				print"<tr>
				<td>".date("d/m/Y",$arr[2])."</td>
				<td>".date("Gi",$arr[2])."</td>
				<td >$arr[4]</td>
				<td class = \"locoSel\">
          <a href = \"index.php?getLocation=1&theLocation=".$arr[5]."\">$arr[5]</a></td>
				<td class = \"locoSel\">
          <a href = \"index.php?getTrainNumber=1&trainNumSel=".$arr[6]."\">$arr[6]</a></td>
        <td>";
        //special case for motive power
        //each word in the entry should be exploded into an ArrayAccess
        //and then given it's own link EvWatcher
        //use foreach
        $mpArray = explode(" ",$arr[3]);
        print"<div class = \"dropdown\">
          <button class=\"btn btn-primary dropdown-toggle btn-xs btn-block\" type=\"button\" data-toggle=\"dropdown\">
            ".$mpArray[0]." (".count($mpArray).")
          <span class=\"caret\"></span></button>
            <ul class=\"dropdown-menu\">";
          foreach($mpArray as $mp){
              print"<li><a href = \"index.php?getLocoNumber=1&locoNumSel=".$mp."\">$mp</a></li>";
          }
       print"</ul></div></div></td>
				<td><a href = \"index.php?del=1&id=$arr[0]\"><i class=\"fa fa-ban text-danger\"></i></a></td>
				<td><a href = \"index.php?view=1&id=$arr[0]\"><i class=\"fa fa-sticky-note-o\" aria-hidden=\"true\"></i></a>

        </td>
				</tr>";
			}//<div class = \"notes\">$arr[7]</div>
      /*
      trainNumSel
      */
    }?>
				</tbody>
			</table>
		</div><!-- END DATA DISPLAY -->
<?php
?>
	</div><!-- END ROW -->
</div>
</body>
</html>
