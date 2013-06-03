<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Skin Transformer class for mobile skin
 *
 * This class adds functions to be called from the mobile skin
 *
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * @version $Id$
 */
class ilMobileSkin extends ilSkinTransformer
{
	/**
	 * Specific values to be provided for the XSL scripts
	 *
	 * They are initialized in the constructor
	 * They can be used in XSL by php:function('ilMobileSkin::getValue','name')
	 * They have to be static because calls from the XSL stylesheets can only be static.
	 * Therefore all objects share the same values.
	 *
	 * @var array
	 */
	static $values = array();


	/**
	 * Constructor
	 */
	public function __construct($a_gui_obj)
	{
		parent::__construct($a_gui_obj);

		// set specific values to be accessible for the stylesheet
		self::$values = array();
		$this->theme = "default";
	}


	/**
	 * Apply a transformation
	 *
	 * Redefined from ilSkinTransformer to apply the 'redirect' transformations
	 *
	 * @param  string  code to be transformed
	 * @param  array  transformation definition, see transformations.xml
	 */
	public function transform($a_code, $a_trans)
	{
		if ($a_trans['at'] == 'Services/Utilities/redirect') {
			return self::trimUrl($a_code, true);
		}
		else {
			return parent::transform($a_code, $a_trans);
		}
	}


	/**
	 * Get a named value
	 *
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilMobileSkin::getValue','name')
	 *
	 * @param  string  value name
	 * @return string value data
	 */
	static function getValue($a_name)
	{
		return (string) self::$values[$a_name];
	}


	/**
	 * Trim a url for the use with the mobile skin
	 *
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilMobileSkin::trimUrl','url')
	 *
	 * @param  string   original url
	 * @param boolean  add the style switch to the parameters
	 * @return string  trimmed url
	 */
	static function trimUrl($a_url, $a_add_switch = false)
	{
		$params = array();

		if ($a_add_switch) {
			// add the style switching parameters
			// this allows a bookmarking of the mobile start page after redirect
			$params['skin'] = ilStyleDefinition::getCurrentSkin();
			$params['style'] = ilStyleDefinition::getCurrentStyle();
			if ($_COOKIE["ilStyleSwitching"] == 'on') {
				$params['style_switching'] = 'on';
			}
		}

		// extract a fragment and add it as a mobile_anchor GET parameter
		// (jquery mobile uses the fragment for other purposes)
		$pos = strrpos($a_url, '#');
		if ($pos !== false) {
			$params['mobile_anchor'] = substr($a_url, $pos + 1);
			$a_url = substr($a_url, 0, $pos);
		}

		// add the params to the url
		$first = (strpos($a_url, '?') === false);
		foreach ($params as $key => $value) {
			$a_url .= $first ? '?' : '&';
			$a_url .= $key . '=' . $value;
			$first = false;
		}

		return $a_url;
	}


	/**
	 * Checks if a GUI block should be expanded
	 *
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilMobileSkin::isBlockExpanded','block_id')

	 * Blocks are displayed as collapsibles.
	 * Small side blocks should be collapsed by default.
	 * They should be expanded if a navigation command
	 * within the block is invoked
	 *
	 * @param string   id if the block to check
	 * @return boolean  block is expanded
	 */
	static function isBlockExpanded($a_block_id)
	{
		$block_info = explode('_', $a_block_id);
		$block_type = $block_info[1];

		return false;

		if ($_GET['block_type'] != $block_type) {
			return false;
		}
		elseif ($_GET['cmd'] == 'showMail' or $_GET['cmd'] == 'showNews') {
			return false;
		}
		else {
			return true;
		}
	}


	/**
	 * Checks if a GUI block should be visible
	 *
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilMobileSkin::isBlockVisible','block_id')
	 *
	 * Small side blocks should be hidden if a
	 * big block is displayed
	 *
	 * @param string   id if the block to check
	 * @return boolean  block is visible
	 */
	static function isBlockVisible($a_block_id)
	{
		$block_info = explode('_', $a_block_id);
		$block_type = $block_info[1];

		if ($block_type == "pdcontent") {
			return true;
		}
		elseif ($_GET['cmd'] == 'showMail' or $_GET['cmd'] == 'showNews') {
			return false;
		}
		else {
			return true;
		}
	}


	/**
	 * Get the installation name
	 *
	 * This function can be called from the XSL stylesheet using
	 * php:function('ilMobileSkin::getInstallationName')
	 *
	 */
	static function getInstallationName()
	{
		global $ilSetting;

		if ($ilSetting->get('short_inst_name') != '') {
			return $ilSetting->get('short_inst_name');
		}
		else {
			return 'ILIAS';
		}

	}


	/**
	 * Active-Buttons
	 *
	 *
	 * php:function('ilMobileSkin::getActive')
	 *
	 */
	static function getActive($x)
	{
		//include_once("./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SkinTransformer/classes/class.ilSkinTransformer.php");

		if (!ilSkinTransformer::checkBaseClass($x))
			return "";
		else
			return "ui-btn-active ui-state-persist";
	}


	/**
	 * Beta text zurückgeben
	 * php:function('ilMobileSkin::getBetaTxt')
	 */
	static function getBetaTxt()
	{
		global $lng;
		$path = str_ireplace(basename(__FILE__), "", __FILE__);

		$xslDoc = new DOMDocument();
		$xslDoc->load($path."betainfo.xsl");
		$xmlDoc = new DOMDocument();
		$xmlDoc->load($path."betainfo.xml");
		$xsltProcessor = new XSLTProcessor();
		$xsltProcessor->importStyleSheet($xslDoc);


		return $xsltProcessor->transformToDoc($xmlDoc);
	}


	/**
	 * getThemeValue
	 * @param int $a_val
	 * @return boolean
	 */
	public function getThemeValue($a_val)
	{
		$path = str_ireplace(basename(__FILE__), "", __FILE__);
		$xml = file_get_contents($path."theme.xml");

		$return = new SimpleXMLElement($xml);

		return (string) $return->{$a_val};
	}


	/**
	 * get Theme
	 */
	static function getTheme()
	{
		$theme = self::getThemeValue('name');
		if (!$theme) {
			$theme = "default";
		}
		return $theme;
	}


	/**
	 * getBetaStatus
	 */
	static function getBetaStatus()
	{
		$beta = self::getThemeValue('usebeta');
		if ($beta == '1')
		{
			$beta = "1";
		}
		else
		{
			$beta = "0";
		}
		return $beta;
	}


	/**
	 * getJqueryVersion
	 */
	static function getJqueryVersion()
	{
		$jquery = self::getThemeValue('jquery');
		if (!$jquery) {
			$jquery = "1.0.0";
		}
		return $jquery;
	}


	/**
	 * This function is used to get current language
	 * Qualitus
	 */
	static function getCurrentLanguage()
	{

		if (isset($_SESSION)) {
			return $_SESSION['lang'];
		}else {
			return 0;
		}
	}


	/**
	 * isFullscreen
	 * @return boolean
	 */
	static function isFullscreen()
	{
		if ($_GET['full'] == 1) {
			return "1";
		}
		else {
			return "0";
		}
	}


}
