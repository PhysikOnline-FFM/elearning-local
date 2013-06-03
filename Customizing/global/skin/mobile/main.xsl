<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:php="http://php.net/xsl">

<xsl:output method="html" version="4.0" encoding="UTF-8"/>

<!-- 
	The available parameters are the attributes of the transformation definition
-->
<xsl:param name="of" 	/>
<xsl:param name="at" 	/>
<xsl:param name="by" 	/>
<xsl:param name="with" 	/>
<xsl:param name="for" 	/>
<xsl:param name="next" 	/>
<xsl:param name="keep" 	/>
<xsl:param name="debug" />

<!-- 
	Other global variables can be get with php functions 
 -->
<xsl:variable name="skinDirectory" 		select="php:function('ilSkinTransformer::getSkinDirectory','')" />
<xsl:variable name="loggedIn" 			select="php:function('ilSkinTransformer::loggedIn','')" />
<xsl:variable name="headerTitle" 		select="php:function('ilMobileSkin::getInstallationName')" />
<xsl:variable name="themeString" 		select="php:function('ilMobileSkin::getTheme')" />
<xsl:variable name="betastatus" 		select="php:function('ilMobileSkin::getBetaStatus')" />
<xsl:variable name="jquery" 			select="php:function('ilMobileSkin::getJqueryVersion')" />
<xsl:variable name="fullscreen" 		select="php:function('ilMobileSkin::isFullscreen')" />
<xsl:variable name="mainHeadline" 		select="//*[@id='il_mhead_t_focus' or @class='il_LMHead']" />


<xsl:variable name="supportedTabs" 		select="//div[@id='mobileTabs']/a[php:function('ilSkinTransformer::isUrlSupported', string(@href))]" />
<xsl:variable name="supportedBackTabs" 	select="//div[@id='mobileBackTabs']/a[php:function('ilSkinTransformer::isUrlSupported', string(@href))]" />
<xsl:variable name="supportedSubTabs" 	select="//div[@id='mobileSubTabs']/a[php:function('ilSkinTransformer::isUrlSupported', string(@href))]" />
<xsl:variable name="curTabText" 		select="$supportedTabs[@class='tabactive']" />
<xsl:variable name="curSubTabText" 		select="$supportedSubTabs[@class='subtabactive']" />

<!-- 
	Convention for last sylesheet in transformation sequence:
	Copy childs of <final> without further processing
	Copy descendants of <apply> with further processing
	
	Default behavior of contents without template:
	Copy attributes
	Omit elements but process their childs
-->
<xsl:template match="final"><xsl:copy-of select="node()" /></xsl:template>
<xsl:template match="apply//node()"><xsl:copy><xsl:apply-templates select="@*|node()" /></xsl:copy></xsl:template>
<xsl:template match="@*"><xsl:copy-of select="." /></xsl:template>
<xsl:template match="*"><xsl:apply-templates select="*"/></xsl:template>


<!-- 
	HTML element: starting point for processing
-->	
<xsl:template match="html">
<html>
	<xsl:choose>
		<xsl:when test="$at = 'Services/Container/async_item_list'" >
			<!-- this template is already prepared for other async selection list hooks -->
			<xsl:apply-templates select="." mode="async_advanced_selection_list" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="*" />
		</xsl:otherwise>
	</xsl:choose>
</html>
</xsl:template>


<!-- 
	HTML header: new one prepared for jquery mobile 
