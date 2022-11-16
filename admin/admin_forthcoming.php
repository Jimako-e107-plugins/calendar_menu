<?php

require_once('../../../class2.php');
if (!getperms('P'))
{
	e107::redirect('admin');
	exit;
}

// e107::lan('calendar_menu',true);

require_once('../ecal_class.php');
$ecal_class = new ecal_class;

require("admin_leftmenu.php");

class calendar_menu_ui extends e_admin_ui
{

	protected $pluginTitle = 'Event Calendar';
	protected $pluginName = 'calendar_menu';
	protected $perPage = 10;
	protected $batchDelete = true;
	protected $batchExport = true;
	protected $batchCopy = true;

	protected $listOrder = ' DESC';

	protected $fields = array();

	protected $fieldpref = array();


	// protected $preftabs = array('General', 'Other' );
	protected $prefs = array(
		'eventpost_menuheading' => array(
			'title' => EC_ADLAN_A108, 'tab' => 0, 'type' => 'text', 'data' => 'str', 'help' => '',
			'writeParms' => array('default' => EC_ADLAN_A100)
		),
		'eventpost_daysforward' => array('title' => EC_ADLAN_A101, 'tab' => 0, 'type' => 'number', 'data' => 'str', 'help' => '', 'writeParms' => array()),
		'eventpost_numevents' => array('title' => EC_ADLAN_A102, 'tab' => 0, 'type' => 'number', 'data' => 'str', 'help' => '', 'writeParms' => array()),
		'eventpost_checkrecur' => array('title' => EC_ADLAN_A103, 'tab' => 0, 'type' => 'boolean', 'data' => 'str', 'help' => '', 'writeParms' => array()),
		'eventpost_fe_hideifnone' => array('title' => EC_ADLAN_A107, 'tab' => 0, 'type' => 'boolean', 'data' => 'str', 'help' => '', 'writeParms' => array()),
		'eventpost_fe_showrecent' => array('title' => EC_ADLAN_A199, 'tab' => 0, 'type' => 'boolean', 'data' => 'str', 'help' => '', 'writeParms' => array()),

		'eventpost_namelink' => array('title' => EC_ADLAN_A130, 'tab' => 0, 'type' => 'dropdown', 'data' => 'str', 'help' => '', 'writeParms' => array()),

		'eventpost_linkheader' => array('title' => EC_ADLAN_A104, 'tab' => 0, 'type' => 'boolean', 'data' => 'str', 'help' => '', 'writeParms' => array()),
		'eventpost_showcaticon' => array('title' => EC_ADLAN_A120, 'tab' => 0, 'type' => 'boolean', 'data' => 'str', 'help' => '', 'writeParms' => array()),

		'eventpost_fe_set' =>
		array(
			'title' => EC_ADLAN_A118, 'tab' => 0, 'type' => 'checkboxes',  'data' => 'safestr', 'inline' => true,
			'readParms' => array('type' => 'checkboxes'),  'data' => 'str', 'help' => '', 'writeParms' => array('multiple' => 1)
		),
	);


	public function init()
	{
		$data = e107::getDb()->retrieve("event_cat", "event_cat_id,event_cat_name", " WHERE (event_cat_name != '" . EC_DEFAULT_CATEGORY . "') order by event_cat_name", "nowhere");
		foreach ($data as $val)
		{
			$id = $val['event_cat_id'];
			$cats[$id] = $val['event_cat_name'];
		}
		$this->prefs['eventpost_namelink']['writeParms']['optArray'] = array(1 => EC_ADLAN_A131, 2 => EC_ADLAN_A132);
		$this->prefs['eventpost_fe_set']['writeParms']['optArray'] = $cats;
		$this->prefs['eventpost_fe_set']['writeParms']['multiple'] = 1;
		$this->prefs['eventpost_fe_set']['writeParms']['useKeyValues'] = 1;
	}


	// ------- Customize Create --------

	public function beforeCreate($new_data, $old_data)
	{
		return $new_data;
	}

	public function afterCreate($new_data, $old_data, $id)
	{
		// do something
	}

	public function onCreateError($new_data, $old_data)
	{
		// do something
	}


	// ------- Customize Update --------

	public function beforeUpdate($new_data, $old_data, $id)
	{
		return $new_data;
	}

	public function afterUpdate($new_data, $old_data, $id)
	{
		// do something
	}

	public function onUpdateError($new_data, $old_data, $id)
	{
		// do something
	}

	public function beforePrefsSave($new_data, $old_data)
	{
		$new_data['eventpost_fe_set'] = implode(",", $new_data['eventpost_fe_set']);

		return $new_data;
	}

	// left-panel help menu area. (replaces e_help.php used in old plugins)
	public function renderHelp()
	{
		$caption = LAN_HELP;
		$text = 'Some help text';

		return array('caption' => $caption, 'text' => $text);
	}
}



class calendar_menu_form_ui extends e_admin_form_ui
{
}


new calendar_menu_adminArea();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN . "footer.php");
exit;
