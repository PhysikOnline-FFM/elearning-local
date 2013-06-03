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
		LP View  
	-->
	<xsl:template match="//table[@class='ilTabContentOuter']" >
		<final>
			<xsl:apply-templates select="*" mode="lp" />
		</final>
		<!-- copy all advanced selection lists outside <final> for further processing by main.xsl -->
		<xsl:copy-of select=".//div[@class='mobileAdvancedSelectionList']" />
		<xsl:copy-of select=".//div[@class='mobileContainerList']" />
	</xsl:template>



	<!-- 
		Liste  
	-->
	<xsl:template match="div[@id='FSXlpViewList']" mode="lp" >
		<ul data-role="listview">
			<xsl:for-each select="//div[@class='FSXlpViewListContainer']">
				<li>
					<a rel="external">
						<xsl:attribute name="href">
							<xsl:value-of select="div[@class='FSXlpViewLinks']/a/@href" />
						</xsl:attribute>
						<xsl:copy-of select="div[@class='FSXlpViewListLi']/*" />
					</a>
				</li>
			</xsl:for-each>	
		</ul>
	</xsl:template>
	

	
</xsl:stylesheet>