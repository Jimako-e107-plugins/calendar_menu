<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2013 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Event calendar plugin - admin functions
 *
 */

/**
 *	e107 Event calendar plugin
 *
 * Event calendar plugin - admin functions
 *
 *	@package	e107_plugins
 *	@subpackage	event_calendar
 */

$eplug_admin = true;		// Make sure we show admin theme
$e_sub_cat = 'event_calendar';
require_once('../../../class2.php');
if (!getperms('P')) 
{
  //headerx('location:'.e_BASE.'index.php');
  e107::redirect();
  exit;
}
e107::lan('calendar_menu', 'admin_calendar_menu', true);
e107::lan('calendar_menu', 'log', true);  	
 
 //include_lan(e_PLUGIN.'calendar_menu/languages/'.e_LANGUAGE.'_admin_calendar_menu.php');

$frm = e107::getForm();
$mes = e107::getMessage();
$sql = e107::getDb();
$uc = e107::getUserClass();		// Userclass object pointer

$message = '';
$calendarmenu_text = '';

$calPref = e107::pref('calendar_menu');

/**
 * Given an array of name => format, reads the $_POST variable of each name, applies the specified formatting, 
 * identifies changes, writes back the changes, makes admin log entry
 *
 *	@param array $prefList - each key is the name of a pref; value is an integer representing its type
 *	@param array $oldPref  - array of current pref values
 *	@param string $logRef  - used as title if any changes to be logged
 *
 *	@return - none
 */
function logPrefChanges(&$prefList, &$oldPref, $logRef)
{
	$admin_log = e107::getAdminLog();
	$calNew = e107::getPlugConfig('calendar_menu');		// Initialize calendar_menu prefs.
	$tp = e107::getParser();
	$prefChanges = array();
	$mes = e107::getMessage();

	foreach ($prefList as $prefName => $process)
	{
		switch ($process)
		{
			case 0 :
				$temp = varset($_POST[$prefName],'');
				break;
			case 1 :
				$temp = intval(varset($_POST[$prefName],0));
				break;
			case 2 :
				$temp = $tp->toDB(varset($_POST[$prefName],''));
				break;
			case 3 :			// Array of integers - turn into comma-separated string
				$tmp = array();
				foreach ($_POST[$prefName] as $v)
				{
					$tmp[] = intval($v);
				}
				$temp = implode(",", $tmp);
				unset($tmp);
				break;
		}
		if (!isset($oldPref[$prefName]) || ($temp != $oldPref[$prefName]))
		{	// Change to process
			$oldPref[$prefName] = $temp;
			$calNew->set($prefName, $temp);
			$prefChanges[] = $prefName.' => '.$temp;
		}
	}
	if (count($prefChanges))
	{
		$result = $calNew->save();
		if ($result === TRUE)
		{
			// Do admin logging
			$logString = implode('[!br!]', $prefChanges);
			$admin_log->log_event($logRef,$logString,'');
			//$mes->addSuccess(LAN_UPDATED); 
		}
		elseif ($result === FALSE)
		{		
			$mes->addError("Error saving calendar prefs"); // TODO LAN
		}
		else
		{	// Should never happen
			$mes->addInfo('Unexpected result: '.$result); // TODO LAN
		}
	}
}


$prefSettings = array(
	'updateOptions' => array(
		'eventpost_admin' =>  1,			// Integer
		'eventpost_super' => 1,				// Integer
		'eventpost_adminlog' =>  1,			// Integer
		'eventpost_menulink' => 1,			// Integer
		'eventpost_showmouseover' => 1,		// Integer
		'eventpost_showeventcount' =>  1,	// Integer
		'eventpost_forum' => 1,				// Integer
		'eventpost_recentshow' => 2,		// String ('LV' or an integer)
		'eventpost_weekstart' => 1, 		// Integer
		'eventpost_lenday' => 1,			// Integer
		'eventpost_dateformat' => 2,		// String ('my' or 'ym')
		'eventpost_datedisplay' => 1,		// Integer
		'eventpost_fivemins' => 1,			// Integer
		'eventpost_editmode' => 1,			// Integer
		'eventpost_caltime' => 1,			// Integer
		'eventpost_timedisplay'	=> 1,		// Integer
		'eventpost_timecustom' => 2,		// String
		'eventpost_dateevent' => 1,			// Integer
		'eventpost_eventdatecustom' => 2,	// String
		'eventpost_datenext' => 1,			// Integer
		'eventpost_nextdatecustom' => 2,	// String
		'eventpost_printlists' => 1,		// Integer
		'eventpost_asubs' => 1,				// Integer
		'eventpost_mailfrom' => 2,			// String
		'eventpost_mailsubject' => 2,		// String
		'eventpost_mailaddress' => 2,		// String
		'eventpost_emaillog' => 1			// Integer
		)
);
if (isset($_POST['updatesettings'])) 
{
	logPrefChanges($prefSettings['updateOptions'], $calPref, 'EC_ADM_06');
	$e107cache->clear('nq_event_cal');		// Clear cache as well, in case displays changed
	//$mes->addSuccess();
}

$action = 'config';		// Default action - show preferences
if (e_QUERY) 
{
	$ec_qs = explode('.', e_QUERY);
	$action = preg_replace('#\W#', '',$ec_qs[0]);
}

