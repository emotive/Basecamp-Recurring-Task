<?php session_start(); 
ini_set("display_errors",1);
if(isset($_REQUEST['logout']) and $_REQUEST['logout']==1){
	session_destroy();
	header("Location: index.php");
}
if(!isset($_SESSION['message']))
{
	$_SESSION['message']="";
}

error_reporting(0);

?>
<html> 
	<head> 
		<title>Basecamp Recurring Tasks</title> 
		    <meta name="description" content="Create recurring and repeating tasks in Basecamp projects using with the Recurring Tasks Scheduler">
    <meta name="keywords" content="37signals, basecamp, recurring, repeating, tasks, task, project">
    <meta name="copyright" content="emotive, llc">
		<link type="text/css" href="css/style.css" rel="stylesheet" />

		<link type="text/css" href="css/sunny/jquery-ui-1.8.4.custom.css" rel="stylesheet" />
		<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
		
		<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>	 
		<script type="text/javascript"> 
			$(document).ready(function() { 			
				$("#loginform").validate({ 
					rules: { 
						sBasecampURL: { required:true, url:true },// simple rule, converted to {required:true} 	
						sUsername: "required",// simple rule, converted to {required:true} 						
						sPassword: "required"// simple rule, converted to {required:true}					
					}
				}); 
				
				$("#formToDo").validate({ 
					rules: { 
						sProject: "required",				
						sTask: "required",
						sDesc: "required",
						sStartDate: "required",
						sEndDate: "required",
						repeatTime: { required: true }, 
						sTaskRepeat: "required",
						sUser: "required"						 
					}				
					
				}); 
				
				$("#weekday").hide();
				$("#monthday").hide();
			}); 
			
			
		</script> 

		<style type="text/css"> 
			body{ font-family: Verdana; font-size: 12px; line-height: 14px; } 
			div{float:left;width:100%;}
			form{padding-top:30px;}
			a{ color: blue; font-weight:bold;}
			.submit { margin-left: 255px; margin-top: 10px;} 
			.label { display: block; float: left; width: 250px; text-align: right; margin-right: 5px; } 
			.form-row { padding: 7px 0; clear: both; width: 800px; } 
			label.error { width: 250px; display: block; float: left; color: red; padding-left: 10px; } 
			input[type=text],input[type=password], textarea { width: 250px; float: left; } 
			select{width: 250px; float:left;}
			textarea { height: 50px; } 
			.logout{float:right; width:100px;}
			.textcenter{padding-left:10px;}
			.emessg{color:red;text-weight:bold;text-align="center";margin-top:10px;margin-left:20px;}			
		</style> 
			    
		<script language="javascript">
			
			 function getProjectUsers(ajax_page, proj_id)  
			 {  				
				 $.ajax({  
				 type: "GET",  
				 url: ajax_page,  
				 data: "pid=" + proj_id,  
				 dataType: "text/html",  
				 success: function(resulthtml){ 							
							$("#sUser").html(resulthtml);     
						  }  			   
				}); 
			}  
			
			function repeatPeriod(selectObj)
			{
				if(selectObj.value==1)
				{					
					$("#monthday").hide();
					$("#weekday").show();
				}
				if(selectObj.value==2)
				{
					$("#weekday").hide();
					$("#monthday").show();
				}				
			}
		</script>
	</head>
	
	<body>
		<div id="container">
		<div id="logo"><img src="images/logo.png"></div>
		
		<?php 
			
			
			// typical REST request
			if(is_array($_REQUEST) && count($_REQUEST) > 0) {	  	
				if(!isset($_REQUEST['todo']))	
					$_SESSION=$_REQUEST;				
			}
			//print_r($_SESSION);
			//exit;
			if(!isset($_SESSION['sBasecampURL']))
			{
			?>
				<div class="textcenter"><h2>Basecamp Login</h2></div>
				<div><hr></div>
						<div id="disclaimer">
		
			This tool enables Basecamp users to create a recurring task in their Basecamp account.  To get started login using your Basecamp credentials below.  Don't worry we don't store any of your information on our servers.  
		
		</div>
				<div class="emessg"><?php echo $_SESSION['message']; ?></div>
				<div>
					<form action="" method="post" name="loginform" id="loginform" >
						<div class="form-row">
							<span class="label">BaseCamp URL *</span>
							<input type="text" name="sBasecampURL" value="" />
							<div class="submit">							
								e.g. https://username.basecamphq.com
							</div>														
						</div>
						
						<div class="form-row">
							<span class="label">Username *</span>
							<input type="text" name="sUsername" value="" />
						</div>
						<div class="form-row">
							<span class="label">Password *</span>
							<input type="password" name="sPassword" value="" />
						</div>
						<div class="form-row">
							<input class="submit" type="submit" value="Submit" />
						</div>					
					</form>	
				</div>
			<?php $_SESSION['message'] = ""; } else { 				
				$_SESSION['message'] = "";
				require('Basecamp.class.php');					
				$bc = new Basecamp($_SESSION['sBasecampURL'],$_SESSION['sUsername'],$_SESSION['sPassword']);	
				$myid=$bc->getMe();
				if($myid['status']=="401 Unauthorized")
				{
					$_SESSION['message'] = "You have entered invalid credentials. Please try again.";
					$_SESSION['sBasecampURL']=NULL;
					//header("Location:index.php");
					echo "<script language='javascript' type='text/javascript'>alert('Invalid login try again.');</script>";
					echo "<meta http-equiv='refresh' content='0;URL=index.php'>"; 

				}
				
				if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == "1"){					
					require('functions.php');					
					if(isset($_REQUEST['sTaskWeekRepeat']) && trim($_REQUEST['sTaskWeekRepeat']) != "") {			
						$arDates = getToDoWeekDates($_REQUEST['sStartDate'],$_REQUEST['sEndDate'],$_REQUEST['sTaskWeekRepeat']);
					}					
					if(isset($_REQUEST['sTaskMonthRepeat']) && trim($_REQUEST['sTaskMonthRepeat']) != "") {
						$arDates = getToDoMonthDates($_REQUEST['sStartDate'],$_REQUEST['sEndDate'],$_REQUEST['sTaskMonthRepeat']);
					}					
					$arListData = $bc->createTodoListForProject($_REQUEST['sProject'],$_REQUEST['sTask']);
					$intListID = $arListData['id'];					
					$content = $_REQUEST['sDesc'];
					$responsible_party_type = "person";					
					$responsible_party_id = $_REQUEST['sUser'];		
					for($i=0;$i<count($arDates);$i++) {
						$arTodoItem = $bc->createTodoItemForList($intListID, $content,$arDates[$i], 	$responsible_party_type, $responsible_party_id, $notify=null);	
					}
					$_SESSION['message']="The To Do list has been added.";
					header("Location: index.php");					
				} else {
					$response = $bc->getProjects();		
				}		
				$arrayprojectData = json_decode(json_encode((array) simplexml_load_string($response['body'])),1);
				if(!isset($arrayprojectData['project'][0]))
				{
					$arrayPData['project'][0]=$arrayprojectData['project'];
				}
				else
				{
					$arrayPData=$arrayprojectData;
				}				
				$arrayData=array();
				
				foreach($arrayPData['project'] as $vdata){
				
					if(isset($vdata['status']) && $vdata['status']=="active")
					{
					
						$arrayData[$vdata['id']]=$vdata['name'];
					}
				}			
				asort($arrayData);
				
			?>
			<!-- required plugins -->
			
			<script type="text/javascript" src="js/jquery-ui-1.8.4.custom.min.js"></script>
			
			<script type="text/javascript">
				$(function(){

					// Start Datepicker
					$('#datepicker1').datepicker({
						inline: true,
						dateFormat: "yy-mm-dd"
					});	

					// End Datepicker
					$('#datepicker2').datepicker({
						inline: true,
						dateFormat: "yy-mm-dd"
					});	
						
					
					//hover states on the static widgets
					$('#dialog_link, ul#icons li').hover(
						function() { $(this).addClass('ui-state-hover'); }, 
						function() { $(this).removeClass('ui-state-hover'); }
					);
					
				});
			</script>

			<div class="logout"><a href="index.php?logout=1">Logout</a></div>
			<div><h2>To Do Form</h2></div>
			<div>
				<hr>
				<div class="emessg"><?php echo $_SESSION['message']; ?></div>
				<form id="formToDo" name="formToDo" method="post" action=""> 
				<input type="hidden" name="todo" value="1" />
					<div class="form-row">
						<span class="label">To Do List Name *</span>
						<input type="text" name="sTask" id="sTask" />
					</div>
					<div class="form-row">
						<span class="label">To Do Item *</span>
						<textarea name="sDesc" ></textarea>
					</div> 
					<div class="form-row">
						<span class="label">Project *</span>
						<select onChange="javascript:getProjectUsers('project_users.php',this.value);" name="sProject" id="sProject">
							<option value="0">- Select -</option>
							<?php	foreach($arrayData as $k=>$v)
									{
										echo "<option name='projectid' value='".$k."'>".$v."</option>";
									}//foreach($arrayData['project'] as $k=>$v)								
							?>			
						</select>
					</div> 
					      
					
					<div class="form-row">
						<span class="label">Start Date *</span>
						<input type="text" name="sStartDate" id="datepicker1" class="sStartDate"/>
					</div>
					<div class="form-row">
						<span class="label">End Date *</span>
						<input type="text" name="sEndDate" id="datepicker2" class="sEndDate"/>
					</div>
					<div class="form-row">
						<span class="label">How often it repeats *</span>
						<select name="repeatTime" onchange="repeatPeriod(this);">
							<option value="">--Select--</option>
							<option value="1">Weekly by day of week</option>
							<option value="2">Monthly by day of month</option>
						</select>
					</div>
					<div class="form-row" id="weekday">
						<span class="label">Choose a week day *</span>
						<select name="sTaskWeekRepeat">
							<option value="">--Select--</option>
							<option value="Monday">Monday</option>
							<option value="Tuesday">Tuesday</option>
							<option value="Wednesday">Wednesday</option>
							<option value="Thursday">Thursday</option>
							<option value="Friday">Friday</option>
							<option value="Saturday">Saturday</option>
							<option value="Sunday">Sunday</option>
						</select>						
					</div>
					<div class="form-row" id="monthday">
						<span class="label">Choose a month date *</span>
						<select name="sTaskMonthRepeat">
							<option value="">--Select--</option>
							<?php for($i=1;$i<=31;$i++){?>
								<option value="<?php echo $i;?>"><?php if($i<10){ echo "0".$i;} else { echo $i;} ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-row">
						<span class="label">Select User *</span>
						<select name="sUser" id="sUser">
							<option>- Select -</option>
						</select>
					</div>
					<div class="form-row">
						<input class="submit" type="submit" value="Submit">
					</div> 
				</form>
			</div>
		<?php $_SESSION['message'] = ""; } ?>
		<div id="footer">Built by emotive, llc to solve a problem and make Basecamp better<br>
		Not affiliated with 37signals or Basecamp<br>
		Licensed under the GPL
		</div>
		</div>
		
		<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-18413410-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
		
		
	</body> 
</html> 