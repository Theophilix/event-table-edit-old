<?php

/**

 * @version

 * @copyright	Copyright (C) 2007 - 2017 Manuel Kaspar and Theophilix

 * @license		GNU General Public License version 2 or later; see LICENSE.txt

 */



// no direct access

defined('_JEXEC') or die;



jimport('joomla.application.component.controller');



class EventtableeditControllerappointmentform extends JControllerLegacy

{



	function save(){
		$app   = JFactory::getApplication();
		$main  = JFactory::getApplication()->input;
		$post  = $main->getArray($_POST);

		

		$totalappointments_row_col = explode(',', $post['rowcolmix']);
		$tableeditpost = $post['id'];
		$Itemid 	   = $post['Itemid'];
		$model 		   = $this->getModel ( 'appointmentform' );
		$cols 		   = $model->getHeads();
		$rows          = $model->getRows();
		$tableeditpostalldata = $model->getItem($tableeditpost);
		$hoursitem 			  = $tableeditpostalldata->hours;
		$db = JFactory::GetDBO();
		$postdateappointment = explode(',', $post['dateappointment']);
		
		
	//	implode(glue,$user->id);

		// update appointment date to reserved // 
		foreach ($totalappointments_row_col as $rowcol) {
			$temps = explode('_', $rowcol);
			$rops = $temps[0];
			$cops = $temps[1];
			$roweditpost   = $rops;
			$coleditpost   = $cops;

			//	echo $mintdiffrence;
			

			$to_time = strtotime($rows['rows'][0][0]);
			$from_time = strtotime($rows['rows'][1][0]);
			$mintdiffrence =  round(abs($from_time - $to_time) / 60,2);
			$findupdatecell = $cols[$coleditpost]->head;
			$rowupdates = $roweditpost +1;
			

			$selectuserd = "SELECT ".$findupdatecell." FROM #__eventtableedit_rows_".$tableeditpost." WHERE id='".$rowupdates."'";
			$db->setQuery($selectuserd);
			$getUserbooking = $db->loadResult();
			
			if($getUserbooking == 'free'){
				$reserved_app = $post['first_name'].' '.$post['last_name'];
			}else{
				$temp_check = strtolower(trim($getUserbooking));
				$posUser = strpos($temp_check, '<br />');
				if ($posUser !== false) {
					$reserved_app = $getUserbooking.'<br />'.$post['first_name'].' '.$post['last_name'];
				}else{
					$reserved_app = $post['first_name'].' '.$post['last_name'];
				}
			}


			$Update = "UPDATE  #__eventtableedit_rows_".$tableeditpost." SET ".$findupdatecell."='".$reserved_app."' WHERE id='".$rowupdates."'";
			$db->setQuery($Update);
			$db->query();
		}
		// END update appointment date to reserved // 
	
		// create ics files //		
		$ttemp = 0;
		$addAttachment = array();
		

		$timeArr = $postdateappointment;
		sort($timeArr);

	

		$date_array = array();
		$start = '';
		$ref_start = &$start;
		$end = '';
		$ref_end = &$end;
		foreach ($timeArr as $time) {
				$date = date("Y-m-d", strtotime($time));
				if($start == '' || strtotime($time) < strtotime($start)){
					$ref_start = $time;
				}
				if (strtotime($time) > strtotime($end) && strtotime($time) <= strtotime('+ '.$mintdiffrence.' minutes',strtotime($end))){
					$ref_end = $time;
				} else {
					$ref_start = $time;
					$ref_end = $time;
					$date_array[$time] = $time;
				}
				$date_array[$start] = $end;
		}


	
		$arrayof_sdates = array();
		$arrayof_times = array();
		foreach ($date_array as $keyu => $valueu) {
			
		
			$exp_startdate	= explode(' ',$keyu);
			$exp_sdate		= explode('-',$exp_startdate[0]);
			$timesremovedsec = explode(':', $exp_startdate[1]);
			$exp_stime		= explode(':',$exp_startdate[1]);
			$starttimeonly = $exp_stime[0].$exp_stime[1].$exp_stime[2];
			$starttimeonly_email = $exp_stime[0].':'.$exp_stime[1];
		  	$startdate		= date('Ymd',strtotime($keyu))."T".$starttimeonly;
		 	$exp_enddate	= explode(' ',$valueu);
			$exp_edate		= explode('-',$exp_enddate[0]);
			$exp_etime		= explode(':',$exp_enddate[1]);
			$mintplus = intval($exp_etime[1]) + intval($mintdiffrence);
			if($mintplus >= 60){
				$mintsend = $mintplus - 60;
				if($mintsend > 9){
					$mintsendadd = $mintsend;
				}else{
					$mintsendadd = '0'.$mintsend;
				}
				if($exp_etime[0] > 9){
					$hoursends = $exp_etime[0] + 1; 
				}else{
					$hoursends1 = $exp_etime[0] + 1;
					$hoursends = '0'.$hoursends1;
				}
				if($hoursends == 24){
					$endtimeonly = '00'.$mintsendadd.$exp_etime[2];
					
					$enddate		= date('Ymd',strtotime($valueu) + 3600*24)."T".$endtimeonly;
					
				
				
				}else{
					$endtimeonly = $hoursends.$mintsendadd.$exp_etime[2];
					$enddate		= date('Ymd',strtotime($valueu))."T".$endtimeonly;
					
				
				}
				   
				  
				 
			}else{
				$endtimeonly = $exp_etime[0].$mintplus.$exp_etime[2];
				$enddate		= date('Ymd',strtotime($valueu))."T".$endtimeonly;
				}
			$arrayof_sdates[] = date('d.m.Y',strtotime($keyu));
			$arrayof_times[] = $starttimeonly_email;
			
			// START CAL // 

				$config = JFactory::getConfig();
				$summary     = $tableeditpostalldata->summary;
				$datestart   = $startdate;
				$dateend     = $enddate;
				
				$address     = $tableeditpostalldata->location;
				$uri         = JURI::root();
				$description = $post['comment'];
				$tableeditpostalldata->icsfilename = str_replace('{first_name}',$post['first_name'] , $tableeditpostalldata->icsfilename);
				$tableeditpostalldata->icsfilename = str_replace('{last_name}',$post['last_name'] , $tableeditpostalldata->icsfilename);
				
				$summary = str_replace('{first_name}',$post['first_name'] , $summary);
				$summary = str_replace('{last_name}',$post['last_name'] , $summary);
				
				$ttemp1 = $ttemp+1;
				$filename    = $tableeditpostalldata->icsfilename.$ttemp1.'.ics';

$ical = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTEND:'.$dateend.'
UID:'.uniqid().'
DTSTAMP:'.$datestart.'
LOCATION:'.$this->escapeString($address).'
DESCRIPTION:'.$this->escapeString($description).'
URL;VALUE=URI:'.$this->escapeString($uri).'
SUMMARY:'.$this->escapeString($summary).'
DTSTART:'.$datestart.'
END:VEVENT
END:VCALENDAR';

				file_put_contents(JPATH_BASE.'/components/com_eventtableedit/template/ics/'.$filename,$ical);
				$addAttachment[] = JPATH_BASE.'/components/com_eventtableedit/template/ics/'.$filename;
			$msg = JText::_('COM_EVENTEDITTABLE_APPOINTMENT_SUCCESSFULLY_BOOKED');
		$ttemp++;
		}

	
/*
		foreach ($postdateappointment as $appointmentsics) {
			
			$multipleics = $appointmentsics;
			$exp_startdate	= explode(' ',$multipleics);
			$exp_sdate		= explode('-',$exp_startdate[0]);
			$timesremovedsec = explode(':', $exp_startdate[1]);
			$exp_stime		= explode(':',$exp_startdate[1]);
			$starttimeonly = $exp_stime[0].$exp_stime[1].$exp_stime[2];
		 	$startdate		= date('Ymd',strtotime($multipleics))."T".$starttimeonly;


			$exp_enddate	= explode(' ',$multipleics);
			$exp_edate		= explode('-',$exp_enddate[0]);
			$exp_etime		= explode(':',$exp_enddate[1]);
			$mintplus = $exp_stime[1] + $mintdiffrence;
			if($mintplus >= 60){
				$mintsend = $mintplus - 60;
				if($mintsend > 9){
					$mintsendadd = $mintsend;
				}else{
					$mintsendadd = '0'.$mintsend;
				}
				if($exp_stime[0] > 9){
					$hoursends = $exp_stime[0] + 1; 
				}else{
					$hoursends1 = $exp_stime[0] + 1;
					$hoursends = '0'.$hoursends1;
				}
				if($hoursends == 24){
					$endtimeonly = '00'.$mintsendadd.$exp_stime[2];
					 $enddate		= date('Ymd',strtotime($multipleics) + 3600*24)."T".$endtimeonly;
				
				}else{
					$endtimeonly = $hoursends.$mintsendadd.$exp_stime[2];
					 $enddate		= date('Ymd',strtotime($multipleics))."T".$endtimeonly;
				
				}
				   
				  
				 
			}else{
				$endtimeonly = $exp_stime[0].$mintplus.$exp_stime[2];
				$enddate		= date('Ymd',strtotime($multipleics))."T".$endtimeonly;
			}
			
			

			// START CAL // 

				$config = JFactory::getConfig();
				$summary     = $tableeditpostalldata->summary;
				$datestart   = $startdate;
				$dateend     = $enddate;
				
				$address     = $tableeditpostalldata->location;
				$uri         = JURI::root();
				$description = $post['comment'];
				$tableeditpostalldata->icsfilename = str_replace('{first_name}',$post['first_name'] , $tableeditpostalldata->icsfilename);
				$tableeditpostalldata->icsfilename = str_replace('{last_name}',$post['last_name'] , $tableeditpostalldata->icsfilename);
				$ttemp1 = $ttemp+1;
				$filename    = $tableeditpostalldata->icsfilename.$ttemp1.'.ics';

$ical = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTEND:'.$dateend.'
UID:'.uniqid().'
DTSTAMP:'.$datestart.'
LOCATION:'.$this->escapeString($address).'
DESCRIPTION:'.$this->escapeString($description).'
URL;VALUE=URI:'.$this->escapeString($uri).'
SUMMARY:'.$this->escapeString($summary).'
DTSTART:'.$datestart.'
END:VEVENT
END:VCALENDAR';

				file_put_contents(JPATH_BASE.'/components/com_eventtableedit/template/ics/'.$filename,$ical);
				$addAttachment[] = JPATH_BASE.'/components/com_eventtableedit/template/ics/'.$filename;
			$msg = JText::_('COM_EVENTEDITTABLE_APPOINTMENT_SUCCESSFULLY_BOOKED');
		$ttemp++;
		
		}
*/

		// echo '<pre>';
		// print_r($arrayof_sdates);
		// print_r($arrayof_edates);
		// print_r($date_array);
		// exit;
		// for ($b=0; $b < $arrayof_sdates; $b++) { 
		// 	$array = 
		// }




			
				$replace_onlydate = implode(' / ',$arrayof_sdates);
				$replace_onlytime = implode(' / ',$arrayof_times);
				
			// START user email // 
				$mailer = JFactory::getMailer();
				$config = JFactory::getConfig();
				$sender = array( 
				    $tableeditpostalldata->email,
				    $tableeditpostalldata->displayname 
				);
				//$body = JText::sprintf('COM_EVENTEDITTABLE_APPOINTMENT_USER_BODY',$exp_startdate[0],$exp_startdate[1],	$tableeditpostalldata->email,$tableeditpostalldata->phone);
				//echo JText::_($tableeditpostalldata->useremailsubject);
				//echo '<br>';
				$subject = $tableeditpostalldata->useremailsubject;
				$subject = str_replace('{date}', str_replace('-', '.', $exp_startdate[0]), $subject);
				$subject = str_replace('{time}', $timesremovedsec[0].':'.$timesremovedsec[1], $subject);
				$body =  $tableeditpostalldata->useremailtext;


				$body = str_replace('{date}', $replace_onlydate, $body);
				$body = str_replace('{time}',$replace_onlytime, $body);
				//$body = $this->escapeString($description);
				$mailer->setSender($sender);		
				$mailer->addRecipient($post['email']);
				$mailer->setSubject($subject);
				$mailer->isHTML(true);
				$mailer->Encoding = 'base64';
				$mailer->setBody($body);
				// Optional file attached
				$mailer->addAttachment($addAttachment);
				$mailer->Send();
			// End user email //

			// Start admin email //
				$mailer = JFactory::getMailer();
				$config = JFactory::getConfig();
				$sender = array( 
				    $config->get( 'mailfrom' ),
				    $tableeditpostalldata->displayname
				);
				//$adminsubject = JText::sprintf('COM_EVENTEDITTABLE_APPOINTMENT_ADMIN_BODY',$post['first_name'],	$post['last_name']);
				$adminsubject = $tableeditpostalldata->adminemailsubject;
				$adminsubject = str_replace('{first_name}', $post['first_name'], $adminsubject);
				$adminsubject = str_replace('{last_name}', $post['last_name'], $adminsubject);
				
				$description = $post['comment'];

				$description_adminbody = $tableeditpostalldata->adminemailtext;
				$description_adminbody = str_replace('{comment}',$post['comment'], $description_adminbody);
				$description_adminbody = str_replace('{date}',$replace_onlydate, $description_adminbody);
				$description_adminbody = str_replace('{time}',$replace_onlytime, $description_adminbody);
				
				//$adminbody   = $this->escapeString($description_adminbody);
				$adminbody   = $description_adminbody;
				$mailer->setSender($sender);		
				$mailer->addRecipient($tableeditpostalldata->email);
				$mailer->isHTML(true);
				$mailer->setSubject($adminsubject);
				
				$mailer->Encoding = 'base64';
				$mailer->setBody($adminbody);
				// Optional file attached
				$mailer->addAttachment($addAttachment);
				$mailer->Send();
			// End admin email //
				if(count($addAttachment) > 0){
					foreach ($addAttachment as $oneattachment) {
						if(file_exists($oneattachment)){
							unlink($oneattachment);
						}
					}
				}

		$app->redirect(JRoute::_('index.php?option=com_eventtableedit&view=appointments&id='.$tableeditpost.'&Itemid='.$Itemid,false),$msg);



		

	}
	function escapeString($string) {
  return preg_replace('/([\,;])/','\\\$1', $string);
}

	
}

?>