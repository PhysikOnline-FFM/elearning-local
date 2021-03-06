<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:php="http://php.net/xsl">

<xsl:output method="html" encoding="UTF-8" omit-xml-declaration="yes" />

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
	info screen
	(set contents as final but keep div outside for further recognition in specific modules)
-->
<xsl:template match="div[@class='mobileInfoScreen']">
	<div class='mobileInfoScreen'>
		<final>
			<xsl:apply-templates match="*"/>
		</final>
	</div>
	<!-- copy all advanced selection lists outside <final> for further processing by main.xsl -->
	<xsl:copy-of select=".//div[@class='mobileAdvancedSelectionList']" />
</xsl:template>

<!-- 
	row in info screen
 -->
<xsl:template match="li[@class='mobilePropertyRow']">
	<xsl:choose>
	<xsl:when test=".//div[@class='mobileContainerItem']">
		<xsl:apply-templates select=".//div[@class='mobileContainerItem']" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:copy>
			<xsl:apply-templates match = "*" />
		</xsl:copy>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!--
	 material list for sessions 
-->
<xsl:template match="div[@class='mobileContainerItem']" >
	<li>
	<a>
		<xsl:copy-of select="a[@class='mobileContainerItemLink']/@*" />
		<xsl:copy-of select="img" />
		<xsl:copy-of select="h1" />
		<xsl:apply-templates select="div[@class='mobileContainerItemDetails']" />					
	</a>
	<xsl:apply-templates select=".//div[@class='mobileAdvancedSelectionList']" />
	</li>
</xsl:template>

<!-- make advanced selection lists to links -->
<xsl:template match="div[@class='mobileAdvancedSelectionList']" >
	<a data-role="button" href="{concat('#', @id)}">
		<xsl:value-of select="a" />
	</a> 
</xsl:template>



<!--  
	toolbars on info screen
-->
<xsl:template match="div[@class='ilToolbar']">
<div data-role="controlgroup">
	<xsl:for-each select=".//a">
	<xsl:if test="php:function('ilSkinTransformer::isUrlSupported', string(@href))" >
		<a data-role="button" data-theme="a" rel="external">
			<xsl:apply-templates select="./@href" />
			<xsl:value-of select="." />
		</a>
	</xsl:if>
	</xsl:for-each>
</div> 
<br />
</xsl:template>


<xsl:template match="div[@class='il_ButtonGroup']">
<fieldset data-role="controlgroup" > 
	<xsl:for-each select=".//input">
	<xsl:copy-of select="." />
	</xsl:for-each>
</fieldset>
<br />
</xsl:template>


<!-- 
	Replace paragraphs with spans 
	(these won't be shortened on small screen)
-->
<xsl:template match="p">
<span>
	<xsl:apply-templates select="* | text()" />
</span>
</xsl:template>


<!-- 
	Anchors in general: set external rel
 -->
<xsl:template match="a">
	<a rel="external">
		<xsl:apply-templates select="@* | text()" />					
	</a>
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


<!--
	Button Group for test or survey 
	@to do:differenzieren von Input-Buttons DJOUF
 -->
<xsl:template match="div[@class='il_ButtonGroup']"> 
<fieldset data-role="controlgroup" > 
	<xsl:for-each select=".//input">
		<input>
			<xsl:copy-of select="@*" />
			<xsl:attribute name="data-theme">g</xsl:attribute>
		</input>	
	</xsl:for-each>
</fieldset>
<br />
</xsl:template>


<!--
	
 -->
<xsl:template match="a[@onclick='toggleSections();']"> 
</xsl:template>



</xsl:stylesheet>

