<script type="text/javascript" src="jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="TimeCircles/TimeCircles.js"></script>
<link rel="stylesheet" type="text/css" href="TimeCircles/TimeCircles.css">

<!--<?php
    $connection = mysqli_connect("localhost", "root", "1234", "countdown");
    $sql = "SELECT * FROM orders WHERE orderNumber='10100'";
    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_object($result);
?>-->

<div data-date="<?php echo $row->requiredDate; ?>" id="count-down" ></div>

<script type="text/javascript">
    $(function () {  
    //$("#count-down").TimeCircles();
	$("#count-down").TimeCircles().end().fadeOut(); 
		
		
$(".example.stopwatch").TimeCircles();
$(".start").click(function(){ $(".example.stopwatch").TimeCircles().start(); });
$(".stop").click(function(){ $(".example.stopwatch").TimeCircles().stop(); });
$(".restart").click(function(){ $(".example.stopwatch").TimeCircles().restart(); });
    });
</script>


<div class="example stopwatch" data-timer="60"></div>
<button class="btn btn-success start">Start</button>
<button class="btn btn-danger stop">Stop</button>
<button class="btn btn-info restart">Restart</button>