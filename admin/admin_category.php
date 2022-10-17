<?php

// Generated e107 Plugin Admin Area

require_once('../../../class2.php');
if (!getperms('P'))
{
e107::redirect('admin');
exit;
}

// e107::lan('calendar_menu',true);

require("admin_leftmenu.php");
 
class event_cat_ui extends e_admin_ui
{

protected $pluginTitle = 'Event Calendar';
protected $pluginName = 'calendar_menu';
// protected $eventName = 'calendar_menu-event_cat'; // remove comment to enable event triggers in admin.
protected $table = 'event_cat';
protected $pid = 'event_cat_id';
protected $perPage = 10;
protected $batchDelete = true;
protected $batchExport = true;
protected $batchCopy = true;

// protected $sortField = 'somefield_order';
// protected $sortParent = 'somefield_parent';
// protected $treePrefix = 'somefield_title';

 protected $tabs = array(LAN_SETTINGS, LAN_MESSAGES, "Other Info"); // Use 'tab'=>0 OR 'tab'=>1 in the $fields below to enable.

// protected $listQry = "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.

protected $listOrder = 'event_cat_id DESC';

protected $fields = array (
'checkboxes' => array ( 'title' => '', 'type' => null, 'data' => null, 'width' => '5%', 'thclass' => 'center', 'forced' => 'value', 'class' => 'center', 'toggle' => 'e-multiselect', 'readParms' => array (), 'writeParms' => array (),),
'event_cat_id' => array ( 'title' => LAN_ID, 'data' => 'int', 'width' => '5%', 'help' => '', 'readParms' => array (), 'writeParms' => array (), 'class' => 'left', 'thclass' => 'left',),
'event_cat_name' => array ( 'title' => EC_ADLAN_A21, 'type' => 'text', 'data' => 'safestr', 'width' => 'auto', 'inline' => 'value', 'help' => '', 
'readParms' => array (), 'writeParms' => array ('size'=>"xlarge"), 'class' => 'left', 'thclass' => 'left',),
'event_cat_description' => array('title' => EC_ADLAN_A121, 'type' => 'textarea', 'data' => 'str', 'width' => '40%', 'help' => '', 
'readParms' => array(), 'writeParms' => array(), 'class' => 'left', 'thclass' => 'left',),
'event_cat_class' => array('title' => EC_ADLAN_A80, 'type' => 'userclass', 'data' => 'int', 'width' => 'auto', 'batch' => 'value', 'filter' => 'value', 'inline' => 'value', 'help' => '', 'readParms' => array(), 'writeParms' => array(), 'class' => 'left', 'thclass' => 'left',),
'event_cat_addclass' => array('title' => EC_ADLAN_A94, 'type' => 'userclass', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => array(), 'writeParms' => array(), 'class' => 'left', 'thclass' => 'left',),
'event_cat_icon' => array ( 'title' => LAN_ICON, 'type' => 'image', 'data' => 'safestr', 'width' => 'auto', 'help' => '',
'readParms' => array('legacy' => '{e_IMAGE}icons/'), 'writeParms' => 'glyphs=1', 'class' => 'left', 'thclass' => 'left',),
'event_cat_subs' => array ( 'title' => EC_ADLAN_A81, 'type' => 'boolean', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => array (), 'writeParms' => array (), 'class' => 'left', 'thclass' => 'left',),

'event_cat_notify' => array ( 'title' => EC_ADLAN_A86, 'type' => 'dropdown', 'data' => 'int', 'width' => 'auto', 'help' => '', 
'readParms' => array (), 'writeParms' => array (), 'class' => 'left', 'thclass' => 'left',),
'event_cat_force_class' => array('title' => EC_ADLAN_A82, 'type' => 'userclass', 'data' => 'int', 'width' => 'auto', 'help' => '', 
'readParms' => array(), 'writeParms' => array(), 'class' => 'left', 'thclass' => 'left',),
'event_cat_ahead' => array ( 'title' => EC_ADLAN_A83, 'type' => 'number', 'data' => 'int', 'width' => 'auto', 'help' => '', 
'readParms' => array (), 'writeParms' => array (), 'class' => 'left', 'thclass' => 'left',),

'event_cat_msg1' => array ( 'title' => EC_ADLAN_A84, 'type' => 'textarea', 'data' => 'str', 'tab'=>1, 
'width' => 'auto', 'help' => EC_ADLAN_A189, 'readParms' => array (), 'writeParms' => array (), 'class' => 'left', 'thclass' => 'left',),
'event_cat_msg2' => array ( 'title' => EC_ADLAN_A117, 'type' => 'textarea', 'data' => 'str', 'tab' => 1, 
'width' => 'auto', 'help' => EC_ADLAN_A189, 'readParms' => array (), 'writeParms' => array (), 'class' => 'left', 'thclass' => 'left',),

'event_cat_last' => array ( 'title' => 'Last cron run', 'type' => 'datestamp', 'data' => 'int', 'width' => 'auto', 'help' => '','tab' => 2,  
'readParms' => array (), 'writeParms' => array ('readonly' => 1  ), 'class' => 'left', 'thclass' => 'left',),
 
'event_cat_today' => array(
	'title' => 'Today cron run', 'type' => 'datestamp', 'data' => 'int', 'width' => 'auto', 'help' => '', 'tab' => 2,   
	'readParms' => array(), 'writeParms' => array('readonly' => 1), 'class' => 'left', 'thclass' => 'left',
),

'event_cat_lastupdate' => array ( 'title' => 'Last Updated', 'type' => 'datestamp', 'data' => 'int', 'width' => 'auto', 'help' => '','tab' => 2,   
'readParms' => array (), 'writeParms' => array ('readonly' => 1 ), 'class' => 'left', 'thclass' => 'left',),
'options' => array ( 'title' => LAN_OPTIONS, 'type' => null, 'data' => null, 'width' => '10%', 'thclass' => 'center last', 'class' => 'center last', 'forced' => 'value', 'readParms' => array (), 'writeParms' => array (),),
);

protected $fieldpref = array('event_cat_name', 'event_cat_class');


// protected $preftabs = array('General', 'Other' );
protected $prefs = array(
);


public function init()
{
		$event_cat_notify[0] = EC_ADLAN_A87;
		$event_cat_notify[1] = EC_ADLAN_A88;
		$event_cat_notify[2] = EC_ADLAN_A89;
		$event_cat_notify[3] = EC_ADLAN_A90;
		$event_cat_notify[4] = EC_ADLAN_A110;
		$event_cat_notify[5] = EC_ADLAN_A111;
 
		$this->fields['event_cat_notify']['writeParms']['optArray'] = $event_cat_notify;

}


// ------- Customize Create --------

public function beforeCreate($new_data,$old_data)
{
		$new_data['event_cat_lastupdate'] = time();
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
		$new_data['event_cat_lastupdate'] = time();
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

// left-panel help menu area. (replaces e_help.php used in old plugins)
public function renderHelp()
{
$caption = LAN_HELP;
$text = 'Some help text';

return array('caption'=>$caption,'text'=> $text);

}

/*
// optional - a custom page.
public function customPage()
{
$text = 'Hello World!';
$otherField = $this->getController()->getFieldVar('other_field_name');
return $text;

}




*/

}



class event_cat_form_ui extends e_admin_form_ui
{

}


new calendar_menu_adminArea();

require_once(e_ADMIN."auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN."footer.php");
exit;