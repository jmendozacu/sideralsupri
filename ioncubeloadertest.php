<?php
/*
IonCube Advance Tester
http://blog.resellerclub.com/2010/09/15/how-to-check-if-ioncube-is-installed-on-linux-servers/
*/

if (isset($_GET['phpinfo'])) {
	phpinfo();
	exit;
}

?>
<html>
<head>

<style>
body, td
{
	font-size:70%;
	font-family:verdana, helvetica, arial;
}

div.main
{
	width:80%;
	text-align:left;
	top:20px;
	position:relative;
	border:2px solid #F0F0F0;
	padding:20px;
}
</style>

<title>IonCube Advance System Test</title>
</head>
<body>
<div align="center"><div class="main">

<center><h2>IonCube Advance System Test</h2></center>

<?php

echo intro();
echo "<br /><br />\n";
echo ioncube_test();
echo "<br /><br />\n";
echo server_software();
echo "<br /><br />\n";
echo additional_info();
echo "<br />\n";
echo more_info();

?>

</div></div>
</body>
</html>

<?php

//rtl tester specific functions

//
// Detect some system parameters
//
function ic_system_info()
{
  $thread_safe = false;
  $debug_build = false;
  $cgi_cli = false;
  $php_ini_path = '';

  ob_start();
  phpinfo(INFO_GENERAL);
  $php_info = ob_get_contents();
  ob_end_clean();

  foreach (split("\n",$php_info) as $line) {
    if (eregi('command',$line)) {
      continue;
    }

    if (preg_match('/thread safety.*(enabled|yes)/Ui',$line)) {
      $thread_safe = true;
    }

    if (preg_match('/debug.*(enabled|yes)/Ui',$line)) {
      $debug_build = true;
    }

    if (eregi("configuration file.*(</B></td><TD ALIGN=\"left\">| => |v\">)([^ <]*)(.*</td.*)?",$line,$match)) {
      $php_ini_path = $match[2];

      //
      // If we can't access the php.ini file then we probably lost on the match
      //
      if (!@file_exists($php_ini_path)) {
	$php_ini_path = '';
      }
    }

    $cgi_cli = ((strpos(php_sapi_name(),'cgi') !== false) ||
		(strpos(php_sapi_name(),'cli') !== false));
  }

  return array('THREAD_SAFE' => $thread_safe,
	       'DEBUG_BUILD' => $debug_build,
	       'PHP_INI'     => $php_ini_path,
	       'CGI_CLI'     => $cgi_cli);
}

