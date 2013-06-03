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
		Transformation of generic forms (Local and OpenId login)  
	-->
	<xsl:template match="div[@id='mobileSearch']">
		<final>
			<xsl:apply-templates select="*"  mode="news"/>
		</final>
	</xsl:template>
	

	
	
	
	<!-- 
		SearchForm  
	-->
	<xsl:template match="div[@id='mobileSearchForm']" mode="news">
		
		<!-- vars -->
		<xsl:variable name="url" 		select="form/@action"/>
		<xsl:variable name="term" 		select="//input[@id='term']/@value"/>
		<xsl:variable name="submit" 	select="//input[@type='SUBMIT']/@value"/>
		
		<form>
			<!-- Form Attributes -->
			<xsl:attribute name="action"><xsl:value-of select="$url" /></xsl:attribute>
			<xsl:attribute name="method">post</xsl:attribute>
			
			<!-- Text -->
			<!--<h3><xsl:value-of select="$submit" />:</h3>-->
			
			<!-- Input -->
			<input type="text" id="term" maxlength="200" name="term" autocomplete="off">
				<xsl:attribute name="value">
					<xsl:value-of select="$term" />
				</xsl:attribute>
			</input>
			
			<!-- Senden -->
			<input type="SUBMIT" name="cmd[performSearch]" value="Suche">
				<xsl:attribute name="value">
					<xsl:value-of select="$submit" />
				</xsl:attribute>
			</input>
			
		</form>

		
	</xsl:template>
	
	<!-- 
		SearchResult  
	-->
	
	<xsl:template match="div[@id='mobileSearchResult']" mode="news" >
		<br></br>
		<ul data-theme="c" data-role="listview" data-dividertheme="d">
			<li data-role="list-divider">
				<h3>Results</h3>
			</li>
			<xsl:for-each select="//li[@class='FSXsearch']"> <!-- div[@class='mobileContainerItem'] -->
				<xsl:variable name="url" select="div[@class='mobileContainerItem']/a/@href"/> 
				<xsl:variable name="img" select="img/@src"/> 
				<li>
					<a>
						<xsl:attribute name="href">
							<xsl:value-of select="$url" />
						</xsl:attribute>
						<img class="ui-li-icon ui-li-thumb">
							<xsl:attribute name="src">
								<xsl:value-of select="$img" />
							</xsl:attribute>
						</img>
						<h3>
							<xsl:value-of select="div[@class='mobileContainerItem']/h4" />
						</h3>
						<p>
							<xsl:copy-of select="div[@class='mobileContainerItem']/div[@class='mobileContainerItemDetails']/p/span" />
						</p>
					</a>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	
	<!-- 
		Wird offenbar benötigt :-)
	 -->
	<xsl:template match="*" mode="news" >
	</xsl:template>
	
	
</xsl:stylesheet>