-->
<xsl:template match="head">
<head>
	<xsl:copy-of select="title" />
	<xsl:copy-of select="meta" />
	<xsl:copy-of select="base" />
	
	<!-- <xsl:copy-of select="link" /> -->
	<!-- <xsl:copy-of select="style" /> -->
	<xsl:copy-of select="script" />
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="./{$skinDirectory}/mobile.css" />
	<link rel="stylesheet" href="./{$skinDirectory}/themes/{$themeString}/mob.css" />
	<link rel="stylesheet" href="./{$skinDirectory}/themes/{$themeString}/custom.css" />
	<link rel="stylesheet" href="./{$skinDirectory}/themes/{$jquery}/jquery.mobile.structure.css" />	
	<link rel="stylesheet" href="./{$skinDirectory}/themes/red_theme.css" />
	<link rel="stylesheet" href="./{$skinDirectory}/themes/notification_theme.css" />	
	<link rel="stylesheet" type="text/css" href="./{$skinDirectory}/themes/jquery.mobile.simpledialog.min.css" /> 
	
	<script src="./{$skinDirectory}/themes/{$jquery}/jquery.min.js"></script> 
	<script src="./{$skinDirectory}/themes/{$jquery}/jquery.mobile.js"></script>
	<script type="text/javascript" src="./{$skinDirectory}/themes/jquery.mobile.simpledialog.min.js"></script>
	
	<xsl:if test="php:function('ilSkinTransformer::isWinMobile') = 'true'">
		<link rel="stylesheet" href="./{$skinDirectory}/themes/winphone7.css" />
	</xsl:if>

	<script>
		$(document).bind("mobileinit", function(){
			$.mobile.touchOverflowEnabled = true;
		});

		/*$(document).ready(function(){
			$('.noaccess').parent().click(function(e){
				alert($('#noaccess').text());
			});
			
			$('.ui-btn-back').live('tap',function(){
				history.back();
				return false;
			}).live('click',function(){
				return false;
			});
		});*/

		$(document).ready(function()
		{ 
			$("h1.noaccess").click(function(){
				$("h1.noaccess").simpledialog(
				{
					'mode' : 'bool',
					'prompt': 'INFO',
					'subTitle': $('#noaccess').text(),
					'forceInput': false,
					'buttons' :
					{
						'OK':
						{
							click: function () {},
							icon: "",
							theme: "a"
						}
					}
				});
			});
	    });

		
		var mobile_timer = false;
		if(navigator.userAgent.match(/iPhone/i))
		{
			$('#viewport').attr('content','width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0');
			$(window).bind('gesturestart',function () {
				clearTimeout(mobile_timer);
				$('#viewport').attr('content','width=device-width,minimum-scale=1.0,maximum-scale=10.0');
			}).bind('touchend',function () {
				clearTimeout(mobile_timer);
				mobile_timer = setTimeout(function () {
					$('#viewport').attr('content','width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0');
				},1000);
			});
		}

	</script>
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="viewport" id="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=10.0,initial-scale=1.0;" />
    <link rel="apple-touch-icon" href="./{$skinDirectory}/themes/{$themeString}/icon.png" />
    <link rel="apple-touch-startup-image" href="./{$skinDirectory}/themes/{$themeString}/startup.png" />
	<xsl:apply-templates select="final" />
</head>
</xsl:template>

<!--  
	HTML body: divided into separate pages for jquery mobile 
