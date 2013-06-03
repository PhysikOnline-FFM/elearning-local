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
		Join
		Benötigt Codeanpassungen in ILIAS
	-->
	<!--<xsl:template match="//form[@id='form_FSXmobileRegistration']">
		<div class='mobileInfoScreen'>
			<final>
				<form method="post" rel="external" data-rel="external" data-ajax="false">
					<xsl:attribute name="action">
						<xsl:value-of select="@action" />
					</xsl:attribute>
					<xsl:copy-of select="./*" />
				</form>
			</final>
		</div>
		<xsl:copy-of select=".//div[@class='mobileAdvancedSelectionList']" />
		<xsl:copy-of select=".//div[@class='mobileContainerList']" />
	</xsl:template>
	-->

	
	<!-- 
		Join
		Benötigt Codeanpassungen in ILIAS
	-->
	<xsl:template match="//div[@id='il_center_col']/form">
		<div class='mobileInfoScreen'>
			<final>
				<form method="post" rel="external" data-rel="external" data-ajax="false">
					<xsl:attribute name="action">
						<xsl:value-of select="@action" />
					</xsl:attribute>
					<xsl:copy-of select="./*" />
				</form>
			</final>
		</div>
		<xsl:copy-of select=".//div[@class='mobileAdvancedSelectionList']" />
		<xsl:copy-of select=".//div[@class='mobileContainerList']" />
	</xsl:template>
	
	
	<!-- 
		Drop
	-->
	<xsl:template match="//form[@id='form_FSXmobileDrop']">
		<div class='mobileInfoScreen'>
			<final>
				<form method="post" rel="external" data-rel="external" data-ajax="false">
					<xsl:attribute name="action">
						<xsl:value-of select="@action" />
					</xsl:attribute>
					<xsl:copy-of select="./*" />
				</form>
			</final>
		</div>
		<xsl:copy-of select=".//div[@class='mobileAdvancedSelectionList']" />
		<xsl:copy-of select=".//div[@class='mobileContainerList']" />
	</xsl:template>
</xsl:stylesheet>