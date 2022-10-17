<?php

// Generated e107 Plugin Admin Area

require_once('../../../class2.php');
if (!getperms('P'))
{
	e107::redirect('admin');
	exit;
}

e107::lan('calendar_menu', 'admin_calendar_menu', true); 


class calendar_menu_adminArea extends e_admin_dispatcher
{

	protected $modes = array(

		'main' => array(
			'controller' => 'event_cat_ui',
			'path' => null,
			'ui' => 'event_cat_form_ui',
			'uipath' => null
		),

		'cat' => array(
			'controller' => 'event_cat_ui',
			'path' => null,
			'ui' => 'event_cat_form_ui',
			'uipath' => null
		)


	);


	protected $adminMenu = array(

		'main/config' => array('caption' => EC_ADLAN_A10, 'perm' => 'P', "uri"=> "admin_config.php"),
 
		'cat/list' => array('caption' => EC_ADLAN_A11, 'perm' => 'P'),
		'cat/create' => array('caption' => LAN_CREATE, 'perm' => 'P'),

		'main/cat' => array('caption' => "Test Emails", 'perm' => 'P', "uri" => "admin_config.php?cat"),

		'main/forthcoming' => array('caption' => EC_ADLAN_A100, 'perm' => 'P', "uri" => "admin_config.php?forthcoming"),

		'main/maint' => array('caption' => EC_ADLAN_A141, 'perm' => 'P', "uri" => "admin_config.php?maint"),

		'main/subs' => array('caption' => EC_ADLAN_A173, 'perm' => 'P', "uri" => "admin_config.php?subs"),

	);

	protected $adminMenuAliases = array(
		'main/edit' => 'main/list'
	);

	protected $menuTitle = EC_ADLAN_A12;
}
