<?php

/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
* Skin Transformer class
* 
* This class (or the child classes) do an actual transformation
*
* @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
* @version $Id$
*/
class ilSkinTransformer
{
	/**
	 * UIHookGUI object
	 * 
	 * Has to be static because calls from the XSL stylesheet can only be static.
	 * @var object
	 */
	static $gui_object;
	
	
	/**
	 * Plugin object
	 * 
	 * Has to be static because calls from the XSL stylesheet can only be static.
	 * @var object
	 */
	static $plugin_object;

	
	/**
	 * Cache for xsl processor objects
	 * 
	 * @var array	xsl file name => processor object
	 */
	static $kept_processors = array();
	
	
	/**
	 * constructor
	 * 
	 * sets the UIHookGUI gui object
	 */
	public function __construct($a_gui_obj)
	{
		self::$gui_object = $a_gui_obj;
		self::$plugin_object = self::$gui_object->getPluginObject();
	}
	

	/**
	 * Apply an xsl transformation
	 * 
	 * @param 	string		code to be transformed
	 * @param 	array		transformation definition, see transformations.xml
	 * 
	 * @return	string		transformed code
	 */
	public function transform($a_code, $a_trans)
	{
		// Return empty string if 'with' is not specified
		// (child classes may overwrite this behavoir)
		if (!$a_trans['with'])
		{
			return '';
		}
		// Return the file contents directly if 'with' is not an xsl file
		// (child classes may overwrite this behavior)
		elseif(pathinfo($a_trans['with'], PATHINFO_EXTENSION) != 'xsl')
		{
			$html = @file_get_contents(self::$gui_object->getSkinDirectory()
											.'/'.$a_trans['with']);			
			if ($html !== false)
			{
				return $html;
			}
			else
			{
				return '';
			}
		}
		
		// Get the processor with loaded XSL stylesheet
		if (!$xslt = $this->getXSLProcessor($a_trans))
		{
			// output an error message if xsl is not found
			echo "XSL not loaded: ";
			print_r($a_trans);
			return $a_code;
		}
				
		// Provide the function parameters directly
		$xslt->setParameter('', $a_trans);

		// Get the code to be transformed as a DOM object
		// Use HTML loading for fault tolerance (doesn't need to be well-formed)
		// Apply handling of utf-8 due to bugs in loadHTML()
		// Note: <html> and <body> elements will automatically be added!
		
		$dom_doc = new DOMDocument('1.0', 'UTF-8');
		if ($a_trans['utf8fix'] == 'entities')
		{
			@$dom_doc->loadHTML(mb_convert_encoding($a_code, 'HTML-ENTITIES', "UTF-8")); 
			
		}
		elseif ($a_trans['utf8fix'] == 'prefix')
		{
			@$dom_doc->loadHTML('<?xml encoding="UTF-8"?'.'>'. $a_code); 
			
		}
		else
		{
			@$dom_doc->loadHTML($a_code);
	    }
		        		
		if ($a_trans['debug'] == 'dom')
		{
			return $dom_doc->saveHTML();
		}
		
		// Process and supress warnings (e.g. due to '&' in links)
		//echo "<pre>".print_r($dom_doc,1)."</pre>";
		return $xslt->transformToXML($dom_doc);
	}
	
	
	/**
	 * Get the XSL processor for a transformation
	 * 
	 * Optionally cache the transformer, if $a_trans['keep'] == 'true'
	 * 
	 * @param 	array		transformation definition, see transformations.xml
	 * @return	object		transformer_object
	 */
	protected function getXSLProcessor($a_trans)
	{
		$xsl_file = self::$gui_object->getSkinDirectory().'/'.$a_trans['with'];
		
		if (isset(self::$kept_processors[$xsl_file]))
		{
			// take a cached processor, if exist
			return self::$kept_processors[$xsl_file];	
		}
		elseif (!$xsl_code = file_get_contents($xsl_file))
		{
			// stylesheet not found
			return null;
		}
		else
		{		
			// create a new processor
			$xsl_doc = DOMDocument::loadXML($xsl_code);	 				
			
			$xslt = new XSLTProcessor();
			$xslt->registerPhpFunctions();
			$xslt->importStyleSheet($xsl_doc);
	
			// optionally keep the processor objects for further transformations
			if ($a_trans['keep'] == 'true')
			{
				self::$kept_processors[$xsl_file] = $xslt;
			}
			return $xslt;
		}
	}
	
	
	/** 
	 * Get the skin directory
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getSkinDirectory')
	 *
	 * @return	string	skin directory (relative path)
	 */
	static function getSkinDirectory()
	{
		return (string) self::$gui_object->getSkinDirectory();
	}
	
	/** 
	 * Get an ILIAS setting
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getSetting','setting_name')
	 *
	 * @param 	string	setting name
	 * @return	string	param value
	 */
	static function getSetting($a_name)
	{
		global $ilSetting;
		
		return (string) $ilSetting->get($a_name);
	}