require_once('../ecal_class.php'); 
$ecal_class = new ecal_class;


// ****************** MAINTENANCE ******************
if (isset($_POST['deleteold']) && isset($_POST['eventpost_deleteoldmonths']))
{
	$back_count = intval($_POST['eventpost_deleteoldmonths']);
	if (($back_count >= 1) && ($back_count <= 12))
	{
		$old_date = intval(mktime(0,0,0,$ecal_class->cal_date['mon']-$back_count,1,$ecal_class->cal_date['year']));
		$old_string = strftime("%d %B %Y",$old_date);
	//	$message = "Back delete {$back_count} months. Oldest date = {$old_string}";
		$action = 'confdel';
		$ec_qs[1] = $old_date;
	}
	else
		//$message = EC_ADLAN_A148;
		$mes->addError(EC_ADLAN_A148);
}


if (isset($_POST['cache_clear']))
{
	$action = 'confcache';
}


//-------------------------------------------------

require_once(e_ADMIN.'auth.php');

if (!defined('USER_WIDTH')){ define('USER_WIDTH','width:auto'); }



// Actually delete back events
if (isset($_POST['confirmdeleteold']) && ($action == 'backdel'))
{
	$old_date = intval($ec_qs[1]);
	$old_string = strftime("%d %B %Y",$old_date);
	// Check both start and end dates to avoid problems with events originally entered under 0.617
	$qry = "event_start < {$old_date} AND event_end < {$old_date} AND event_recurring = 0";
	//  $message = "Back delete {$back_count} months. Oldest date = {$old_string}  Query = {$qry}";
	if ($sql -> db_Delete('event',$qry))
	{
		// Add in a log event
		$ecal_class->cal_log(4,"db_Delete - earlier than {$old_string} (past {$back_count} months)",$qry);
		//$message = EC_ADLAN_A146.$old_string.EC_ADLAN_A147;
		$mes->addSuccess(EC_ADLAN_A146 . $old_string . EC_ADLAN_A147);
	}
	else
	{
		//$message = EC_ADLAN_A149." : ".$sql->mySQLresult;
		$mes->addError(EC_ADLAN_A149." : ".$sql->mySQLresult);
	}

	$action = 'maint';
}


// Actually empty cache
if (isset($_POST['confirmdelcache']) && ($action == 'cachedel'))
{
  $e107cache->clear('nq_event_cal');
  //$message = EC_ADLAN_A163;
  $mes->addSuccess(EC_ADLAN_A163); // TODO LAN
  $action = 'maint';			// Re-display maintenance menu
  $ns->tablerender($caption, $mes->render() . $text);
}


// Prompt to delete back events
if ($action == 'confdel')
{
	$old_string = strftime("%d %B %Y",$ec_qs[1]);
	$text = "
	<form method='post' action='".e_SELF."?backdel.{$ec_qs[1]}'>
	<table class='table adminform'>
	<tr>
		<td>".EC_ADLAN_A150.$old_string."</td>
	</tr>
	</table>
	<div class='buttons-bar center'>
		".$frm->admin_button('confirmdeleteold', LAN_UI_DELETE_LABEL, 'delete')."
	</div>
	</form>
	</div>";

	$ns->tablerender(LAN_UI_DELETE_LABEL, $mes->render() . $text); 
}



// Prompt to clear cache
if ($action == 'confcache')
{
	$text = "
	<form method='post' action='".e_SELF."?cachedel'>
	<table class='table adminform'>
	<tr>
		<td>".EC_ADLAN_A162." </td>
	</tr>
	</table>
	<div class='buttons-bar center'>
		".$frm->admin_button('confirmdelcache', LAN_UI_DELETE_LABEL, 'delete')."
	</form>";
}



// Just delete odd email subscriptions
if (isset($ec_qs[2]) && isset($ec_qs[3]) && ($action == 'subs') && ($ec_qs[2] == 'del') && is_numeric($ec_qs[3]))
{
	if ($sql->db_Delete('event_subs',"event_subid='{$ec_qs[3]}'"))
		$mes->addSuccess(LAN_DELETED.$ec_qs[3]);
	else
		$mes->addError(LAN_DELETED_FAILED.$ec_qs[3]);
}


$ns->tablerender($caption, $mes->render() . $text); 


