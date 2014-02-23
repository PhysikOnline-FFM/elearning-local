<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:php="http://php.net/xsl">

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
		<xsl:copy><xsl:apply-templates select="@*|node()" /></xsl:copy>
	</xsl:template>
	
	
	<xsl:template match="div[@class='ilTabContentInner']">
		<xsl:copy>
				<!-- 
					Pre-selection of blocks to show
				-->
				<xsl:copy-of select=".//div[@id='block_pdcontent_0_collapsible']" />
            <xsl:copy-of select=".//div[@id='block_pditems_0_collapsible']" />
            <xsl:copy-of select=".//div[@id='block_pdnews_0_collapsible']" />
            <xsl:copy-of select=".//div[@id='block_pdsysmess_0_collapsible']" />

				<xsl:copy-of select=".//div[@id='block_pdmail_0_collapsible']" />
				<xsl:copy-of select=".//div[@id='block_pdcal_0_collapsible']" />
				<xsl:copy-of select=".//div[@id='block_pdnotes_0_collapsible']" />
				<xsl:copy-of select=".//div[@id='block_pdbookm_0_collapsible']" />
				<xsl:copy-of select=".//div[@id='block_pdusers_0_collapsible']" />

				<!-- function pages are directly copied by main.xsl -->
				<!--<xsl:copy-of select=".//div[@id='block_pditems_0_functions']" />-->
				<!--<xsl:copy-of select=".//div[@id='block_pdcontent_0_functions']" />-->
				<!--<xsl:copy-of select=".//div[@id='block_pdsysmess_0_functions']" />-->
				<!--<xsl:copy-of select=".//div[@id='block_pdmail_0_functions']" />-->
		 		<!--<xsl:copy-of select=".//div[@id='block_pdnews_0_functions']" />-->
		
				<!-- 
		 		<xsl:copy-of select=".//div[@id='block_pdcal_0_content']" />
				<xsl:copy-of select=".//div[@id='block_pdnotes_0_functions']" />
				<xsl:copy-of select=".//div[@id='block_pdbookm_0_functions']" />
				<xsl:copy-of select=".//div[@id='block_pdusers_0_functions']" />
				 -->
		</xsl:copy>
	</xsl:template>
	

	
</xsl:stylesheet>


