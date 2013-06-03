<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:php="http://php.net/xsl">

<xsl:output method="html" encoding="UTF-8" omit-xml-declaration="yes" />

<xsl:variable name="txtActions" select="php:function('ilSkinTransformer::getTxt', 'actions')" />

<!-- 
	Convention for stylesheets in transformation sequence:
	Copy all <final> subtrees without processing 
	Copy everything not specified and process the childs
-->
<xsl:template match="final">
	<xsl:copy-of select="." />
</xsl:template>
<xsl:template match="@*|node()">
	<xsl:copy><xsl:apply-templates select="@*|node()" /></xsl:copy>
</xsl:template>


<!-- 
	Header: include content styles 
-->
<xsl:template match="head">
<head>
	<xsl:copy-of select="title" />
	<xsl:copy-of select="meta" />
	<xsl:copy-of select="base" />
	<xsl:copy-of select="link" />
	
	<final>
	<xsl:copy-of select="link[@rel='stylesheet' and contains(@href, './data/')]" />
	<xsl:copy-of select="link[@rel='stylesheet' and contains(@href, './Services/COPage/')]" />
	</final>

	<xsl:apply-templates select="final" />
</head>
</xsl:template>


<!-- 
	Page Content with navigation
 -->
<xsl:template match="table[@class='ilc_page_frame_PageFrame']" >
<final>
	<xsl:apply-templates select=".//div[@class='ilc_page_tnav_TopNavigation']" />
	<xsl:apply-templates select=".//table[@class='ilc_page_cont_PageContainer']" />
	<!-- 
	<xsl:apply-templates select=".//div[@class='ilc_page_bnav_BottomNavigation']" />
	 -->
</final>
</xsl:template>


<!-- 
	Top/Bottom navigation 
-->
<xsl:template match="div[@class='ilc_page_tnav_TopNavigation' or @class='ilc_page_bnav_BottomNavigation']" >
<fieldset data-role="controlgroup" data-type="horizontal">
	<xsl:for-each select=".//a[@class='ilc_page_lnavlink_LeftNavigationLink']">
		<a rel="external" data-role="button" data-theme="a" data-icon="arrow-l" data-iconpos="left" href="{@href}">
			<xsl:value-of select="." />
		</a>
	</xsl:for-each>
	<xsl:for-each select=".//a[@class='ilc_page_rnavlink_RightNavigationLink']">
		<a rel="external" data-role="button" data-theme="a" data-icon="arrow-r" data-iconpos="right" href="{@href}">
			<xsl:value-of select="." />
		</a>
	</xsl:for-each>
</fieldset>
	
</xsl:template>


<!--
	Urls in general: remove fragments 
	(they don't work with jquery mobile)
 -->
<xsl:template match="@href"> 
	<xsl:attribute name="href">
		<xsl:value-of select="php:function('ilMobileSkin::trimUrl', string(.))" />
	</xsl:attribute>
</xsl:template>

</xsl:stylesheet>


