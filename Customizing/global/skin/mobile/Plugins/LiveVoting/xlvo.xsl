<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

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
		<xsl:copy>
			<xsl:apply-templates select="@*|node()" />
		</xsl:copy>
	</xsl:template>

	<xsl:template match="//div[@class='lvo_body']">
		<final>
			<br />
			<p>
				<xsl:copy-of select="//div[@class='lvo_header_title']" />
			</p>
			<xsl:copy-of select="//script[@id='async_isactive']" />
			<div id="lvo_mobile_vote_links" data-role="controlgroup">
				<xsl:for-each select="//div[@class='lvo_choice_container']">
					<a data-role="button" data-rel="external" rel="external" data-ajax="false">
						<xsl:variable name="unvote" select=".//div[@class='lvo_choice']/div/div/@class" />
						<xsl:attribute name="data-theme">
							<xsl:choose>
								<xsl:when test="$unvote = 'lvo_percentage glow'">a</xsl:when>
								<xsl:otherwise>c</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:attribute name="href">
							<xsl:value-of select=".//a[@class='lvo_vote_link']/@href" />
						</xsl:attribute>
						<xsl:value-of select=".//div[@class='lvo_choice_cipher']" />
					</a>
				</xsl:for-each>
			</div>
			<xsl:for-each select="//table[@class='lvo_cipher_titles']/tr/td">
				<p>
					<xsl:value-of select="." />
				</p>
			</xsl:for-each>
			<br></br>
			<br></br>
			<xsl:copy-of select="//a[@id='backtopin']" />

		</final>
	</xsl:template>

	<xsl:template match="//div[@class='lvo_pinform']">
		<final>
			<br />
			<p>
				<xsl:value-of select="//div[@class='lvo_pinform_info']" />
			</p>
			<xsl:copy-of select="link" />
			<xsl:copy-of select="form[@id='lvo_pinform_form']" />
		</final>
	</xsl:template>

</xsl:stylesheet>

