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
	Column template (for output of tpl.column.html)
-->
<xsl:template match="div[@class='mobileColumn']">
	<!-- 
		omit the surrounding divs with the block id added by ilBlockGUI 
		(these will be embedded in the collapsible)
	-->
	<xsl:copy-of select="//div[@class='mobileBlockCollapsible']" />
	<xsl:copy-of select="//div[@class='mobileBlockFunctions']" />

	<!-- omit the block management functions -->
</xsl:template>


<!-- 
	Block template (for output of tpl.block.html)
 -->
<xsl:template match="div[@class='mobileBlock']">
	<xsl:variable name="blockId" select="@id" />

	<xsl:choose>
		<!-- 
			currently only support personal desktop blocks
			(these will be further selected and sorted in desktop.xsl)
		-->
		<xsl:when test="not(contains($blockId,'block_pd'))">
			<xsl:apply-templates select="." mode="content" />
		</xsl:when>

		<!-- 
			hide small blocks when a detailed view is shown in a big block
		 -->
		<xsl:when test="not(php:function('ilMobileSkin::isBlockVisible',string($blockId)))">
		</xsl:when>
	
		<!-- 
			async mode: 
			output the content replacement 
			copy the script to apply the jquery mobile processing
		-->
		<xsl:when test="php:function('ilSkinTransformer::isAsync')">
			<xsl:apply-templates select="." mode="content" />
			<xsl:copy-of select="script" />
		</xsl:when>
		
		<!-- sync mode: 
			embed the block in a collapsible 
			put <apply> around contents to allow a further procesing
			add a separate block functions page outside collapsible
		-->
		<xsl:otherwise>			
			<div class="mobileBlockCollapsible"  data-collapsed="false" data-role="collapsible" data-content-theme="c" id="{$blockId}_collapsible"><!--  -->
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

			<apply>	
				<xsl:if test="$blockId='block_pdcontent_0'">
					<div data-role="controlgroup" data-type="horizontal">
						<xsl:copy-of select=".//a[@class='mobileBlockHeaderCommand']" />	
					</div>
				</xsl:if>		

				<!-- close button -->
				<xsl:if test="@type!='small' and a[@class='mobileBlockCloseButton']">
					<a>
						<xsl:copy-of select="a[@class='mobileBlockCloseButton']/@*" />
						<xsl:value-of select="php:function('ilSkinTransformer::getTxt', 'close')" />
					</a>
				</xsl:if>
				
				<!-- headline -->
				<xsl:copy-of select="h3" />
			
				<!-- 
					embed here the content 	
					this div may be replaced asynchronously
				 -->
				<div id="{$blockId}">
					<xsl:apply-templates select="." mode="content" />
				</div>

				<!-- block commands -->
				<div data-role="controlgroup" data-type="horizontal">
				<xsl:choose>
					<xsl:when test="$blockId='block_pdcontent_0'">
						<xsl:copy-of select=".//a[@class='mobileBlockHeaderCommand']" />	
					</xsl:when>		
					<xsl:otherwise>			
						<!-- link to separate functions page -->
						<!--<a data-role="button" data-theme="a" data-icon="gear" href="{$blockId}_functions">
							<xsl:value-of select="php:function('ilSkinTransformer::getTxt','settings')" />
						</a>-->
					</xsl:otherwise>
				</xsl:choose>
				</div>

				<xsl:copy-of select="div[@class='mobileBlockDetailsInfo']" />			
		
				<!-- close button -->
				<xsl:if test="@type!='small'">
					<xsl:copy-of select="a[@class='mobileBlockCloseButton']" />
				</xsl:if>

				<!-- not yet
					<xsl:copy-of select="div[@class='mobileBlockFootInfo']" />			
					<xsl:copy-of select="div[@class='mobileBlockFootCommands']" />	
				 -->
			</apply>			
			</div>

			<xsl:apply-templates select="." mode="functions" />			
		</xsl:otherwise>
	</xsl:choose>
</xsl:template> 
 
<!-- 
	Block content (identical for sync and async blocks)
 --> 
<xsl:template match="div[@class='mobileBlock']" mode="content"> 
	<xsl:variable name="blockId" select="@id" />

	<div class="mobileBlockContent" id="{$blockId}_content">
	
		<xsl:copy-of select="div[@class='mobileBlockSubTitle']" />

		<!-- top paging -->
		<xsl:if test="div[@class='mobileBlockTopNumInfo']/text()" >
			<p><xsl:value-of select="div[@class='mobileBlockTopNumInfo']" /></p>			
			<xsl:apply-templates select="div[@class='mobileBlockTopLinks']" mode="paging" />	
		</xsl:if>
		
		<!-- content -->
		<xsl:choose>
		<xsl:when test="$blockId='block_pditems_0'">
				<xsl:copy-of select="div[@class='mobileBlockTable']/*" />
		</xsl:when>
		<xsl:when test="$blockId='block_pdsysmess_0' or 
						$blockId='block_pdmail_0' or 
						$blockId='block_pdbookm_0' or
						$blockId='block_pdnews_0'" >
			<ul data-role="listview" data-split-theme="c" data-inset="true">
				<xsl:copy-of select="div[@class='mobileBlockTable']/*" />
			</ul>
		</xsl:when>
		<xsl:when test="$blockId='block_pdcontent_0'">
				<xsl:copy-of select="div[@class='mobileBlockTable']/*" />		
		</xsl:when>		
		<xsl:otherwise>
			<table class="mobileBlockTable">
				<xsl:copy-of select="div[@class='mobileBlockTable']/*" />
			</table>
		</xsl:otherwise>
		</xsl:choose>
		
		<xsl:copy-of select="div[@class='mobileBlockData']" />

		<!-- bottom paging -->
		<xsl:if test="div[@class='mobileBlockFootNumInfo']/text()" >			
			<p><xsl:value-of select="div[@class='mobileBlockFootNumInfo']" /></p>			
			<xsl:apply-templates select="div[@class='mobileBlockFootLinks']" mode="paging" />	
		</xsl:if>
				
	</div>
</xsl:template>


<!-- 
	Block functions (separate mobile page)
 --> 
<xsl:template match="div[@class='mobileBlock']" mode="functions"> 
	<xsl:variable name="blockId" select="@id" />
	
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

<!-- details selector: shorter naming -->
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

<!-- top/foot links: settings mode -->
<xsl:template match="div[@class='mobileBlockTopLinks' or @class='mobileBlockFootLinks']" mode="settings">
	<xsl:if test="a[@href != '']">
		<div data-role="controlgroup" class="{@class}" mode="settings">
				<xsl:for-each select="a">
					<a>
						<xsl:copy-of select="@*" />
						<!-- don't provide async lading for links on settings page -->
						<xsl:attribute name="onclick"></xsl:attribute>
						<xsl:value-of select="." />
					</a>	
				</xsl:for-each>
	
		</div>
	</xsl:if>
</xsl:template>

<!-- top/foot links: paging mode -->
<xsl:template match="div[@class='mobileBlockTopLinks' or @class='mobileBlockFootLinks']" mode="paging">
	<xsl:if test="a[@href != '']">
		<div data-role="controlgroup" data-type="horizontal" class="{@class}" mode="paging">
			<xsl:copy-of select="a[not(@data-icon) and @href != '']" />
		</div>
	</xsl:if>
</xsl:template>


</xsl:stylesheet>