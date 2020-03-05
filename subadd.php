<?php

require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<?php
include 'exam.php';
$exam = new Exam();
$db = DB::getInstance();
extract($_POST);
if (isset($_POST['sub_name'])) {
	$sub_name = $_POST['sub_name'];
}

if (isset($_POST['add'])) {
	$funcall = ($exam->subadd($sub_name));
	//$sql = $db->query("INSERT INTO subject(sub_name) VALUES('$sub_name')");
	echo "<script>alert('subject Added sucessfull!!!');</script>";
}
?>
		<div class="row">
			<div class="col-sm-12">
			<center><h2>Add Subject</h2></center><br><br>
			<center>
				<form method="post">
				<label>Enter Subject Name:</label>
				<input type="text" name="sub_name" placeholder="Enter Subject name " required><br><br>
				<input type="submit" name="add" value="add">
				</form>	
			</center>
			</div>
		</div>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