function ioncube_test()
{
	$working = "";
	$instructions = "";
	$status = "";

	$working = "\n";
	$status = "";
	$instructions = "";

	$ok					= true;

	$status_class = "red";

	if ( extension_loaded('ionCube Loader') ) {
	  $ioncube_loader_version = ioncube_loader_version_array();
	  $working .= "";
	  $status .= "Installed: version " . $ioncube_loader_version['version'];
	  if ($ioncube_loader_version['major'] < 3 ||
	     ($ioncube_loader_version['major'] == 3 && $ioncube_loader_version['minor'] < 1) ) {
	  	$instructions .= "Ioncube loader is installed but needs to be updated.<br />
			The most recent version of the loader can be found
	  		<a href=\"http://www.ioncube.com/loaders.php\" target=\"_blank\">here</a>.
	  		";
	  	$status_class = "orange";
	  } else {
	  	$instructions .= "No additional configuration required.";
	  	$status_class = "green";
	  }
	} else {
	  $working .= "Testing whether your system supports run-time loading...<br />\n";
	  $sys_info = ic_system_info();
	  if ($sys_info['THREAD_SAFE'] && !$sys_info['CGI_CLI']) {
		$status .= "Your PHP install appears to have threading support and run-time Loading\n"
	."is only possible on threaded web servers if using the CGI, FastCGI or\n"
	."CLI interface.<br />\n";
		$instructions .= "To run encoded files please install the Loader in the php.ini file.\n"
	."Instructions can be found <a href=\"http://www.ioncube.com/loader_installation.php\" target=\"_blank\">here</a>.";
		$ok = false;
	  }

	  if ($sys_info['DEBUG_BUILD']) {
		$status .= "Your PHP installation appears to be built with debugging support\n"
.	"enabled and this is incompatible with ionCube Loaders.<br />\n<br />\nDebugging support in PHP produces slower execution, is\n"
.	"not recommended for production builds and was probably a mistake.<br />\n";

		$instructions .= "You should rebuild PHP without the --enable-debug option.<br />\n";
		$ok = false;
	  }

	  //
	  // Check safe mode and for a valid extensions directory
	  //
	  if ( ini_get('safe_mode') ) {
		$status .= "PHP safe mode is enabled and run time loading will not be possible.";
		$instructions .=   "To run encoded files please install the Loader in the php.ini file.\n"
		.			"Instructions can be found <a href=\"http://www.ioncube.com/loader_installation.php\" target=\"_blank\">here</a>.\n"
		.		    "Alternatively contact your hosting provider or system administrator,\n"
		.		    "and ask them to enable safe mode for your account.";
		$ok = false;
	  }
	  /*
		elseif (!is_dir(realpath(ini_get('extension_dir')))) {
		echo "The setting of extension_dir in the php.ini file is not a directory
		or may not exist and run time loading will not be possible. You do not need
		write permissions on the extension_dir but for run-time loading to work
		a path from the extensions directory to wherever the Loader is installed
		must exist.<br />\n";
		$ok = false;
		}
	  */

	  // If ok to try and find a Loader
	  if ($ok) {
		//
		// Look for a Loader
		//

		// Old style naming should be long gone now
		$test_old_name = false;

		$_u = php_uname();
		$_os = substr($_u,0,strpos($_u,' '));
		$_os_key = strtolower(substr($_u,0,3));

		$_php_version = phpversion();
		$_php_family = substr($_php_version,0,3);

		$_loader_sfix = (($_os_key == 'win') ? '.dll' : '.so');

		$_ln_old="ioncube_loader.$_loader_sfix";
		$_ln_old_loc="/ioncube/$_ln_old";

		$_ln_new="ioncube_loader_${_os_key}_${_php_family}${_loader_sfix}";
		$_ln_new_loc="/ioncube/$_ln_new";

		$working .= "<br />\nLooking for Loader '$_ln_new'";
		if ($test_old_name) {
		  $working .= " or '$_ln_old'";
		}
		$working .= "<br />\n<br />\n";

		$_extdir = ini_get('extension_dir');
		if ($_extdir == './') {
		  $_extdir = '.';
		}

		$_oid = $_id = realpath($_extdir);

		$_here = dirname(__FILE__);
		if ((@$_id[1]) == ':') {
		  $_id = str_replace('\\','/',substr($_id,2));
		  $_here = str_replace('\\','/',substr($_here,2));
		}
		$_rd=str_repeat('/..',substr_count($_id,'/')).$_here.'/';

		if ($_oid !== false) {
		  $working .= "Extensions Dir: $_extdir ($_id)<br />\n";
		  $working .= "Relative Path:  $_rd<br />\n";
		} else {
		  $working .= "Extensions Dir: $_extdir (NOT FOUND)<br />\n<br />\n";

			$status .= "The directory set for the extension_dir entry in the\n"
			.	"php.ini file may not exist, and run time loading will not be possible.<br />\n";
			$instructions .=   "Please ask your hosting provider or system administrator to create the\n"
			.		    "directory<br />\n<br />\n"
			.		    "$_extdir<br />\n<br />\n"
			.		    "ensuring that it is accessible by the web server software. They do not\n"
			.		    "need to restart the server. Then rerun this script. As an alternative,\n"
			.		    "your host could install the Loader in the php.ini file. Instructions can be found <a href=\"http://www.ioncube.com/loader_installation.php\" target=\"_blank\">here</a>.<br />\n";
		  $ok = false;
		}

		if ($ok) {
		  $_ln = '';
		  $_i=strlen($_rd);
		  while($_i--) {
		if($_rd[$_i]=='/') {
		  if ($test_old_name) {
			// Try the old style Loader name
			$_lp=substr($_rd,0,$_i).$_ln_old_loc;
			$_fqlp=$_oid.$_lp;
			if(@file_exists($_fqlp)) {
			  $working .= "Found Loader:   $_fqlp<br />\n";
			  $_ln=$_lp;
			  break;
			}
		  }
		  // Try the new style Loader name
		  $_lp=substr($_rd,0,$_i).$_ln_new_loc;
		  $_fqlp=$_oid.$_lp;
		  if(@file_exists($_fqlp)) {
			$working .= "Found Loader:   $_fqlp<br />\n";
			$_ln=$_lp;
			break;
		  }
		}
		  }

		  //
		  // If Loader not found, try the fallback of in the extensions directory
		  //
		  if (!$_ln) {
		if ($test_old_name) {
		  if (@file_exists($_id.$_ln_old_loc)) {
			$_ln = $_ln_old_loc;
		  }
		}
		if (@file_exists($_id.$_ln_new_loc)) {
		  $_ln = $_ln_new_loc;
		}

		if ($_ln) {
		  $working .= "Found Loader $_ln in extensions directory.<br />\n";
		}
		  }

		  $working .= "<br />\n";

		  if ($_ln) {
		$working .= "Trying to install Loader - this may produce an error...<br />\n<br />\n";
		@dl($_ln);

		if( extension_loaded('ionCube Loader') ) {
		  $ioncube_loader_version = ioncube_loader_version_array();
		  $status .= "The Loader version ".$ioncube_loader_version['version']." was successfully installed and encoded files should be able to\n"
.				"automatically install the Loader when needed.";
		  if ($ioncube_loader_version['major'] < 3 ||
		    ($ioncube_loader_version['major'] == 3 && $ioncube_loader_version['minor'] < 1) ) {
		    $instructions .= "Ioncube loader is installed but needs to be updated.<br />
		      The most recent version of the loader can be found
		      <a href=\"http://www.ioncube.com/loaders.php\" target=\"_blank\">here</a>.
		      ";
		      $status_class = "orange";
		  } else {
		    $instructions .=  "No changes to your php.ini file\n"
.				"are required to use encoded files on this system.";
		    $status_class = "green";
		  }
		} else {
		  $status .= "The Loader was not installed.";
		}
		  } else {
		$status .= 	"Run-time loading should be possible on your system but no suitable Loader\n"
		.				"was found.";
		$instructions .= 	"The Loader for <b>$_os</b> (PHP $_php_family) is required.<br />\n"
		.					"Loaders can be downloaded from <a href=\"http://www.ioncube.com/loaders.php\" target=\"_blank\">www.ioncube.com</a><br />\n";
		$instructions .= 	"Please download the appropriate loader, extract the package and upload the 'ioncube' folder to the root directory of your site (e.g. '/public_html/ioncube/' or '/htdocs/ioncube/').\n"
		.                   "After that run this script again.";

		$status_class = "orange";

		  }
		}
	  }
	}

	

	//$instructions should never be empty.
	//if it is, then put default:
	if (empty($instructions)) {
		$instructions = "Run-time loading is not currently possible.<br />\n"
		.				"Please contact your web-host asking to install ionCube loader in php.ini<br />\n"
		.				"Loaders can be downloaded from <a href=\"http://www.ioncube.com/loaders.php\" target=\"_blank\">www.ioncube.com</a><br />\n"
		.				"Instructions can be found <a href=\"http://www.ioncube.com/loader_installation.php\" target=\"_blank\">here</a><br /><br />\n"
		.				"For additional questions please contact support at $email, providing a link to this script.";
	}

	//echo "<br />\nPlease send the output of this script to $email if you have questions or require further assistance.<br />\n<br />\n";

	$body = "<!-- $working -->"
	.		"<b><font color=\"$status_class\">ionCube Loader</font></b><br />\n"
	.       "<b>Status:</b> $status<br />\n"
	.		"<b>Instructions:</b> $instructions";

	return $body;

}
//END OF RTL-TESTER FUNCTIONS

