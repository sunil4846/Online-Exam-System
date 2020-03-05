<?php
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
// UserSpice Specific Functions
require_once $abs_us_root.$us_url_root.'usersc/includes/custom_functions.php';

$user_agent = $_SERVER['HTTP_USER_AGENT'];

if(!function_exists('testUS')) {
	function testUS(){
		echo "<br>";
		echo "UserSpice Functions have been properly included";
		echo "<br>";
	}
}

if(!function_exists('randomstring')) {
	function randomstring($len){
		$len = $len++;
		$string = "";
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for($i=0;$i<$len;$i++)
		$string.=substr($chars,rand(0,strlen($chars)),1);
		return $string;
	}
}

if(!function_exists('get_gravatar')) {
	function get_gravatar($email, $s = 120, $d = 'mm', $r = 'pg', $img = false, $atts = array() ) {
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
}


//Check if a permission level ID exists in the DB
if(!function_exists('permissionIdExists')) {
	function permissionIdExists($id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT id FROM permissions WHERE id = ? LIMIT 1",array($id));
		$num_returns = $query->count();

		if ($num_returns > 0) {
			return true;
		} else {
			return false;
		}
	}
}

//Check if a user ID exists in the DB
if(!function_exists('userIdExists')) {
	function userIdExists($id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT * FROM users WHERE id = ?",array($id));
		$num_returns = $query->count();
		if ($num_returns > 0){
			return true;
		}else{
			return false;
		}
	}
}

//Retrieve list of groups that can access a menu
if(!function_exists('fetchGroupsByMenu')) {
	function fetchGroupsByMenu($menu_id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT id, group_id FROM groups_menus WHERE menu_id = ? ",array($menu_id));
		$results = $query->results();
		return($results);
	}
}

//Delete all authorized groups for the given menu(s) and then add from args
if(!function_exists('updateGroupsMenus')) {
	function updateGroupsMenus($group_ids, $menu_ids) {
		$db = DB::getInstance();
		$sql = "DELETE FROM groups_menus WHERE menu_id = ?";
		foreach((array)$menu_ids as $menu_id) {
			#echo "<pre>DEBUG: UGM: group_id=$group_id, menu_id=$menu_id</pre><br />\n";
			$db->query($sql,[$menu_id]);
		}
		return addGroupsMenus($group_ids, $menu_ids);
	}
}

//Add all groups/menus to the groups_menus mapping table
if(!function_exists('addGroupsMenus')) {
	function addGroupsMenus($group_ids, $menu_ids) {
		$db = DB::getInstance();
		$i = 0;
		$sql = "INSERT INTO groups_menus (group_id,menu_id) VALUES (?,?)";
		foreach((array)$group_ids as $group_id){
			foreach((array)$menu_ids as $menu_id){
				#echo "<pre>DEBUG: AGM: group_id=$group_id, menu_id=$menu_id</pre><br />\n";
				if($db->query($sql,[$group_id,$menu_id])) {
					$i++;
				}
			}
		}
		return $i;
	}
}

//Retrieve information for a single permission level
if(!function_exists('fetchPermissionDetails')) {
	function fetchPermissionDetails($id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT id, name FROM permissions WHERE id = ? LIMIT 1",array($id));
		$results = $query->first();
		$row = array('id' => $results->id, 'name' => $results->name);
		return ($row);
	}
}

//Change a permission level's name
if(!function_exists('updatePermissionName')) {
	function updatePermissionName($id, $name) {
		$db = DB::getInstance();
		$fields=array('name'=>$name);
		$db->update('permissions',$id,$fields);
	}
}

//Checks if a username exists in the DB
if(!function_exists('usernameExists')) {
	function usernameExists($username)   {
		$db = DB::getInstance();
		$query = $db->query("SELECT * FROM users WHERE username = ?",array($username));
		$results = $query->results();
		return ($results);
	}
}

//Retrieve information for all users
if(!function_exists('fetchAllUsers')) {
	function fetchAllUsers($orderBy=[], $desc=[], $disabled=true) {
		$db = DB::getInstance();
		$q = "SELECT * FROM users";
		if(!$disabled) {
			$q.=" WHERE permissions=1";
		}
		if(!empty($orderBy)){
			if ($desc === TRUE){
				$q.= " ORDER BY $orderBy DESC";
			}else{
				$q.= " ORDER BY $orderBy";
			}
		}
		$query = $db->query($q);
		$results = $query->results();
		return ($results);
	}
}

//Retrieve complete user information by username, token or ID
if(!function_exists('fetchUserDetails')) {
	function fetchUserDetails($username=NULL,$token=NULL, $id=NULL){
		if($username!=NULL) {
			$column = "username";
			$data = $username;
		}elseif($id!=NULL) {
			$column = "id";
			$data = $id;
		}
		$db = DB::getInstance();
		$query = $db->query("SELECT * FROM users WHERE $column = $data LIMIT 1");
		$results = $query->first();
		return ($results);
	}
}

//Retrieve list of permission levels a user has
if(!function_exists('fetchUserPermissions')) {
	function fetchUserPermissions($user_id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT * FROM user_permission_matches WHERE user_id = ?",array($user_id));
		$results = $query->results();
		return ($results);
	}
}

//Retrieve list of users who have a permission level
if(!function_exists('fetchPermissionUsers')) {
	function fetchPermissionUsers($permission_id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT id, user_id FROM user_permission_matches WHERE permission_id = ?",array($permission_id));
		$results = $query->results();
		return ($results);
		$row[$user] = array('id' => $id, 'user_id' => $user);
		if (isset($row)){
			return ($row);
		}
	}
}

//Unmatch permission level(s) from user(s)
if(!function_exists('removePermission')) {
	function removePermission($permissions, $members) {
		$db = DB::getInstance();
		if(is_array($members)){
			$memberString = '';
			foreach($members as $member){
				$memberString .= $member.',';
			}
			$memberString = rtrim($memberString,',');

			$q = $db->query("DELETE FROM user_permission_matches WHERE permission_id = ? AND user_id IN ({$memberString})",[$permissions]);
		}elseif(is_array($permissions)){
			$permissionString = '';
			foreach($permissions as $permission){
				if(is_numeric($permission)) {
					$permissionString .= $permission.',';
				}
			}
			$permissionString = rtrim($permissionString,',');
			$q = $db->query("DELETE FROM user_permission_matches WHERE user_id = ? AND permission_id IN ({$permissionString})",[$members]);
		}
		return $q->count();
	}
}

//Retrieve a list of all .php files in root files folder
if(!function_exists('getPageFiles')) {
	function getPageFiles() {
		$directory = "../";
		$pages = glob($directory . "*.php");
		foreach ($pages as $page){
			$fixed = str_replace('../','/'.$us_url_root,$page);
			$row[$fixed] = $fixed;
		}
		return $row;
	}
}

//Retrive a list of all .php files in users/ folder
if(!function_exists('getUSPageFiles')) {
	function getUSPageFiles() {
		$directory = "../users/";
		$pages = glob($directory . "*.php");
		foreach ($pages as $page){
			$fixed = str_replace('../users/','/'.$us_url_root.'users/',$page);
			$row[$fixed] = $fixed;
		}
		return $row;
	}
}

//Delete a page from the DB
if(!function_exists('deletePages')) {
	function deletePages($pages) {
		$db = DB::getInstance();
		if(!$query = $db->query("DELETE FROM pages WHERE id IN ({$pages})")){
			throw new Exception('There was a problem deleting pages.');
		}else{
			return true;
		}
	}
}

//Fetch information on all pages
if(!function_exists('fetchAllPages')) {
	function fetchAllPages() {
		$db = DB::getInstance();
		$query = $db->query("SELECT id, page, title, private, re_auth FROM pages ORDER BY id DESC");
		$pages = $query->results();
		//return $pages;

		if (isset($row)){
			return ($row);
		}else{
			return $pages;
		}
	}
}

//Fetch information for a specific page
if(!function_exists('fetchPageDetails')) {
	function fetchPageDetails($id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT id, page, title, private, re_auth FROM pages WHERE id = ?",array($id));
		$row = $query->first();
		return $row;
	}
}

//Check if a page ID exists
if(!function_exists('pageIdExists')) {
	function pageIdExists($id) {
		$db = DB::getInstance();
		$query = $db->query("SELECT private FROM pages WHERE id = ? LIMIT 1",array($id));
		$num_returns = $query->count();
		if ($num_returns > 0){
			return true;
		}else{
			return false;
		}
	}
}

//Toggle private/public setting of a page
if(!function_exists('updatePrivate')) {
	function updatePrivate($id, $private) {
		$db = DB::getInstance();
		if($private == 0) $result = $db->query("UPDATE pages SET private = ?,re_auth = ? WHERE id = ?",array($private,0,$id));
		else $result = $db->query("UPDATE pages SET private = ? WHERE id = ?",array($private,$id));
		return $result;
	}
}

//Add a page to the DB
if(!function_exists('createPages')) {
	function createPages($pages) {
		$db = DB::getInstance();
		foreach($pages as $page){
			$setting = $db->query("SELECT page_default_private FROM settings")->first();
			$fields=array('page'=>$page, 'private'=>$setting->page_default_private);
			$db->insert('pages',$fields);
		}
	}
}

//Match permission level(s) with page(s)
if(!function_exists('addPage')) {
function addPage($page, $permission) {
	$db = DB::getInstance();
	$i = 0;
	if (is_array($permission)){
		foreach($permission as $id){
			$query = $db->query("INSERT INTO permission_page_matches (
				permission_id, page_id ) VALUES ( $id , $page )");
				$i++;
	}
} elseif (is_array($page)){
	foreach($page as $id){
		$query = $db->query("INSERT INTO permission_page_matches (
			permission_id, page_id ) VALUES ( $permission , $id )");
			$i++;
		}
	} else {
		$query = $db->query("INSERT INTO permission_page_matches (
			permission_id, page_id ) VALUES ( $permission , $page )");
			$i++;
		}
		return $i;
	}
}

//Retrieve list of permission levels that can access a page
if(!function_exists('fetchPagePermissions')) {
	function fetchPagePermissions($page_id) {
	$db = DB::getInstance();
	$query = $db->query("SELECT id, permission_id FROM permission_page_matches WHERE page_id = ? ",array($page_id));
	$results = $query->results();
	return($results);
	}
}

//Retrieve list of pages that a permission level can access
if(!function_exists('fetchPermissionPages')) {
	function fetchPermissionPages($permission_id) {
	$db = DB::getInstance();

	$query = $db->query(
		"SELECT m.id as id, m.page_id as page_id, p.page as page, p.private as private
		FROM permission_page_matches AS m
		INNER JOIN pages AS p ON m.page_id = p.id
		WHERE m.permission_id = ?",[$permission_id]);
		$results = $query->results();
		return ($results);
	}
}

//Unmatched permission and page
if(!function_exists('removePage')) {
	function removePage($pages, $permissions) {
		$db = DB::getInstance();
		if(is_array($permissions)){
			$ids = '';
			for($i = 0; $i < count($permissions);$i++){
				$ids .= $permissions[$i].',';
			}
			$ids = rtrim($ids,',');
			if($query = $db->query("DELETE FROM permission_page_matches WHERE permission_id IN ({$ids}) AND page_id = ?",array($pages))){
				return $query->count();
			}
		}elseif(is_array($pages)){
			$ids = '';
			for($i = 0; $i < count($pages);$i++){
				$ids .= $pages[$i].',';
			}
			$ids = rtrim($ids,',');
			if($query = $db->query("DELETE FROM permission_page_matches WHERE page_id IN ({$ids}) AND permission_id = ?",array($permissions))){
				return $query->count();
			}
		}
	}
}

//Delete a defined array of users
if(!function_exists('deleteUsers')) {
	function deleteUsers($users) {
		$db = DB::getInstance();
		$i = 0;
		foreach($users as $id){
			$query1 = $db->query("DELETE FROM users WHERE id = ?",array($id));
			$query2 = $db->query("DELETE FROM user_permission_matches WHERE user_id = ?",array($id));
			$i++;
		}
		return $i;
	}
}

// retrieve ?dest=page and check that it exists in the legitimate pages in the
// database or is in the Config::get('whitelisted_destinations')
if(!function_exists('sanitizedDest')) {
	function sanitizedDest($varname='dest') {
		if ($dest = Input::get($varname)) {
			// if it exists in the database then it is a legitimate destination
			$db = DB::getInstance();
			$query = $db->query("SELECT id, page, private FROM pages WHERE page = ?",[$dest]);
			$count = $query->count();
			if ($count>0){
				return $dest;
			}
			// if the administrator has intentionally whitelisted a destination it is legitimate
			if ($whitelist = Config::get('whitelisted_destinations')) {
				if (in_array($dest, (array)$whitelist)) {
					return $dest;
				}
			}
		}
		return false;
	}
}


//Does user have permission
//This is the old school UserSpice Permission System
if(!function_exists('checkPermission')) {
	function checkPermission($permission) {
		$db = DB::getInstance();
		global $user;
		//Grant access if master user
		$access = 0;

		foreach($permission[0] as $perm){
			if ($access == 0){
				$query = $db->query("SELECT id FROM user_permission_matches  WHERE user_id = ? AND permission_id = ?",array($user->data()->id,$perm->permission_id));
				$results = $query->count();
				if ($results > 0){
					$access = 1;
				}
			}
		}
		if ($access == 1){
			return true;
		}
		if ($user->data()->id == 1){
			return true;
		}else{
			return false;
		}
	}
}

if(!function_exists('checkMenu')) {
	function checkMenu($permission, $id = 0) {
		$db = DB::getInstance();
		global $user;
	            if ($id == 0 && $user->isLoggedIn()) $id = $user->data()->id;
		//Grant access if master user
		$access = 0;

		if ($access == 0){
			$query = $db->query("SELECT id FROM user_permission_matches  WHERE user_id = ? AND permission_id = ?",array($id,$permission));
			$results = $query->count();
			if ($results > 0){
				$access = 1;
			}
		}
		if ($access == 1){
			return true;
		}
		if ($user->isLoggedIn() && $user->data()->id == 1){
			return true;
		}else{
			return false;
		}
	}
}

//Retrieve information for all permission levels
if(!function_exists('fetchAllPermissions')) {
	function fetchAllPermissions() {
		$db = DB::getInstance();
		$query = $db->query("SELECT id, name FROM permissions");
		$results = $query->results();
		return ($results);
	}
}

//Displays error and success messages
if(!function_exists('resultBlock')) {
	function resultBlock($errors,$successes){
		//Error block
		if(count($errors) > 0){
			echo "<div class='alert alert-danger alert-dismissible' role='alert'> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
			<ul style='padding-left:1.25rem !important'>";
			foreach($errors as $error){
				echo "<li>".$error."</li>";
			}
			echo "</ul>";
			echo "</div>";
		}

		//Success block
		if(count($successes) > 0){
			echo "<div class='alert alert-success alert-dismissible' role='alert'> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
			<ul style='padding-left:1.25rem !important'>";
			foreach($successes as $success){
				echo "<li>".$success."</li>";
			}
			echo "</ul>";
			echo "</div>";
		}
	}
}

//Inputs language strings from selected language.
if(!function_exists('lang')) {
	function lang($key,$markers = NULL){
		global $lang, $us_url_root, $abs_us_root;
		if($markers == NULL){
			if(isset($lang[$key])){
			$str = $lang[$key];
		}else{
			$str = "";
		}
		}else{
			//Replace any dyamic markers
			if(isset($lang[$key])){
			$str = $lang[$key];
			$iteration = 1;
			foreach($markers as $marker){
				$str = str_replace("%m".$iteration."%",$marker,$str);
				$iteration++;
			}
		}else{
			$str = "";
		}
		}


		//Ensure we have something to return
		// dump($key);
		if($str == ""){
			if(isset($lang["MISSING_TEXT"])){
				$missing = $lang["MISSING_TEXT"];
			}else{
				$missing = "Missing Text";
			}
			//if nothing is found, let's check to see if the language is English.
			if(isset($lang['THIS_CODE']) && $lang['THIS_CODE'] != "en-US"){
				$save = $lang['THIS_CODE'];
				if($save == ''){
					$save = 'en-US';
				}
				//if it is NOT English, we are going to try to grab the key from the English translation
				include($abs_us_root.$us_url_root."users/lang/en-US.php");
				if($markers == NULL){
					if(isset($lang[$key])){
					$str = $lang[$key];
				}else{
					$str = "";
				}
				}else{
					//Replace any dyamic markers
					if(isset($lang[$key])){
					$str = $lang[$key];
					$iteration = 1;
					foreach($markers as $marker){
						$str = str_replace("%m".$iteration."%",$marker,$str);
						$iteration++;
					}
				}else{
					$str = "";
				}
				}
				$lang = [];
				include($abs_us_root.$us_url_root."users/lang/$save.php");
				if($str == ""){
					//This means that we went to the English file and STILL did not find the language key, so...
					$str = "{ $missing }";
					return $str;
				}else{
					//falling back to English
					return $str;
				}
			}else{
				//the language is already English but the code is not found so...
				$str = "{ $missing }";
				return $str;
			}
		}else{
			return $str;
		}
	}
}

//Check if a permission level name exists in the DB
if(!function_exists('permissionNameExists')) {
	function permissionNameExists($permission) {
		$db = DB::getInstance();
		$query = $db->query("SELECT id FROM permissions WHERE
			name = ?",array($permission));
		$results = $query->results();
		if($results) return true;
		else return false;
	}
}

if(!function_exists('addPermission')) {
	//Match permission level(s) with user(s)
	function addPermission($permission_ids, $members) {
		$db = DB::getInstance();
		$i = 0;
		if(is_array($permission_ids)){
			foreach($permission_ids as $permission_id){
				if(is_numeric($permission_id)) {
					if($db->query("INSERT INTO user_permission_matches (user_id,permission_id) VALUES (?,?)",[$members,$permission_id])){
						$i++;
					}
				}
			}
		}elseif(is_array($members)){
			foreach($members as $member){
				if(is_numeric($member)) {
					if($db->query("INSERT INTO user_permission_matches (user_id,permission_id) VALUES (?,?)",[$member,$permission_ids])){
						$i++;
					}
				}
			}
		}
		return $i;
	}
}

if(!function_exists('deletePermission')) {
	//Delete a permission level from the DB
	function deletePermission($permission) {
		global $errors;
		$i = 0;
		$db = DB::getInstance();
		foreach($permission as $id){
			if ($id == 1){
				$errors[] = lang("CANNOT_DELETE_NEWUSERS");
			}
			elseif ($id == 2){
				$errors[] = lang("CANNOT_DELETE_ADMIN");
			}else{
				$query1 = $db->query("DELETE FROM permissions WHERE id = ?",array($id));
				$query2 = $db->query("DELETE FROM user_permission_matches WHERE permission_id = ?",array($id));
				$query3 = $db->query("DELETE FROM permission_page_matches WHERE permission_id = ?",array($id));
				$i++;
			}
		}
		return $i;
	}
}

if(!function_exists('isValidEmail')) {
	//Checks if an email is valid
	function isValidEmail($email){
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		else {
			return false;
		}
	}
}

if(!function_exists('emailExists')) {
	//Check if an email exists in the DB
	function emailExists($email) {
		$db = DB::getInstance();
		$query = $db->query("SELECT email FROM users WHERE email = ?",array($email));
		$num_returns = $query->count();
		if ($num_returns > 0){
			return true;
		}else{
			return false;
		}
	}
}

if(!function_exists('updateEmail')) {
	//Update a user's email
	function updateEmail($id, $email) {
		$db = DB::getInstance();
		$fields=array('email'=>$email);
		$db->update('users',$id,$fields);

		return true;
	}
}

if(!function_exists('echoId')) {
	function echoId($id,$table,$column){
		$db = DB::getInstance();
		$query = $db->query("SELECT $column FROM $table WHERE id = $id LIMIT 1");
		$count=$query->count();

		if ($count > 0) {
			$results=$query->first();
			foreach ($results as $result){
				echo $result;
			}
		} else {
			echo "Not in database";
			Return false;
		}
	}
}

if(!function_exists('bin')) {
	function bin($number){
		if ($number == 0){
			echo "<strong><font color='red'>No</font></strong>";
		}
		if ($number == 1){
			echo "<strong><font color='green'>Yes</font></strong>";
		}
		if ($number != 0 && $number !=1){
			echo "<strong><font color='blue'>Other</font></strong>";
		}
	}
}

if(!function_exists('echouser')) {
	function echouser($id){
		$db = DB::getInstance();
		$settingsQ = $db->query("SELECT echouser FROM settings");
		$settings = $settingsQ->first();

		if($settings->echouser == 0){
			$query = $db->query("SELECT fname,lname FROM users WHERE id = ? LIMIT 1",array($id));
			$count=$query->count();
			if ($count > 0) {
				$results=$query->first();
				echo $results->fname." ".$results->lname;
			} else {
				echo "Unknown";
			}
		}

		if($settings->echouser == 1){
			$query = $db->query("SELECT username FROM users WHERE id = ? LIMIT 1",array($id));
			$count=$query->count();
			if ($count > 0) {
				$results=$query->first();
				echo ucfirst($results->username);
			} else {
				echo "-";
			}
		}

		if($settings->echouser == 2){
			$query = $db->query("SELECT username,fname,lname FROM users WHERE id = ? LIMIT 1",array($id));
			$count=$query->count();
			if ($count > 0) {
				$results=$query->first();
				echo ucfirst($results->username).'('.$results->fname.' '.$results->lname.')';
			} else {
				echo "Unknown";
			}
		}

		if($settings->echouser == 3){
			$query = $db->query("SELECT username,fname FROM users WHERE id = ? LIMIT 1",array($id));
			$count=$query->count();
			if ($count > 0) {
				$results=$query->first();
				echo ucfirst($results->username).'('.$results->fname.')';
			} else {
				echo "Unknown";
			}
		}
	}
}

if(!function_exists('echousername')) {
	function echousername($id){
		$db = DB::getInstance();
		$query = $db->query("SELECT username FROM users WHERE id = ? LIMIT 1",array($id));
		$count=$query->count();
		if ($count > 0) {
			$results=$query->first();
			return ($results->username);
		} else {
			return "Unknown";
		}
	}
}

if(!function_exists('generateForm')) {
	function generateForm($table,$id, $skip=[]){
		$db = DB::getInstance();
		$fields = [];
		$q=$db->query("SELECT * FROM {$table} WHERE id = ?",array($id));
		$r=$q->first();

		foreach($r as $field => $value) {
			if(!in_array($field, $skip)){
				echo '<div class="form-group">';
				echo '<label for="'.$field.'">'.ucfirst($field).'</label>';
				echo '<input type="text" class="form-control" name="'.$field.'" id="'.$field.'" value="'.$value.'">';
				echo '</div>';
			}
		}
		return true;
	}
}

if(!function_exists('generateAddForm')) {
	function generateAddForm($table, $skip=[]){
		$db = DB::getInstance();
		$fields = [];
		$q=$db->query("SELECT * FROM {$table}");
		$r=$q->first();

		foreach($r as $field => $value) {
			if(!in_array($field, $skip)){
				echo '<div class="form-group">';
				echo '<label for="'.$field.'">'.ucfirst($field).'</label>';
				echo '<input type="text" class="form-control" name="'.$field.'" id="'.$field.'" value="">';
				echo '</div>';
			}
		}
		return true;
	}
}

if(!function_exists('updateFields2')) {
	function updateFields2($post, $skip=[]){
		$fields = [];
		foreach($post as $field => $value) {
			if(!in_array($field, $skip)){
				$fields[$field] = sanitize($post[$field]);
			}
		}
		return $fields;
	}
}

if(!function_exists('hasPerm')) {
	function hasPerm($permissions, $id=null,$deny_master_account=false) {
		if(is_null($id)) {
			global $user;
			if($user->isLoggedIn()) $id=$user->data()->id;
			else {
				return false;
			}
		}
		if($id=='') {
			return false;
		}
		$db = DB::getInstance();
		global $user;
		global $master_account;
		//Grant access if master user
		$access = 0;
		if($id==null) {
			$id=$user->data()->id;
		}

		if(count($permissions)>0) {

			if($access == 0) {
				$query = $db->query("SELECT id FROM user_permission_matches  WHERE user_id = ? AND permission_id IN (".substr(str_repeat(",?",count($permissions)),1).")", array_merge([$id],$permissions));
				$results = $query->count();
				if($results > 0) {
					$access = 1;
				}
			}
		}
		if($access == 1) {
			return true;
		}
		if(in_array($user->data()->id, $master_account) && !$deny_master_account) {
			return true;
		} else {
			return false;
		}
	}
}

if(!function_exists('echopage')) {
	function echopage($id){
		$db = DB::getInstance();
		$query = $db->query("SELECT page FROM pages WHERE id = ? LIMIT 1",array($id));
		$count=$query->count();

		if ($count > 0) {
			$results=$query->first();
			echo $results->page;
		} else {
			echo "Unknown";
		}
	}
}

if(!function_exists('mqtt')) {
	function mqtt($id,$topic,$message){
		//id is the server id in the mqtt_settings.php
		$db = DB::getInstance();
		$query = $db->query("SELECT * FROM mqtt WHERE id = ?",array($id));
		$count=$query->count();
		if($count > 0){
			$server = $query->first();

			$host = $server->server;
			$port = $server->port;
			$username = $server->username;
			$password = $server->password;

			$mqtt = new phpMQTT($host, $port, "ClientID".rand());

			if ($mqtt->connect(true,NULL,$username,$password)) {
				$mqtt->publish($topic,$message, 0);
				$mqtt->close();
			}else{
				echo "Fail or time out";
			}
		}else{
			echo "Server not found. Please check your id.";
		}
	}
}

if(!function_exists('updateUser')) {
	//Update User
	function updateUser($column, $id, $value) {
		$db = DB::getInstance();
		$result = $db->query("UPDATE users SET $column = ? WHERE id = ?",array($value,$id));
		return $result;
	}
}

if(!function_exists('clean')) {
	//Cleaning function
	function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.

		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}
}

if(!function_exists('stripPagePermissions')) {
	function stripPagePermissions($id) {
		$db = DB::getInstance();
		$result = $db->query("DELETE from permission_page_matches WHERE page_id = ?",array($id));
		return $result;
	}
}

if(!function_exists('encodeURIComponent')) {
	function encodeURIComponent($str) {
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($str), $revert);
	}
}

if(!function_exists('fetchUserName')) {
	//Fetchs CONCAT of Fname Lname
	function fetchUserName($username=NULL,$token=NULL, $id=NULL){
		if($username!=NULL) {
			$column = "username";
			$data = $username;
		}elseif($id!=NULL) {
			$column = "id";
			$data = $id;
		}
		$db = DB::getInstance();
		$query = $db->query("SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE $column = $data LIMIT 1");
		$count = $query->count();
		if ($count > 0) {
			$results = $query->first();
			return ($results->name);
		} else {
			return "Unknown";
		}
	}
}

if(!function_exists('fetchMessageUsers')) {
	function fetchMessageUsers() {
		$db = DB::getInstance();
		$settingsQ = $db->query("SELECT msg_blocked_users FROM settings");
		$settings = $settingsQ->first();
		if($settings->msg_blocked_users==0) $queryUser = $db->query("SELECT * FROM users WHERE msg_exempt = 0 AND permissions = 1");
		if($settings->msg_blocked_users==1) $queryUser = $db->query("SELECT * FROM users WHERE msg_exempt = 0");
		$resultsUser = $queryUser->results();
		return ($resultsUser);
	}
}

if(!function_exists('archiveThreads')) {
	function archiveThreads($threads,$user_id,$status) {
		$db = DB::getInstance();
		$i = 0;
		foreach($threads as $id){
			if(is_numeric($id)) {
				$query = $db->query("SELECT msg_from,msg_to FROM message_threads WHERE id = ?",array($id));
				$results = $query->first();
				if($results->msg_from == $user_id) {
					$db->query("UPDATE message_threads SET archive_from = ? WHERE id = ?",array($status,$id));
					if($status == 1) $db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_to = ?",array(1,$id,$user_id));
				}
				if($results->msg_to == $user_id) {
					$db->query("UPDATE message_threads SET archive_to = ? WHERE id = ?",array($status,$id));
					if($status == 1) $db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_to = ?",array(1,$id,$user_id));
				}
				$i++;
				logger($user_id,"Messaging","Archived Thread $id.");
			}
		}
		return $i;
	}
}

if(!function_exists('deleteMessages')) {
	function deleteMessages($threads,$status,$user_id) {
		$db = DB::getInstance();
		$i = 0;
		foreach($threads as $id){
			if(is_numeric($id)) {
				$db->query("UPDATE messages SET deleted = ?,msg_read = 1 WHERE id = ?",array($status,$id));
				$i++;
				logger($user_id,"Messaging - Admin","Deleted Message ID $id.");
			}
		}
		return $i;
	}
}

if(!function_exists('deleteThread')) {
	function deleteThread($threads,$user_id,$status) {
		$db = DB::getInstance();
		$i = 0;
		foreach($threads as $id){
			if(is_numeric($id)) {
				$query = $db->query("SELECT msg_from,msg_to FROM message_threads WHERE id = ?",array($id));
				$results = $query->first();
				if($results->msg_from == $user_id) {
					$db->query("UPDATE message_threads SET hidden_from = ? WHERE id = ?",array($status,$id));
					$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_to = ?",array(1,$id,$user_id));
				}
				if($results->msg_to == $user_id) {
					$db->query("UPDATE message_threads SET hidden_to = ? WHERE id = ?",array($status,$id));
					$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_from = ?",array(1,$id,$user_id));
				}
				$i++;
				logger($user_id,"Messaging","Deleted Thread $id.");
			}
		}
		return $i;
	}
}

if(!function_exists('adminArchiveThread')) {
	function adminArchiveThread($threads,$type,$user_id) {
		$db = DB::getInstance();
		$i = 0;
		foreach($threads as $id){
			if($type=="both") {
				$db->query("UPDATE message_threads SET archive_from = ?,archive_to = ? WHERE id = ?",array(1,1,$id));
				$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ?",array(1,$id));
				logger($user_id,"Messaging - Admin","Archived both for $id.");
			}
			if($type=="msg_to") {
				$db->query("UPDATE message_threads SET archive_to = ? WHERE id = ?",array(1,$id));
				$user = $db->query("SELECT msg_to FROM message_threads WHERE id = ?",array($id))->first()->msg_to;
				$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_to = ?",array(1,$id,$user));
				logger($user_id,"Messaging - Admin","Archived to for $id.");
			}
			if($type=="msg_from") {
				$db->query("UPDATE message_threads SET archive_from = ? WHERE id = ?",array(1,$id));
				$user = $db->query("SELECT msg_from FROM message_threads WHERE id = ?",array($id))->first()->msg_from;
				$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_from = ?",array(1,$id,$user));
				logger($user_id,"Messaging - Admin","Archived from for $id.");
			}
			$i++;
		}
		return $i;
	}
}

if(!function_exists('adminUnarchiveThread')) {
	function adminUnarchiveThread($threads,$type,$user_id) {
		$db = DB::getInstance();
		$i = 0;
		foreach($threads as $id){
			if($type=="both") {
				$db->query("UPDATE message_threads SET archive_from = ?,archive_to = ?,hidden_from = ?,hidden_to = ? WHERE id = ?",array(0,0,0,0,$id));
				logger($user_id,"Messaging - Admin","Unarchived and Undeleted both for $id.");
			}
			if($type=="msg_to") {
				$db->query("UPDATE message_threads SET archive_to = ?,hidden_to = ? WHERE id = ?",array(0,0,$id));
				logger($user_id,"Messaging - Admin","Unarchived and Undeleted to for $id.");
			}
			if($type=="msg_from") {
				$db->query("UPDATE message_threads SET archive_from = ?,hidden_from = ? WHERE id = ?",array(0,0,$id));
				logger($user_id,"Messaging - Admin","Unarchived and Undeleted from for $id.");
			}
			$i++;
		}
		return $i;
	}
}

if(!function_exists('adminDeleteThread')) {
	function adminDeleteThread($threads,$type,$user_id) {
		$db = DB::getInstance();
		$i = 0;
		foreach($threads as $id){
			if($type=="both") {
				$db->query("UPDATE message_threads SET hidden_from = ?,hidden_to = ?,archive_to = ?,archive_from = ? WHERE id = ?",array(1,1,1,1,$id));
				$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ?",array(1,$id));
				logger($user_id,"Messaging - Admin","Deleted both for $id.");
			}
			if($type=="msg_to") {
				$db->query("UPDATE message_threads SET hidden_to = ?,archive_from = ? WHERE id = ?",array(1,1,$id));
				$user = $db->query("SELECT msg_to FROM message_threads WHERE id = ?",array($id))->first()->msg_to;
				$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_to = ?",array(1,$id,$user));
				logger($user_id,"Messaging - Admin","Deleted to for $id.");
			}
			if($type=="msg_from") {
				$db->query("UPDATE message_threads SET hidden_from = ?,archive_from = ? WHERE id = ?",array(1,1,$id));
				$user = $db->query("SELECT msg_from FROM message_threads WHERE id = ?",array($id))->first()->msg_from;
				$db->query("UPDATE messages SET msg_read = ? WHERE msg_thread = ? AND msg_from = ?",array(1,$id,$user));
				logger($user_id,"Messaging - Admin","Deleted from for $id.");
			}
			$i++;
		}
		return $i;
	}
}

if(!function_exists('messageUser')) {
	function messageUser($user_id,$request_user,$subject,$body) {
		$db = DB::getInstance();
		$settingsQ = $db->QUERY("SELECT * FROM settings");
		$settings = $settingsQ->first();
		$userData = $db->query("SELECT fname FROM users WHERE id = ?",array($user_id))->first();
		$date = date("Y-m-d H:i:s");

		$thread = array(
			'msg_from'    => $user_id,
			'msg_to'      => $request_user,
			'msg_subject' => $subject,
			'last_update' => $date,
			'last_update_by' => $user_id,
			'hidden_from' => $settings->msg_default_to,
		);
		$db->insert('message_threads',$thread);
		$newThread = $db->lastId();


		$fields = array(
			'msg_from'    => $user_id,
			'msg_to'      => $request_user,
			'msg_body'    => $body,
			'msg_thread'  => $newThread,
			'sent_on'     => $date,
		);

		$db->insert('messages',$fields);
		$email = $db->query("SELECT fname,email,msg_notification FROM users WHERE id = ?",array($request_user))->first();
		if($settings->msg_notification == 1 && $email->msg_notification == 1) {
			$to = rawurlencode($email->email);
			$params = array(
				'fname' => $email->fname,
				'sendfname' => $userData->fname,
				'body' => $body,
				'msg_thread' => $newThread,
			);
			$to = rawurlencode($email->email);
			$emailbody = email_body('_email_msg_template.php',$params);
			email($to,$subject,$emailbody);
		}
		logger($user_id,"Messaging","Sent a message to $email->fname.");
	}
}

if(!function_exists('logger')) {
	function logger($user_id,$logtype,$lognote) {
		$db = DB::getInstance();

		$fields = array(
			  'user_id' => $user_id,
			  'logdate' => date("Y-m-d H:i:s"),
			  'logtype' => $logtype,
			  'lognote' => $lognote,
				'ip'			=> $_SERVER['REMOTE_ADDR'],
			);
		$db->insert('logs',$fields);
		$lastId = $db->lastId();
		return $lastId;
	}
}

if(!function_exists('echodatetime')) {
	function echodatetime($ts) {
		$ts_converted = strtotime($ts);
			$difference = ceil((time() - $ts_converted) / (60 * 60 * 24));
			// if($difference==0) { $last_update = "Today, "; $last_update .= date("g:i A",$convert); }
			if($difference >= 0 && $difference < 7) {
							$today = date("j");
							$ts_date = date("j",$ts_converted);
							if($today==$ts_date) { $date = "Today, "; $date .= date("g:i A",$ts_converted); }
							else {
			$date = date("l g:i A",$ts_converted); } }
			elseif($difference >= 7) { $date = date("M j, Y g:i A",$ts_converted); }
			return $date;
	}
}

if(!function_exists('time2str')) {
	function time2str($ts)
	{
	    if(!ctype_digit($ts))
	        $ts = strtotime($ts);

	    $diff = time() - $ts;
	    if($diff == 0)
	        return 'now';
	    elseif($diff > 0)
	    {
	        $day_diff = floor($diff / 86400);
	        if($day_diff == 0)
	        {
	            if($diff < 60) return 'just now';
	            if($diff < 120) return '1 minute ago';
	            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
	            if($diff < 7200) return '1 hour ago';
	            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
	        }
	        if($day_diff == 1) return 'Yesterday';
	        if($day_diff < 7) return $day_diff . ' days ago';
	        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
	        if($day_diff < 60) return 'last month';
	        return date('F Y', $ts);
	    }
	    else
	    {
	        $diff = abs($diff);
	        $day_diff = floor($diff / 86400);
	        if($day_diff == 0)
	        {
	            if($diff < 120) return 'in a minute';
	            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
	            if($diff < 7200) return 'in an hour';
	            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
	        }
	        if($day_diff == 1) {
						if($day_diff < 4) {
							return date('l', $ts);
						} else {
							return 'Tomorrow';
						}
					}
	        if($day_diff < 4) return date('l', $ts);
	        if($day_diff < 7 + (7 - date('w'))) return 'next week';
	        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
	        if(date('n', $ts) == date('n') + 1) return 'next month';
	        return date('F Y', $ts);
	    }
	}
}

if(!function_exists('ipReason')) {
	function ipReason($reason){
		if($reason == 0){
			echo "Manually Entered";
		}elseif($reason == 1){
			echo "Invalid Attempts";
		}else{
			echo "Unknown";
		}
	}
}

if(!function_exists('checkBan')) {
	function checkBan($ip){
		$db = DB::getInstance();
	  $c = $db->query("SELECT id FROM us_ip_blacklist WHERE ip = ?",array($ip))->count();
		if($c > 0){
			return true;
		}else{
			return false;
		}
	}
}

if(!function_exists('random_password')) {
	function random_password( $length = 16 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
	}
}


if(!function_exists('returnError')) {
function returnError($errorMsg){
    $responseAr = [];
    $responseAr['success'] = true;
    $responseAr['error'] = true;
    $responseAr['errorMsg'] = $errorMsg;
    die(json_encode($responseAr));
}
}

if(!function_exists('userHasPermission')) {
function userHasPermission($userID,$permissionID) {
    $permissionsAr = fetchUserPermissions($userID);
    //if($permissions[0])
    foreach($permissionsAr as $perm)
    {
        if($perm->permission_id == $permissionID)
        {
            return TRUE;
        }
    }
    return FALSE;
}
}

if(!function_exists('requestCheck')) {
function requestCheck($expectedAr)
{
    if(isset($_GET) && isset($_POST))
    {
        $requestAr = array_replace_recursive($_GET, $_POST);
    }elseif(isset($_GET)){
        $requestAr = $_GET;
    }elseif(isset($_POST)){
        $requestAr = $_POST;
    }else{
        $requestAr = array();
    }
    $diffAr = array_diff_key(array_flip($expectedAr),$requestAr);
    if(count($diffAr) > 0)
    {
        returnError("Missing variables: ".implode(',',array_flip($diffAr)).".");
    }else {
        return $requestAr;
    }
}
}

if(!function_exists('adminNotifications')) {
function adminNotifications($type,$threads,$user_id) {
	$db = DB::getInstance();
	$i = 0;
		foreach($threads as $id){
			if($type=="read") {
				$db->query("UPDATE notifications SET is_read = 1 WHERE id = $id");
				logger($user_id,"Notifications - Admin","Marked Notification ID #$id read.");
			}
			if($type=="unread") {
				$db->query("UPDATE notifications SET is_read = 0,is_archived=0 WHERE id = $id");
				logger($user_id,"Notifications - Admin","Marked Notification ID #$id unread.");
			}
			if($type=="delete") {
				$db->query("UPDATE notifications SET is_archived = 1 WHERE id = $id");
				logger($user_id,"Notifications - Admin","Deleted Notification ID #$id.");
			}
			$i++;
		}
	return $i;
	}
}

if(!function_exists('lognote')) {
	function lognote($logid) {
		$db = DB::getInstance();
		$logQ=$db->query("SELECT * FROM logs WHERE id=?",[$logid]);
		if($logQ->count()>0) {
			$log=$logQ->first();
			if(1==2) return 'This is a placeholder';
			/* elseif here for your custom hooks! */
			else return $log->lognote;
			/*With this function you can use whatever hooks you want within your admin logs. Say for example you track each the Page ID the user visits, and you
			want to return the page name, you would store the Page ID in the lognote and 'Page' as the LogType, and do this:
			elseif($row->logtype=='Page') {
				$pageQ = $db->query("SELECT title FROM pages WHERE id = ?",[$row->lognote]);
				if($pageQ->count()>0) return $pageQ->first()->title;
				else return 'Error finding page information';
			}
			---
			You can have as many elseifs that you want! You can also replace the Placeholder with a normal if.
			TO USE THIS FUNCTION:
			Copy it all except the function_exists (outside wrapper) to your usersc/includes/custom_functions.php. It will override any
			chages we make to this helper and avoids you from editing core files. */
		}
		else return false;
	}
}

if(!function_exists('isAdmin')) {
	function isAdmin() {
		global $user;
		if(($user->isLoggedIn() && hasPerm([2],$user->data()->id)) || (isset($_SESSION['cloak_from']) && hasPerm([2],$_SESSION['cloak_from']))){
			return true;
		} else {
			return false;
		}
	}
}

if(!function_exists('isLocalhost')) {
	function isLocalhost() {
		if($_SERVER["REMOTE_ADDR"]=="127.0.0.1" || $_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="localhost"){
		  return true;
		}else{
		  return false;
		}
	}
}

if(!function_exists('currentPageStrict')) {
	function currentPageStrict() {
		$uri=$_SERVER['PHP_SELF'];
		$abs_us_root=$_SERVER['DOCUMENT_ROOT'];

		$self_path=explode("/", $_SERVER['PHP_SELF']);
		$self_path_length=count($self_path);
		$file_found=FALSE;

		for($i = 1; $i < $self_path_length; $i++){
			array_splice($self_path, $self_path_length-$i, $i);
			$us_url_root=implode("/",$self_path)."/";

			if (file_exists($abs_us_root.$us_url_root.'z_us_root.php')){
				$file_found=TRUE;
				break;
			}else{
				$file_found=FALSE;
			}
		}

		$urlRootLength=strlen($us_url_root);
		$page=substr($uri,$urlRootLength,strlen($uri)-$urlRootLength);
		return $page;
	}
}

if(!function_exists('UserSessionCount')) {
	function UserSessionCount() {
		global $user;
		$db=DB::getInstance();
		$q=$db->query("SELECT * FROM us_user_sessions WHERE fkUserID = ? AND UserSessionEnded=0",[$user->data()->id]);
		return $q->count();
	}
}

if(!function_exists('fetchUserSessions')) {
	function fetchUserSessions($all=false) {
		global $user;
		$db = DB::getInstance();
		if(!$all) $q = $db->query("SELECT * FROM us_user_sessions WHERE fkUserID = ? AND UserSessionEnded=0 ORDER BY UserSessionStarted",[$user->data()->id]);
		else $q = $db->query("SELECT * FROM us_user_sessions WHERE fkUserID = ? ORDER BY UserSessionStarted",[$user->data()->id]);
		if($q->count()>0) return $q->results();
		else return false;
	}
}

if(!function_exists('fetchAdminSessions')) {
	function fetchAdminSessions($all=false) {
		global $user;
		$db = DB::getInstance();
		if(!$all) $q = $db->query("SELECT * FROM us_user_sessions WHERE UserSessionEnded=0 ORDER BY UserSessionStarted");
		else $q = $db->query("SELECT * FROM us_user_sessions ORDER BY UserSessionStarted");
		if($q->count()>0) return $q->results();
		else return false;
	}
}

if(!function_exists('killSessions')) {
	function killSessions($sessions,$admin=false) {
		global $user;
		$db = DB::getInstance();
		$i=0;
		foreach($sessions as $session) {
			if(is_numeric($session)) {
				if(!$admin) $db->query("UPDATE us_user_sessions SET UserSessionEnded=1,UserSessionEnded_Time=NOW() WHERE kUserSessionID = ? AND fkUserId = ?",[$session,$user->data()->id]);
				else $db->query("UPDATE us_user_sessions SET UserSessionEnded=1,UserSessionEnded_Time=NOW() WHERE kUserSessionID = ?",[$session]);
				if(!$db->error()) {
					$i++;
					logger($user->data()->id,"User Tracker","Killed Session ID#$session");
				} else {
					$error=$db->errorString();
					logger($user->data()->id,"User Tracker","Error killing Session ID#$session: $error");
				}
			}
		}
		if($i>0) return $i;
		else return false;
	}
}

if(!function_exists('passwordResetKillSessions')) {
	function passwordResetKillSessions($uid=NULL) {
		global $user;
		$db = DB::getInstance();
		if(is_null($uid)) $q = $db->query("UPDATE us_user_sessions SET UserSessionEnded=1,UserSessionEnded_Time=NOW() WHERE fkUserID = ? AND UserSessionEnded=0 AND kUserSessionID <> ?",[$user->data()->id,$_SESSION['kUserSessionID']]);
		else $q = $db->query("UPDATE us_user_sessions SET UserSessionEnded=1,UserSessionEnded_Time=NOW() WHERE fkUserID = ? AND UserSessionEnded=0",[$uid]);
		if(!$db->error()) {
			$count=$db->count();
			if(is_null($uid)) {
				if($count==1) logger($user->data()->id,"User Tracker","Killed 1 Session via Password Reset.");
				if($count >1) logger($user->data()->id,"User Tracker","Killed $count Sessions via Password Reset.");
			} else {
				if($count==1) logger($user->data()->id,"User Tracker","Killed 1 Session via Password Reset for UID $uid.");
				if($count >1) logger($user->data()->id,"User Tracker","Killed $count Sessions via Password Reset for UID $uid.");
			}
			return $count;
		} else {
			$error=$db->errorString();
			if(is_null($uid)) {
					logger($user->data()->id,"User Tracker","Password Reset Session Kill failed, Error: ".$error);
			} else {
				logger($user->data()->id,"User Tracker","Password Reset Session Kill failed for UID $uid, Error: ".$error);
			}
			return $error;
		}
	}
}

if(!function_exists('username_helper')) {
	function username_helper($fname,$lname,$email) {
		$db = DB::getInstance();
		$settings=$db->query("SELECT * FROM settings")->first();
			$preusername = $fname[0];
			$preusername .= $lname;
			$preusername=strtolower(clean($preusername));
			$preQ = $db->query("SELECT username FROM users WHERE username = ?",array($preusername));
			if(!$db->error()) {
				$preQCount=$preQ->count();
			} else {
				return false;
			}
			if($preQCount == 0) {
				return $preusername;
			}
			$preusername = $fname;
			$preusername .= $lname[0];
			$preusername=strtolower(clean($preusername));
			$preQ = $db->query("SELECT username FROM users WHERE username = ?",array($preusername));
			if(!$db->error()) {
				$preQCount=$preQ->count();
			} else {
				return false;
			}
			if($preQCount == 0) {
				return $preusername;
			}
			return $email;
	}
}

if(!function_exists('oxfordList')){
	function oxfordList($data,$opts=[]){
		$msg = '';
		if(is_array($data)){
			if($opts == []){
				echo implode(", ", $data);
			}else{
				$final = $opts['final'];
				$c= count($data);
				for($i = 0; $i<=$c; $i++){
					if(isset($data[$i])){
						if($i == $c-1){
							$msg .= " ".$final." ";
						}

						$msg .= $data[$i];
						if($i < $c-1){
							$msg .= ",";
						}

					}
				}
			}
		}
		return $msg;
	}
}

if(!function_exists('currentFile')) {
	function currentFile() {
		$abs_us_root=$_SERVER['DOCUMENT_ROOT'];

		$self_path=explode("/", $_SERVER['PHP_SELF']);
		$self_path_length=count($self_path);
		$file_found=FALSE;

		for($i = 1; $i < $self_path_length; $i++){
			array_splice($self_path, $self_path_length-$i, $i);
			$us_url_root=implode("/",$self_path)."/";

			if (file_exists($abs_us_root.$us_url_root.'z_us_root.php')){
				$file_found=TRUE;
				break;
			}else{
				$file_found=FALSE;
			}
		}

		$urlRootLength=strlen($us_url_root);
		return substr($_SERVER['PHP_SELF'],$urlRootLength,strlen($_SERVER['PHP_SELF'])-$urlRootLength);
	}
}

if(!function_exists('importSQL')) {
	function importSQL($file) {
		global $db;
		$lines = file($file);
		// Loop through each line
		foreach ($lines as $line)
		{
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')
			continue;

			// Add this line to the current segment
			$templine .= $line;
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';')
			{
				// Perform the query
				$db->query($templine);
				// Reset temp variable to empty
				$templine = '';
			}
		}
	}
}

