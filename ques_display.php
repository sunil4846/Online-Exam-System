<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>
<?php  
include 'exam.php';
$exam = new Exam();
$user = new User();
$db = DB::getInstance();
//$ques_display=$exam->delete($id);
$query = $db->query("SELECT * FROM quesadd");
$qd = $query->results();

if (isset($_GET['id1'])) {
	$id = $_GET['id1'];
	
	$exam->delete($id);
	$msg = "<div class='alert alert-success'><strong>Success!</strong>Record deleted Successfully</div>";
	echo $msg;
}
?>
<div><center><h2>Questions To Edit Or Delete</h2></center></div>
<?php  $i = 1;
foreach ($qd as $result) {
//$id =$result->id;

?>
	
	<div id='question<?php echo $i;?>' class='cont'>
    <p class='questions' id="qname<?php echo $i;?>"> <?php echo $i?>.<?php echo $result->questions;?>&nbsp;&nbsp;<a href="edit.php?edit1=<?php echo $result->id;?>">EDIT</a>&nbsp;&nbsp;
    <a href="ques_display.php?id1=<?php echo $result->id;?>" >DELETE</a></p>
    
    <input type="radio" value="1" id='radio1_<?php echo $result->id;?>' name='<?php echo $result->id;?>'/><?php echo $result->option1;?>
    <br/>
    <input type="radio" value="2" id='radio1_<?php echo $result->id;?>' name='<?php echo $result->id;?>'/><?php echo $result->option2;?>
    <br/>
  	<input type="radio" value="3" id='radio1_<?php echo $result->id;?>' name='<?php echo $result->id;?>'/><?php echo $result->option3;?>
    <br/>
   	<input type="radio" value="4" id='radio1_<?php echo $result->id;?>' name='<?php echo $result->id;?>'/><?php echo $result->option4;?>
    <br/>
    <input type="radio" checked='checked' style='display:none' value="5" id='radio1_<?php echo $result->id;?>' name='<?php echo $result->id;?>'/> 

<?php $i++;} 

?>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>