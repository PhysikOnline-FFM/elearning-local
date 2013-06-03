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
	MainPageLayout
-->
<xsl:template match="//td[@class='il_CenterColumn']">
	<final>
		<div class="FSXlists">
			<ul data-role="listview">
				<li data-theme="a" data-icon="forward"  >
					<a href="#wikiMenu" class="small" data-transition="flip" data-rel="page" data-inline="true" data-iconpos="top">Wiki Menu</a>
				</li>
			</ul>
		</div>
		<xsl:apply-templates match="*" mode="wiki" />
	</final>
</xsl:template>


<xsl:template match="*" mode="wiki">
	<xsl:copy>
		<xsl:copy-of select="@*" mode="wiki"/>
		<xsl:apply-templates match="*" mode="wiki" />
	</xsl:copy>
</xsl:template>


<xsl:template match="h1[@class='ilc_heading1_Headline1']|h2|h3|h4|h5"  mode="wiki">
	<p>
		<strong>
			<xsl:value-of select="." />
		</strong>
	</p>
</xsl:template>

<xsl:template match="div[@class='ilc_text_block_Standard']"  mode="wiki">
	<p>
		<xsl:copy-of select="." />
	</p>
</xsl:template>

<xsl:template match="div[@id='block_wikisearch_0']"  mode="wiki">
	<final>
		<xsl:copy-of select="." />
	</final>
</xsl:template>


<xsl:template match="ul|span[@id='ilPageTocOff']|div[@class='ilc_page_toc_PageTOC']"  mode="wiki">
		<p>Wiki Inhaltsverzeichnis </p>
</xsl:template>


<xsl:template match="table[@class='fullwidth']"  mode="wiki">
	<ul data-role="listview" data-theme="a" class="FSXlists">
		<xsl:for-each select="tr[@class='mobileWikiLC']">
			<li>
				<a rel="external">
					<xsl:attribute name="href">
						<xsl:value-of select="td[@class='mobileWikiLCpage']/a[@class='mobileWikiLCpage']/@href" />
					</xsl:attribute>
					<xsl:value-of select="td[@class='mobileWikiLCpage']/a[@class='mobileWikiLCpage']" />
					<p class="ui-li-aside ui-li-desc">
						<xsl:value-of select="td[@class='mobileWikiLCdate']" />
					</p>
				</a>
			</li>
		</xsl:for-each>
	</ul>
</xsl:template>






<!-- 
	Suche /////////////////////////////////////////////////////////////////////////
-->
<xsl:template match="//td[@class='il_RightColumn']">
<!--	<final>-->
	<div data-role="page" id="wikiMenu">
		<ul data-role="listview" data-theme="a">
			<xsl:for-each select="div/div">
				<li>
					<xsl:apply-templates match="*" mode="wikiMenu" />
				</li>
			</xsl:for-each>
		</ul>
	
	</div>
<!--	</final>		-->
</xsl:template>


<xsl:template match="*" mode="wikiMenu">
	<xsl:copy>
		<xsl:copy-of select="@*" mode="wikiMenu"/>
		<xsl:apply-templates match="*" mode="wikiMenu" />
	</xsl:copy>
</xsl:template>

<xsl:template match="form" mode="wikiMenu">
	<form>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="data-ajax">false</xsl:attribute>
		<xsl:apply-templates match="*" mode="wikiMenu" />
	</form>
</xsl:template>

<xsl:template match="input[@type='submit']" mode="wikiMenu">
	<input>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="type">submit</xsl:attribute>
		<xsl:value-of select="." />
	</input>
</xsl:template>

<xsl:template match="input[@type='text']" mode="wikiMenu">
	<input>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="size"></xsl:attribute>
		<xsl:attribute name="style">lorem</xsl:attribute>
		<xsl:value-of select="." />
	</input>
</xsl:template>


<xsl:template match="ul[@class='ilWikiBlockListNoIndent']" mode="wikiMenu">
	<div data-role="controlgroup">
		<xsl:for-each select="li[@class='ilWikiBlockItem']//a">
			<a data-role="button" rel="external">
				<xsl:copy-of select="@*" />
				<xsl:value-of select="." />
 			</a>
		</xsl:for-each>
	</div>
</xsl:template>

<xsl:template match="div[@class='small']" mode="wikiMenu">
	<div data-role="controlgroup">
		<xsl:variable name="set" select="php:function('ilSkinTransformer::getTxt', 'settings')" />
		<xsl:variable name="cont" select="php:function('ilSkinTransformer::getTxt', 'wiki_contributors')" />
	
		<xsl:for-each select="p/a">
			
			
			<xsl:variable name="str">
				<xsl:value-of select="." />	
			</xsl:variable>

			<xsl:if test="$str != $cont and $str != $set "> <!-- Settings und Mitwirkende nicht anzeigen -->
				<a data-role="button" rel="external">
					<xsl:copy-of select="@*" />
					<xsl:value-of select="." />
 				</a>
 			</xsl:if>
		</xsl:for-each>
	</div>
</xsl:template>





		
</xsl:stylesheet>