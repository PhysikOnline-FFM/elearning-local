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
			<!-- ev. als Buttons lösen -->
			<xsl:copy-of select="//div[@class='ilTableHeaderTitle']"></xsl:copy-of>

			<div data-role="content">
				<xsl:apply-templates select="//div[@class='ilTableOuter']" mode="mediacast" />
			</div>
			<div data-role="content">

				<xsl:apply-templates select="//div[@class='ilTableNav']" mode="mediacast" />
				<xsl:copy-of select="//div[@class='ilc_text_block_Standard']"></xsl:copy-of>

			</div>
			<div data-role="content">
				<xsl:apply-templates select="//div[@class='ilTableNav']" mode="mediacastNav" />
			</div>
		</final>
	</xsl:template>

	<xsl:template match="div[@class='ilTableNav']" mode="mediacastNav">
		<div data-role="collapsible" data-content-theme="c">
			<h3>Navigation</h3>
			<xsl:apply-templates select="//div[@class='ilTableNav']" mode="navigation" />
		</div>
	</xsl:template>

	<xsl:template match="div[@class='ilTableNav']" mode="mediacast">
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

	<xsl:template match="div[@class='ilTableOuter']" mode="mediacast">
		<ul data-role="listview" data-inset="false">
			<xsl:for-each select="table/tr[not(@class='tblheader')]">
				<xsl:variable name="id" select="position()" />
				<xsl:variable name="file" select=".//audio/@src" />
				<li>
					<a href="#page_{$id}">
						<img src=''>
							<xsl:attribute name="src">
								<xsl:value-of select="php:function('ilMobileSkin::getMp3Image', string($file))" />
							</xsl:attribute>
						</img>
						<xsl:value-of select="td/p[@class='small media_cast_title']/b" />
					</a>
				</li>
				<div data-role="page" id="page_{$id}">
					<div data-role="header" data-theme="c">
						<a href="#" data-rel="back" data-theme="a" data-iconpos="notext" data-icon="arrow-l" />
					</div>
					<div data-role="content">
						<img width='120px'>
							<xsl:attribute name="src">
								<xsl:value-of select="php:function('ilMobileSkin::getMp3Image', string($file))" />
							</xsl:attribute>
						</img>
						<br />
						<br />
						<xsl:copy-of select="td/p[@class='small media_cast_title']/b" />
						<xsl:copy-of select="td/div[@class='media_cast_properties']" />

						<xsl:copy-of select="td//audio" />

						<script type="text/javascript">
							jQuery(document).ready(function($) {
								$('audio,video').mediaelementplayer({audioWidth: 300});
								$('button').attr('data-role', 'none');
							});
						</script>


						<br />
						<br />
						<xsl:apply-templates select="td" mode="mediacastMedia" />
					</div>
					<!-- /page -->
				</div>
			</xsl:for-each>
		</ul>
	</xsl:template>

	<xsl:template match="td" mode="mediacastMedia">
		<!--<xsl:copy-of select="div" />-->
		<xsl:apply-templates select="div[@class='ilPlayerPreviewOverlayOuter']/div/audio" mode="mediacastSound" />
		<xsl:apply-templates select="div[@class='ilPlayerPreviewOverlayOuter']/div[@class='ilNoDisplay']/div/video/source" mode="mediacastVideo" />
	</xsl:template>

	<xsl:template match="audio" mode="mediacastSound">
		<a data-role='button' target="_blank">
			<xsl:attribute name="href">
				<xsl:value-of select="@src" />
			</xsl:attribute>
			<xsl:value-of select="php:function('ilSkinTransformer::getTxt','mcst_play')" />
		</a>
	</xsl:template>

	<xsl:template match="source" mode="mediacastVideo">
		<br />
		<a data-role='button' target="_blank">
			<xsl:attribute name="href">
				<xsl:value-of select="@src" />
			</xsl:attribute>
		</a>
	</xsl:template>
</xsl:stylesheet>
