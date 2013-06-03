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
<xsl:template match="div[@class='mobileLoginForm']">
	<final>
		<xsl:apply-templates match="*" mode="form" />
		<br /><br /><br /><br /><br />
	</final>
</xsl:template>


<xsl:template match="div[@id='mobileRegistration']">
	<final>
		<xsl:apply-templates match="*" mode="form" />
	</final>
</xsl:template>


<xsl:template match="*" mode="form">
	<xsl:copy>
		<xsl:copy-of select="@*" />
		<xsl:apply-templates match="*" mode="form" />
	</xsl:copy>
</xsl:template>


<xsl:template match="form" mode="form">
	<form>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="data-ajax">false</xsl:attribute>
		<xsl:apply-templates match="*" mode="form" />
	</form>
</xsl:template>


<xsl:template match="table" mode="form">
	<ul data-role="listview" data-theme="c" class="FSXlists2">
		<xsl:apply-templates match="*" mode="form" />
	</ul>
</xsl:template>


<xsl:template match="tr" mode="form">
	<li>
		<xsl:if test="@class='ilFormHeader'">
			<xsl:attribute name="data-role">list-divider</xsl:attribute>
		</xsl:if>
		<xsl:apply-templates match="*" mode="form" />
	</li>
</xsl:template>


<xsl:template match="td" mode="form">
	<xsl:apply-templates match="*" mode="form" />
</xsl:template>


<xsl:template match="input[@type='SUBMIT']" mode="form">
	<input>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="type">submit</xsl:attribute>
		<xsl:attribute name="data-theme">c</xsl:attribute>
		<xsl:value-of select="." />
	</input>
</xsl:template>


<xsl:template match="input[@type='text']" mode="form">
	<input>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="data-theme">c</xsl:attribute>
		<xsl:value-of select="." />
	</input>
</xsl:template>

<xsl:template match="input[@type='image']" mode="form">

</xsl:template>

<xsl:template match="input[@type='password']" mode="form">

	<input>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="data-theme">c</xsl:attribute>
		<xsl:value-of select="." />
	</input>
</xsl:template>

<xsl:template match="input[@id='usr_agreement']" mode="form">

	<input type="checkbox" name="usr_agreement" id="usr_agreement2" class="custom" value="1"/>
	<label for="usr_agreement2">I agree</label>
	
</xsl:template>




</xsl:stylesheet>