-->
<xsl:template match="body">
<body>
	
	<!-- 
		Notification when click Course as Non-Member
	-->
	<div id="noaccess">
		<!-- <xsl:value-of select="php:function('ilSkinTransformer::getTxt', 'status_no_permission')" /> status_no_permission -->
		Sie haben leider noch keinen Zugriff zu diesem Kurs. <br/>Bitte benutzen Sie die Beitreten-Funktion oder wenden Sie sich an Ihren Kursadministrator.
	</div>
	
	<!-- 
		main page: displayed immediately 
	-->
	<div id="mobileMainPage" data-role="page" data-theme="c">
		<xsl:if test="$fullscreen = 0">
		<div data-role="header" data-theme="c">

			<xsl:choose>
				<xsl:when test="$loggedIn = 1">
					<a href="#" data-rel="back" data-theme="a" data-iconpos="notext" data-icon="arrow-l"></a>
				</xsl:when>
				<xsl:otherwise>
					<a href="/" rel="external" data-theme="b" class="ui-btn-left" data-iconpos="notext" data-icon="home"></a>
				</xsl:otherwise>
			</xsl:choose>

			<div id="fsxLogo"></div>
			
			<xsl:if test="$betastatus = 1">
				<a href="#betainfo" data-theme="b" data-iconpos="notext" data-icon="info" data-rel="dialog" data-transition="slidedown" data-corners="true" data-shadow="true"></a>
			</xsl:if>
			
		</div> 
	</xsl:if>
		<div data-role="content">
			<!-- Benutzername anzeigen -->
			<xsl:if test="php:function('ilSkinTransformer::checkBaseClass','ilpersonaldesktopgui')">
				<div class="FSXlists2">
					<ul data-role="listview" data-dividertheme="a">
						<li data-role="list-divider">
							<h2> </h2>
							<p class="FSXusername">
								<xsl:value-of select="php:function('ilSkinTransformer::getUserName')" />
							</p>
						</li>
					</ul>
				</div>
			</xsl:if>
			
			<!-- neuer Header Title -->
			<xsl:if test="$loggedIn &gt; 0">
				<div class="FSXlists">
					<ul data-role="listview" data-dividertheme="a">
						<li data-role="list-divider">
							<xsl:value-of select="$mainHeadline" />
						</li>
					</ul>
				</div>
			</xsl:if>
			
			<div id="defcontent">
				<!-- put messages below tabs -->
				<xsl:copy-of select=".//div[@class='mobileMessages']" />
							
				<!-- put blocks as collapsibles before other contents -->
				<xsl:apply-templates select="//div[@class='mobileBlockCollapsible']" mode="blocks"/>
				
				<!--  apply any other content -->
				<xsl:apply-templates select="*" />
			</div>
	 	</div>
		<xsl:call-template name="footer" />
  	</div>
  	
  	
  	<!-- 
  		menu page 
  	-->
 	<div id="mobileMenuPage" data-role="page" data-theme="b">
		<div data-role="content">
			<!-- main menu -->
			<xsl:apply-templates select="//div[@id='mobileMainMenu']" mode="main-menu" />
			<!-- login / logout -->
			<xsl:copy-of select="//div[@id='mobileLoginSection']" />
			<!-- language selection -->	
			<xsl:for-each select="//div[@id='mobileAdvancedSelectionList_asl']">
				<a data-role="button" data-theme="c" data-icon="arrow-r" data-iconpos="right" href="{concat('#', @id)}">
					<xsl:value-of select="a" />
				</a> 
			</xsl:for-each>
 		</div>
 		
		<xsl:call-template name="footer" />
  	</div>  
  	
  	<!-- 
		betaInfo
	-->
	<div id="betainfo" data-role="dialog" data-theme="a">
		<div data-role="header" data-theme="a"> 
			<div id="fsxLogo"></div>
		</div> 
		<div data-role="content">
			<xsl:copy-of select="php:function('ilMobileSkin::getBetaTxt', '')" />
		</div>
	</div>
  	
  	
  	
  	
  	<!-- 
  		tabs page
  	-->
  	<div id="mobileTabsPage" data-role="page" data-theme="a">
		<div data-role="header">
			<h1><xsl:value-of select="$mainHeadline" /></h1>
		</div>
		<div data-role="content">
			<xsl:apply-templates select=".//div[@id='mobileTabs' or @id='mobileBackTabs']" mode="tabs-page" />
 		</div>
		<xsl:call-template name="footer" />
  	</div>

	<!-- 
		subtabs page
	-->
 	<div id="mobileSubTabsPage" data-role="page" data-theme="a">
		<div data-role="header">
			<h1><xsl:value-of select="$mainHeadline" /></h1>
		</div>
		<div data-role="content">
			<xsl:apply-templates select=".//div[@id='mobileSubTabs']" mode="tabs-page" />
		</div>
		<xsl:call-template name="footer" />
 	</div>
 	
 	<!-- 
 		advanced selection lists (sync mode) 
 		can be anywhere in the content and are applied here
 	--> 		
 	<xsl:for-each select="//div[@class='mobileAdvancedSelectionList']" >
		<xsl:apply-templates select="." mode="sync_advanced_selection_list" />
 	</xsl:for-each>
 	
 	
 	<!-- 
 		specific pages 
 		can be defined anywhere in the content and are copied here
 	-->
 	<xsl:for-each select="//div[@data-role='page']">
 		<xsl:copy>
 			<xsl:copy-of select="@*" />
 			<xsl:attribute name="data-theme">a</xsl:attribute>
			<xsl:if test="not(div[@data-role='header'])">
				<div data-role="header">
					<h1><xsl:value-of select="$mainHeadline" /></h1>
				</div>
			</xsl:if>
			<xsl:copy-of select="*" />			
			<xsl:if test="not(div[@data-role='footer'])">
				<xsl:call-template name="footer" />
			</xsl:if>
 		</xsl:copy>
 	</xsl:for-each>
 	
 	
 	<!-- 
  		FSX Search Page
  	-->
 	<div id="search_page" data-role="dialog" data-fullscreen="false" data-theme="a">
 		<div data-role="header" data-theme="a">
			<h1>
				<xsl:value-of select="php:function('ilSkinTransformer::getTxt', 'search')" />
			</h1>
		</div>
		<div data-role="content" data-theme="a">
			<br/>
			<h1>
				<xsl:value-of select="php:function('ilSkinTransformer::getTxt', 'search')" />
			</h1>
			<form method="post" target="_top" >
				<xsl:attribute name="data-rel">external</xsl:attribute>
				<xsl:attribute name="data-ajax">false</xsl:attribute>
				<xsl:attribute name="action">
					<xsl:value-of select="//form[@class='ilMainMenuSearch']/@action" />
				</xsl:attribute>
				<input type="text" id="main_menu_search" name="queryString"  data-theme="c" />
				<xsl:copy-of select="//form[@class='ilMainMenuSearch']//input[@type='hidden']" />
				<input type="submit" value="suchen" data-theme="a" />
			</form>
		</div>
 	</div>

 	<!-- 
  		FSX Navigation Page
  	-->
 	<div id="navigation" data-role="page" data-fullscreen="false" data-theme="a">
 		<div data-role="header" data-theme="a">
			<h1>
				Menu
			</h1>
		</div>
		<div data-role="content" data-theme="a">
			<br/>
			<br/>
			<ul data-role="listview" data-theme="a" data-divider-theme="a">
				<!-- breadcrumb -->
				<xsl:apply-templates select="//div[@id='fsxbread']" mode="navigation" />
				<!--<li data-role="list-divider" data-theme="b">Repository</li> -->
				<xsl:apply-templates select="//div[@id='FSXmm_rep_ov']" mode="navigation" />
			</ul>
			
			<!-- styleswitch -->
			<p>
				<br/>
			</p>
			
			<div data-role="controlgroup" data-inline="true">
				<!-- <a data-role="button" data-theme="a" href="{//div[@id='FSXdash']/a[3]/@href}" rel="external">
					<xsl:value-of select="//div[@id='FSXdash']/a[3]" />
				</a> -->

				<xsl:for-each select="//div[@class='iosStyleSwitch']//a">
					<a data-role="button" data-theme="a" href="{@href}" rel="external">
						<xsl:value-of select="." />
					</a>
				</xsl:for-each>
				<xsl:copy-of select="//div[@id='mobileLoginSection']/a[@class='FSXReg']" />
				<xsl:copy-of select="//div[@id='mobileLoginSection']/a[@class='FSXLogout']" />
				<xsl:copy-of select="//div[@id='mobileLoginSection']/a[@class='FSXLogin']" />
			</div>
		</div>
 	</div>
	</body>
