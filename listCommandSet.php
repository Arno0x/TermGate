<?php
/**
 * TermGate listCommandSet file - This script list the available commands from the command set
 * stored in the database.
 * 
 * It generates HTML ouput suitable for the bootstrap navbar/dropdown item. 
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file 
require_once('config.php');

//------------------------------------------------------
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

// Retrieve all commands stored in the database
$commandList = $dbManager->getCommandList();

// Check if we have at least one element (faster than counting the number of elements in the array)
if (!isset($commandList['0'])) {
	echo '<li class="dropdown-header">No command saved in the set</li>';
} else {
	foreach ($commandList as $command) {
		echo "<li>";
		echo "<a data-command-id=\"".$command['ID']."\">".$command['LABEL']." ";
		if ($command['ISDYNAMIC'] === 1) {
			echo "<span style=\"color: blue\" class=\"fa fa-keyboard-o\"></span>";
		}
		echo "</a>";
		echo "</li>";
	}
}
$dbManager->close();
?>