<?php

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

// Für die Einbindung der FPraktikum-Seite
require ("./Services/UIComponent/UserInterfaceHook/POIncludes/include/fPraktikum.php");

class ilPOIncludesUIHookGUI extends ilUIHookPluginGUI {
        /**
         * Modify HTML output of GUI elements. Modifications modes are:
         * - ilUIHookPluginGUI::KEEP (No modification)
         * - ilUIHookPluginGUI::REPLACE (Replace default HTML with your HTML)
         * - ilUIHookPluginGUI::APPEND (Append your HTML to the default HTML)
         * - ilUIHookPluginGUI::PREPEND (Prepend your HTML to the default HTML)
         *
         * @param string $a_comp component
         * @param string $a_part string that identifies the part of the UI that is handled
         * @param string $a_par array of parameters (depend on $a_comp and $a_part)
         *
         * @return array array with entries "mode" => modification mode, "html" => your html
         */
	function getHTML($a_comp, $a_part, $a_par = array()) {
		// will hooken in ilTemplate::show(), "UIComponent", "uihk", "template_show"
		// gemaess getHTML()-Aufruf dort:
		if($a_comp == "" && $a_part == 'template_get') {
			// $a_par = array("tpl_id" => $this->tplIdentifier, "tpl_obj" => $this, "html" => $html)
			$ret = array();

			if(0 && strpos($a_par['html'], '<PO:') === false) {
				// keine PhysikOnline-Addins auf dieser Seite. Performance sparen.
				$ret['mode'] = ilUIHookPluginGUI::KEEP;
				return $ret;
			}
			
			// Finde <PO:nobreak>blabla\nblabla\n</PO:nobreak>-Anweisungen
			$ret['html'] = preg_replace_callback('/<PO:(nobreak|html)>(.+?)<\/PO:\1>/i',
				function($m) {
					// strip all breaks
					$html = str_replace(array('<br>','<br/>'), array('',''), $m[2]);
					// strip all newlines
					$html = str_replace("\n", '', $html);
					// add escaped brakes
					return str_replace(array('<PO:br>', '<PO:br/>'), array('<br>', '<br/>'), $html);
				}, $a_par['html']);

			$t = $this; // aliasing, PHP brainfuck
			// Finde <PO:Include id="foobar" />-Anweisungen
			$ret['html'] = preg_replace_callback('/<PO:Include\s+id=(["\'])([0-9a-z_-]+)\1\s*\/?>/i',
				function($m) use ($t) {
					return $t->po_include($m[2]);
				}, $ret['html']);

			$ret['mode'] = ilUIHookPluginGUI::REPLACE;
			return $ret;
		}
	} // getHTML

	function po_include($id) {
		switch($id) {
			case "foo": return "Bar <b>Bar</b>\n\n\n<div class=\"color:red\">BAZ</div>";
			case "fpraktikum" return fPraktikumResult; 
			default:
				# gehe von maskierung aus - auf sauberen string im regex oben schon gecheckt
				$includes_path = "/home/elearning-www/public_html/elearning/local/includes/";
				$file = $includes_path . $id . ".htm";
				if(file_exists($file)) {
					if(!is_readable($file))
						return "<b style='color:red'>PO Include: File <i>$id</i> not readable!</b>";
					return file_get_contents($file);
				} else 
					return "<b style='color:Red'>PO Include: Id <i>$id</i> unbekannt!</b>";
		}
	}
}
