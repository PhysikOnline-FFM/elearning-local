<?php

/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");
require_once('class.ilSkinTransformer.php');

/**
* Skin Transformer User Interface Hook
* 
* The SkinTransformer plugin provides a single instance of this class
*
* @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
* @version $Id$
*/
class ilSkinTransformerUIHookGUI extends ilUIHookPluginGUI
{
	
	private $skin;
	private $skin_directory;
	
	/**
	* All defined transformations
	* @var 	array
	*/
	private $transformations = array();	
	
	/**
	* The templates used by ILIAS so far
	* @var		array
	*/
	private $used_templates = array();

	/**
	* The components with templates used by ILIAS so far
	* @var		array
	*/
	private $used_components = array();
	

	/**
	* Cache for transformer objects
	* @var		array		classname => object
	*/
	private $kept_transformers = array();


	/**
	* Default support status of functions
	* 
	* @var boolean
	*/
	private $support_default = true;
	
	
	/**
	* Support criteria for ILIAS functions
	* 
	* @var boolean
	*/
	private $support_criteria = array();
	
	
	/**
	* Enabling of style switching functionality
	* 
	* @var unknown_type
	*/
	private $style_switching = false;
	
	
	/**
	* Show DELOS FSX
	* 
	* @var unknown_type
	*/
	private $show_delos = true;
	

	/**
	* Main initialisation
	* 
	* called from the Services/Init/init_style hook 
	* because the current skin is now known
	*/
	public function init()
	{
		// first handle the switching which may change the current skin
		$this->handleStyleSwitching();
		
		$this->skin = ilStyleDefinition::getCurrentSkin();
		if ($this->skin == 'default')
		{
			$this->skin_directory = 'templates/' . $this->skin;
		}
		else
		{
			$this->skin_directory = 'Customizing/global/skin/' . $this->skin;
		}
		
		if (is_file($file = $this->skin_directory . '/skin_transformations.xml'))
		{
			$this->readTransformations($file);	
		}

		if (is_file($file = $this->skin_directory . '/skin_support.xml'))
		{
			$this->readSupport($file);	
		}
	}

	
	/**
	* get the actual skin directory
	* 
	* @return 	string	skin directory
	*/
	public function getSkinDirectory()
	{
		return $this->skin_directory;
	}
	
	
	/**
	* Get html for ui area
	*
	* @param	string		component name, e.g. "Services/MainMenu"
	* @param	string		part name, e.g. main_menu_list_entries
	* @param	array		specific parameters, e.g:
	* 						html	=> html code to be transformed
	* 						tpl_id	=> template name with component, e.g. /Modules/Forum/tpl.threads
	* 						tpl_obj => template object (if called from ilTemplate)
	* @return	array		e.g. array("mode" => ilUIHookPluginGUI::KEEP, "html" => "")
	*/
	public function getHTML($a_comp, $a_part, $a_par = array())
	{
		$html = $a_par['html'];
		$mode = ilUIHookPluginGUI::KEEP;		
		$hook = $a_comp . '/'. $a_part;
		$transformations = array();
		
		// decide what to do
		switch ($hook)
		{			
			case '/template_add':
			case '/template_load':
			$this->addTemplateUsage($a_par['tpl_id']);
			$transformations = $this->findTransformations($a_par['tpl_id'], 'input');
			break;
				
			case '/template_get':
			case '/template_show':
			if ($a_par['tpl_id'] == "Services/UICore/tpl.footer.html" || $a_par['tpl_id'] == "tpl.footer.html")
			{
				// add style switch to footer
				$this->addStyleSwitchCSS();
				$html = $html . $this->getStyleSwitchHTML();
				$mode = ilUIHookPluginGUI::REPLACE;
			}
			$transformations = $this->findTransformations($a_par['tpl_id'], 'output');
			break;
				
			// specific hooks added for the mobile skin	
			case 'Services/Utilities/redirect':
			case 'Services/Container/async_item_list':
			// existing hooks in 4.2	
			case 'Services/MainMenu/main_menu_list_entries':	
			case 'Services/PersonalDesktop/right_column':
			case 'Services/PersonalDesktop/left_column':
			case 'Services/Locator/left_column':
			case 'Services/Locator/main_locator':
			// anything else
			default:				
			$transformations = $this->findTransformations('', $hook);
			break;
		}
		
		// apply the transformations
		if (count($transformations))
		{
			$html = $this->applyTransformations($html, $transformations);
			$mode = ilUIHookPluginGUI::REPLACE;
		}
		
		return array('mode' => $mode, 'html' => $html);
	}