//category
$ecal_send_email = 0;
if($action == 'cat')
{
// This uses two hidden fields, preset from the category selection menu:
//	  calendarmenu_action
//		'update' - to create or update a record (actually save the info)
//		'dothings' - create/edit/delete just triggered - $calendarmenu_do = $_POST['calendarmenu_recdel']; has action 1, 2, 3
//    calendarmenu_id - the number of the category - zero indicates a new category
// We may also have $_POST['send_email_1'] or $_POST['send_email_2'] set to generate a test email as well as doing update/save

	if (is_readable(THEME.'templates/calendar_menu/ec_mailout_template.php')) 
	{  // Has to be require
		require(THEME.'templates/calendar_menu/ec_mailout_template.php');
	}
	else 
	{
		require(e_PLUGIN.'calendar_menu/templates/ec_mailout_template.php');
	}
	$calendarmenu_db = new DB;
	$calendarmenu_action = '';
	if (isset($_POST['calendarmenu_action'])) $calendarmenu_action = $_POST['calendarmenu_action'];
	$calendarmenu_edit = FALSE;
	// * If we are updating then update or insert the record
	if ($calendarmenu_action == 'update')
	{
		$calendarmenu_id = intval($_POST['calendarmenu_id']);
		$calPars = array();
		$calPars['event_cat_name']			= $tp->toDB($_POST['event_cat_name']);
		$calPars['event_cat_description']	= $tp->toDB($_POST['event_cat_description']);
		$calPars['event_cat_icon']			= $tp->toDB($_POST['ne_new_category_icon']);
		$calPars['event_cat_class']			= intval($_POST['event_cat_class']);
		$calPars['event_cat_subs']			= intval($_POST['event_cat_subs']);
		$calPars['event_cat_force_class']	= intval($_POST['event_cat_force_class']);
		$calPars['event_cat_ahead']			= intval($_POST['event_cat_ahead']);
		$calPars['event_cat_msg1']			= $tp->toDB($_POST['event_cat_msg1']);
		$calPars['event_cat_msg2']			= $tp->toDB($_POST['event_cat_msg2']);
		$calPars['event_cat_notify']		= intval($_POST['event_cat_notify']);
		$calPars['event_cat_lastupdate']	= intval(time());
		$calPars['event_cat_addclass']		= intval($_POST['event_cat_addclass']);
		if ($calendarmenu_id == 0)
		{ 	// New record so add it
			if ($calendarmenu_db->db_Insert("event_cat", $calPars))
			{
				$mes->addSuccess(LAN_CREATED);
				$admin_log->log_event(EC_ADM_08,$calPars['event_cat_name'],'');
			}
			else
			{

				$mes->addError(LAN_CREATED_FAILED);
			} 
		}
		else
		{ 	// Update existing
			if ($calendarmenu_db->db_UpdateArray("event_cat", $calPars, 'WHERE `event_cat_id` = '.$calendarmenu_id))
			{ 	// Changes saved
				$mes->addSuccess(LAN_UPDATED);
				$admin_log->add(LAN_AL_EC_ADM_09,'ID: '.$calendarmenu_id.', '.$calPars['event_cat_name'],'');
			}
			else
			{
				$mes->addError(LAN_UPDATED_FAILED);
			} 
		}
		// Now see if we need to send a test email
	  if (isset($_POST['send_email_1'])) $ecal_send_email = 1;
	  if (isset($_POST['send_email_2'])) $ecal_send_email = 2;
	  if ($ecal_send_email != 0)
	  {
		$calendarmenu_action = 'dothings';    // This forces us back to category edit screen
		$_POST['calendarmenu_selcat'] = $calendarmenu_id;   // Record number to use
		$_POST['calendarmenu_recdel'] = '1';		// This forces re-read of the record
	  }
	} 
	
	
	// We are creating, editing or deleting a record
	if ($calendarmenu_action == 'dothings')
	{
		$calendarmenu_id = intval($_POST['calendarmenu_selcat']);
		$calendarmenu_do = intval($_POST['calendarmenu_recdel']);
		$calendarmenu_dodel = false;

		switch ($calendarmenu_do)
		{
			case '1': // Edit existing record
				// We edit the record
				$calendarmenu_db->db_Select('event_cat', '*', 'event_cat_id='.$calendarmenu_id);
				$calendarmenu_row = $calendarmenu_db->db_Fetch() ;
				extract($calendarmenu_row);
				$calendarmenu_cap1 = LAN_EDIT ." ". LAN_CATEGORY;
				$calendarmenu_edit = TRUE;
				if ($ecal_send_email != 0)
				{  // Need to send a test email
				  // First, set up a dummy event
				  $thisevent = array('event_start' => $ecal_class->time_now, 'event_end' => ($ecal_class->time_now)+3600,
									 'event_title' => 'Test event', 'event_details' => EC_ADLAN_A191,
									 'event_cat_name' => $event_cat_name, 'event_location' => EC_ADLAN_A192,
									 'event_contact' => USEREMAIL, 
									 'event_thread' => SITEURL.'dodgypage',
									 'event_id' => '6');
				
				// *************** SEND EMAIL HERE **************
				  //require_once(e_PLUGIN.'calendar_menu/calendar_shortcodes.php'); WHY???? 
				  require_once(e_HANDLER . 'mail.php');
				  switch ($ecal_send_email)
				  {
					case 1 : $cal_msg = $event_cat_msg1;
							  break;
					case 2 : $cal_msg = $event_cat_msg2;
							 break;
				  }
				  $cal_msg = $tp -> parseTemplate($cal_msg, TRUE);
				  $cal_title = $tp -> parseTemplate($calPref['eventpost_mailsubject'], TRUE);
				  $user_email = USEREMAIL;
				  $user_name  = USERNAME;
				  $send_result = sendemail($user_email, $cal_title, $cal_msg, $user_name, $calPref['eventpost_mailaddress'], $calPref['eventpost_mailfrom']); 
				  
				  if ($send_result)
				  {
				  	$mes->addSuccess(EC_ADLAN_A187);
				  }
				  else
				  {
				  	$mes->addError(EC_ADLAN_A188);
				  }
				}
				break;

			case '2': // New category
				// Create new record
				$calendarmenu_id = 0; 
				// set all fields to zero/blank
				$calendar_category_name = '';
				$calendar_category_description = '';
				$calendarmenu_cap1 = LAN_CREATE ." ". LAN_CATEGORY;
				$calendarmenu_edit = TRUE;
				$event_cat_name = '';		// Define some variables for notice removal
				$event_cat_description = '';
				$event_cat_class = e_UC_MEMBER;
				$event_cat_addclass = e_UC_ADMIN;
				$event_cat_icon = '';
				$event_cat_subs = 0;
				$event_cat_notify = 0;
				$event_cat_force_class = '';
				$event_cat_ahead = 5;
				$event_cat_msg1 = '';
				$event_cat_msg2 = '';
				break;

			case '3':		// Delete record
				if ($_POST['calendarmenu_okdel'] == '1')
				{
					if ($calendarmenu_db->select('event', 'event_id', 'event_category='.$calendarmenu_id, 'nowhere'))
					{
						$mes->addError(EC_ADLAN_A59);
					}
					else
					{
						if ($calendarmenu_db->db_Delete('event_cat', 'event_cat_id='.$calendarmenu_id))
						{
							$admin_log->log_event(EC_ADM_10,'ID: '.$calendarmenu_id,'');
							$mes->addSuccess(LAN_DELETED);
						}
						else
						{
							$mes->addError(LAN_DELETED_FAILED);
						}
					} 
				}
				else
				{
					$mes->addError(LAN_CONFIRMDEL);
				} 
				$calendarmenu_dodel = TRUE;
				$calendarmenu_edit = FALSE;
				break;
		} 

		if (!$calendarmenu_dodel)
		{
			//require_once(e_HANDLER.'file_class.php');
			$calendarmenu_text .= "
			<form id='calformupdate' method='post' action='".e_SELF."?cat'>
			<fieldset id='plugin-ecal-categories'>
			<table class='table adminform'>
			<colgroup span='2'>
				<col style='width:20%;' class='col-label' />
				<col style='width:80%;' class='col-control' />
			</colgroup>
			<tr>
				<th colspan='2' style='text-align:center'>{$calendarmenu_cap1}
					<input type='hidden' value='{$calendarmenu_id}' name='calendarmenu_id' />
					<input type='hidden' value='update' name='calendarmenu_action' />
				</th>
			</tr>
 
			<tr>
				<td style='vertical-align:top;'>".EC_ADLAN_A84;
		if ($calendarmenu_do == 1) 
		  $calendarmenu_text .= "<br /><br /><br /><input type='submit' name='send_email_1' value='".EC_ADLAN_A186."' class='btn tbox' />"; 
		$calendarmenu_text .= "</td>
				<td>  
				</td>
			</tr>
			<tr>
				<td style='vertical-align:top;'>".EC_ADLAN_A117;
		if ($calendarmenu_do == 1) 
		  $calendarmenu_text .= "<br /><br /><br /><input type='submit' name='send_email_2' value='".EC_ADLAN_A186."' class='btn tbox' />"; 
		$calendarmenu_text .= "</td>
				<td> 
				</td>
			</tr>		
			</table>
			<div class='buttons-bar center'>";
				if($calendarmenu_do == 1)
				{
					$calendarmenu_text .= $frm->admin_button('submits', LAN_UPDATE, 'update');
				}
				else
				{
					$calendarmenu_text .= $frm->admin_button('submits', LAN_CREATE, 'update');	
				}
		$calendermenu_text .= "</div>
			</fieldset>
			</form>";
		} 
	} 
	if (!$calendarmenu_edit)
	{ 
		// Get the category names to display in combo box then display actions available
		$calendarmenu2_db = new DB;
		$calendarmenu_catopt = '';
		if (!isset($calendarmenu_id)) $calendarmenu_id = -1;
		if ($calendarmenu2_db->db_Select('event_cat', 'event_cat_id,event_cat_name', ' order by event_cat_name', 'nowhere'))
		{
			while ($row = $calendarmenu2_db->db_Fetch())
			{
				$calendarmenu_catopt .= "<option value='".$row['event_cat_id']."' ".($calendarmenu_id == $row['event_cat_id'] ?" selected='selected'":"")." >".$row['event_cat_name']."</option>";
			} 
		}
		else
		{
			$calendarmenu_catopt .= "<option value=0'>".EC_ADLAN_A33."</option>";
		} 

		$calendarmenu_text .= "
		<form id='calform' method='post' action='".e_SELF."?cat'>
		
		<table class='table adminform'>
		<tr>
			<td>".EC_ADLAN_A11."<input type='hidden' value='dothings' name='calendarmenu_action' /></td>
			<td><select name='calendarmenu_selcat' class='tbox'>{$calendarmenu_catopt}</select></td>
		</tr>
		<tr>
			<td>".EC_ADLAN_A18."</td>
			<td>
				<input type='radio' name='calendarmenu_recdel' value='1' checked='checked' /> ".LAN_EDIT."<br />
			</td>
		</tr>
		</table>
		<div class='buttons-bar center'>
		".$frm->admin_button('submits', EC_ADLAN_A17, 'submit')."
		</div>
		</form>";
	}
	if(isset($calendarmenu_text))
	{
	  $ns->tablerender(EC_ADLAN_1." - ".EC_ADLAN_A19, $mes->render() . $calendarmenu_text);
	}
}
 

