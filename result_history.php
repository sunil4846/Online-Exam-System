<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>
<?php 
$db = DB::getInstance(); 
$user = new User();
$user_id = $user->data()->id;
echo $user_id;
exit();

?>













<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>