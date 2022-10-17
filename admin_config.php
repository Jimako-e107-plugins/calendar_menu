<?php

// Generated e107 Plugin Admin Area 

require_once('../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}

// e107::lan('calendar_menu',true);


class calendar_menu_adminArea extends e_admin_dispatcher
{

	protected $modes = array(	
	
		'main'	=> array(
			'controller' 	=> 'event_cat_ui',
			'path' 			=> null,
			'ui' 			=> 'event_cat_form_ui',
			'uipath' 		=> null
		),
		

	);	
	
	
	protected $adminMenu = array(

		'main/list'			=> array('caption'=> LAN_MANAGE, 'perm' => 'P'),
		'main/create'		=> array('caption'=> LAN_CREATE, 'perm' => 'P'),

		// 'main/div0'      => array('divider'=> true),
		// 'main/custom'		=> array('caption'=> 'Custom Page', 'perm' => 'P'),
		
	);

	protected $adminMenuAliases = array(
		'main/edit'	=> 'main/list'				
	);	
	
	protected $menuTitle = 'Event Calendar';
}




				
class event_cat_ui extends e_admin_ui
{
			
		protected $pluginTitle		= 'Event Calendar';
		protected $pluginName		= 'calendar_menu';
	//	protected $eventName		= 'calendar_menu-event_cat'; // remove comment to enable event triggers in admin. 		
		protected $table			= 'event_cat';
		protected $pid				= 'event_cat_id';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
		protected $batchExport     = true;
		protected $batchCopy		= true;

	//	protected $sortField		= 'somefield_order';
	//	protected $sortParent      = 'somefield_parent';
	//	protected $treePrefix      = 'somefield_title';

	//	protected $tabs				= array('Tabl 1','Tab 2'); // Use 'tab'=>0  OR 'tab'=>1 in the $fields below to enable. 
		
	//	protected $listQry      	= "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.
	
		protected $listOrder		= 'event_cat_id DESC';
	
		protected $fields 		= array (
			'checkboxes'              => array (  'title' => '',  'type' => null,  'data' => null,  'width' => '5%',  'thclass' => 'center',  'forced' => 'value',  'class' => 'center',  'toggle' => 'e-multiselect',  'readParms' =>  array (),  'writeParms' =>  array (),),
			'event_cat_id'            => array (  'title' => LAN_ID,  'data' => 'int',  'width' => '5%',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_name'          => array (  'title' => LAN_TITLE,  'type' => 'text',  'data' => 'safestr',  'width' => 'auto',  'inline' => 'value',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_icon'          => array (  'title' => LAN_ICON,  'type' => 'icon',  'data' => 'safestr',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_class'         => array (  'title' => LAN_USERCLASS,  'type' => 'userclass',  'data' => 'int',  'width' => 'auto',  'batch' => 'value',  'filter' => 'value',  'inline' => 'value',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_subs'          => array (  'title' => 'Subs',  'type' => 'boolean',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_ahead'         => array (  'title' => 'Ahead',  'type' => 'boolean',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_msg1'          => array (  'title' => 'Msg1',  'type' => 'textarea',  'data' => 'str',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_msg2'          => array (  'title' => 'Msg2',  'type' => 'textarea',  'data' => 'str',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_notify'        => array (  'title' => 'Notify',  'type' => 'dropdown',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',  'filter' => false,  'batch' => false,),
			'event_cat_last'          => array (  'title' => 'Last',  'type' => 'boolean',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_today'         => array (  'title' => 'Today',  'type' => 'boolean',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_lastupdate'    => array (  'title' => 'Lastupdate',  'type' => 'boolean',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_addclass'      => array (  'title' => 'Addclass',  'type' => 'boolean',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_description'   => array (  'title' => LAN_DESCRIPTION,  'type' => 'textarea',  'data' => 'str',  'width' => '40%',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'event_cat_force_class'   => array (  'title' => 'rrr',  'type' => 'boolean',  'data' => 'int',  'width' => 'auto',  'help' => '',  'readParms' =>  array (),  'writeParms' =>  array (),  'class' => 'left',  'thclass' => 'left',),
			'options'                 => array (  'title' => LAN_OPTIONS,  'type' => null,  'data' => null,  'width' => '10%',  'thclass' => 'center last',  'class' => 'center last',  'forced' => 'value',  'readParms' =>  array (),  'writeParms' =>  array (),),
		);		
		
		protected $fieldpref = array('event_cat_name', 'event_cat_class');
		

	//	protected $preftabs        = array('General', 'Other' );
		protected $prefs = array(
		); 

	
		public function init()
		{
			// This code may be removed once plugin development is complete. 
			if(!e107::isInstalled('calendar_menu'))
			{
				e107::getMessage()->addWarning("This plugin is not yet installed. Saving and loading of preference or table data will fail.");
			}
			
			// Set drop-down values (if any). 
			$this->fields['event_cat_notify']['writeParms']['optArray'] = array('event_cat_notify_0','event_cat_notify_1', 'event_cat_notify_2'); // Example Drop-down array. 
	
		}

		
		// ------- Customize Create --------
		
		public function beforeCreate($new_data,$old_data)
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
			$otherField  = $this->getController()->getFieldVar('other_field_name');
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