// ====================================================
//			MAINTENANCE OPTIONS
// ====================================================

if(($action == 'maint'))
{
	$text = "
	<form method='post' action='".e_SELF."?maint'>
	<fieldset id='plugin-ecal-maintenance'>
	<table class='table adminform'>
	<tr>
		<td style='width:40%;vertical-align:top;'>".EC_ADLAN_A142." </td>
		<td style='width:60%;vertical-align:top;'>
			<select name='eventpost_deleteoldmonths' class='tbox'>
			<option value='12' selected='selected'>12</option>
			<option value='11'>11</option>
			<option value='10'>10</option>
			<option value='9'>9</option>
			<option value='8'>8</option>
			<option value='7'>7</option>
			<option value='6'>6</option>
			<option value='5'>5</option>
			<option value='4'>4</option>
			<option value='3'>3</option>
			<option value='2'>2</option>
			<option value='1'>1</option>
			</select>
			<span class='field-help'><em>".EC_ADLAN_A143."</em></span>
		</td>
	</tr>
	</table>
	<div class='buttons-bar center'>
		".$frm->admin_button('deleteold', EC_ADLAN_A145, 'delete')."
	</div>
	</fieldset>
	</form>
	<br /><br />";
	
	$ns->tablerender(EC_ADLAN_1." - ".EC_ADLAN_A141, $mes->render() . $text);

	$text = "
	<form method='post' action='".e_SELF."?maint'>
	<fieldset id='plugin-ecal-cache'>
	<table class='table adminform'>
	<tr>
		<td colspan='2' class='smalltext'><em>".EC_ADLAN_A160."</em> </td>
	</tr>
	</table>
	<div class='buttons-bar center'>
		".$frm->admin_button('cache_clear', EC_ADLAN_A161, 'delete')."
	</fieldset>
	</form>";
	
	$ns->tablerender(EC_ADLAN_1." - ".EC_ADLAN_A159, $mes->render() . $text);

}

