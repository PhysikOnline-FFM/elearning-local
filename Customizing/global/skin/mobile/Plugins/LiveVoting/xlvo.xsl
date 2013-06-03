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

<xsl:template match="//div[@class='lvo_body']">
	<final>
		<br />
		<h1>
			<xsl:value-of select="//div[@class='lvo_header_title']" />
		</h1>
		<div data-role="controlgroup">
			<xsl:for-each select="//div[@class='lvo_choice_container']">
				<a data-role="button" data-rel="external" rel="external" data-ajax="false" >
					<xsl:variable name="unvote" select=".//div[@class='lvo_choice']/div/@class" />
					
					<xsl:attribute name="data-theme">
					<xsl:choose>
						<xsl:when test="$unvote = 'lvo_percentage glow'" >a</xsl:when>
						<xsl:otherwise></xsl:otherwise>
					</xsl:choose>
					</xsl:attribute>

					<xsl:attribute name="href">
						<xsl:value-of select=".//a[@class='lvo_vote_link']/@href" />
					</xsl:attribute>
					<xsl:value-of select=".//div[@class='lvo_choice_cipher']"/>
				</a>
			</xsl:for-each>
		</div>	
	</final>
</xsl:template>

<xsl:template match="//div[@class='lvo_pinform']">
	<final>
		<br />
		<xsl:copy-of select="link" />
		<xsl:copy-of select="//form[@id='lvo_pinform_form']" />
	</final>
</xsl:template>


</xsl:stylesheet>