function server_software() {
	if (isset($_SERVER['SERVER_SOFTWARE'])) {
		$status = $_SERVER['SERVER_SOFTWARE'];
	} else if (($sf = getenv('SERVER_SOFTWARE'))) {
		$status = $sf;
	} else {
		$status = 'n/a';
	}

	if ( strcasecmp(substr($status, 0, 6), "Apache") == 0 ) {
		$status_class = "green";
	} else {
		$status_class = "red";
	}

	$body = "<b><font color=\"$status_class\">Server Software</font></b><br />\n"
	.       "<b>Status:</b> $status<br />\n"
	.		"<b>Instructions:</b> You must run Apache web server. This script does not support IIS.";

	return $body;
}

function additional_info() {

	$php_version = phpversion() . " (" . php_sapi_name() . ")";
	$php_flavour = substr($php_version,0,3);
	$os_name = substr(php_uname(),0,strpos(php_uname(),' '));
	$os_code = strtolower(substr($os_name,0,3));
	$safe_mode = ini_get('safe_mode') ? 'Enabled' : 'Disabled';
	$enable_dl = ini_get('enable_dl') ? 'Enabled' : 'Disabled';
	$sys_info = ic_system_info();
	$cgi = $sys_info['CGI_CLI'] ? 'Yes' : 'No';
	$thread_safe = $sys_info['THREAD_SAFE'] ? 'Yes' : 'No';
	$server_name = $_SERVER['SERVER_NAME'];
	$server_ip = $_SERVER['SERVER_ADDR'];
	$resolved_ip = @gethostbyname($server_name);
	$path = getcwd();

	$body = "<b>Additional Information</b><br />\n"
	.       "<table cellpadding=1 cellspacing=1 border=0>\n"
	.       "<tr><td>PHP Version:</td><td>$php_version</td></tr>\n"
	.       "<tr><td>Operating System:</td><td>$os_name</td></tr>\n"
	.       "<tr><td>safe_mode:</td><td>$safe_mode</td></tr>\n"
	.       "<tr><td>enable_dl:</td><td>$enable_dl</td></tr>\n"
	.       "<tr><td>PHP as CGI:</td><td>$cgi</td></tr>\n"
	.       "<tr><td>Thread safety:</td><td>$thread_safe</td></tr>\n"
	.       "<tr><td>Server name:</td><td>$server_name</td></tr>\n"
	.       "<tr><td>Server IP:</td><td>$server_ip</td></tr>\n"
	.       "<tr><td>Resolved IP:</td><td>$resolved_ip</td></tr>\n"
	.       "<tr><td>Absolute path:</td><td>$path</td></tr>\n"
	.       "<tr><td>PHP info:</td><td><a href=\"".$_SERVER['PHP_SELF']."?phpinfo=1\">Click here</a></td></tr>\n"
	.       "</table>\n";

	return $body;
}