// ====================================================
//			SUBSCRIPTIONS OPTIONS
// ====================================================

if($action == 'subs')
{
	$mes = e107::getMessage();
	echo $mes->render() . $text;

	$from = 0;
	$amount = 20;		// Number per page - could make configurable later if required
	if (isset($ec_qs[1])) $from = intval($ec_qs[1]);

	$num_entry = $sql->db_Count("event_subs", "(*)", "");		// Just count the lot

	$qry = "SELECT es.*, u.user_id, u.user_name, u.user_class, ec.event_cat_id, ec.event_cat_name, ec.event_cat_class FROM `#event_subs` AS es 
                     LEFT JOIN `#user` AS u ON es.event_userid = u.user_id
					 LEFT JOIN `#event_cat` AS ec ON es.event_cat = ec.event_cat_id
					 ORDER BY u.user_id
					 LIMIT {$from}, {$amount} ";

	$text = "
	<form method='post' action='".e_SELF."?subs.".$from."'>
	<fieldset id='plugin-ecal-subscriptions'>
	<table class='table adminform'>
	<colgroup>
		<col style='width:10%; vertical-align:top;' />
		<col style='width:20%; vertical-align:top;' />
		<col style='width:30%; vertical-align:top;' />
		<col style='width:30%; vertical-align:top;' />
		<col style='width:10%; vertical-align:top;' />
	</colgroup>";
	
  	if (!$sql->db_Select_gen($qry))
	{
	  $text .= "<tbody><tr><td colspan='5'>".EC_ADLAN_A174."</td></tr>";
	  $num_entry = 0;
	}
	else
	{
		$text .= "<thead><tr><td>".EC_ADLAN_A175.'</td><td>'.EC_ADLAN_A176."</td>
			<td>".EC_ADLAN_A177."</td><td>".EC_ADLAN_A178.'</td><td>'.EC_ADLAN_A179.'</td></tr></thead><tbody>';
		while ($row = $sql->db_Fetch())
		{
	  // Columns - UID, User name, Category name, Action
			$problems = "";
			if (!isset($row['user_id']) || ($row['user_id'] == 0) || (!isset($row['user_name'])) || ($row['user_name'] == ""))
			  $problems = EC_ADLAN_A198;
			if (!check_class($row['event_cat_class'],$row['user_class']))
			{
			  if ($problems != "") $problems .= "<br />";
			  $problems .= EC_ADLAN_A197;
			}
			$text .= "
				<tr>
				<td>".$row['user_id']."</td>
				<td>".$row['user_name']."</td>
				<td>".$row['event_cat_name']."</td>
				<td>".$problems."</td>
				<td style='text_align:center'><a href='".e_SELF."?".$action.".".$from.".del.".$row['event_subid']."'>
				  <img src='".e_IMAGE_ABS."admin_images/delete_16.png' alt='".LAN_DELETE."' title='".LAN_DELETE."' /></a></td>
				</tr>";
		}  // End while  // TODO / FIX admin_images to ad_links constant? 

		// Next-Previous. ==========================
		if ($num_entry > $amount) 
		{
			$parms = "{$num_entry},{$amount},{$from},".e_SELF."?".$action.'.[FROM]';
			$text .= "<tr><td colspan='5' style='text-align:center'>".$tp->parseTemplate("{NEXTPREV={$parms}}".'</td></tr>');
		}
	}
	$text .= "</tbody></table></fieldset></form></div>";

	$text .= "&nbsp;&nbsp;&nbsp;".str_replace("--NUM--", $num_entry, EC_ADLAN_A182);
	
	$ns->tablerender(EC_ADLAN_1." - ".EC_ADLAN_A173, $text);
}




