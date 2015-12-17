<?php
/**
 * TermGate runCommand file - This script runs a shell command.
 * 
 * Shell command is passed either:
 * 1. as POST parameter (ie: it is not a predefined command stored in the command set database)
 * or
 * 2. as an ID, in a GET parameter, relating to a command already stored in the command set database.
 * 
 * WARNING: if the RESTRICTED_COMMAND_SET parameter is set to false, only commands stored in the command set database
 * can be called.
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file 
require_once('config.php');

// First check if a command has been specified as a POST parameter
// Only allowed if the RESTRICTED_COMMAND_SET is set to false (ie: not restricted)
if (isset($_POST['oneTimeCommand']) && RESTRICTED_COMMAND_SET === false) {
	
	$isDynamic = isset($_POST['oneTimeCommandIsDynamic']) ? true : false;
	
	// Perform a sanitization of the input variable
	$command = escapeshellcmd(urldecode($_POST['oneTimeCommand']));

}
// Second, check if a commandID as been specified as a GET parameter
else if (isset($_GET['commandID'])) {
	
	$commandID = escapeshellcmd(urldecode($_GET['commandID']));
	// Import the DBManager library
	require_once(DBMANAGER_LIB);
	
	//------------------------------------------------------
	// Open the command set database
	try { 
	    $dbManager = new DBManager (COMMANDSET_SQL_DATABASE_FILE, SQLITE3_OPEN_READONLY);
	} catch (Exception $e) {
	    echo "[ERROR] Could not open database. Exception received : " . $e->getMessage();
	    exit();
	}
	
	if($ret=$dbManager->getCommandAndIsDynamicByID($commandID)) {
		$command = escapeshellcmd($ret['COMMAND']);
		$isDynamic = $ret['ISDYNAMIC'];
	}

	$dbManager->close();
}

// If it's not a dynamic/interactive command simply call the command and return the result
if (!$isDynamic) {
	echo "<div style=\"margin: 10px\">Command: ".$command."</div>";
	
	// Prepare the command
	$command = "sudo -u ".RUN_AS_USER." -i ".$command;
	exec ($command, $return);

	echo "<pre>";
	echo implode("\n", $return);
	echo "</pre>";	
}
// Else, if it's a dynamic/interactive command, we have to instantiate Gotty along with the proper command.
// Note: commands are executed as the user specified in the RUN_AS_USER setting
else {
	// Prepare the Gotty command
	// ** A bit of explanation about the use of the DISPLAY environment **
	// ** Most SUDOERS environment will have the 'env_reset' option set, especially for untrusted account such as ones used to run the web server.
	// ** With this option set, the environment variables that can be set by the sudo command is restricted to a small set, including the DISPLAY variable.
	// ** Because in a TermGate + Gotty typical usage, this variable is most probably unused or, if required, has 90% of chances to point back to the client IP
	// ** I'm using it to store the client IP, as it can be a useful information once in a Gotty shell.
	$gottyCommand = "sudo -b -u ".RUN_AS_USER." DISPLAY=".$_SERVER['REMOTE_ADDR']." TERM=".GOTTY_TERM." -i ".GOTTY_PATH." --once -w -p ".GOTTY_TCP_PORT." -a ".GOTTY_BIND_INTERFACE." ".$command." > /dev/null 2>&1";

	
	// Execute the command
	exec($gottyCommand);
	
	// DIRTY (!): Wait 500ms, just the time for gotty to be ready to accept incoming connections
	usleep(500000);
	
	echo "<object class=\"terminal\" data=\"".GOTTY_URL."\" type=\"text/html\"></object>";
}
?>