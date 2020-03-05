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
//error_reporting(0);

if(isset($_GET['edit1']))
{
  $id= $_GET['edit1'];

  $query=$db->query("SELECT * FROM quesadd WHERE id=?",[$id])->results();
}

if(isset($_POST['submit']))
{
 
 $questions = $_POST['questions'];
 $option1 = $_POST['option1'];
 $option2 = $_POST['option2'];
 $option3 = $_POST['option3'];
 $option4 = $_POST['option4'];
 $correctans = $_POST['correctans'];
 $result=$exam->edit($id,$questions,$option1,$option2,$option3,$option4,$correctans);
 if($result==true)
 {
  $msg = "<div class='alert alert-info'>
    <strong>WOW!</strong> Record was updated successfully <a href='edit.php'></a>!
    </div>";
 }
 else
 {
  $msg = "<div class='alert alert-warning'>
    <strong>SORRY!</strong> ERROR while updating record !
    </div>";
 }
 header('location:ques_display.php');
}
?>
<body>      
  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    
    <div class="row">
      <div class="col-lg-12"> 
        
      <div class="panel panel-default">
          <div class="panel-heading">Edit</div>
          <div class="panel-body">
            <div class="col-md-12">
              <?php
               
               if(isset($msg))
               {
                echo $msg;
              }

               
              ?>
              <form role="form" method="post" action="">
               <?php foreach ($query as $result) {   ?>
                <div class="form-group">
                  <label>Question</label>
                  <input class="form-control" type="text" name="questions" value="<?php echo $result->questions; ?>">
                </div>
                <div class="form-group">
                  <label>option1</label>
                  <input type="text" class="form-control" name="option1" value="<?php echo $result->option1; ?>">
                </div>
                
                <div class="form-group">
                  <label>option2</label>
                  <input type="text" class="form-control" name="option2" value="<?php echo $result->option2; ?>">
                </div>
                <div class="form-group">
                  <label>option3</label>
                  <input type="text" class="form-control" name="option3" value="<?php echo $result->option3; ?>">
                </div>
                <div class="form-group">
                  <label>option4</label>
                  <input type="text" class="form-control" name="option4" value="<?php echo $result->option4; ?>">
                </div>

                <div class="form-group">
                  <label>correctans</label>
                  <input class="form-control" type="text"  name="correctans" value="<?php echo $result->correctans; ?>">

                </div>
                <?php } ?>              
                <div class="form-group has-success">
                  <button type="submit" class="btn btn-success" name="submit">EDIT</button>
                </div>  
                
              </form>
            </div>
          </div>
        </div><!-- /.panel-->
      </div><!-- /.col-->
    </div><!-- /.row -->
  </div><!--/.main-->

</body>


<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>