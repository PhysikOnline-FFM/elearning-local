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


<!-- 
	Threads table
	(extract the info from the standard table colums)
-->
<xsl:template match="//div[@id='mobileForumThreadsTable']">
	<final>
		<ul data-role="listview"> 
			<li data-role="list-divider">
				<xsl:value-of select="//th[2]" />
			</li>
			<xsl:for-each select="..//tr[@class='mobile']">
				<li>
					<a rel="external" href="{td[2]//a/@href}">
						<p>
							<strong><xsl:value-of select="./td[2]//a" /></strong>
						</p>
						<p class="ui-li-count">
							<!-- count bubble -->
							<xsl:value-of select="td[4]/text()" />
						</p>
						<p>
							<!-- last entry -->
							<xsl:value-of select="//th[6]" />:
							<xsl:value-of select="./td[6]/div[1]" /> 
						</p>
						<p>
							<!-- unread /new -->
							<xsl:value-of select="td[4]/span[1]/text()" /><xsl:text> </xsl:text>
							<xsl:value-of select="td[4]/span[2]/text()" />
						</p>
					</a>
				</li>
			</xsl:for-each>
		</ul>
	</final>
</xsl:template>


<!-- 
	Single thread
	(don't show the thread together with posr/reply forms)
 -->
<xsl:template match="//div[@id='mobileForumPosts']">
 	<final>
 		<!--
 		<ul data-role="listview"  class="FSXlist"> 
			<li data-role="list-divider">
				<xsl:value-of select="//h1" />
			</li>
 		</ul>
 		<br />
 		-->
	 	<xsl:copy>
		 	<xsl:choose>
		 		<!-- copy the delete/censorship forms -->
			 	<xsl:when test=".//form[@id='mobileDeletePostForm' or @id='mobileCensorshipPostForm']">
					<xsl:copy-of select=".//form" />
			 	</xsl:when>

		 		<!-- use a specific template for reply/modify form -->
			 	<xsl:when test=".//div[@class='mobileForumPostForm']/form">
					<xsl:apply-templates select=".//div[@class='mobileForumPostForm']/form" mode="post-form" />
			 	</xsl:when>
			 	
		 		<!-- process a normal thread view -->
			 	<xsl:otherwise>
		 			<xsl:apply-templates />
		 		</xsl:otherwise>
		 	</xsl:choose>
	 	</xsl:copy>
	</final>
 </xsl:template>


<!-- 
	New thread
 -->
<xsl:template match="form">
 	<xsl:choose>
 		<!-- copy the delete/censorship forms -->
	 	<xsl:when test=".//input[@name='cmd[addThread]']">
			<final>
				<xsl:apply-templates select="." mode="post-form" />
			</final>
	 	</xsl:when>

	 	<xsl:otherwise>
 			<xsl:apply-templates />
 		</xsl:otherwise>
 	</xsl:choose>
</xsl:template>


<!-- 
	Post form
	(build a new form with the necessary fields)
 -->
<xsl:template match="form" mode="post-form">
	<form action="{php:function('ilMobileSkin::trimUrl', string(@action))}" method="{@method}" data-ajax="false">
		<ul data-role="listview" data-theme="c" data-inset="true"> 
			<li data-role="list-divider">
				<xsl:value-of select=".//h3[@class='ilFormHeader']" />
			</li>
			<li data-role="fieldcontain">
				<label for="subject"><xsl:value-of select=".//label[@for='subject']" /></label>
				<input type="text" id="subject" name="subject" maxlength="64" value="{.//input[@id='subject']/@value}" />
			</li>
			<li data-role="fieldcontain">
				<label for="message"><xsl:value-of select=".//label[@for='message']" /></label>
				<textarea id="message" name="message" wrap="virtual" rows="15">
					<xsl:value-of select=".//textarea[@id='message']" />
				</textarea>		
			</li>
			<!--
			<li data-role="fieldcontain">	
				<input type="checkbox" id="notify" name="notify" value="{.//input[@id='notify']/@value}" />
				<label for="notify"><xsl:value-of select=".//td[@id='il_prop_cont_notify']/following-sibling::td[1]/div[@class='ilFormInfo']" /></label>
			</li>
			-->
			<li class="ui-body ui-body-b"> 
				<fieldset class="ui-grid-a"> 
						<div class="ui-block-a">
							<xsl:copy-of select="//td[@class='ilFormFooter']/div/input[@name='cmd[cancelPost]']" />
						</div> 
						<div class="ui-block-b">
							<xsl:copy-of select="//td[@class='ilFormFooter']/div/input[@name='cmd[savePost]']" />
						</div>
						<div class="ui-block-a">
							<xsl:copy-of select="//td[@class='ilFormFooter']/div/input[@name='cmd[showThreads]']" />
						</div> 						
						<div class="ui-block-b">
							<xsl:copy-of select="//td[@class='ilFormFooter']/div/input[@name='cmd[addThread]']" />
						</div> 
			    </fieldset> 
			</li> 		
		</ul>
	</form>
</xsl:template>


<!-- 
	Threads toolbar
 -->
<xsl:template match="div[@class='ilToolbar']">
	<xsl:if test="//div[@id='mobileForumThreadsTable']" >
		<final>
		<div data-role="navbar">
			<ul>
				<xsl:for-each select=".//a">
					<small>
						<li>
							<a rel="external" data-role="button"  href="{@href}" >
								<xsl:value-of select="." />
							</a>
						</li>
					</small>
				</xsl:for-each>
			</ul>
		</div>
		<br />
		</final>
	</xsl:if>
</xsl:template> 


<!-- 
	Posts Toolbar
 -->
<xsl:template match="div[@class='mobileForumPostsToolbar']">
	<final>
	<div data-role="navbar">
		<ul>
			<li>
				<a rel="external" data-role="button" href="{//a[@data-icon='back']/@href}" >
					<xsl:value-of select="//a[@data-icon='back']" />
				</a>
			</li>
			<li>
				<a rel="external" data-role="button" href="{.//a[1]/@href}" >
					<xsl:value-of select=".//a[1]" />
				</a>
			</li>
		</ul>
	</div>
	<br />
	</final>
</xsl:template> 


<!-- 
	Header of action lists in posting sub menu
-->
<xsl:template match="li[@class='mobileForumPostActionsTitle']"> 
	<div id="forum_nav" data-role="page" data-fullscreen="false" data-theme="c">
		<div data-role="header">
			back
		</div> 
		<div data-role="content">
			<xsl:copy>
				<xsl:copy-of select="@*" />
				<xsl:value-of select="$txtActions" />
			</xsl:copy>
		</div>
	</div>
</xsl:template>

<!-- 
	Author and update info: remove links
	(they don't work with the nested lists in jquery mobile)
 -->
<xsl:template match="span[@class='mobileForumPostAuthor' or @class='mobileForumPostUpdated']/a" >
	<xsl:value-of select="." />
</xsl:template>


<!--
	Urls in general: remove fragments 
	(they don't work with jquery mobile)
 -->
<xsl:template match="@href"> 
	<xsl:attribute name="href">
		<xsl:value-of select="php:function('ilMobileSkin::trimUrl', string(.))" />
	</xsl:attribute>
</xsl:template>

</xsl:stylesheet>