// ========================================================
//				MAIN OPTIONS MENU
// ========================================================
if($action == 'config')
{
	function select_day_start($val)
	{
		if ($val == 'sun') $val = 0; elseif ($val == 'mon') $val = 1;	// Legacy values
		$ret = "<select name='eventpost_weekstart' class='tbox'>\n";
		foreach (array(EC_LAN_18,EC_LAN_12,EC_LAN_13,EC_LAN_14,EC_LAN_15,EC_LAN_16,EC_LAN_17) as $k => $v)
		{
			$sel = ($val == $k) ? " selected='selected'" : '';
			$ret .= "<option value='{$k}'{$sel}>{$v}</option>\n";
		}
		$ret .= "</select>\n";
		return $ret;
	}
 

	$text = "
	<form method='post' action='".e_SELF."'>
	<fieldset id='plugin-ecal-prefs'>
	<table class='table adminform'>
	<colgroup>
		<col style='width:40%;' />
		<col style='width:60%;' />
	</colgroup>
	<tr>
		<td>".EC_ADLAN_A208." </td>
		<td>". $uc->uc_dropdown('eventpost_admin', $calPref['eventpost_admin'], 'public, nobody, member, admin, classes, main, no-excludes')."
		</td>
	</tr>
	";
$text .= "
	<tr>
		<td>".EC_ADLAN_A211." </td>
		<td>". $uc->uc_dropdown('eventpost_super', $calPref['eventpost_super'],  'public, nobody, member, admin, classes, main, no-excludes')."
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A134."<br><div class='label bg-info'>".EC_ADLAN_A137."</div></td>
		<td>
			<select name='eventpost_adminlog' class='tbox'>
			<option value='0' ".($calPref['eventpost_adminlog']=='0'?" selected='selected' ":"")." >". EC_ADLAN_A87." </option>
			<option value='1' ".($calPref['eventpost_adminlog']=='1'?" selected='selected' ":"")." >".EC_ADLAN_A135." </option>
			<option value='2' ".($calPref['eventpost_adminlog']=='2'?" selected='selected' ":"")." >".EC_ADLAN_A136." </option>
			</select>
      
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A165."<br><div class='label bg-info'>What link will be used for menu header</div></td>
		<td>
			<select name='eventpost_menulink' class='tbox'>
			<option value='0' ".($calPref['eventpost_menulink']=='0'?" selected='selected' ":"")." >".EC_ADLAN_A209." </option>
			<option value='1' ".($calPref['eventpost_menulink']=='1'?" selected='selected' ":"")." >".EC_ADLAN_A210." </option>
			<option value='2' ".($calPref['eventpost_menulink']=='2'?" selected='selected' ":"")." >".EC_ADLAN_A185." </option>
			</select>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A183."<br><div class='label bg-warning'>".EC_ADLAN_A184."</div></td>
		<td><input class='tbox' type='checkbox' name='eventpost_showmouseover' value='1' ".($calPref['eventpost_showmouseover']==1?" checked='checked' ":"")." />
	</tr>

	<tr>
		<td>".EC_ADLAN_A140."</td>
		<td><input class='tbox' type='checkbox' name='eventpost_showeventcount' value='1' ".($calPref['eventpost_showeventcount']==1?" checked='checked' ":"")." /></td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A213."<br><div class='label bg-info'>".EC_ADLAN_A22."</div></td>
		<td>
		  <input class='tbox' type='checkbox' name='eventpost_forum' value='1' ".($calPref['eventpost_forum']==1?" checked='checked' ":"")." />
		  </td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A171."<br><div class='label bg-info'>".EC_ADLAN_A172."</div></td>
		<td><input class='tbox' type='text' name='eventpost_recentshow' size='10' value='".$calPref['eventpost_recentshow']."' maxlength='5' />
		</td>
	</tr>  

	<tr>
		<td>".EC_ADLAN_A212."</td>
		<td>".select_day_start($calPref['eventpost_weekstart'])."</td>
	</tr>
	<tr>
		<td>".EC_ADLAN_A214."<br /></td>
		<td>
			<select name='eventpost_lenday' class='tbox'>
			<option value='1' ".($calPref['eventpost_lenday']=='1'?" selected='selected' ":"")." > 1 </option>
			<option value='2' ".($calPref['eventpost_lenday']=='2'?" selected='selected' ":"")." > 2 </option>
			<option value='3' ".($calPref['eventpost_lenday']=='3'?" selected='selected' ":"")." > 3 </option>
			</select>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A215."<br /></td>
		<td>
			<select name='eventpost_dateformat' class='tbox'>
			<option value='my' ".($calPref['eventpost_dateformat']=='my'?" selected='selected' ":"")." >".EC_ADLAN_A216."</option>
			<option value='ym' ".($calPref['eventpost_dateformat']=='ym'?" selected='selected' ":"")." >".EC_ADLAN_A217."</option>
			</select>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A133."<br /></td>
		<td>
			<select name='eventpost_datedisplay' class='tbox'>
			<option value='1' ".($calPref['eventpost_datedisplay']=='1'?" selected='selected' ":"")." > yyyy-mm-dd</option>
			<option value='2' ".($calPref['eventpost_datedisplay']=='2'?" selected='selected' ":"")." > dd-mm-yyyy</option>
			<option value='3' ".($calPref['eventpost_datedisplay']=='3'?" selected='selected' ":"")." > mm-dd-yyyy</option>
			<option value='4' ".($calPref['eventpost_datedisplay']=='4'?" selected='selected' ":"")." > yyyy.mm.dd</option>
			<option value='5' ".($calPref['eventpost_datedisplay']=='5'?" selected='selected' ":"")." > dd.mm.yyyy</option>
			<option value='6' ".($calPref['eventpost_datedisplay']=='6'?" selected='selected' ":"")." > mm.dd.yyyy</option>
			<option value='7' ".($calPref['eventpost_datedisplay']=='7'?" selected='selected' ":"")." > yyyy/mm/dd</option>
			<option value='8' ".($calPref['eventpost_datedisplay']=='8'?" selected='selected' ":"")." > dd/mm/yyyy</option>
			<option value='9' ".($calPref['eventpost_datedisplay']=='9'?" selected='selected' ":"")." > mm/dd/yyyy</option>
			</select>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A138."</td>
		<td><input class='tbox' type='checkbox' name='eventpost_fivemins' value='1' ".($calPref['eventpost_fivemins']==1?" checked='checked' ":"")." />&nbsp;&nbsp;<span class='field-help'><em>".EC_ADLAN_A139."</em></span>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A200."<br /></td>
		<td>
			<select name='eventpost_editmode' class='tbox'>
			<option value='0' ".($calPref['eventpost_editmode']=='0'?" selected='selected' ":"")." >".EC_ADLAN_A201."</option>
			<option value='1' ".($calPref['eventpost_editmode']=='1'?" selected='selected' ":"")." >".EC_ADLAN_A202."</option>
			<option value='2' ".($calPref['eventpost_editmode']=='2'?" selected='selected' ":"")." >".EC_ADLAN_A203."</option>
			</select>
		</td>
	</tr>

	
	<tr>
		<td>".EC_ADLAN_A122."<br />
 
		<span class='field-help'>Your time: </span>".date('r')."<br />
		<br><div class='label bg-info'>new e107 works with time different way</div></td>
		<td>
    Check: <br />
		".$ecal_class->time_string($ecal_class->cal_timedate)."<br />
    Year: ".$ecal_class->cal_date['year'].", 
    Month: ".$ecal_class->cal_date['mon'].",   Month: ".$ecal_class->cal_date['month'].",
    Day: ".$ecal_class->cal_date['mday']."<br />    
    Minutes: ".$ecal_class->cal_date['minutes'].", 
    Hours: ".$ecal_class->cal_date['hours'].",  
    Seconds: ".$ecal_class->cal_date['seconds']." 
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A123."<br />
		<span class='field-help'>".EC_ADLAN_A127."</span> </td>
		</td>
		<td>
			<select name='eventpost_timedisplay' class='tbox'>
			<option value='1' ".($calPref['eventpost_timedisplay']=='1'?" selected='selected' ":'')." > 24-hour hhmm </option>
			<option value='4' ".($calPref['eventpost_timedisplay']=='4'?" selected='selected' ":'')." > 24-hour hh:mm </option>
			<option value='2' ".($calPref['eventpost_timedisplay']=='2'?" selected='selected' ":'')." > 12-hour </option>
			<option value='3' ".($calPref['eventpost_timedisplay']=='3'?" selected='selected' ":'')." > Custom </option>
			</select>
            <input class='tbox' type='text' name='eventpost_timecustom' size='20' value='".$calPref['eventpost_timecustom']."' maxlength='30' />
        <br><div class='label bg-info'>".EC_ADLAN_A128."</div>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A166."<br />
		<span class='field-help'>".EC_ADLAN_A169."</span> 
		</td>
		<td>
			<select name='eventpost_dateevent' class='tbox'>
			<option value='1' ".($calPref['eventpost_dateevent']=='1'?" selected='selected' ":'')." > dayofweek day month yyyy </option>
			<option value='2' ".($calPref['eventpost_dateevent']=='2'?" selected='selected' ":'')." > dyofwk day mon yyyy </option>
			<option value='3' ".($calPref['eventpost_dateevent']=='3'?" selected='selected' ":'')." > dyofwk dd-mm-yy </option>
			<option value='0' ".($calPref['eventpost_dateevent']=='0'?" selected='selected' ":'')." > Custom </option>
			</select>
            <input class='tbox' type='text' name='eventpost_eventdatecustom' size='20' value='".$calPref['eventpost_eventdatecustom']."' maxlength='30' />
         <br><div class='label bg-info'>".EC_ADLAN_A168."</div></td>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A167."<br />
		<span class='field-help'>".EC_ADLAN_A170."</span>
		</td>
		<td>
			<select name='eventpost_datenext' class='tbox'>
			<option value='1' ".($calPref['eventpost_datenext']=='1'?" selected='selected' ":'')." > dd month </option>
			<option value='2' ".($calPref['eventpost_datenext']=='2'?" selected='selected' ":'')." > dd mon </option>
			<option value='3' ".($calPref['eventpost_datenext']=='3'?" selected='selected' ":'')." > month dd </option>
			<option value='4' ".($calPref['eventpost_datenext']=='4'?" selected='selected' ":'')." > mon dd </option>
			<option value='0' ".($calPref['eventpost_datenext']=='0'?" selected='selected' ":'')." > Custom </option>
			</select>
            <input class='tbox' type='text' name='eventpost_nextdatecustom' size='20' value='".$calPref['eventpost_nextdatecustom']."' maxlength='30' />
			<br /><div class='label bg-info'>".EC_ADLAN_A168."</div></td>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A193."<br /></td>
		<td>
			<select name='eventpost_printlists' class='tbox'>";
	$listOpts = array( '0' => EC_ADLAN_A194, '1' => EC_ADLAN_A195);
	if (e107::isInstalled('pdf')) { $listOpts['2'] = EC_ADLAN_A196; }
	foreach ($listOpts as $v => $t)
	{
		$s = $calPref['eventpost_printlists'] == $v ? " selected='selected'" : '';
		$text .= "<option value='{$v}'{$s}>{$t}</option>\n";
	}
	$text .= "
			</select>
		</td>
	</tr>

	<tr>
		<td>".EC_ADLAN_A95."</td>
		<td><input class='tbox' type='checkbox' name='eventpost_asubs' value='1' ".($calPref['eventpost_asubs']==1?" checked='checked' ":'')." />
    <br><div class='label bg-info'>".EC_ADLAN_A96."</div>
		</td>
	</tr>
	
	<tr>
		<td>".EC_ADLAN_A92."</td>
		<td><input class='tbox' type='text' name='eventpost_mailfrom' size='60' value='".$calPref['eventpost_mailfrom']."' maxlength='100' />
		</td>
	</tr>  

	<tr>
		<td>".EC_ADLAN_A91."</td>
		<td><input class='tbox' type='text' name='eventpost_mailsubject' size='60' value='".$calPref['eventpost_mailsubject']."' maxlength='100' />
		</td>
	</tr>  

	<tr>
		<td>".EC_ADLAN_A93."</td>
		<td><input class='tbox' type='text' name='eventpost_mailaddress' size='60' value='".$calPref['eventpost_mailaddress']."' maxlength='100' />
		</td>
	</tr>  

	<tr>
		<td>".EC_ADLAN_A114."<br /></td>
		<td>
			<select name='eventpost_emaillog' class='tbox'>
			<option value='0' ".($calPref['eventpost_emaillog']=='0'?" selected='selected' ":"")." >". EC_ADLAN_A87." </option>
			<option value='1' ".($calPref['eventpost_emaillog']=='1'?" selected='selected' ":"")." >".EC_ADLAN_A115."  </option>
			<option value='2' ".($calPref['eventpost_emaillog']=='2'?" selected='selected' ":"")." >".EC_ADLAN_A116." </option>
			</select>
		</td>
	</tr>
	</table>
	<div class='buttons-bar center'>
		".$frm->admin_button('updatesettings', LAN_UPDATE, 'update')."
	</div>
	</fieldset>
	</form>
	";
	
	$ns->tablerender(EC_ADLAN_1." - ".EC_ADLAN_A207, $text);
}


