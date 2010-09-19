<?php 

$stStartDate = "07-08-2010";
$stEndDate = "20-09-2010";
$strDaySelected= "Tuesday";
//$arDates = getToDoWeekDates($stStartDate,$stEndDate,$strDaySelected);

//echo "<pre>";
//print_r($arDates);


function getToDoWeekDates($stStartDate, $stEndDate,$strDaySelected) {
	
	if($stStartDate && $stEndDate && $strDaySelected) {
		
		// explode both dates
		$arStartDate = explode("-",$stStartDate);
		$arEndDate = explode("-",$stEndDate);

		$m = $arStartDate[1];
		$d = $arStartDate[2];
		$yy = $arStartDate[0];

	 	$m1 = $arEndDate[1];
	 	$d1 = $arEndDate[2];
	 	$yy1 = $arEndDate[0];

		// convert to timestamp to get diff. b/w dates
		$stDate = mktime(0,0,0,$m,$d,$yy) ;
		$enDate = mktime(0,0,0,$m1,$d1,$yy1) ;

		$dateDiff = $enDate - $stDate;
		$fullDays = floor($dateDiff/(60*60*24));
	
		//get the date ($nxtDay) as per user selection day strDaySelected and exit from the loop.		
		for($j=0;$j<7;$j++) {
			$jd = cal_to_jd(CAL_GREGORIAN,$m,$d+$j,$yy);	
			$strDay =jddayofweek($jd,1);		
			if($strDay == $strDaySelected) {
				$nxtDay  = date("Y-m-d",mktime(0, 0, 0, $m,$d+$j,$yy));				
				break;
			}
		} // for
		
		// explode again to get other dates in array by adding 7 more days		
		$arNewDate = explode("-",$nxtDay);		
			
		for($k=0;$k<$fullDays;$k=$k+7) {						
			$stMKTime = mktime(0,0,0,$arNewDate[1],$arNewDate[2]+$k,$arNewDate[0]);
			$stNewDate = date("Y-m-d",$stMKTime);
			// check that the date we need to get b/w start and end dates.
			if($stMKTime < $enDate) 
				$stDates[] = $stNewDate;
		}
		
		// return all dates
		return $stDates;
	}
}

function getToDoMonthDates($stStartDate, $stEndDate,$strDateSelected) {  
  // Vars  
  $day = 86400; // Day in seconds  
  $format = 'Y-m-d'; // Output format (see PHP date funciton)  
  $sTime = strtotime($stStartDate); // Start as time  
  $eTime = strtotime($stEndDate); // End as time  
  $numDays = round(($eTime - $sTime) / $day) + 1;  
  
   
  // Get days  
  for ($d = 0; $d < $numDays; $d++) {  
	   $ddays = date($format, ($sTime + ($d * $day)));
	   $tocheck= date("d",strtotime($ddays)); 
	   if($tocheck==$strDateSelected){
			$stDates[] = $ddays;	
	   }
  }     
  // Return days  
  return $stDates;  
 } 

?> 
