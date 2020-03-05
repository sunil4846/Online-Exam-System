<?php

require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<?php
//php goes here
?>
		<div class="row">
			<div class="col-sm-12">
			<center>Subject Add</center><br><br>
			<center>
				<label>Enter Subject Name</label>
				<input type="text" name="subadd" placeholder="Enter Subject name "><br><br>
				<input type="submit" name="add" value="add">	
			</center>
			</div>
		</div>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
