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


<!-- local login form -->
<xsl:template match="form[@id='localLoginForm']">
	<final>
	<form><xsl:copy-of select="@*" />
		<ul data-role="listview" data-inset="true">
			<li data-role="list-divider">
				<xsl:copy-of select=".//h3/a/text()" />	
			</li>
			<li>
				<xsl:copy-of select=".//div[@class='ilFormInfo']/text()" />	
			</li>
			<li data-role="fieldcontain">
				<xsl:copy-of select=".//label[@for='username']" />
				<xsl:copy-of select=".//input[@name='username']" />
			</li>
			<li data-role="fieldcontain">
				<xsl:copy-of select=".//label[@for='password']" />
				<xsl:copy-of select=".//input[@name='password']" />
			</li>
			<li class="ui-body">
				<button type="submit" data-theme="a"><xsl:value-of select=".//input[@class='submit']/@value" /></button>
			</li>
		</ul>
	</form>
	</final>
</xsl:template>

<!--  sso login form -->
<xsl:template match="form[@id='SSOLoginForm']">
	<final>
	<form><xsl:copy-of select="@*" />
		<ul data-role="listview" data-inset="true">
			<li data-role="list-divider">
				<xsl:copy-of select=".//h3//text()" />	
			</li>
			<li>
				<xsl:copy-of select=".//div[@class='ilFormInfo']/text()" />	
			</li>
			<li class="ui-body">
				<button type="submit" data-theme="a"><xsl:value-of select="php:function('ilSkinTransformer::getTxt','log_in')" /></button>
			</li>
		</ul>
	</form>
	</final>
</xsl:template>

</xsl:stylesheet>