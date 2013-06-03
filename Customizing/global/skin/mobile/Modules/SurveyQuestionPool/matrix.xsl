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


<!-- copy bipolar texts from first to all rows -->
<xsl:template match="div[@class='mobileSurveyMatrixBipolarStart']" >
	<xsl:copy-of select="../..//p[@class='mobileSurveyMatrixBipolarStart'][1]" />
</xsl:template>
<xsl:template match="div[@class='mobileSurveyMatrixBipolarEnd']" >
	<xsl:copy-of select="../..//p[@class='mobileSurveyMatrixBipolarEnd'][1]" />
</xsl:template>

<!-- copy header colums to labels of answers -->
<xsl:template match="div[@class='mobileSurveyMatrixAnswers']">
	<xsl:for-each select="*">
		<xsl:apply-templates select=".">
			<xsl:with-param name="mypos" select="position()" />
		</xsl:apply-templates>
	</xsl:for-each>
</xsl:template>
<xsl:template match="div[@class='mobileSurveyMatrixAnswer']">
	<xsl:param name="mypos" />
	<div>
		<label for="{label/@for}">
			<xsl:copy-of select="../../..//p[@class='mobileSurveyMatrixHeader'][$mypos]/*" /> 
		</label>
		<xsl:copy-of select="input" />
	</div>
</xsl:template>

<!-- copy label of neutral answer to all rows -->
<xsl:template match="div[@class='mobileSurveyMatrixNeutralAnswer']">
	<div>
		<label for="{label/@for}">
			<xsl:copy-of select="../../..//p[@class='mobileSurveyMatrixNeutralHeader']/*" /> 
		</label>
	</div>
	<xsl:copy-of select="input" />
</xsl:template>


<!-- suppress header row -->
<xsl:template match="div[@class='mobileSurveyMatrixHeaderRow']"> 
</xsl:template>



</xsl:stylesheet>