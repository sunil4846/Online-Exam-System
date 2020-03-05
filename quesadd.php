<?php

require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>
<?php $db = DB::getInstance(); ?>
<?php
include 'exam.php';
$exam = new Exam();

//this query displays all the subject name
//$sql = $db->query("SELECT * FROM testadd ORDER BY subject_names ASC")->results();
$sql = $exam->testadds();
//to store the selected subject and total question
if (isset($_POST['subject_names'])) {
	$sn = $_POST['subject_names'];
	$question = $_POST['questions'];
	$option1 = $_POST['option1'];
	$option2 = $_POST['option2'];
	$option3 = $_POST['option3'];
	$option4 = $_POST['option4'];
	$correctans = $_POST['correctans'];
}

if (isset($_POST['add'])) {
	$funcall = $exam->quesadd($sn,$question,$option1,$option2,$option3,$option4,$correctans);	
	echo "<script>alert('Question Added sucessfull!!!');</script>";
}

?>
		<div class="row">
			<div class="col-sm-12">
			<center><h2>Add Questions</h2></center><br>
			<center>
			<form method="post">
			
				<label> Select Subject :</label>
					
				<select name="subject_names">
 
 					<option>---Select subject---</option>
 
 					<?php foreach($sql as $s){ ?>
					<option value="<?php echo $s->subject_names;?>" required> <?php echo $s->subject_names;?></option>
					<?php } ?>
 				</select><br><br>
 				<label>Enter Question:</label>
 				<input type="textarea" name="questions" placeholder="Enter Question" required><br><br>
 				
 				<label>Enter option1:</label>
 				<input type="text" name="option1" placeholder="Enter option1 answer" required><br><br>

 				<label>Enter option2:</label>
 				<input type="text" name="option2" placeholder="Enter option2 answer" required><br><br>

 				<label>Enter option3:</label>
 				<input type="text" name="option3" placeholder="Enter option3 answer" required><br><br>

 				<label>Enter option4:</label>
 				<input type="text" name="option4" placeholder="Enter option4 answer" required><br><br>

 				<label>Enter correct Answer:</label>
 				<input type="text" name="correctans" placeholder="Enter correct answer" required><br><br>
 				<input type="submit" name="add" value="Add">
			</form>
			</center>
			</div>
		</div>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