if(!function_exists('pluginActive')) {
	function pluginActive($plugin) {
		global $db,$user,$us_url_root;
		$check = $db->query("SELECT id FROM us_plugins WHERE plugin = ? and status = ?",array($plugin,"active"))->count();
		if($check != 1) {
			logger($user->data()->id,"Errors","Attempted to access disabled $plugin");
			Redirect::to($us_url_root.'users/admin.php?view=plugins&err=Plugin+is+disabled');
			return false;
		}else{
			return true;
		}
	}
}

if(!function_exists('languageSwitcher')) {
	function languageSwitcher() {
		global $db,$user,$abs_us_root,$us_url_root,$currentPage,$settings,$token;
		if($settings->allow_language != 1){
			return false;
		}
		$your_token = $_SERVER['REMOTE_ADDR'];
		if(!empty($_POST['language_selector'])){

				$the_token = Input::get("your_token");
			if($your_token != $the_token){
				err("Language change failed");
				return false;
			}else{
				$count = 0;
			  $set = '';
			  foreach($_POST as $k=>$v){
			    $count++;

			    if($count != 3){
			      continue;
			    }else{
			      $set = substr($k, 0, -2);
			    }
			  }
				if(strlen($set) != 5 || (substr($set,2,1) != '-')){
					//something is fishy with this language key
					err("Language change failed");
					return false;
				}
			  $_SESSION['us_lang']=$set;
				if(isset($user) && $user->isLoggedIn()){
				$db->update('users',$user->data()->id,['language'=>$set]);
				}
			  header("Refresh:0");
			}

			}
		$_SESSION['your_token']=$your_token;
		$languages = scandir($abs_us_root.$us_url_root."users/lang");?>

		<form class="" action="" method="post">
			<p align="center">
			<input type="hidden" name="your_token" value="<?=$your_token?>">

			<input type="hidden" name="language_selector" value="1">
			<?php
			foreach($languages as $k=>$v){
				$languages[$k] = substr($v,0,-4);
				if(file_exists($abs_us_root.$us_url_root."users/lang/flags/".$languages[$k].".png")){?>
					<input type="image" title="<?=$languages[$k]?>" alt="<?=$languages[$k]?>" name="<?=$languages[$k]?>" src="<?=$us_url_root."users/lang/flags/".$languages[$k].".png"?>" border="0" alt="Submit" style="width: 40px;" />
				<?php }
			} ?>
		</p>
		<input type="hidden" name="csrf" value="<?=$token?>">
	</form>
	<?php
		}
	}

	if(!function_exists('getMyHooks')) {
		function getMyHooks($opts = []) {
			global $db, $currentPage;

		if($opts == []){
			$hooks = $db->query("SELECT * FROM us_plugin_hooks WHERE page = ? AND disabled = 0",[$currentPage])->results();
		}elseif(isset($opts['page']) && $opts['page'] != ''){
			$hooks = $db->query("SELECT * FROM us_plugin_hooks WHERE page = ? AND disabled = 0",[$opts['page']])->results();
		}
			$data = [];
      $data['post'] = [];
      $data['form'] = [];
      $data['body'] = [];
      $data['bottom'] = [];
      $counter = 0;
			foreach($hooks as $h){
				if($h->position == "post"){
					$data['post'][$counter] = $h->folder.'/'.$h->hook;
				}elseif($h->position == "form"){
					$data['form'][$counter] = $h->folder.'/'.$h->hook;
				}elseif($h->position == "body"){
					$data['body'][$counter] = $h->folder.'/'.$h->hook;
				}elseif($h->position == "bottom"){
					$data['bottom'][$counter] = $h->folder.'/'.$h->hook;
				}
        $counter++;
			}
					return $data;
	}
}

if(!function_exists('includeHook')) {
	function includeHook($hooks,$position) {
		global $db, $abs_us_root, $us_url_root, $usplugins;
    foreach($hooks[$position] as $h)
		if(isset($h) && file_exists($abs_us_root.$us_url_root.'usersc/plugins/'.$h) && $h != ''){
			$plugin = strstr($h, '/', 'before_needle');
			if($usplugins[$plugin] == 1){//only include this file if plugin is installed and active.
			include $abs_us_root.$us_url_root.'usersc/plugins/'.$h;
			}
		}else{
			//automatically disable hook ...eventually
		}
}
}



if(!function_exists('registerHooks')) {
	function registerHooks($hooks,$plugin_name) {
		global $db;
		foreach($hooks as $k=>$v){
		foreach($v as $key=>$value){
			$fields = array(
				'page'=>$k,
				'folder'=>$plugin_name,
				'position'=>$key,
				'hook'=>$value,
			);
			$db->insert('us_plugin_hooks',$fields);
		}
		}
}
}

if(!function_exists('deRegisterHooks')) {
	function deRegisterHooks($plugin_name) {
		global $db;
		$hooks = $db->query("DELETE FROM us_plugin_hooks WHERE folder = ?",[$plugin_name]);

}
}
