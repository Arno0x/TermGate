<?php
/**
 * TermGate command set database creation file - This script creates the SQLite3 database
 * and creates the command table. 
 * 
 * This script is run only once on FIRST TIME INSTALL or after the database file has been deleted
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file
require_once ('config.php');

//------------------------------------------------------
// If the Command DB file already exists, interrupt the installation process
if (file_exists(COMMANDSET_SQL_DATABASE_FILE)) {
    $message = "[<strong>ERROR <span class='fa fa-frown-o'></span></strong></strong>] Database already installed. If you want to start the installation process over again, delete the command set database file, and then call this page again.";
} else {
	
	//==========================================
	// Proceed with the installation
	//==========================================
	
	//------------------------------------------------------
	// On first installation, create some directories
	if (!file_exists(COMMANDSET_SQL_DATABASE_DIRECTORY)) { mkdir(COMMANDSET_SQL_DATABASE_DIRECTORY); }
	
	//------------------------------------------------------
	// Import the DBManager library
	require_once(DBMANAGER_LIB);
		
	// Allow included script to be included from this script
	define('INCLUSION_ENABLED',true);
	
	//------------------------------------------------------
	// Check for SQLite3 support
	if(!class_exists('SQLite3')) {
	  exit ("SQLite 3 NOT supported");  
	}
	
	//------------------------------------------------------
	// Create and open the database
	try { 
	    $dbManager = new DBManager (COMMANDSET_SQL_DATABASE_FILE, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
	} catch (Exception $e) {
	    echo "[ERROR] Could not create database. Exception received : " . $e->getMessage();
	    exit();
	}
	
	//------------------------------------------------------
	// Drop the COMMANDS table
	$sql = "DROP TABLE COMMANDS;";
	
	if(!($ret = $dbManager->exec($sql))) {
	} else {
		$message = $message."[<strong>OK <span class='fa fa-smile-o'></span></strong>] Previous COMMANDS table dropped successfully<br>";
	}
	
	//------------------------------------------------------
	// Create the COMMANDS table
	$sql =<<<EOF
	      CREATE TABLE COMMANDS (
	      ID INTEGER PRIMARY KEY NOT NULL,
	      COMMAND       VARCHAR(255) UNIQUE NOT NULL,
	      LABEL     VARCHAR(35)    NOT NULL,
	      ISDYNAMIC TINYINT NOT NULL);
EOF;
	
	if(!($ret=$dbManager->exec($sql))) {
		$message = $message."[<strong>ERROR <span class='fa fa-frown-o'></span></strong>] Could not create table COMMANDS<br>";
	  	echo $dbManager->lastErrorMsg();
	} else {
		$message = $message."[<strong>OK <span class='fa fa-smile-o'></span></strong>] Table created successfully<br>";
	}
	
	//------------------------------------------------------
	// Check GoTTY is present on the system and executable
	if (is_executable(GOTTY_PATH)) {
		$message = $message."[<strong>OK <span class='fa fa-smile-o'></span></strong>] GoTTY binary found and is executable<br>";
	} else {
		$message = $message."[<strong>ERROR <span class='fa fa-frown-o'></span></strong>] GoTTY binary has not been found or is not executable. Please check the config file.<br>";
	}
	
	//------------------------------------------------------
	// Check the web server user can sudo to the configured user
	$command = "sudo -u ".RUN_AS_USER." -i id";
	$serverUser = posix_getpwuid(posix_geteuid())['name'];
	
	exec ($command, $return, $returnValue);
	
	if ($returnValue === 0) {
		$message = $message."[<strong>OK <span class='fa fa-smile-o'></span></strong>] Current web server user ".$serverUser." is allowed to sudo to user ".RUN_AS_USER."<br>";
	}else {
		$message = $message."[<strong>ERROR <span class='fa fa-frown-o'></span></strong>] Current web server user ".$serverUser." is NOT allowed to sudo to user ".RUN_AS_USER."<br>";
		$message = $message."You must add the following line to your /etc/sudoers file :<br>";
		$message = $message."<code>".$serverUser." ALL=(".RUN_AS_USER.") NOPASSWD:/bin/bash</code><br>";
	}
	
	// Close the database
	$dbManager->close();
}

echo <<<EOT
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>TermGate</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-default navbar-static-top navbar-inverse">
	<div class="container">
  		<div class="navbar-header">
    		<span class="navbar-brand brand-title" href="#">TermGate<span class="blinking-cursor">&#x25ae;</span></span>
    	</div>
    </div>
</nav>
<div class="jumbotron">
<p>
EOT;
	
// Echoing all installation messages
echo $message;

echo <<<EOT
<br>
<br>
If there's no error messages, you can proceed with TermGate <a href="index.php">here</a>.
</p></div></body>
</html>
EOT;
?>