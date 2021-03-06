<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
// check if we are on Bret or Joseph's page
if (file_exists("brett")){
  $dbhost = 'oniddb.cws.oregonstate.edu';
  $dbname = 'anderbre-db';
  $dbuser = 'anderbre-db';
  $dbpass = 'mkfCwxMsmsXjCDc7';
} else {
  $dbhost = 'oniddb.cws.oregonstate.edu';
  $dbname = 'mcmurroj-db';
  $dbuser = 'mcmurroj-db';
  $dbpass = 'uHM64jmm6DzuW1qr';
}
// create new mysqli object
$mysqli = new mysqli($dbhost,$dbname,$dbpass,$dbuser);
if($mysqli->connect_errno){
    echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Final Project: Volleyball Database</title>
    <link rel="stylesheet" href="stylesheet.css" type="text/css">
</head>
<html>
<body>
  <ul class="NavBar">
    <li class="navItem"><a class="navlink" href="vbphp.php">Home</a></li>
    <li class="navItem"><a class="navlink" href="athletes.php">Athletes</a></li>
    <li class="navItem"><a class="active navlink"  href="coaches.php">Coaches</a></li>
    <li class="navItem"><a class="navlink"  href="teams.php">Teams</a></li>
    <li class="navItem"><a class="navlink"  href="positions.php">Positions</a></li>
  </ul>
<h1>All about coaches</h1>
<p> Remember the following rules apply to coaches:</p>
<ul>
	<li>Coaches can belong to more than one team.</li>
	<li>Coaches can have multiple positions, but only one per team.</li>
</ul>
<h3>Table 1: Coach name, team, and position held </h3>

<div>
	<table id="coach_table">
		<tr class="heading" >
			<th> First Name </th>
			<th> Last Name </th>
			<th> Team Name </th>
      <th> Age Group </th>
      <th> Level </th>
      <th> Position </th>
		</tr>
<?php
// create sql query to grab al coaches, team names and such
if(!($stmt = $mysqli->prepare("SELECT coaches.first_name, coaches.last_name, teams.name, teams.age_group, teams.level, positions.type
FROM coaches LEFT JOIN position_coach_team ON position_coach_team.coachID = coaches.id
LEFT JOIN positions ON positions.id = position_coach_team.positionID
LEFT JOIN teams ON position_coach_team.teamID = teams.id
ORDER BY teams.age_group DESC")))
{
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
if(!$stmt->bind_result($fname, $lname, $tname, $ageGroup, $level, $pos)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
// take the results, and build a row for the table form the bound variables
while($stmt->fetch()){
 echo "<tr>\n<td>" . $fname . "</td>\n<td>" . $lname . "</td>\n<td>" . $tname . "</td>\n<td>" . $ageGroup. "</td>\n<td>" . $level . "</td>\n<td>" . $pos . "</td>\n</tr>";
}
$stmt->close();
?>
	</table>
</div>

<br>
<h3>Add or update coaches</h3>
<p>To add a coach, simply enter a first and last name. Then click Submit.
  To update a coach, check the update button, and then select the coach's name
  from the drop down box. Make any changes to the name, and click submit.</p>
<p>Coaches positions on teams will be added on the <a href=positions.php>Positions</a>
  page.</p>
<!-- create form to transmit data -->
<form method="post" action="add_up_coach.php">
  <input type="checkbox" name="type" value="update" id="formType">Update
  <select name=coachToUpdate id="coach_name" style="visibility: hidden">
    <option value="-1">Select a coach</option>
    <?php
    // prepare sql query for loading the option drop down
    if(!($stmt = $mysqli->prepare("SELECT id, first_name, last_name FROM coaches ORDER BY last_name"))){
    	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
    }

    if(!$stmt->execute()){
    	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    if(!$stmt->bind_result($id, $firstname, $lastname)){
    	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    while($stmt->fetch()){
    	echo '<option value=" '. $id . ' "> ' . $lastname .', '. $firstname .'</option>';
    }
    $stmt->close();
    ?>
  </select>
		<fieldset>
			<legend>Coach</legend>
      <input type="hidden" name="coach_id" id="coach_ID"/>
			<p>First Name: <input type="text" name="first_name" id="coach_f_name"/></p>
			<p>Last Name: <input type="text" name="last_name" id="coach_l_name"/></p>
		</fieldset>
		<input type="submit" name="Submit" value="submit" />
	</form>

<script type="text/javascript">
// The js is to make the page a litle cleaner and dynamic.
// In a nutshell, it hides or shows the drop down. Then, if you
// select a name from the drop down, it pre-fills the form.
document.getElementById("formType").onchange = function(){
  if (this.checked){
    document.getElementById("coach_name").style.visibility = "visible";
  } else {
    document.getElementById("coach_name").style.visibility = "hidden";
    document.getElementById("coach_f_name").value = "";
    document.getElementById("coach_l_name").value = "";
    document.getElementById("coach_ID").value = this.value;
  }
}
document.getElementById("coach_name").onchange = function(){
  if (this.value != -1){
    var box = document.getElementById("coach_name");
    var name = box.options[box.selectedIndex].text.split(',');
    first = name[1].substr(1);
    last = name[0];
    document.getElementById("coach_f_name").value = first;
    document.getElementById("coach_l_name").value = last;
    document.getElementById("coach_ID").value = this.value;
  } else {
    document.getElementById("coach_f_name").value = "";
    document.getElementById("coach_l_name").value = "";
    document.getElementById("coach_ID").value = this.value;
  }
}
</script>

</body>
</html>
