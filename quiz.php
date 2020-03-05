<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>
<!--countdown timer code-->
<script type="text/javascript">
	function timeout()
	{
		var hours=Math.floor(timeLeft/3600);
		var minute=Math.floor((timeLeft-(hours*60*60))/60);
		var second=timeLeft%60;
		var hrs=checktime(hours);
		var mint=checktime(minute);
		var sec=checktime(second);
		if(timeLeft<=0)
		{
			clearTimeout(tm);
			document.getElementById("form1").submit();
		}
		else
		{

			document.getElementById("time").innerHTML=hrs+":"+mint+":"+sec;
		}
		timeLeft--;
		var tm= setTimeout(function(){timeout()},1000);
	}
	function checktime(msg)
	{
		if(msg<10)
		{
			msg="0"+msg;
		}
		return msg;
	}
</script>

<?php
include 'exam.php';
$exam = new Exam();
$user = new User();
$db = DB::getInstance();
error_reporting(0);
$selected_option=$quiz_correctans=$result=$id=$user_id="";
$questions ="";
$q = "";
$apart = $_SESSION['subject_names'];  //subject name selected by the user;
$user_id = $user->data()->id; //fetch user_id

if($apart=='')
	{
        echo("You didn't select any subject!!!.");
    }
else{
    $sqlQ = $db->query("SELECT * FROM quesadd WHERE subject_names=?",[$apart]);
    $sqlC = $sqlQ->count();
    if($sqlC < 1){
        echo("<h3>There are no questions on that subject.</h3>");
        exit();
    }else{
        $questions = $sqlQ->results();
        //dump($questions);
    }
}  

?>

<head>

</head>


<body onload="timeout()">
<div class="row">
	<div class="col-sm-12">	
		<h2><center>Onilne Quiz System </center>	
		<script type="text/javascript">
		  var timeLeft=1*60*60;		  
		</script>
		  
		<div id="time"style="float:right">timeout</div></h2><br><br>
				<form class="form-horizontal" role="form1" id='form1' method="post" action="results.php">
			<?php 
			$i=1;
			foreach ($questions as $result) {
				$id = $result->id;
				?>
	                    
	            <div id='question<?php echo $i;?>' class='cont'>
	            <p class='questions' id="qname<?php echo $i;?>"> <?php echo $i?>.<?php echo $result->questions;?></p>
	            <input type="radio" value="<?php echo $result->option1;?>" id='radio1_<?php echo $result->id;?>' name='questions[<?php echo $result->id;?>]'/><?php echo $result->option1;?>
	           <br/>
	            <input type="radio" value="<?php echo $result->option2;?>" id='radio1_<?php echo $result->id;?>' name='questions[<?php echo $result->id;?>]'/><?php echo $result->option2;?>
	            <br/>
	            <input type="radio" value="<?php echo $result->option3;?>" id='radio1_<?php echo $result->id;?>' name='questions[<?php echo $result->id;?>]'/><?php echo $result->option3;?>
	            <br/>
	            <input type="radio" value="<?php echo $result->option4;?>" id='radio1_<?php echo $result->id;?>' name='questions[<?php echo $result->id;?>]'/><?php echo $result->option4;?>
	            <br/>
	            <input type="radio" checked='checked' style='display:none' value="<?php echo $result->id;?>" id='radio1_<?php echo $result->id;?>' name='<?php echo $result->id;?>'/> 
            <?php $i++; } ?>                                                                     
                    <br/>
                    <button <?php echo $i;?> class='next btn btn-success' type='submit'  name="submit1">Submit</button>
                    <br/>
                    </div>
				</form>
	</div>
</div>

</body>

<?php  
    if (isset($_POST['submit1'])) {    	    	
        //$selected_option = $result->id;
        	$quiz_correctans = $result->correctans;
        	$ans = json_encode($_POST['questions']);
        	$result = 0;
        	$funcall = $exam->quiz($ans,$quiz_correctans,$result,$id,$user_id);
        	header('location:results.php');
        	exit();
        
   	}
?>





<!--script for next and previous button
	

	<script type="text/javascript">
		$('.cont').addClass('hide');
		count=$('.quesadd').length;
		 $('#question'+1).removeClass('hide');
		 var next;

		 $(document).on('click','.next',function(){
		     element=$(this).attr('id');
		     last = parseInt(element.substr(element.length - 1));
		     nex=last+1;
		     $('#question'+last).addClass('hide');

		     $('#question'+nex).removeClass('hide');
		 });
		 var previous;
		 $(document).on('click','.previous',function(){
             element=$(this).attr('id');
             last = parseInt(element.substr(element.length - 1));
             pre=last-1;
             $('#question'+last).addClass('hide');

             $('#question'+pre).removeClass('hide');
         });

</script>-->	




<!--if (isset($_POST['submit'])) {
if(isset($_POST['$q->id']))
{

	@$selected_option = $_POST['$q->id'];
	
	$qc = $db->query("SELECT correctans FROM quesadd WHERE id = $id")->results();//query to store the correctans
	//$qzc =$qc->count(); 
	$quiz_correctans = $qc;
	
	if ($quiz_correctans ==$selected_option) {
		$result = '1';
	}
	else{
		$result = '0';
	}


	//selected option by the user is stored in quiz table
	//$funcall = ($exam->quiz($selected_option,$quiz_correctans,$result,$id,$user_id));
	
	//header('location:results.php');
}

}*/

?>-->

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>


