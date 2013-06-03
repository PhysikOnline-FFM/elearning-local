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
	Tool ist nicht Mobile-tauglich
-->
<xsl:template match="//div">
	<xsl:variable name="message_nonmobile" select="php:function('ilSkinTransformer::getTxt', 'message_nonmobile')" />
	<final>
		<p>
			<br />
			Dieses Tool ist nicht in der mobilen Version vorhanden. Wechseln Sie die Ansicht mit dem Button unten.
			<!--<xsl:value-of select="$message_nonmobile" />-->
		</p>
		<div data-role="controlgroup" data-inline="true">
			<xsl:for-each select="//div[@class='iosStyleSwitch']//a">
				<a data-role="button" data-theme="c" href="{@href}" rel="external">
					<xsl:value-of select="." />
				</a>
			</xsl:for-each>
		</div>
	</final>
</xsl:template>

</xsl:stylesheet>


