<?php
/**
 * TermGate configuration file
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */

//========================================================================
// Commands database settings
//========================================================================

// Setting the path to the users database file
define('COMMANDSET_SQL_DATABASE_DIRECTORY', dirname(__FILE__).DIRECTORY_SEPARATOR.'db');
define('COMMANDSET_SQL_DATABASE_FILE', dirname(__FILE__).DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.'command_set.sqlite3');

//========================================================================
// Libraries used
//========================================================================
// Setting the path to the lib directory
define('LIB_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR);

// Setting the path for the DBManager library
define('DBMANAGER_LIB',LIB_DIR.'dbManager.php');

//========================================================================
// Gotty parameters
//========================================================================

// Set the path for 'gotty' binary
define('GOTTY_PATH','/usr/sbin/gotty');

// Set the TCP listening port for 'gotty'. Make it match the GOTTY_URL setting below, or your Nginx configuration
define('GOTTY_TCP_PORT',3850);

// Set the IP binding interface for 'gotty'. Make it match the GOTTY_URL setting below, or your Nginx configuration
define('GOTTY_BIND_INTERFACE','127.0.0.1');

// GoTTY doesn't set the TERM environment so we have to set and force it
define('GOTTY_TERM','xterm');

// Set the gotty URL. This might change depending on whether 'gotty' is accessed directly
// or behind a reverse-proxy such as Nginx.
define('GOTTY_URL','https://www.example.com/gotty/');

//========================================================================
// Security Settings
//========================================================================

// Set the user commands will be run as
define('RUN_AS_USER','pi');

// Set whether or not this application should run over HTTPS only. Depending on the commands
// you're planning to run, especially interactive ones, it is highly recommended to use HTTPS only
define('HTTPS_ONLY',true);

// When RESTRICTED_COMMAND_SET is set to 'true', TermGate will only allow commands already defined in the 
// command database. Also, no commands can be added or deleted from the commands set.
// When you first want to define the allowed command set, set this setting to 'false' so that you can
// define and save commands in the database. Once done, you can set it to true to improve security.
define('RESTRICTED_COMMAND_SET',false);
?>