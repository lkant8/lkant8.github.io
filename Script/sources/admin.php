<?php
function PageMain() {
	global $TMPL, $db, $confUrl;
	$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
	
	$time = time()+86400;
	$exp_time = time()-86400;
	
	$TMPL['loginForm'] = '
	<div class="card my-5">
	    <div class="card-header">
	        <div class="font-weight-bold">Login</div>
        </div>
        
        <div class="card-body">
            <form action="'.$confUrl.'/index.php?a=admin" method="post">
            
            '.(isset($_GET['msg']) && $_GET['msg'] == 2 ? '<div class="alert alert-danger">Invalid username or password.</div>' : '').'
        
            <div class="form-group">
                <label class="input-container">Username</label>
                <input type="text" name="username" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="input-container">Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            
            <input type="submit" value="Log In" name="login" class="btn btn-primary">
        
        </form>
    </div>
	
    </div>
	';
	$TMPL['contentTitle'] = '';
	if(isset($_POST['login'])) { // Set cookies for Log-in.
		header("Location: ".$confUrl."/index.php?a=admin");
		$username = $_POST['username'];
		$password = md5($_POST['password']);
		
		setcookie("adminUser", $username, $time);
		setcookie("adminPass", $password, $time);
				
		$query = sprintf('SELECT * from admin where username = "%s" and password ="%s"', 
		mysqli_real_escape_string($db, $_COOKIE['adminUser']), 
		mysqli_real_escape_string($db, $_COOKIE['adminPass'])
		);

        if(mysqli_fetch_row(mysqli_query($db, $query))) {
            header("Location: ".$confUrl."/index.php?a=admin");
            exit();
        } else {
            header("Location: ".$confUrl."/index.php?a=admin&msg=2");
            exit();
        }
	} elseif(isset($_COOKIE['adminUser']) && isset($_COOKIE['adminPass'])) { // If cookie admin & pass is set, check for credentials
		$query = sprintf('SELECT * from admin where username = "%s" and password ="%s"', mysqli_real_escape_string($db, $_COOKIE['adminUser']), mysqli_real_escape_string($db, $_COOKIE['adminPass']));
		if(mysqli_fetch_row(mysqli_query($db, $query))) { // If true - Logged-in
			$TMPL['contentTitle'] = '
			<ul class="nav nav-pills mt-3">
              <li class="nav-item">
                <a class="nav-link '.(isset($_GET['b']) == false || empty($_GET['b']) ? 'active' : '').'" href="'.$confUrl.'/index.php?a=admin">General</a>
              </li>
              <li class="nav-item">
                <a class="nav-link '.(isset($_GET['b']) && $_GET['b'] == 'security' ? 'active' : '').'" href="'.$confUrl.'/index.php?a=admin&b=security">Security</a>
              </li>
              <li class="nav-item">
                <a class="nav-link '.(isset($_GET['b']) && $_GET['b'] == 'delete' ? 'active' : '').'" href="'.$confUrl.'/index.php?a=admin&b=delete">Delete</a>
              </li>
            </ul>';
			$TMPL['loginForm'] = '';
			
			$TMPL_old = $TMPL; $TMPL = array();
			$TMPL['url'] = $confUrl; 
			if($_GET['b'] == 'security') { // Security Admin Tab
				$skin = new skin('admin/security'); $settings = '';

				$TMPL['msg'] = '';

                if (isset($_POST['current_password'])) {
                    $queryCurrent = sprintf("SELECT * FROM `admin` WHERE password = '%s' AND `username` = '%s'", mysqli_real_escape_string($db, md5($_POST['current_password'])), mysqli_real_escape_string($db, $_COOKIE['adminUser']));

                    // If the current password is valid
                    if (mysqli_fetch_row(mysqli_query($db, $queryCurrent))) {
                        // If the new password is valid
                        if (isset($_POST['password']) && isset($_POST['repeat_password']) && $_POST['password'] == $_POST['repeat_password'] && !empty($_POST['password'])) {
                            $password = md5($_POST['password']);
                            $query = sprintf("UPDATE `admin` SET password = '%s' WHERE `username` = '%s'", mysqli_real_escape_string($db, $password), mysqli_real_escape_string($db, $_COOKIE['adminUser']));
                            mysqli_query($db, $query);

                            setcookie("adminPass", $password, $time);
                            $_COOKIE['adminPass'] = $password;


                            header("Location: ".$confUrl."/index.php?a=admin&b=security&msg=1");
                            exit();
                        } else {
                            $TMPL['msg'] .= '<div class="alert alert-danger">'.($_POST['password'] ? 'Password does not match.' : 'New password can\'t be empty').'</div>';
                        }
                    } else {
                        $TMPL['msg'] .= '<div class="alert alert-danger">Current password is invalid.</div>';
                    }
                }

                if (isset($_GET['msg'])) {
                    if ($_GET['msg'] == 1) {
                        $TMPL['msg'] .= '<div class="alert alert-success">Password changed.</div>';
                    }
                }

				$TMPL['url1'] = $confUrl;
				$settings .= $skin->make();
			} elseif($_GET['b'] == 'delete') {
                $skin = new skin('admin/delete'); $settings = '';
                if(isset($_POST['delete']) && !empty($_POST['delete'])) { // If is set post && password is not empty then save the password
                    $query = sprintf("DELETE from `links` WHERE `gid` = '%s' || `alias` = '%s'", mysqli_real_escape_string($db, $_POST['delete']), mysqli_real_escape_string($db, $_POST['delete']));
                    mysqli_query($db, $query);
                    header("Location: ".$confUrl."/index.php?a=admin&b=delete&msg=".$_POST['delete']);
                }

                if(isset($_GET['msg'])) {
                    $TMPL['msg'] = '<div class="alert alert-success">The link <strong>'.htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8').'</strong> has been removed.</div>';
                }

                $TMPL['url1'] = $confUrl;
                $settings .= $skin->make();
			} else {
				$skin = new skin('admin/general'); $settings = '';
				// Current Values
				$TMPL['currentTitle'] = $resultSettings[0]; $TMPL['ad1'] = $resultSettings[2]; $TMPL['ad2'] = $resultSettings[3]; $TMPL['ad3'] = $resultSettings[4]; $TMPL['twitter'] = $resultSettings[6]; $TMPL['facebook'] = $resultSettings[7]; $TMPL['gplus'] = $resultSettings[8]; $TMPL['currentApi'] = $resultSettings[1];
				if($resultSettings[5] == '1') {
					$TMPL['on'] = 'selected="selected"';
				} else {
					$TMPL['off'] = 'selected="selected"';
				}
				
				if($resultSettings[9] == '1') {
					$TMPL['con'] = 'selected="selected"';
				} else {
					$TMPL['coff'] = 'selected="selected"';
				}
				
				// Updating the Values
				if(isset($_POST['title']) || isset($_POST['api']) || isset($_POST['ads1']) || isset($_POST['ads2']) || isset($_POST['ads3']) || isset($_POST['captcha']) || isset($_POST['dropdown']) || isset($_POST['twitter']) || isset($_POST['facebook']) || isset($_POST['gplus'])) {
					$query = sprintf("UPDATE `settings` SET title = '%s', api = '%s', ad1 = '%s', ad2 = '%s', ad3 = '%s', captcha = '%s', twitter = '%s', facebook = '%s', gplus = '%s', frame = '%s'",
					mysqli_real_escape_string($db, $_POST['title']),
					mysqli_real_escape_string($db, $_POST['api']),
					mysqli_real_escape_string($db, $_POST['ads1']),
					mysqli_real_escape_string($db, $_POST['ads2']),
					mysqli_real_escape_string($db, $_POST['ads3']),
					mysqli_real_escape_string($db, $_POST['captcha']),
					mysqli_real_escape_string($db, $_POST['twitter']),
					mysqli_real_escape_string($db, $_POST['facebook']),
					mysqli_real_escape_string($db, $_POST['gplus']),
					mysqli_real_escape_string($db, $_POST['dropdown']));
					mysqli_query($db, $query);
					header("Location: ".$confUrl."/index.php?a=admin&msg=1");
				}

                if (isset($_GET['msg']) && $_GET['msg'] == 1) {
                    $TMPL['msg'] = '<div class="alert alert-success">Settings saved.</div>';
                }

				$settings .= $skin->make();
			}
			$TMPL = $TMPL_old; unset($TMPL_old);
			$TMPL['settings'] = $settings;
			
			if(isset($_GET['logout']) == 1) { // Log-out (unset cookies)
				setcookie('adminUser', '', $exp_time);
				setcookie('adminPass', '', $exp_time);
				header("Location: ".$confUrl."/index.php?a=admin");
			}
		} else { // Not Logged-in
			unset($_COOKIE['adminUser']);
			unset($_COOKIE['adminPass']);
		}
	}

	$TMPL['localurl'] = $confUrl;
	$TMPL['titleh'] = $resultSettings[0];
	$TMPL['title'] = 'Admin - '.$resultSettings[0];

	$skin = new skin('admin/content');
	return $skin->make();
}
?>