function intro() {
	return "This script will check if IonCube will work on your site.\n"
	.      "The results are displayed in <b><font color=\"green\">green</font></b>, <b><font color=\"orange\">orange</font></b> or <b><font color=\"red\">red</font></b> color.<br />\n"
	.      "If some of the results are not <b><font color=\"green\">green</font></b> please follow the instructions and run this script again.";
}

function more_info() {
	return "This script also displays other important information about your server.<br />\n";
}

function ioncube_loader_version_array () {
	if ( function_exists('ioncube_loader_iversion') ) {
		// Mmmrr
		$ioncube_loader_iversion = ioncube_loader_iversion();
		$ioncube_loader_version_major = (int)substr($ioncube_loader_iversion,0,1);
		$ioncube_loader_version_minor = (int)substr($ioncube_loader_iversion,1,2);
		$ioncube_loader_version_revision = (int)substr($ioncube_loader_iversion,3,2);
		$ioncube_loader_version = "$ioncube_loader_version_major.$ioncube_loader_version_minor.$ioncube_loader_version_revision";
	} else {
		$ioncube_loader_version = ioncube_loader_version();
		$ioncube_loader_version_major = (int)substr($ioncube_loader_version,0,1);
		$ioncube_loader_version_minor = (int)substr($ioncube_loader_version,2,1);
	}
	return array('version'=>$ioncube_loader_version, 'major'=>$ioncube_loader_version_major, 'minor'=>$ioncube_loader_version_minor);
}
