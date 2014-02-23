<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

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
		<xsl:copy>
			<xsl:apply-templates select="@*|node()" />
		</xsl:copy>
	</xsl:template>


	<xsl:template match="//div[@id='il_center_col']">
		<script type="text/javascript" src="./Services/Table/js/ServiceTable.js"></script>
		<final>

			<div data-role="content">
				<xsl:apply-templates select="//div[@class='ilTableOuter']" mode="glossary" />

			</div>
			<div data-role="content">

				<xsl:apply-templates select="//div[@class='ilTableNav']" mode="glossary" />
				<!--<xsl:copy-of select="//div[@class='ilc_text_block_Standard']"></xsl:copy-of>-->
				<xsl:copy-of select="//div[@id='ilGloContent']/div"></xsl:copy-of>

			</div>
			<div data-role="content">
				<xsl:apply-templates select="//div[@class='ilTableNav']" mode="glossaryNav" />
			</div>
		</final>
	</xsl:template>

	<xsl:template match="div[@class='ilTableNav']" mode="glossaryNav">
		<div data-role="collapsible" data-content-theme="c">
			<h3>Navigation</h3>
			<xsl:apply-templates select="//div[@class='ilToolbar']" mode="glossary" />
			<xsl:apply-templates select="//div[@class='ilTableNav']" mode="navigation" />
		</div>
	</xsl:template>

	<xsl:template match="div[@class='ilTableNav']" mode="glossary">
		<div style="text-align:left; margin-top:5px;">
			<xsl:copy-of select="div/span|div/a"></xsl:copy-of>
		</div>
	</xsl:template>

	<xsl:template match="div[@class='ilTableNav']" mode="navigation">
		<xsl:variable name="link" select="//form[not(@class)]/@action" />
		<form action="{$link}" method="post">
			<xsl:copy-of select="div/select"></xsl:copy-of>
		</form>
	</xsl:template>

	<xsl:template match="div[@class='ilTableOuter']" mode="glossary">
		<!--<div data-role="page">-->
		<ul data-role="listview" data-inset='true'>
			<li>
				Term
			</li>
			<xsl:for-each select="table/tr/td/a[not(@name)]">
				<li>
					<xsl:copy-of select="."></xsl:copy-of>
				</li>
			</xsl:for-each>
		</ul>
		<!--</div>-->
	</xsl:template>

	<xsl:template match="div[@class='ilToolbar']" mode="glossary">
		<xsl:for-each select="a">
			<span style="margin-right: 5px; text-align: center">
				<xsl:copy-of select="."></xsl:copy-of>
			</span>
		</xsl:for-each>
	</xsl:template>


</xsl:stylesheet>