</xsl:template>


<!--
	FSX navigation
 -->
<xsl:template match="div[@id='FSXmm_rep_ov']" mode="navigation" >
	<xsl:for-each select="*">
			<xsl:variable name="class" select="@class" />
			<xsl:variable name="url" select="@href" />
			<xsl:choose>
				<xsl:when test="$class='ilGroupedListLE'" >
					<xsl:if test="position() &gt; 1 and position() &lt; 6">
					<li><a rel="external">
						<xsl:attribute name="href"><xsl:value-of select="$url" /></xsl:attribute>
						<xsl:value-of select="." />
					</a></li>
					</xsl:if>
				</xsl:when>
				<xsl:when test="$class='ilGroupedListH'" >
					<li data-role="list-divider" data-theme="b">
						<xsl:value-of select="." />
					</li>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="." />
				</xsl:otherwise>
			</xsl:choose>
		
	</xsl:for-each>
</xsl:template>




<!-- 
	Locator FSX
-->
<xsl:template match="//div[@id='fsxbread']" mode="navigation" >
	<xsl:for-each select="*">
			<xsl:variable name="class" select="@class" />
			<xsl:variable name="url" select="@href" />
			<xsl:choose>
				<xsl:when test="$class='mobileLocatorEntry'" >
					<li>
						<a>
							<xsl:attribute name="href"><xsl:value-of select="$url" /></xsl:attribute>
							<xsl:attribute name="rel">external</xsl:attribute>
							<xsl:value-of select="." />
						</a>
					</li>
				</xsl:when>
				<xsl:when test="$class='mobileLocatorHeadline'" >
					<li data-role="list-divider" data-theme="b">
						<xsl:value-of select="." />
					</li>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="." />
				</xsl:otherwise>
			</xsl:choose>
	</xsl:for-each>
