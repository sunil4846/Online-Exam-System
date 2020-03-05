<?php

require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<?php
//php goes here
include 'exam.php';
$exam = new Exam();
$db = DB::getInstance();
//this query displays all the subject name
$sql = $db->query("SELECT * FROM subject ORDER BY sub_name ASC")->results();

//to store the selected subject and total question
if (isset($_POST['subject_names'] )) {
	$sn = $_POST['subject_names'];
	$tq = $_POST['total_que'];

}
if (isset($_POST['add'])) {
	//$add = $db->query("INSERT INTO testadd(subject_names,total_que) VALUES('$sn','$tq')");
	$funcall = ($exam->testadd($sn,$tq));
	$_SESSION['total_que'] = $tq;
	
}

?>
		<div class="row">
			<div class="col-sm-12">
			<center><h2>Add Test</h2></center><br><br>
			<center>
				<form method="post" action="">
					<label>Enter Subject Id:</label>
					<select name="subject_names">
 
 						<option>---Select subject---</option>
 
 						<?php foreach($sql as $s){ ?>
						<option value="<?php echo $s->sub_name;?>" required> <?php echo $s->sub_name;?></option>
						<?php } ?>
 					</select><br><br>
 					<label>Enter Total Question</label>
 					<input type="number" name="total_que" placeholder="Total questions to be added" min="0" required><br><br>
 					<input type="submit" name="add" value="Add">
				</form>
			</center>
			</div>
		</div>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
