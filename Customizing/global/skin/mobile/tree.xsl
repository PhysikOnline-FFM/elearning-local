<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:php="http://php.net/xsl">

<xsl:output method="html" version="4.0" encoding="UTF-8"/>

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
	Suppress the nesting ul's
	(they are only used to count the depth)
 -->
<xsl:template match="ul[@class='mobileTreeList' or @class='mobileTreeList']"> 
	<xsl:apply-templates select="*" />
</xsl:template>


<!-- 
	Use depth for indentation of list titles and texts
-->
<xsl:template match="div[@class='mobileTreeTitle']" >
	<xsl:variable name="depth" select="count(ancestor::ul[@class='mobileTreeList'])" />
	<xsl:copy>
		<xsl:attribute name="style">
			<xsl:value-of select="concat('padding-left:', string($depth - 1), 'em')" />
		</xsl:attribute>
		<xsl:apply-templates select="node()" />
	</xsl:copy>
</xsl:template>


<!-- 
	Expand / fold function
	(select visibility as data split icon, trim url)
 -->
<xsl:template match="a[@class='mobileTreeExpand']">
	<xsl:variable name="icon" select="php:functionString('ilSkinTransformer::getUrlFile', @src)" />
	
	<xsl:if test="$icon = 'plus.gif' or $icon = 'minus.gif'">
		<xsl:copy>
			<xsl:copy-of select="@*" />
			<xsl:attribute name="href">
			<xsl:value-of select="php:function('ilMobileSkin::trimUrl', string(@href))" />
			</xsl:attribute>
			<xsl:value-of select="." />
		</xsl:copy>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>