</xsl:template>


	<!-- 
		Suche  
	-->
	<xsl:template match="//div[@id='mobileSearch']">
		<final>
			<xsl:apply-templates select="*"  mode="search"/>
		</final>
	</xsl:template>

	<!-- 
		SearchForm  
	-->
	<xsl:template match="div[@id='mobileSearchForm']" mode="search">
		
		<!-- vars -->
		<xsl:variable name="url" 		select="form/@action"/>
		<xsl:variable name="term" 		select="//input[@id='term']/@value"/>
		<xsl:variable name="submit" 	select="//input[@type='SUBMIT']/@value"/>
		<br/>
		<form>
			<xsl:attribute name="data-rel">external</xsl:attribute>
			<xsl:attribute name="data-ajax">false</xsl:attribute>
			
			<!-- Form Attributes -->
			<xsl:attribute name="action">
				<xsl:value-of select="$url" />
			</xsl:attribute>
			<xsl:attribute name="method">post</xsl:attribute>
			
			<!-- Input -->
			<input type="text" id="term" name="term" autocomplete="off">
				<xsl:attribute name="value">
					<xsl:value-of select="$term" />
				</xsl:attribute>
			</input>
			
			<!-- Senden -->
			<input type="SUBMIT" name="cmd[performSearch]" data-theme="a">
				<xsl:attribute name="value">
					<xsl:value-of select="$submit" />
				</xsl:attribute>
			</input>
		</form>
	</xsl:template>
	
	
	<!-- 
		SearchResult  
	-->
	<xsl:template match="div[@id='mobileSearchResult']" mode="search" >
	
		<xsl:variable name="result" select="php:function('ilSkinTransformer::getTxt', 'search_result')" />
	
		<br></br>
		<ul data-theme="c" data-role="listview" data-dividertheme="a" data-split-icon="gear" data-split-theme="c">
			<li data-role="list-divider">
				<h1>
					<xsl:value-of select="$result" />
				</h1>
			</li>
			<xsl:for-each select="//li[@class='FSXsearch']"> <!-- div[@class='mobileContainerItem'] -->
				<xsl:variable name="url" select="div[@class='mobileContainerItem']/a/@href"/> 
				<xsl:variable name="img" select="img/@src"/> 
				<li>
					<a data-rel="external" rel="external">
						<xsl:attribute name="href">
							<xsl:value-of select="$url" />
						</xsl:attribute>
						<img class="ui-li-icon ui-li-thumb">
							<xsl:attribute name="src">
								<xsl:value-of select="$img" />
							</xsl:attribute>
						</img>
						<xsl:copy-of select="div[@class='mobileContainerItem']/h1" />
						
						<p>
						<xsl:value-of select="div[@class='mobileContainerItem']/div[@class='mobileContainerItemDetails']" />
					</p>
					</a>
					
						
					<xsl:apply-templates select=".//div[@class='mobileAdvancedSelectionList']" mode="link_advanced_selection_list">
						<xsl:with-param name="list_title" select="h1" />
					</xsl:apply-templates>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>


<!--
	Main menu 
 -->
<xsl:template match="div[@id='mobileMainMenu']" mode="main-menu">
	<xsl:copy>
		<xsl:copy-of select="@*" />	
		<xsl:apply-templates select="*" mode="main-menu" />
	</xsl:copy>
</xsl:template>
<xsl:template match="*" mode="main-menu">
</xsl:template> 


<!-- Links in main menu -->
<xsl:template match="a" mode="main-menu">
	<xsl:param name="href" select="@href" />
	<xsl:if test="php:functionString('ilSkinTransformer::isUrlSupported',$href)">
		<a rel="external" data-role="list">
			<xsl:copy-of select="@*" />
			<xsl:attribute name="href"><xsl:value-of select="$href" /></xsl:attribute>
			<xsl:choose>
			<!-- StudOn specific home link -->
			<xsl:when test="@class='mobileHomeLink' and //div[@class='ilTopIcon']/a/@href != ''">
				<xsl:attribute name="href"><xsl:value-of select="//div[@class='ilTopIcon']/a/@href" /></xsl:attribute>
				<xsl:value-of select="'Startseite'" />
			</xsl:when>
			<xsl:when test="@class='MMActive'">
				<xsl:attribute name="data-icon">check</xsl:attribute>
				<xsl:attribute name="data-theme">a</xsl:attribute>
				<xsl:value-of select="." />
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="data-icon"></xsl:attribute>
				<xsl:attribute name="data-theme">a</xsl:attribute>
				<xsl:value-of select="." />
			</xsl:otherwise>
			</xsl:choose>
		</a>
	</xsl:if>
