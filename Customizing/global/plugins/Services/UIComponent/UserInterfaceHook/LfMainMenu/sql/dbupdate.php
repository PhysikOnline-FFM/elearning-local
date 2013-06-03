<#1>
<?php
$fields = array(
	'id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'menu_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'nr' => array (
		'type' => 'integer',
		'length' => 2,
		'notnull' => true
	),
	'target' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => true
	),
	'acc_ref_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'acc_perm' => array (
		'type' => 'text',
		'length' => 20,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_it", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_it", array("id"));
$ilDB->createSequence('ui_uihk_lfmainmenu_it');

?>
<#2>
<?php
$fields = array(
	'id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'type' => array (
		'type' => 'text',
		'length' => 10,
		'notnull' => true
	),
	'nr' => array (
		'type' => 'integer',
		'length' => 2,
		'notnull' => true
	),
	'acc_ref_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'acc_perm' => array (
		'type' => 'text',
		'length' => 20,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_mn", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_mn", array("id"));
$ilDB->createSequence('ui_uihk_lfmainmenu_mn');

?>
<#3>
<?php
$fields = array(
	'id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'type' => array (
		'type' => 'text',
		'length' => 5,
		'notnull' => true
	),
	'lang' => array (
		'type' => 'text',
		'length' => 2,
		'notnull' => true
	),
	'title' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_tl", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_tl", array("id", "type"));
$ilDB->createSequence('ui_uihk_lfmainmenu_tl');

?>
<#4>
<?php
$ilDB->modifyTableColumn("ui_uihk_lfmainmenu_tl", "title",
		array (
		'type' => 'text',
		'length' => 200,
		'notnull' => false
	));
?>
<#5>
<?php
$ilDB->modifyTableColumn("ui_uihk_lfmainmenu_it", "target",
		array (
		'type' => 'text',
		'length' => 200,
		'notnull' => false
	));

?>
<#6>
<?php

	$ilDB->dropPrimaryKey("ui_uihk_lfmainmenu_tl");
	$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_tl", array("id", "type", "lang"));

?>
<#7>
<?php
	//
?>
<#8>
<?php
	//
?>
<#9>
<?php
	//
?>
<#10>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_mn", "pmode",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#11>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "pmode",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#12>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "it_type",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#13>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "ref_id",
	array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => false,
		'default' => 0
	));
?>
<#14>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_mn", "active",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#15>
<?php
$fields = array(
	'item_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'lang' => array (
		'type' => 'text',
		'length' => 2,
		'notnull' => true
	),
	'target' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => true
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_ldt", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_ldt", array("item_id", "lang"));

?>
<#16>
<?php
$ilDB->dropTable("ui_uihk_lfmainmenu_ldt");

$fields = array(
	'item_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'lang' => array (
		'type' => 'text',
		'length' => 2,
		'notnull' => true
	),
	'target' => array (
		'type' => 'text',
		'length' => 200,
		'notnull' => false
	)
);
$ilDB->createTable("ui_uihk_lfmainmenu_ldt", $fields);
$ilDB->addPrimaryKey("ui_uihk_lfmainmenu_ldt", array("item_id", "lang"));

?>
<#17>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_mn", "append_last_visited",
	array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => true,
		'default' => 0
	));
?>
<#18>
<?php
$ilDB->addTableColumn("ui_uihk_lfmainmenu_it", "newwin",
		array (
		'type' => 'integer',
		'length' => 1,
		'default' => 0,
		'notnull' => false
	));

?>

