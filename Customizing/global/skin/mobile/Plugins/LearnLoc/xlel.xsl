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


<xsl:template match="//div[@id='lel_mobile']">
	<final>

		<ul data-role="listview" data-dividertheme="a">
			<li data-role="list-divider">
				Karte
			</li>
			<li>
				<xsl:copy-of select="//div[@id='lel_map']"/>
			</li>
			<li data-role="list-divider">
				Material
			</li>
			<xsl:for-each select="//a[@class='xlel_matlist']">
				<li>
					<xsl:copy-of select="."/>
				</li>
			</xsl:for-each>
			<li data-role="list-divider">
				Kommentare
			</li>
		</ul>
		
	</final>
</xsl:template>

</xsl:stylesheet>

