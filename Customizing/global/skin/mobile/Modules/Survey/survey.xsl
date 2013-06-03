<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

	<xsl:output method="html" encoding="UTF-8"
		omit-xml-declaration="yes" />
		
	<!--
		Global variable 
	 -->	
	 <xsl:variable name="lang" select="php:function('ilMobileSkin::getCurrentLanguage')" />	
		

	<!-- Convention for stylesheets in transformation sequence: Copy all <final> 
		subtrees without processing Copy everything not specified and process the 
		childs -->
	<xsl:template match="final">
		<xsl:copy-of select="." />
	</xsl:template>
	<xsl:template match="@*|node()">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()" />
		</xsl:copy>
	</xsl:template>

	<!-- ### Survey output page ### -->
	<xsl:template match="div[@id='mobileSurveyOutput']">
		<final>
			<xsl:copy>
				<xsl:apply-templates select="@*|*" />
			</xsl:copy>
		</final>
	</xsl:template>

	<!-- Prev/Next navigation --> <!-- Djouf 28.06.2012 -->
	<xsl:template match="input[@id='prevButton' or @id='prevButtonBottom']">
		<input>
			<xsl:copy-of select="@*" />
			<xsl:if test="$lang = 'en'">
				<xsl:choose>
					<xsl:when test="@value='Go To Start Page'">						
						<xsl:attribute name="value">Home</xsl:attribute>
												
					</xsl:when>
					<xsl:otherwise>					
							<xsl:attribute name="value"><xsl:value-of select="php:functionString('str_replace','&lt;&lt;', '', @value)" /></xsl:attribute>																
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<xsl:if test="$lang = 'de'">
				<xsl:choose>
					<xsl:when test="@value='&lt;&lt; Zur Startseite'"> 
						<xsl:attribute name="value">Startseite</xsl:attribute>												
					</xsl:when>
					<xsl:otherwise>					
							<xsl:attribute name="value"><xsl:value-of select="php:functionString('str_replace','&lt;&lt;', '', @value)" /></xsl:attribute>																
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<xsl:if test="$lang = 'fr'">
				<xsl:choose>
					<xsl:when test="@value='&lt;&lt;&lt; revenir à la page de départ'"> 
						<xsl:attribute name="value">Départ</xsl:attribute>												
					</xsl:when>
					<xsl:otherwise>					
							<xsl:attribute name="value">Précédante</xsl:attribute>									
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</input>
	</xsl:template>
	<!--
	 next 
	 -->
	<xsl:template match="input[@id='nextButton' or @id='nextButtonBottom']">
		<input>
			<xsl:copy-of select="@*" />
			<xsl:choose>
					<xsl:when test="$lang = 'fr'">
						<xsl:attribute name="value">Suivante</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="value"><xsl:value-of select="php:functionString('str_replace','&gt;&gt;', '', @value)" /></xsl:attribute>
						
					</xsl:otherwise>
			</xsl:choose>
		</input>
	</xsl:template>	
	<!-- 
		Suspend Button 
	-->
	<xsl:template match="a[@id='suspendbtn']">
	<a>
		<xsl:copy-of select="@*" />				
		<xsl:choose>
			<xsl:when test="$lang = 'en'">						
				<xsl:text>Suspend</xsl:text>												
			</xsl:when>
			<xsl:when test="$lang = 'de'">						
				<xsl:text>Abbrechen</xsl:text>												
			</xsl:when>
			<xsl:when test="$lang = 'fr'">						
				<xsl:text>Interrompre</xsl:text>												
			</xsl:when>
			<xsl:otherwise>					
					<xsl:value-of select="." />																
			</xsl:otherwise>
		</xsl:choose>		
	</a>	
	</xsl:template>
	


</xsl:stylesheet>