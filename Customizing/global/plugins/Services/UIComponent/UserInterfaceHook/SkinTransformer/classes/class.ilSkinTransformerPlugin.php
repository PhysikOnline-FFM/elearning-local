<?php

/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");

/**
* Skin Transformer plugin class
*
* @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
* @version $Id$
*
*/
class ilSkinTransformerPlugin extends ilUserInterfaceHookPlugin
{
	/**
	* Get Plugin Name. Must be same as in class name il<Name>Plugin
	* and must correspond to plugins subdirectory name.
	*
	* @return	string	Plugin Name
	*/
	final function getPluginName()
	{
		return "SkinTransformer";
	}
	
	
	/**
	 * Get UI plugin class instance
	 * 
	 * (redefined to prevent multiple instantiations)
	 */
	public function getUIClassInstance()
	{
		static $instance;
		if (!isset($instance))
		{
			$instance = parent::getUIClassInstance(); 			
		}
		return $instance;
	}
	
	
}
?>