function admin_config_adminmenu()
{
		if (e_QUERY) {
			$tmp = explode(".", e_QUERY);
			$action = $tmp[0];
		}
		if (!isset($action) || ($action == ""))
		{
		  $action = "config";
		}
		$var['config']['text'] = EC_ADLAN_A10;
		$var['config']['link'] = "admin_config.php";

		$var['catlist']['text'] = EC_ADLAN_A11;
		$var['catlist']['link'] = "admin_category.php?mode=cat&action=list";

		$var['catedit']['text'] = LAN_CREATE;
		$var['catedit']['link'] = "admin_category.php?mode=cat&action=create";
			
		$var['cat']['text'] = "Test Emails";
		$var['cat']['link'] ="admin_config.php?cat";
		
		$var['forthcoming']['text'] = EC_ADLAN_A100;
		$var['forthcoming']['link'] = "admin_forthcoming.php?mode=menu&action=prefs";

		$var['maint']['text'] = EC_ADLAN_A141;
		$var['maint']['link'] ="admin_config.php?maint";
		
		$var['subs']['text'] = EC_ADLAN_A173;
		$var['subs']['link'] ="admin_config.php?subs";
		
		show_admin_menu(EC_ADLAN_A12, $action, $var);
}


require_once(e_ADMIN."footer.php");

?>