</xsl:template>


<!-- 
	Tabs and subtabs 
-->
<xsl:template name="tabs">
	<xsl:variable name="tabs" select="count($supportedTabs)" />
	<xsl:variable name="backtabs" select="count($supportedBackTabs)" />
	<xsl:variable name="subtabs" select="count($supportedSubTabs)" />
	
	<xsl:if test="$tabs+$backtabs &gt; 1 or $subtabs &gt; 1">
		<div data-role="controlgroup" data-type="horizontal" style="display:none;">
			<xsl:if test="$tabs+$backtabs &gt; 1">
				<a href="#mobileTabsPage" data-role="button" data-icon="arrow-r" data-iconpos="right">
					<xsl:value-of select="php:function('ilSkinTransformer::getTxt','tabs')" />
					</a>
			</xsl:if>
			<xsl:if test="$subtabs &gt; 1">
				<a href="#mobileSubTabsPage" data-role="button" data-icon="arrow-r" data-iconpos="right">
					<xsl:value-of select="php:function('ilSkinTransformer::getTxt','subtabs')" />
				</a>
			</xsl:if>
				</div>
			</xsl:if>
		<br />
</xsl:template>

<xsl:template match="*" mode="tabs-page">
	<xsl:copy>
		<xsl:copy-of select="@*" />
		<xsl:choose>
			<xsl:when test="@id='mobileBackTabs'">
				<xsl:apply-templates select="$supportedBackTabs" mode="tabs-page" />
			</xsl:when>
			<xsl:when test="@id='mobileTabs'">
				<xsl:apply-templates select="$supportedTabs" mode="tabs-page" />
			</xsl:when>			
			<xsl:when test="@id='mobileSubTabs'">
				<xsl:apply-templates select="$supportedSubTabs" mode="tabs-page" />
			</xsl:when>			
		</xsl:choose>
	</xsl:copy>
</xsl:template>

<xsl:template match="a" mode="tabs-page">
	<xsl:copy>
		<xsl:copy-of select="@*" />
		<xsl:choose>
		<xsl:when test="@data-icon">
			<xsl:attribute name="data-theme">a</xsl:attribute>
		</xsl:when>
		<xsl:when test="@class='tabactive' or @class='subtabactive'">
			<xsl:attribute name="data-icon">check</xsl:attribute>
			<xsl:attribute name="data-theme">a</xsl:attribute>
		</xsl:when>
		<xsl:otherwise>
			<xsl:attribute name="data-icon"></xsl:attribute>
			<xsl:attribute name="data-theme">a</xsl:attribute>
		</xsl:otherwise>
		</xsl:choose>
		<xsl:value-of select="." />
	</xsl:copy>
</xsl:template>


<!-- 
	Container lists and items FSX
 -->
<xsl:template match="div[@class='mobileContainerList']" >
		<xsl:apply-templates mode="container-list" />
</xsl:template>

<xsl:template match="*" mode="container-list" >
	<xsl:copy>
		<xsl:copy-of select="@*" />
		<xsl:apply-templates mode="container-list" />					
	</xsl:copy>
</xsl:template>

<xsl:template match="div[@class='mobileContainerItem']" mode="container-list" >
	<a>
		<xsl:copy-of select="a[@class='mobileContainerItemLink']/@*" />
		<xsl:copy-of select="img" />
		<xsl:choose>
		<xsl:when test="input">
			<label>
				<xsl:copy-of select="label/@*" />
				<xsl:value-of select="h4" />
			</label>
			<xsl:copy-of select="input" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:copy-of select="h1" />
		</xsl:otherwise>
		</xsl:choose>
		<xsl:apply-templates select="div[@class='mobileContainerItemDetails']" mode="container-list" />
			</a>
	
	
	
	<xsl:apply-templates select=".//div[@class='mobileAdvancedSelectionList']" mode="link_advanced_selection_list">
		<xsl:with-param name="list_title" select="h4" />
	</xsl:apply-templates>
