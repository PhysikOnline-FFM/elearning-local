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
	Block
 -->
<xsl:template match="div[@class='mobileBlock']"> 
	<xsl:variable name="blockId" select="h3/@block-id" />
	
	<div class="mobileBlock" id="{$blockId}_content">
		<xsl:attribute name="data-role">collapsible</xsl:attribute>

		<xsl:if test="@type='small'">
			<xsl:choose>
			<xsl:when test="php:function('ilMobileSkin::isBlockExpanded',string($blockId))" >
				<xsl:attribute name="data-collapsed">false</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="data-collapsed">true</xsl:attribute>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		
		<!-- headline -->
		<xsl:copy-of select="h3" />
		<xsl:copy-of select="div[@class='mobileBlockSubTitle']" />
	
		<!-- content -->
		<xsl:choose>
		<xsl:when test="$blockId='block_pditems_0'">
				<xsl:copy-of select="div[@class='mobileBlockTable']/*" />
		</xsl:when>
		<xsl:when test="$blockId='block_pdsysmess_0' or $blockId='block_pdmail_0'">
			<ul data-role="listview" data-split-theme="a" data-inset="true">
				<xsl:copy-of select="div[@class='mobileBlockTable']/*" />
			</ul>
		</xsl:when>
		<xsl:otherwise>
			<table class="mobileBlockTable">
				<xsl:copy-of select="div[@class='mobileBlockTable']/*" />
			</table>
		</xsl:otherwise>
		</xsl:choose>
		
		<xsl:copy-of select="div[@class='mobileBlockData']" />

		<!-- paging -->
		<xsl:if test="div[@class='mobileBlockTopNumInfo']/text()" >
			<p><xsl:copy-of select="div[@class='mobileBlockTopNumInfo']" /></p>			
			<xsl:apply-templates select="div[@class='mobileBlockTopLinks']" mode="paging" />	
		</xsl:if>

		<xsl:if test="div[@class='mobileBlockFootNumInfo']/text()" >			
			<p><xsl:copy-of select="div[@class='mobileBlockFootNumInfo']" /></p>			
			<xsl:apply-templates select="div[@class='mobileBlockFootLinks']" mode="paging" />	
		</xsl:if>

		<!-- block commands -->
		<div data-role="controlgroup" data-type="horizontal">
		<xsl:choose>
			<xsl:when test="$blockId='block_pdcontent_0'">
				<xsl:copy-of select=".//a[@class='mobileBlockHeaderCommand']" />	
			</xsl:when>

			<xsl:otherwise>			
				<!-- link to separate functions page -->
				<a data-role="button" data-theme="c" data-icon="gear" href="{$blockId}_functions">
					<xsl:value-of select="php:function('ilSkinTransformer::getTxt','settings')" />
				</a>
				<!-- close button -->
				<xsl:if test="@type!='small'">
					<xsl:copy-of select="a[@class='mobileBlockCloseButton']" />
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
		</div>

			<xsl:copy-of select="div[@class='mobileBlockDetailsInfo']" />			
			<xsl:copy-of select="div[@class='mobileBlockFootInfo']" />			

		<!-- not yet
			<xsl:copy-of select="div[@class='mobileBlockFootCommands']" />	
		 -->
	</div>
	
	<!-- put commands on a separate page -->
	<div class="mobileBlockFunctions" data-role="page" id="{$blockId}_functions">
		<div data-role="header">
			<h1><xsl:value-of select="h3" /></h1>
		</div>
		<div data-role="content">
			<xsl:copy-of select="div[@class='mobileBlockHeaderCommands']" />
			<xsl:copy-of select="div[@class='mobileBlockHeaderLinks']" />

			<xsl:if test="not(div[@class='mobileBlockTopNumInfo']/text())" >			
				<xsl:apply-templates select="div[@class='mobileBlockTopLinks']" mode="settings" />	
			</xsl:if>
			<xsl:if test="not(div[@class='mobileBlockFootNumInfo']/text())" >			
				<xsl:apply-templates select="div[@class='mobileBlockFootLinks']" mode="settings" />	
			</xsl:if>
			
			<xsl:apply-templates select="div[@class='mobileBlockDetailsLinks']" />	
		</div>
	</div>
	
</xsl:template>

<xsl:template match="div[@class='mobileBlockDetailsLinks']">
	<xsl:copy>
		<xsl:copy-of select="@*" />
		<xsl:for-each select="a">
			<a>
			<xsl:copy-of select="@*" />
			<xsl:value-of select="../@title" />
			<xsl:text>: </xsl:text>
			<xsl:value-of select="position()" />
			</a>
		</xsl:for-each>
	</xsl:copy>
</xsl:template>

<xsl:template match="div[@class='mobileBlockTopLinks' or @class='mobileBlockFootLinks']" mode="settings">
	<div data-role="controlgroup">
		<xsl:copy-of select="a" />
	</div>
</xsl:template>

<xsl:template match="div[@class='mobileBlockTopLinks' or @class='mobileBlockFootLinks']" mode="paging">
	<div data-role="controlgroup" data-type="horizontal">
		<xsl:copy-of select="a[not(@data-icon)]" />
	</div>
</xsl:template>


</xsl:stylesheet>