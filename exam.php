<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>
<?php $db = DB::getInstance(); ?>
<?php
error_reporting(0);
class Exam
{
	
	function subadd($sub_name){
		global $db;
		$sql=$db->query("INSERT INTO subject(sub_name) VALUES('$sub_name')");
		//return $sql;
	}

	function testadd($sn,$tq){
		global $db;
		$add = $db->query("INSERT INTO testadd(subject_names,total_que) VALUES('$sn','$tq')");
	}

	function quesadd($sn,$question,$option1,$option2,$option3,$option4,$correctans){
		if (!empty($sn && $question && $option1 && $option2 && $option3 && $option4 && $correctans)) {
			$sn = json_encode($sn);
			$question = json_encode($question);
			$option1 = json_encode($option1);
			$option2 = json_encode($option2);
			$option3 = json_encode($option3);
			$option4 = json_encode($option4);
			$correctans = json_encode($correctans);
 		}
		global $db;
		$quesadd = $db->query("INSERT INTO quesadd(subject_names,questions,option1,option2,option3,option4,correctans) VALUES($sn,$question,$option1,$option2,$option3,$option4,$correctans)")->results();
		//$qad = json_encode($quesadd);
		if ($quesadd) {
			echo "Question Added Successfully";	
		}
		else{
			echo "Something Went Wrong";
		}
	}

	function testadds()
	{
		global $db;
		$sql = $db->query("SELECT * FROM testadd ORDER BY subject_names ASC")->results();
		return $sql;
	}

	//function for inserting the quiz data
	function quiz($ans,$quiz_correctans,$result,$id,$user_id)
	{
		if (!empty($quiz_correctans)) {
			$quiz_correctans = json_encode($quiz_correctans);
		}
		if (!empty($ans)) {
			$ans = json_encode($ans);
		}
		global $db;
		$sql = $db->query("INSERT INTO quiz(selected_option,quiz_correctans,results,ques_id,user_id) VALUES($ans,$quiz_correctans,$result,$id,$user_id)")->results();
		//print json_encode($sql);

	}

	function answer($data)
	{
		global $db;
		$quesid = $db->query("SELECT * FROM quiz");
		$id = $quesid->count();
		$ans=implode("",$data);
		$right=0;
		$wrong=0;
		$no_answer=0;
		$query=$db->query("select quiz_id,quiz_correctans from quiz where ques_id='".$id."'");
	    while($qust=$query->results())		
		{			
			if($qust->quiz_correctans == $_POST[$qust->ques_id])
			{
				 $right++;
			}
			elseif($_POST[$qust->ques_id]=="no_attempt")
			{
				 $no_answer++;
			}
			else
			{
				$wrong++;
			}
		}
		$array=array();
		$array['right']=$right;
		$array['wrong']=$wrong;
		$array['no_answer']=$no_answer;
		return $array;
		
	}	

	function delete($id)
	{
		global $db;
		$sql = $db->query("DELETE FROM quesadd WHERE id=?",[$id]);
		$query = $sql->results();
		
		if ($query==true) {
			$msg = "<div class='alert alert-success'><strong>Success!</strong>Record deleted Successfully</div>";
			return $msg;
			header('location:ques_display.php');
		}
		else{
			echo "Unable to delete";
			return false;
		}

	}

	function edit($id,$questions,$option1,$option2,$option3,$option4,$correctans)
	{
		global $db;
		try{
			$sql = $db->query("UPDATE quesadd SET questions='$questions',option1='$option1',option2='$option2',option3='$option3',option4='$option4',correctans='$correctans' WHERE id='$id'")->results();
			return true; 
  		}
  		catch(PDOException $e)
  		{
   			echo $e->getMessage(); 
   			return false;
  		}
	}
}
?>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
