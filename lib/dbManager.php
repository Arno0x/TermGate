<?php

/**
 * TermGate database manager class - This class handles all interactions with
 * the command set database. It extends the SQLite3 class.
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
class DBManager extends SQLite3 {
    
    //--------------------------------------------------------
    // Class constructor
    // @param dbFilename : The path to the database filename
    // @return bool : true if the database was properly opend, false otherwise
    function __construct ($dbFilename, $flags = SQLITE3_OPEN_READWRITE) {
    	    parent::__construct($dbFilename, $flags);
    }
    
    //--------------------------------------------------------
    // Adds a command to the database
    // @param command: the command to add
    // @param label: the display label corresponding to the added command
    // @param isDynamic: whether or not the command should be run in a dynamic terminal rendering
    // @return bool : TRUE if no error, FALSE otherwise
    public function addCommand ($command, $label, $isDynamic = 0) {
        
        // Prepare variables before the query
        $command = SQLite3::escapeString ($command);
        $label = SQLite3::escapeString ($label);
        
        // Prepare SQL query
        $sqlQuery = "INSERT INTO COMMANDS (COMMAND ,LABEL ,ISDYNAMIC) ";
        $sqlQuery .= "VALUES ('".$command."','".$label."','".$isDynamic."');";
        
        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
    }
    
    //--------------------------------------------------------
    // Delete a command
    // @param commandID : The command ID
    // @return bool : TRUE if the command was deleted, FALSE otherwise
    public function deleteCommand ($commandID) {
        
        // Prepare variables before the query
        $commandID = SQLite3::escapeString ($commandID);
        
        // Prepare SQL query
        $sqlQuery = "DELETE from COMMANDS where ID='".$commandID."';";

        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
    }
    
    //--------------------------------------------------------
    // Get the list of commands in the database
    // @return array : an array of all commands in the set (ie the database), or FALSE if there was an error
    public function getCommandList () {
        
        // Prepare SQL query
        $sqlQuery = "SELECT * from COMMANDS;";

        // Perform SQL query
        if(!($ret = $this->query($sqlQuery))) {
            return false;
        }
        else {
        	$result = array();
        	$i=0;
        	while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        		$result[$i++] = $row;
        	}
			return $result;
        }
    }
    
    //--------------------------------------------------------
    // Get a command and its IsDynamic status from the database by its ID
    // @return array : an array containing the command, or FALSE if there was an error
    public function getCommandAndIsDynamicByID ($commandID) {
        
        // Prepare SQL query
        $sqlQuery = "SELECT COMMAND,ISDYNAMIC from COMMANDS where ID='".$commandID."';";

        // Perform SQL query
        if(!($ret = $this->querySingle($sqlQuery, true))) {
            return false;
        }
        else {
            return $ret;
        }
    }
}
?>