	/** 
	 * Get a localized text from the global language
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getTxt','lang_var')
	 *
	 * @param 	string	lang var
	 * @return	string	text value
	 */
	static function getTxt($a_name)
	{
		global $lng;
		
		return (string) $lng->txt($a_name);
	}
	
	
	/** 
	 * Get a localized text from the plugin
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getPluginTxt','lang_var')
	 *
	 * @param 	string	lang var
	 * @return	string	text value
	 */
	static function getPluginTxt($a_name)
	{
		return (string) self::$plugin_object->txt($a_name);
	}
	
	
	/**
	 * Check if a skin template is already used
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getTemplateUsage','template_id')	  
	 * 
	 * @param 	string 	template_id, e.g. Services/MainMenu/tpl.main_menu.html
	 * @return	boolean
	 */
	static function getTemplateUsage($a_tpl_id)
	{
		return self::$gui_object->isTemplateUsed($a_tpl_id);
	}

	
	/**
	 * Check if any template of a component is already used
	 * or if the component is in the caller history
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getComponentUsage','component_id')	  
	 * 
	 * @param 	string 	component_id, e.g. Services/MainMenu
	 * @return	boolean
	 */
	static function getComponentUsage($a_comp_id)
	{
		return self::$gui_object->isComponentUsed($a_comp_id);
	}


	/**
	 * Get a parameter from a url
	 *
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getUrlParameter','url', 'param_name')	  
	 *
	 * @param 	string		a url or empty (then the current url will be used)
	 * @param 	string		parameter name
	 * @return	string		parameter value
	 */
	static function getUrlParameter($a_url = '', $a_param)
	{
		if ($a_url == "")
		{
			$a_url = $_SERVER['REQUEST_URI'];
		}
		
		if (!is_int($pos = strpos($a_url, '?')))
		{
			return '';
		}
		
		parse_str(html_entity_decode(substr($a_url, $pos + 1)), $params);
		return (string) $params[$a_param];
	}

	

	/**
	 * Get the file name of a url
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::getUrlFile','url'')	  
	 *
	 * @param 	string		a url or empty (then the current url will be used)
	 * @return	string		file name
	 */
	static function getUrlFile($a_url)
	{
		if (is_int($pos = strpos($a_url, '?')))
		{
			return basename(substr($a_url, 0, $pos));
		}
		else
		{
			return basename($a_url);
		}
		
	}
	
	
	/**
	 * Add one or more parameters to a url
	 *
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::addUrlParameter','url', 'param_name', 'param_value', 'param_name', 'param_value', ...)	  
	 *
	 * @param 	string		a url
	 * @param 	string		parameter name
	 * @param	string		parameter value
	 * @return	string		url with added parameter
	 */
	static function addUrlParameter($a_url)
	{
		for ($i = 1; $i < func_num_args(); $i += 2)
		{
			if (is_int(strpos($a_url, '?')))
			{
				$a_url .= '&' . func_get_arg($i) . "=" . urlencode(func_get_arg($i+1)); 
			}
			else
			{
				$a_url .= '?' . func_get_arg($i) . "=" . urlencode(func_get_arg($i+1)); 
			}
		}
		
		return $a_url;
	}
	
	
	/**
	 * Checks if a url is supported
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::isUrlSupported','url', 'mode')	  
	 *
	 * @param 	string 		url
	 * @param 	string 		mode (optional)
	 * @return	boolean		
	 */
	static function isUrlSupported($a_url, $a_mode = '')
	{
		return self::$gui_object->isUrlSupported($a_url, $a_mode);	
	}
	
	/**
	 * Checks if a request is async
	 * 
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilSkinTransformer::isAsync')	  
	 * 
	 * @return boolean
	 */
	static function isAsync()
	{
		global $ilCtrl;
		return $ilCtrl->isAsynch();
	}
	
	/**
	 * isWinMobile
	 * FSX
	 * Browserweiche
	 */
	static function isWinMobile()
	{
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$mobile_ua = substr($agent,0,4);
		if(strpos($agent, 'windows phone os 7.5'))
			return true;
		else
			return false;
	}
	
	/**
	 * loggedIn
	 * FSX
	 * Ist User eingelogged
	 */
	static function loggedIn()
	{
		global $ilSetting;

		if(($_SESSION["AccountId"] == ANONYMOUS_USER_ID || $_SESSION["AccountId"] =="" || $_SERVER[SCRIPT_NAME]=="/logout.php"))
		{
			if($ilSetting->get('pub_section') == 1)
			{
				return '3'; // Öffentlicher Bereich
			}
			else
			{
				return '0'; // Nicht eingeloggt
			}
		}
		else
		{
			if($_GET[cmd]=="getAcceptance")
			{
				return '2'; // Eingeloggt, aber UserAgreement noch nicht ausgefüllt.
			}
			else
			{
				return '1'; // Eingeloggt
			}
		}
	}
	
	
	/**
	 * checkBaseClass
	 * FSX
	 * Prüft BaseClass
	 */
	static function checkBaseClass($bc)
	{
		if(strtolower($bc) == strtolower($_GET['baseClass']))
			return true;
		else
			return false;
	}
	
	/**
	 * getUserName
	 * FSX
	 * Gibt Benutzernamen aus
	 */
	static function getUserName()
	{
		global $ilias;
		return $ilias->account->getFullname();
	}
	
	
	/**
	 * detect_mobile
	 * FSX
	 * Browserweiche
	 */
	public function detect_mobile()
	{	
		/*
		 * http://detectmobilebrowsers.com/
		 */
		$useragent=$_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent,0,4)))
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
}

?>
