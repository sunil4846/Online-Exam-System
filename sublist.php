<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<?php

include'exam.php';
$db = DB::getInstance();
$exam = new Exam();
//$sql = $db->query("SELECT * FROM testadd ORDER BY subject_names ASC")->results();
$sql = $exam->testadds();

if (isset($_POST['submit'])) {
	$apart = $_POST['subject_names'];
	$_SESSION['subject_names'] = $apart;
	header('location:quiz.php');
	//exit;
}

?>
		<div class="row">
			<div class="col-sm-12">
				<center><h2>Select the Subject to give the test</h2></center>
				<center><form method="post">
					<select name="subject_names">
 
 						<option>---Select subject---</option>
 
 						<?php foreach($sql as $s){ ?>
						<option value="<?php echo $s->subject_names;?>" required> <?php echo $s->subject_names;?></option>
						<?php } ?>
	 				</select><br><br>				
	 				<input type="submit" name="submit" value="Submit">
	 				</form>
 				</center>

			</div>
		</div>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>

