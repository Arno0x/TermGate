<?php
/**
 * TermGate index file - This is the main TermGate page.
 * 
 * It displays all content and forms necessary to run/save commands.
 * 
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file 
require_once('config.php');

if (HTTPS_ONLY && !isset($_SERVER['HTTPS'])) {
	echo "[ERROR] TermGate is configured to allow HTTPS (secured over SSL) connections only.<br>";
	echo "You can disable this by editing the configuration file (not advised) or ensure you're running TermGate over an SSL connection.";
	exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>TermGate</title>
<!-- Bootstrap -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- Static navbar -->
<nav class="navbar navbar-static-top navbar-inverse">
	<div class="container">
  		<div class="navbar-header">
    		<span class="navbar-brand brand-title" href="#">TermGate<span class="blinking-cursor">&#x25ae;</span></span>
    	</div>
    	<div id="navbar" class="navbar-collapse collapse">
    	<ul class="nav navbar-nav">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Command Set <span class="caret"></span></a>
				<ul id="listCommandSet" class="dropdown-menu">
					<?php require_once('listCommandSet.php'); ?>
				</ul>
			</li>
      	</ul>
      	<?php if (!RESTRICTED_COMMAND_SET) { 
      		echo <<<EOT
      	<button id="addCommand" class="btn btn-success navbar-btn">Add <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
      	<button id="deleteCommand" class="btn btn-danger navbar-btn">Delete <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
      	<ul class="nav navbar-nav navbar-right">
      		<div class="navbar-form navbar-left">
	     		<form id="runCommandForm" class="form-group">
					<input id="oneTimeCommand" name="oneTimeCommand" type="text" size="25" class="form-control" placeholder="Enter a command to run...">
	  				<div class="checkbox navbar-btn">
			            <label class="navbar-link">
			                <input type="checkbox" name="oneTimeCommandIsDynamic">
			                	<a class="tooltips" href="#">Dynamic/Interactive
								<span>Check this for:<br>1. Commands requiring dynamic updates (<i>ex: tail, top</i>)<br>2. Commands requiring interaction (<i>ex: vi, bash, ssh</i>)</span>
								</a>
			            </label>
        			</div>
		  			<button id="runCommand" class="btn btn-primary">Run <span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span></button>
	  			</form>
	  		</div>
      	</ul>
EOT;
      	} ?>
		</div><!--/.nav-collapse -->
  	</div>
</nav>

<?php if (!RESTRICTED_COMMAND_SET) { 
echo <<<EOT
<!-- Add command form -->
<div id="overlayBackground" class="overlay-back"></div>
<div id="overlayMain" class="overlay-div">
	<a href="#"><span onclick="$('#overlayMain, #overlayBackground').fadeOut()" style="font-size: 1.5em;" class="fa fa-close pull-right"></span></a>
	<div class="panel panel-default">
	    <div class="panel-heading" style="text-align: center">
			<span class="panel-title"><strong>Add a command to the set</strong></span>
	    </div> 	<!-- End of panel heading -->
	    <form id="addCommandForm" style="padding: 5px">
			<div id="inputgroup1" class="input-group">
				<span class="input-group-addon"><span class="glyphicon glyphicon-console" aria-hidden="true"></span></span>
				<input id="command" name="command" type="text" maxlength="255" class="form-control" placeholder="Type in the command" autocomplete="off" autofocus>
			</div>
			<div id="commandFeedback" class="text-center feedback"></div>
			<br>
			<div id="inputgroup2" class="input-group">
				<span class="input-group-addon"><span class="fa fa-tags" aria-hidden="true"></span></span>
				<input id="label" name="label" type="text" maxlength="30" placeholder="Label for this command" autocomplete="off" class="form-control">
			</div>
			<div id="labelFeedback" class="text-center feedback"></div>
			<br>
			<div style="text-align: center">
				<input type="checkbox" id="isDynamic" name="isDynamic">
				<a class="tooltips" href="#">Dynamic/Interactive
					<span>Check this for:<br>1. Commands requiring dynamic updates (<i>ex: tail, top</i>)<br>2. Commands requiring interaction (<i>ex: vi, bash, ssh</i>)</span>
				</a>
				<div id="isDynamicFeedback" class="text-center feedback"></div>
			</div>
			<br>
			<div class="text-center"><button id="submit" type="button" class="btn btn-sm btn-primary">Save <span class="glyphicon glyphicon-save" aria-hidden="true"></span></button></div>
		</form>
	</div> <!-- End of panel -->
</div> <!-- End of overlay -->
EOT;
} ?>
<div id="terminal" class="maindiv">
	<div class="img-container">
		<img src="img/terminal.png">
	</div>
</div>

<!-- All javascript scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){

	// Initially disable the delete button
	$('#deleteCommand').prop('disabled', true);
	
	//----------------------------------------------------------------
	// Add Command Form submission validation, performs some checks on the user input
	$("#submit").click(function(e) {
		// Prevent default form behaviour
		e.preventDefault();
		
	   	// Get form parameters attributes and perform basic checks, even if all these will be checked server side as well
		var command = $("#command").val();
		var label = $("#label").val();
		var error = false;

        if (command === '') {
			error = true;	
			$("#inputgroup1").addClass("has-error");
			$("#commandFeedback").show();
			$("#commandFeedback").html("Command is empty");
		}
		
		if (label === '') {
			error = true;
			$("#inputgroup2").addClass("has-error");
			$("#labelFeedback").show();
			$("#labelFeedback").html("Label is empty");
		}
		
		if (!error) {
			$.ajax({
				url: "addCommand.php",
				type: "POST",
				data: $("#addCommandForm").serialize(),
				success: function (data) {
					if (data === 'success') {
						// If the command is successful, refresh the command set dropdown list
						$('#listCommandSet').load("listCommandSet.php");	
					}
				},
				dataType: 'text'
			});
			
			$('#overlayMain, #overlayBackground').fadeOut();
		}
	});
	
	//----------------------------------------------------------------
	// Function to show the "Add command" form as well as fade out the background
	$('#addCommand').click(function () {
		$('#overlayMain, #overlayBackground').fadeIn();
	});
	
	//----------------------------------------------------------------
	// Function to call the run a "one time" command
	$('#runCommand').click(function(e) {
		// Prevent default form behaviour
		e.preventDefault();
		
		// Show a waiting message
		$('#terminal').html('Proceeding with you request...');

		// Get form parameters attributes and perform basic checks, even if all these will be checked server side as well
		var command = $("#oneTimeCommand").val();
        if (command === '') {
        	alert ("No command specified...");
			exit();
		}
	
		$.ajax({
			url: "runCommand.php",
			type: "POST",
			data: $("#runCommandForm").serialize(),
			success: function (data) {
				// If the command is successful, update the terminal div
				$('#terminal').html(data);	
			},
			dataType: 'html'
		});
	});
	
	//----------------------------------------------------------------
	// Use the JQuery 'on" method for dynamically generated HTML elements
	$('#listCommandSet').on('click', 'a[data-command-id]', function () {
		// Show a waiting message
		$('#terminal').html('Proceeding with you request...');
		var commandID = $(this).attr("data-command-id");
		
		$('#deleteCommand').attr('data-delete-id',commandID);
		$('#deleteCommand').prop('disabled', false);
		
		$.ajax({
			url: "runCommand.php?commandID="+commandID,
			type: "GET",
			success: function (data) {
				// If the command is successful, update the terminal div
				$('#terminal').html(data);	
			},
			dataType: 'html'
		});
	});
	
	//----------------------------------------------------------------
	// Delete a command from the set
	$('#deleteCommand').click(function () {
		var commandID = $(this).attr("data-delete-id");
		var label = $('a[data-command-id='+commandID+']').text();
		
		if(confirm("Delete command "+label+"\nAre you SURE ?")) {
			$.ajax({
				url: "deleteCommand.php?commandID="+commandID,
				type: "GET",
				success: function (data) {
					if (data === 'success') {
						// If the command is successful, refresh the command set dropdown list
						$('#listCommandSet').load("listCommandSet.php");
						
						// Disable the delete button
						$('#deleteCommand').attr('data-delete-id','none');
						$('#deleteCommand').prop('disabled', true);
						
					}
				},
				dataType: 'text'
			});
		}
	});
});
</script>
</body>
</html>