</xsl:template>

<xsl:template match="a" mode="container-list" >
		<xsl:apply-templates mode="container-list" />					
</xsl:template>



<!--
	Advanced selection list: embedded in content
	(will be transformed into link to sync or async page) 
 -->
<xsl:template match="div[@class='mobileAdvancedSelectionList']" mode="link_advanced_selection_list">

	<xsl:param name="list_title" select="a" />
	<a data-rel="dialog">
		<xsl:choose>
			<!-- synchronous call -->
			<xsl:when test="a/@href = ''">
				<xsl:attribute name="href">
					<xsl:value-of select="concat('#', @id)" />
				</xsl:attribute>
			</xsl:when>
			<!-- asynchronous call for container list -->
			<xsl:when test="contains(@id,'_act_')" >
				<xsl:attribute name="href">
					<xsl:value-of select="php:functionString('ilSkinTransformer::addUrlParameter', a/@href, 'purpose', 'actions', 'list_title', $list_title)" />
				</xsl:attribute>
			</xsl:when>
			<!-- other asynchronous calls -->
			<xsl:otherwise>
				<xsl:attribute name="href">
				<xsl:value-of select="php:functionString('ilSkinTransformer::addUrlParameter', a/@href, 'list_title', $list_title)" />
				</xsl:attribute>
			</xsl:otherwise>

			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:value-of select="a" />
	</a>
</xsl:template>


<!-- 
	Advanced selection list: synchronously called
	(will be transformed to a separate jquery mobile page)
-->
<xsl:template match="div[@class='mobileAdvancedSelectionList']" mode="sync_advanced_selection_list">
	<xsl:variable name="purpose">
		<xsl:choose>
		<xsl:when test="contains(@id,'_act_')" >
			<xsl:value-of select="'actions'" />	
		</xsl:when>
		<xsl:when test="contains(@id,'_lastvisited')" >
			<xsl:value-of select="'lastvisited'" />	
		</xsl:when>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="list_title">
			<xsl:choose>
			<xsl:when test="contains(@id,'_act_') and count(../h4)" >
				<xsl:value-of select="../h4" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="a" />
			</xsl:otherwise>
			</xsl:choose>
	</xsl:variable>
	
	<div id="{@id}" data-role="page" data-fullscreen="false" data-theme="a">
		<div data-role="header" data-theme="a">
			<h1>
			</h1>
		</div>
		<div data-role="content">
			<br/>
			<br/>
			<!-- add the ILIAS locator on the lastvisited page -->
			<!-- 
			<xsl:if test="$purpose='lastvisited'" >
				<xsl:apply-templates select="//ul[@class='mobileLocator']" mode="locator"/>
			</xsl:if>
			-->
			<xsl:apply-templates select="ul" mode="comm_advanced_selection_list"> 
				<xsl:with-param name="purpose" select="$purpose" />
				<xsl:with-param name="list_title" select="$list_title" />
			</xsl:apply-templates>
		</div>

 	</div> 	
</xsl:template>



<!-- 
	Advanced selection list: asynchronously called
	(will deliver a full page that is added jquery mobile hash)
-->
<xsl:template match="body" mode="async_advanced_selection_list">
<xsl:variable name="purpose" select="php:functionString('ilSkinTransformer::getUrlParameter', '', 'purpose')" />
<xsl:variable name="list_title" select="php:functionString('ilSkinTransformer::getUrlParameter', '', 'list_title')" />
<body>
 	<div data-role="page" data-fullscreen="false" data-theme="a">
		<div data-role="header">
			<h1>
			</h1>
		</div>
		<div data-role="content">
			<br/>
			<br/>
			<xsl:apply-templates select="//ul" mode="comm_advanced_selection_list">
				<xsl:with-param name="purpose" select="$purpose" />
			</xsl:apply-templates>
		</div>
 	</div> 	
</body>
</xsl:template>



<!-- 
	Advanced selection list: commands in sync or async list
	(if purpose is 'actions', urls will be ckecked)
