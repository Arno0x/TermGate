<?php
/**
 * TermGate deleteCommand file - This script deletes a command from the command set database.
 * 
 * Deleting a command is permitted only if the RESTRICTED_COMMAND_SET parameter
 * is set to false. 
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */

//------------------------------------------------------
// Include config file 
require_once('config.php');

//------------------------------------------------------
// Deleting a command is only permitted if the RESTRICTED_COMMAND_SET parameter
// is set to false
if (RESTRICTED_COMMAND_SET === true) {
	echo "failure";
	exit();
}

//------------------------------------------------------
// Check if all required parameters have been posted
if (isset($_GET['commandID'])) {
	
	$commandID = $_GET['commandID'];
	
	//------------------------------------------------------
	// Import the DBManager library
	require_once(DBMANAGER_LIB);

	//------------------------------------------------------
	// Open the command set database
	try { 
	    $dbManager = new DBManager (COMMANDSET_SQL_DATABASE_FILE, SQLITE3_OPEN_READWRITE);
	} catch (Exception $e) {
	    echo "[ERROR] Could not open database. Exception received : " . $e->getMessage();
	    exit();
	}
	
	// Add the command to the database and close the database
	if ($dbManager->deleteCommand($commandID)) {
		echo "success";
	} else {
		echo "failure";
	}
	
	$dbManager->close();
}
else {
	echo "failure";
}
?>