	/**
	* Modify user interface
	*
	* @param	string		component name, e.g. "Services/MainMenu"
	* @param	string		part name, e.g. main_menu_list_entries
	* @param	array		specific parameters, e.g. array("main_menu_gui" => $ilMainMenuGUI)
	* @return	unspecified
	*/
	public function modifyGUI($a_comp, $a_part, $a_par = array())
	{
		$hook = $a_comp . '/'. $a_part;

		switch ($hook)
		{
			case 'Modules/LearningModule/lm_menu_tabs':
			break;	

			case 'Services/Init/init_style':
			$this->init();
			break;	
				
			case '/sub_tabs':
			case '/tabs':
			// Don't modify tabs directly yet!
			// - similar hooks for main menu and object actions would be difficult
			// - check can be done in xsl stylesheet
			// $this->removeUnsupportedTabs($a_par['tabs'], $a_part);
			break;
				
			default:
			break;
		}
	}
	
	
	/**
	* check if a certain template is already used
	* 
	* @param 	string		template identifier
	* @return	boolean		template is used (true/false)
	*/
	public function isTemplateUsed($a_template)
	{
		if ($this->used_templates[$a_template])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	* check if a component's template is used
	* or if the component is called in the controller history
	* 
	* @param 	string 		component, e.g. Modules/Forum
	* @return	boolean		component is used (true/false)
	*/
	public function isComponentUsed($a_component)
	{
		global $ilCtrl;

		if ($this->used_components[$a_component])
		{
			return true;
		}
		
		// get the classes in controller history
		$classfiles = array();
		foreach($ilCtrl->getCallHistory() as $entry)
		{
			$classfiles[] = 'class.' . $entry['class']. '.php';
		}		
		
		// check the included files for classes and component path
		foreach (get_included_files() as $file)
		{
			if (strpos($file, $a_component) !== false
			and in_array(basename($file), $classfiles))
			{
				$this->used_components[$a_component] == true;
				return true;
			}
		}
		
		// no template of component is used 
		// and component GUI isn't in call history
		return false;
	}

	
	/**
	* checks if a url is supported
	* 
	* @param 	string 		url to be checked
	* @param 	string 		optional mode parameter
	* @return	boolean		url is supported 
	*/
	public function isUrlSupported($a_url, $a_mode = '')
	{
		// get the url script and parameters	
		if (is_int($pos = strpos($a_url, '?')))
		{
			$script = strtolower(basename(substr($a_url, 0, $pos)));
			parse_str(strtolower(html_entity_decode(substr($a_url, $pos+1))), $params); 
		}
		else
		{
			$script = strtolower(basename($a_url));
			$params = array();
		}
		
		// check all support criteria
		// support is re-defined by any matching criterion
		$support = $this->support_default;
		
		//echo "<pre>".print_r($this->support_criteria,1)."</pre>";
		
		foreach ($this->support_criteria as $criterion)
		{	
			$match = true;	

			if ($a_mode != '' and $criterion['_mode'] == '')
			{
				continue;
			}

			// check the defined attributes of the criterion
			foreach ($criterion as $name => $value)
			{
				switch ($name)
				{
					// this is boolean criterion result (allow/deny)
					case '?':
					break;						

					// this checks the call mode	
					case '_mode':
						if ($value == '')
						{
							$match = ($a_mode == '') ? $match : false;
						}
						elseif ($value != '*')	 
						{
							$match = ($value == $a_mode) ? $match : false;
						}
					break;

					// this checks the script name
					case '_script':
						if ($value != $script)	 
						{
							$match = false;
						}
					break;
						
					// this checks the object type of targets	
					case '_type':
						if ($type)
						{
							$match = ($value == $type) ? $match : false;
						}
						elseif ($ref_id = $params['ref_id'])
						{
							$type = ilObject::_lookupType(ilObject::_lookupObjId($ref_id));
							$match = ($value == $type) ? $match : false;
						}
						elseif ($target = $params['target'])
						{
							$type = substr($target, 0, strpos($target, '_'));
							$match = ($value == $type) ? $match : false;							
						}
						elseif (preg_match('/^goto_.*_([a-z]+)_.*\.html$/', $script, $parts))
						{
							$type = $parts[1];
							$match = ($value == $type) ? $match : false;
						}
						// fim: [link] studon specific perma links
						elseif (preg_match('/^([a-z]+)[0-9]+.*\.html$/', $script, $parts))
						{
							$type = $parts[1];
							$match = ($value == $type) ? $match : false;
						}
						// fim.
						else
						{
							$match = false;
						}
					break;							
					
					// this checks any other parameter	
					default:
					if ($value == '')
					{
						$match = ($params[$name] == '') ? $match : false;
					}
					elseif ($value == '?')
					{
						$match = ($params[$name] != '') ? $match : false;							
					}
					elseif ($value != '*')	 
					{
						$match = ($value == $params[$name]) ? $match : false;
					}
					break;
				}
				
				// any non-matching attribute will falsify the criterion
				if (!$match)
				{
					break;
				}
			}
			
			// set the new support status if the criterion matches
			if ($match)
			{
				$support = $criterion['?'];
			}
		}

		return ($support);
	}
	
	
	/**
	* add the usage of a specific template
	* add also the usage of the template's component
	* 
	* @param 	string			template id, e.g. Services/MainMenu/tpl.main_menu.html
	*/
	private function addTemplateUsage($a_tpl_id)
	{
		$this->used_templates[$a_tpl_id] = true;
		
		if ($pos = strrpos($a_tpl_id, '/'))
		{
			$this->used_components[substr($a_tpl_id, 0, $pos)] = true;
		}	
	}
	

	/**
	* find the applicable transformations for a certain template
	* 
	* @param 	string		template id, e.g. Services/MainMenu/tpl.main_menu.html
	* @param	sting		'input' or 'output'
	*
	* @return 	array
	*/
	private function findTransformations($a_tpl_id, $a_mode)
	{
		$found = array();		
		foreach ($this->transformations as $trans)
		{
			if ($trans['of'] == $a_tpl_id 
			and $trans['at'] == $a_mode)
			{
				if ($trans['for'] == ''				
				or $this->isTemplateUsed($trans['for'])
				or $this->isComponentUsed($trans['for']))
				{
					$found[] = $trans;
					
					// search for other transformations only if specified
					if ($trans['next'] != 'true')
					{
						break;
					}
				}
			}
		}
		
		return $found;
	}
	
	
	/**
	* apply the found transformation in definition order
	* 
	* @param	string		code to be transformed	
	* @param 	array		transformation definitions (see readTransformations)
	* 
	* @return	strimg		modified html
	*/
	private function applyTransformations($code, $a_transformations)
	{
		foreach ($a_transformations as $trans)
		{
			// optionally show the code before transformation
			if ($trans['debug'] == 'before')
			{
				echo $code;
				exit;
			}
			if(!$_GET['dev'] == 1)
			{
				if ($trans_obj = $this->getTransformerObject($trans))
				{
					$code = $trans_obj->transform($code, $trans);
				}
			}
			
			// optionally show the code after transformation
			if ($trans['debug'] == 'after')
			{
				echo $code;
				exit;
			}
		}
		return $code;
	}
	
	
	/**
	* get the transformer object for a certain transformation
	* 
	* @param 	array 	$a_transformation
	*/
	private function getTransformerObject($a_trans)
	{
		// decide which transformer class to use		
		if ($a_trans['by'])
		{
			$classname = str_replace('class.','', basename($a_trans['by'], '.php'));
			$keep = ($a_trans['keep'] == "true");
		}
		else
		{
			$classname = 'ilSkinTransformer';
			$keep = true;
		}
		
		// use an already kept object
		if (isset($this->kept_transformers[$classname]))
		{
			return $this->kept_transformers[$classname];
		}
		
		// always include the base transformation class here
		$this->plugin_object->includeClass('class.ilSkinTransformer.php');
				
		// read a specific transformation class file from the skin
		if ($classname != 'ilSkinTransformer')
		{
			require_once($this->skin_directory . '/' . $a_trans['by']);
		}

		// create the transformer instance with reference to this GUI class
		$object = new $classname($this);

		// eventually keep the object for further usage
		if ($keep)
		{
			$this->kept_transformers[$classname] = $object;
		}
		
		return $object;
	}

	
	/**
	* Read the transformation definitions from an xml file
	* 
	* The base element has to be <transformations>.
		* Each transformation is defined by a <trans> element with some attributes.
			* 
			* @param 	string	xml file
			* @see		transformations.xml	
			*/
			private function readTransformations($a_xml_file)
			{
				$xml = simplexml_load_file($a_xml_file);
				if (!is_object($xml))
				{
					return;
				}
				if ($xml->getName() != 'transformations')
				{
					return;
				}
		
				$defaults = array();
				foreach ($xml->attributes() as $attribute => $value)
				{
					$defaults[(string) $attribute] = (string) $value;
				}
		
				foreach ($xml->children() as $element)
				{
					$trans = $defaults;
					foreach ($element->attributes() as $attribute => $value)
					{
						if ((string) $value)
						{
							$trans[(string) $attribute] = (string) $value;
						}
					}			
					$this->transformations[] = $trans;
				}
			}

	
			/**
			* Read the support definitions from an xml file
			* 
			* The base element has to be <support>.
				* Each criterion is defined by a <allow> or <deny> element with some attributes.
					* 
					* @param 	string	xml file
					* @see		support.xml	
					*/
					private function readSupport($a_xml_file)
					{
						// TODO try catch

						$xml = @simplexml_load_file($a_xml_file);
						if (!is_object($xml))
						{
							return;
						}
						if ($xml->getName() != 'support')
						{
							return;
						}
						switch ((string) $xml['default'])
						{
							case 'allow':
							$this->support_default = true;
							break;
							case 'deny':
							$this->support_default = false;
							break;
						}
				
						foreach ($xml->children() as $element)
						{
							$criterion = array();
							foreach ($element->attributes() as $attribute => $value)
							{
								$criterion[strtolower($attribute)] = strtolower($value);
							}

							switch ($element->getName())
							{
								case 'allow':
								$criterion['?'] = true;
								$this->support_criteria[] = $criterion;
								break;
			
								case 'deny':
								$criterion['?'] = false;
								$this->support_criteria[] = $criterion;
								break;
							}
						}
					}
	
	
					/**
					* Remove tabs or subtabs based on url support check
					* 
					* This function works but is not used!
					* - it needs direct access to the target lists
					* - similar hooks for main menu and object actions would be difficult
					* - check can be done in xsl stylesheet
					* 
					* @param	object	tabs gui
					* @param	string	mode 'tabs' or 'subtabs'
					*/
					private function removeUnsupportedTabs($a_gui, $a_mode)
					{
						switch ($a_mode)
						{
							case 'tabs':
							if ($a_gui->back_2_target != "" and !$this->isUrlSupported($a_gui->back_2_target))
							{
								$a_gui->back_2_title = "";
								$a_gui->back_2_target = "";					
							}
							if ($a_gui->back_target != "" and !$this->isUrlSupported($a_gui->back_target))
							{
								$a_gui->back_title = "";
								$a_gui->back_target = "";					
							}

							$supported = array();
							foreach ($a_gui->target as $target)
							{
								if ($this->isUrlSupported($target['link']))
								{
									$supported[] = $target;
								}
							}
							$a_gui->target = $supported;
				
							$supported = array();
							foreach ($a_gui->non_tabbed_link as $target)
							{
								if ($this->isUrlSupported($target['link']))
								{
									$supported[] = $target;
								}
							}
							$a_gui->non_tabbed_link = $supported;

							break;
				
							case 'sub_tabs':
				
							$supported = array();
							foreach ($a_gui->sub_target as $target)
							{
								if ($this->isUrlSupported($target['link']))
								{
									$supported[] = $target;
								}
							}
							$a_gui->sub_target = $supported;
				
							break;
						}
					}
	
	
					/**
					* Handle the switching of skin and style
					* 
					* called from the main initialisation
					*/
					private function handleStyleSwitching()
					{

						// activate/deactivate the appearance of the style switch
						if (isset($_GET['style_switching']))
						{
							ilUtil::setCookie("ilStyleSwitching", $_GET['style_switching']);
							
							$_COOKIE["ilStyleSwitching"] = $_GET['style_switching'];
						}
						// FSX
						elseif($_COOKIE['ilStyleSwitching']=="")
						{
							global $ilCtrl;
							$_GET['style_switching'] = "on";
							ilUtil::setCookie("ilStyleSwitching", $_GET['style_switching']);
							if(ilSkinTransformer::detect_mobile())
							{
								$_GET['skin'] = "mobile";
								$_GET['style'] = "mobile";
							}
						}
						// END FSX
		
						if ($_COOKIE["ilStyleSwitching"] == "on")
						{
							$this->style_switching = true;
						}
						else
						{
							$this->style_switching = false;
						}

						// process a newly set skin and style
						if (isset($_GET['skin']) && isset($_GET['style']))
						{
							include_once("./Services/Style/classes/class.ilObjStyleSettings.php");
							if (ilStyleDefinition::styleExists($_GET['skin'], $_GET['style']) &&
							ilObjStyleSettings::_lookupActivatedStyle($_GET['skin'], $_GET['style']))
							{
								ilUtil::setCookie("ilSkin", $_GET['skin']);
								ilUtil::setCookie("ilStyle", $_GET['style']);
								ilStyleDefinition::setCurrentSkin($_GET['skin']);
								ilStyleDefinition::setCurrentStyle($_GET['style']);
							}
						}
						// process a previously set skin and style
						elseif (isset($_COOKIE['ilSkin']) && isset($_COOKIE['ilStyle']))
						{
							include_once("./Services/Style/classes/class.ilObjStyleSettings.php");
							if (ilStyleDefinition::styleExists($_COOKIE['ilSkin'], $_COOKIE['ilStyle']) &&
							ilObjStyleSettings::_lookupActivatedStyle($_COOKIE['ilSkin'], $_COOKIE['ilStyle']))
							{
								ilStyleDefinition::setCurrentSkin($_COOKIE['ilSkin']);
								ilStyleDefinition::setCurrentStyle($_COOKIE['ilStyle']);
							}
						}	
					}
	
		
					/** 
					* Get the style switching data
					* 
					* @return	array	arrays of switching data (link, title)
					*/
					private function getStyleSwitchData()
					{
						require_once("Services/Style/classes/class.ilObjStyleSettings.php");

						global $styleDefinition;
		
						$data = array();
						foreach ($styleDefinition->getAllTemplates() as $skin)
						{
							if ($skin['id'] == $styleDefinition->getTemplateId())
							{
								// use the already parsed definition
								$def = $styleDefinition;
							}
							else
							{
								$def = new ilStyleDefinition($skin['id']);
								$def->startParsing();
							}

							foreach ($def->getStyles() as $style)
							{
								// don't add the current skin/style
								if ($skin['id'] == ilStyleDefinition::getCurrentSkin()
								and $style['id'] == ilStyleDefinition::getCurrentStyle())
								{
									continue;
								}
								// only add existing and active skins and styles
								elseif (ilStyleDefinition::styleExists($skin['id'], $style['id']) 
								and	ilObjStyleSettings::_lookupActivatedStyle($skin['id'], $style['id']))
								{	
									// omit empty parts of the skin / style names
									// this allows a more "speaking" renaming of them for the switch buttons
									$text = $def->getTemplateName();
									$text .= ($text and $style['name']) ? ' / ' : '';
									$text .= $style['name'];
										
									$data[] = array(
									'link' => $this->getStyleSwitchLink($skin['id'], $style['id']),
									'text' => $text,
									'id'   => $style['id'] //FSX
									);
								}					
							}
						}
						return $data;
					}

	
					/**
					* Get a link to switch the skin and style
					* 
					* @param string 	target skin
					* @param string 	target style
					* 
					* @return string	switch link
					*/	
					private function getStyleSwitchLink($a_skin, $a_style)
					{		
						global $ilCtrl;
				
						// get the current url parameters
						$get = $_GET;
		
						$newscript = $script = basename($_SERVER['PHP_SELF']);
		
						// fallbacks
						if ($script == 'ilias.php' and $get['baseClass'] == '')
						{
							// show default for object if base class is not given
							if ($get['ref_id'] != '')
							{
								$newscript = 'repository.php';
								$get = array('ref_id' => $get['ref_id']);
							}
						}
						else
						{
							// take the command from ilCtrl (may come from POST)
							$get['cmd'] = $ilCtrl->getCmd();
						}
		
		
						// add the skin and style parameter
						$get['skin'] = $a_skin;
						$get['style'] = $a_style;

		
						$query = '';
						foreach($get as $key => $value)
						{
							if ($value != '' and !is_array($value))
							{
								$query = ilUtil::appendURLParameterString($query, $key . '=' . $value);
							}	
						}
		
						return str_replace($script, $newscript, $_SERVER['PHP_SELF']) . $query;
					}

	
					/**
					* Get the HTML code for a style switch
					*/
					private function getStyleSwitchHTML()
					{
						if (!$this->style_switching)
						{
							return '';
						}
						elseif (!count($switches = $this->getStyleSwitchData()))
						{
							return '';
						}
		
						$stpl = $this->plugin_object->getTemplate('tpl.styleswitch.html');		
						foreach($switches as $switch)
						{
							if ($switch['link'])
							{
								if(!$this->show_delos && $switch['id']!='delos')
								{
									$stpl->setCurrentBlock('styleswitch');
									$stpl->setVariable('STYLESWITCH_LINK', $switch['link']);
									$stpl->setVariable('STYLESWITCH_TEXT', $switch['text']);
									$stpl->parseCurrentBlock();	
								}
								elseif($this->show_delos)
								{
									$stpl->setCurrentBlock('styleswitch');
									$stpl->setVariable('STYLESWITCH_LINK', $switch['link']);
									$stpl->setVariable('STYLESWITCH_TEXT', $switch['text']);
									$stpl->parseCurrentBlock();
								}
							}
						}
						return $stpl->get();	
					}
	
	
					/**
					* Add the style switching CSS to the 
					*/
					private function addStyleSwitchCSS()
					{
						global $tpl;
		
						if ($this->style_switching)
						{
							$tpl->addCss($this->plugin_object->getStyleSheetLocation("styleswitch.css"));
						}
					}
				}
				?>