-->
<xsl:template match="ul" mode="comm_advanced_selection_list" >
	<xsl:param name="purpose" />
	<xsl:param name="list_title" />

	<!-- this creates a result tree fragment, not a node set like for select="..." -->
	<xsl:variable name="commands">	
		<xsl:for-each select="*">
			<xsl:choose>
			<xsl:when test="$purpose='actions' or $purpose='lastvisited'" >
				<xsl:if test="./a[php:function('ilSkinTransformer::isUrlSupported', string(@href))]" >
					<xsl:copy-of select="." />
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<xsl:copy-of select="." />
			</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:variable>
	
	<xsl:if test="string($commands)">
		<xsl:copy>
			<xsl:copy-of select="@*" />
			<!-- add separate list title for last visited -->
			<xsl:if test="$purpose='lastvisited'">
			<li data-role="list-divider">
				<xsl:value-of select="$list_title" />
			</li>
			</xsl:if>
			<xsl:copy-of select="$commands" />
		</xsl:copy>
	</xsl:if>
</xsl:template>


<!-- 
	Locator
	revert the ordering of entries to minimize scrolling
-->
<xsl:template match="ul[@class='mobileLocator']" mode="locator">
	<xsl:copy>
	<xsl:copy-of select="@*" />
	<xsl:copy-of select="li[@class='mobileLocatorHeadline']" />	
	<xsl:for-each select="li[@class='mobileLocatorEntry']">
		<xsl:sort select="position()" 
				  order="descending" 
				  data-type="number" />
		<xsl:copy-of select="." />
	</xsl:for-each>
	</xsl:copy>
	<p><br/></p>
</xsl:template>



<!-- 
	Blocks 
	ignore them in the normal content
	apply them in the dedicated "blocks" collapsible set
-->
<xsl:template match="div[@class='mobileBlockCollapsible']">
</xsl:template>
<xsl:template match="div[@class='mobileBlockCollapsible']" mode="blocks">
	<xsl:copy>
		<xsl:apply-templates select="@*|*" />
	</xsl:copy>
</xsl:template>


<!-- 
	lel_mobile
-->
<!--<xsl:template match="div[@id='lel_mobile']">
	<p>lorem</p>
	<xsl:copy>
		<xsl:copy-of select="." />
	</xsl:copy>
</xsl:template>-->

<!--
	Footer
 -->
 <xsl:template name="footer">
 
 	<xsl:variable name="search_title" select="php:function('ilSkinTransformer::getTxt', 'search')" />
	<xsl:variable name="repository_title" select="php:function('ilSkinTransformer::getTxt', 'repository')" />
 	<xsl:variable name="pd_title" select="php:function('ilSkinTransformer::getTxt', 'personal_desktop')" />
 	<xsl:variable name="menu_mobile" select="php:function('ilSkinTransformer::getTxt', 'cont_glo_menu')" />
 	

 	<!-- Show Footer when online -->
 	<xsl:if test="$fullscreen = 0">
 	<xsl:if test="$loggedIn &gt; 0">
		<div data-position="fixed" data-role="footer" data-theme="a">		
			<div data-role="navbar">
				<ul>
					<xsl:if test="$loggedIn != 3">
						<li>
							<a href="./ilias.php?baseClass=ilPersonalDesktopGUI"  data-icon="home" data-role="button" rel="external">
								<xsl:attribute name="class">
									<xsl:value-of select="php:function('ilMobileSkin::getActive', 'ilPersonalDesktopGUI')" />
								</xsl:attribute>
								<xsl:value-of select="$pd_title" />
							</a>
						</li>
					</xsl:if>
					<li>
						<a href="./goto.php?target=root_1"  data-icon="grid" rel="external">
							<xsl:attribute name="class">
								<xsl:value-of select="php:function('ilMobileSkin::getActive', '')" />
							</xsl:attribute>
							<xsl:value-of select="$repository_title" />
						</a>
					</li>
					<li >
						<a href="#search_page" data-fullscreen="true" data-transition="slideup" data-rel="dialog" data-icon="arrow-u">
							<xsl:value-of select="$search_title" />
						</a>
					</li>
					
					<li >
						<a href="#navigation" data-fullscreen="true" data-transition="slideup" data-rel="dialog" data-icon="arrow-u">
							<!--<xsl:value-of select="$menu_mobile" />-->Menu
						</a>
					</li>
				</ul>
			</div>
		</div>
	</xsl:if>
	</xsl:if>
 </xsl:template>

</xsl:stylesheet>