<?php
// This is a user-facing page
/*
UserSpice 4
An Open Source PHP User Management System
by the UserSpice Team at http://UserSpice.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';

if (!securePage($_SERVER['PHP_SELF'])){die();} ?>
<?php if(!$user->isLoggedIn()) Redirect::to($us_url_root.'users/login.php'); ?>
<?php if(!$settings->twofa==1) Redirect::to($us_url_root.'users/account.php'); ?>
<?php if(!$_SESSION['twofa']==1) Redirect::to($us_url_root.'users/account.php'); ?>
<?php
$errors = $successes = [];
$form_valid=TRUE;
if(isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!='' && !is_null($_SESSION['fingerprint'])) {
  $q = $db->query("SELECT kFingerprintID FROM us_fingerprints f JOIN us_fingerprint_assets fa ON fa.fkFingerprintID=f.kFingerprintID WHERE f.fkUserID = ? AND f.Fingerprint = ? AND f.Fingerprint_Expiry > ? AND fa.IP_Address = ?",[$user->data()->id,$_SESSION['fingerprint'],date("Y-m-d H:i:s"),ipCheck()]);
  if($q->count()>0) {
    $dest=Input::get('dest');
    $redirect=Input::get('redirect');
    unset($_SESSION['twofa']);
    logger($user->data()->id,"Two FA","Two FA Verification passed via Fingerprint.");
    if (!empty($dest) || !$dest=='') {
      $redirect=htmlspecialchars_decode(Input::get('redirect'));
      if(!empty($redirect) || $redirect!=='') Redirect::to($redirect);
      else Redirect::to($dest);
    }
    elseif (file_exists($abs_us_root.$us_url_root.'usersc/scripts/custom_login_script.php')) {
      require_once $abs_us_root.$us_url_root.'usersc/scripts/custom_login_script.php';
    }
    else {
      if (($dest = Config::get('homepage')) ||
        ($dest = 'account.php')) {
        #echo "DEBUG: dest=$dest<br />\n";
        #die;
        Redirect::to($dest);
      }
    }
  }
}
if (!empty($_POST)) {
  $token = $_POST['csrf'];
  if(!Token::check($token)){
    include($abs_us_root.$us_url_root.'usersc/scripts/token_error.php');
  }

  if(!empty($_POST['verifyTwo']) && $settings->twofa == 1) {
    $google2fa = new PragmaRX\Google2FA\Google2FA();
      $twoPassed = false;
      $twoQ = $db->query("select twoKey from users where id = ? and twoEnabled = 1", [$user->data()->id]);
      if($twoQ->count() > 0){
          $twoKey = $twoQ->results()[0]->twoKey;
          $twoCode = trim(Input::get('twoCode'));
          if($google2fa->verifyKey($twoKey, $twoCode) == true){
              $twoPassed = true;
          }
        }
        if($twoQ->count()==0)  $twoPassed=true;
        if($twoPassed==true) {
          unset($_SESSION['twofa']);
          logger($user->data()->id,"Two FA","Two FA Verification passed.");
          if($_SESSION['fingerprint']!='' || !is_null($_SESSION['fingerprint'])) {
            $db->insert('us_fingerprints',['fkUserId' => $user->Data()->id,'Fingerprint' => $_SESSION['fingerprint'],'Fingerprint_Expiry' => date("Y-m-d H:i:s",strtotime("+30 days",strtotime(date("Y-m-d H:i:s")))),'Fingerprint_Added'=>date('Y-m-d H:i:s')]);
            $db->insert('us_fingerprint_assets',['fkFingerprintID' => $db->lastId(),'IP_Address' => ipCheck(),'User_Browser' => getBrowser(),'User_OS' => getOS()]);
          }
          $dest=Input::get('dest');
          if (!empty($dest) || !$dest=='') {
            $redirect=htmlspecialchars_decode(Input::get('redirect'));
            if(!empty($redirect) || $redirect!=='') Redirect::to($redirect);
            else Redirect::to($dest);
          }
          elseif (file_exists($abs_us_root.$us_url_root.'usersc/scripts/custom_login_script.php')) {
            require_once $abs_us_root.$us_url_root.'usersc/scripts/custom_login_script.php';
          }
          else {
            if (($dest = Config::get('homepage')) ||
              ($dest = 'account.php')) {
              #echo "DEBUG: dest=$dest<br />\n";
              #die;
              Redirect::to($dest);
            }
          }
        }
        elseif($twoPassed==false) {
          if(isset($_SESSION['twofa_count']) && $_SESSION['twofa_count']==3) {
            logger($user->data()->id,"Two FA","3 failed verification attempts, logging out");
            Redirect::to('../users/logout.php');
          }
          if($twoCode=='' || empty($twoCode)) $errors[] = lang("2FA_NP");
          else $errors[] = lang("2FA_INV");
          if(isset($_SESSION['twofa_count'])) $_SESSION['twofa_count'] = $_SESSION['twofa_count']+1;
          else $_SESSION['twofa_count'] = 2;
          logger($user->data()->id,"Two FA","Two FA Verification failed.");
        }
        else {
          $errors[] = lang("2FA_FATAL");
          logger($user->data()->id,"Two FA","Two FA Verification Fatal Error.");
        }
      }
    }
$dest=Input::get('dest');
$redirect=Input::get('redirect');
?>
    <!-- Page Heading -->
    <div class="row">
<?=resultBlock($errors,$successes);?>
        <div class="col-sm-12 col-md-6">
        <h1><?=lang("2FA");?></h1>
      </div>

     </div>
    <div class="row">
    <form class="verify-admin" action="twofa.php" method="POST">
    <div class="col-md-5">
    <div class="input-group"><input type="text" class="form-control"  name="twoCode" id="twoCode"  placeholder="<?=lang("2FA_CODE")?>" autocomplete="off" required autofocus>
        <span class="input-group-btn">
        <input class='btn btn-primary' type='submit' name='verifyTwo' value='<?=lang("GEN_VERIFY");?>' />
      </span></div>
    <input type="hidden" name="dest" value="<?=$dest?>" />
    <input type="hidden" name="redirect" value="<?=$redirect?>" />
    <input type="hidden" value="<?=Token::generate();?>" name="csrf">
    </div>
     </div>
   </form><br />



  </div>
</div>
    <!-- End of main content section -->

<?php require_once $abs_us_root.$us_url_root.'users/includes/page_footer.php'; // the final html footer copyright row + the external js calls ?>

    <!-- Place any per-page javascript here -->

<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; // currently just the closing /body and /html ?>
