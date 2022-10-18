<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *	Search shim for event calendar
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/calendar_menu/e_search.php,v $
 * $Revision$
 * $Date$
 * $Author$
 */
 
/**
 *	e107 Event calendar plugin
 *
 *	Search shim for event calendar
 *
 *	@package	e107_plugins
 *	@subpackage	event_calendar
 *	@version 	$Id$;
 */

if (!defined('e107_INIT')) { exit(); }

e107::lan('calendar_menu', 'search', true);

class calendar_menu_search extends e_search // include plugin-folder in the name.
{
	function config()
	{
		$search = array(
			'name'			=> CM_SCH_LAN_1,
	 
			'table'			=> 'event',

			'advanced' 		=> array(),

			'return_fields'	=> array('event_id, event_start, event_title, event_location, event_details'),
			'search_fields'	=> array('event_title' => '1.2', 'event_location' =>'0.6', 'event_details' => '0.6'), // fields and weights.

			'order'			=>  array('event_start' => 'DESC'),
			'refpage'		=> 'calendar.php'
		);

		return $search;
	}

	/* Compile Database data for output */
	function compile($row)
	{
		$res = array();
		$con = e107::getDate();

		$res['link'] 		= 	e_PLUGIN . "calendar_menu/event.php?" . time() . ".event." . $row['event_id'];
		$res['pre_title'] 	= "";
		$res['title'] 		= $row['event_title'];
		$res['summary'] 	= $row['event_details'];
		$res['detail'] 		=
		$row['event_location'] . " | " . $con->convert_date($row['event_start'], "long");

		return $res;
	}

}



