<?php 
	session_start();			
	require('Basecamp.class.php');	
	$bc = new Basecamp($_SESSION['sBasecampURL'],$_SESSION['sUsername'],$_SESSION['sPassword']);
	$response = $bc->getPeopleForProject($_REQUEST['pid']);		
	$arrayData = json_decode(json_encode((array) simplexml_load_string($response['body'])),1);		

	$strUserData = "";					
	foreach($arrayData['person'] as $key=>$val1){						
				
				$strUserData .= "<option value='".$val1['id']."'>".$val1['first-name']." ".$val1['last-name']."</option>";
		
	}		
	echo $strUserData;
	die;
?>
			