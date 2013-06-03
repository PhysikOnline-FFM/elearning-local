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
	### Start /Info page ###
 -->
<xsl:template match="div[@class='mobileInfoScreen']">
	<div class='mobileInfoScreen'>
		<final>
			<xsl:apply-templates select= "final/*" />
		</final>
	</div>
</xsl:template>
 
<!-- suppress button for list of answers --> 
<xsl:template match="input[@name='cmd[outUserListOfAnswerPasses]']"></xsl:template>

<!-- deselect javascript -->
<xsl:template match="input[@name='chb_javascript']">
	<xsl:copy>
		<xsl:copy-of select="@*[name() != 'checked']" />
	</xsl:copy>
</xsl:template>
 
<!-- 
	### List of questions page ###
 -->
<xsl:template match="form[@name='listofquestions']">
	<xsl:variable name="txtOrder" select="string(.//th[1])" />
	<xsl:variable name="txtTitle" select="string(.//th[2])" />
	<xsl:variable name="txtMaxpoints" select="string(.//th[4])" />
	<xsl:variable name="txtAnswered" select="string(.//th[5])" />
	<xsl:variable name="txtMark" select="string(.//th[6])" />

	<final>
		<xsl:copy>
			<xsl:copy-of select="@*" />
			<xsl:attribute name="data-ajax">false</xsl:attribute>

			<fieldset data-role="controlgroup" data-type="horizontal">
			<xsl:for-each select="//div[@class='ilTableCommandRowTop']//input">
				<xsl:copy-of select="." />
			</xsl:for-each>
			</fieldset>			
			<br />	
	
				<ul data-role="listview"> 
					<li data-role="list-divider">
						<xsl:value-of select="//h3" />
					</li>
					<xsl:for-each select=".//tr[@class='tblrow1' or @class='tblrow2']">
						<li data-icon="false">
							<xsl:if test="td[6]/img">
								<xsl:attribute name="data-icon">star</xsl:attribute>
							</xsl:if>
							
							<img class="ui-li-icon" src="{td[5]/img/@src}" alt="{td[5]/img/@alt}" />
							<a rel="external" href="{td[2]//a/@href}">
								<p>									
									<strong>
										<xsl:value-of select="td[1]" />
										<xsl:text>. </xsl:text>
										<xsl:value-of select="./td[2]//a" />
									</strong>
								</p>
								<p>
									<xsl:value-of select="td[2]/span" />
								</p>
								<p class="ui-li-count">
									<!-- count bubble -->
									<xsl:value-of select="td[4]/text()" />
								</p>
							</a>
						</li>
					</xsl:for-each>
				</ul>

		</xsl:copy>
	</final>	
</xsl:template>


<!--
 	### Test output page ###
 -->
<xsl:template match="div[@id='mobileTestOutput']"> 
	<final>
		<xsl:copy>
			<xsl:apply-templates select= "@*|*" />
		</xsl:copy>
	</final>
</xsl:template>

<!-- Prev/Next navigation -->
<xsl:template match="input[@id='prevbutton' or @id='bottomprevbutton']">
	<input>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="value"><xsl:value-of select="php:functionString('str_replace','&lt;&lt;', '', @value)" /></xsl:attribute>
	</input>
</xsl:template>
<xsl:template match="input[@id='nextbutton' or @id='bottomnextbutton']">
	<input>
		<xsl:copy-of select="@*" />
		<xsl:attribute name="value"><xsl:value-of select="php:functionString('str_replace','&gt;&gt;', '', @value)" /></xsl:attribute>
	</input>
</xsl:template>


<!-- allow header scripts -->
<xsl:template match="//head/script"> 
	<final>
		<xsl:copy-of select="." />
	</final>
</xsl:template>

<!-- modify question title -->
<xsl:template match="div[@id='mobileQuestionOutput']//h1" >
	<h3><xsl:value-of select="." /></h3>
</xsl:template>

<!-- modify select fields -->
<xsl:template match="div[@id='mobileQuestionOutput']//select">
	<select data-native-menu="false">
		<xsl:apply-templates select="@*|*" />
	</select>
</xsl:template> 

<!-- don't use tiny mce for textareas -->
<xsl:template match="textarea">
	<xsl:copy>
		<xsl:copy-of select="@*[name() != 'class']" />
		<xsl:copy-of select="*|text()" />
	</xsl:copy>
</xsl:template>

<!-- suppress javascript switch	-->
<xsl:template match="div[@id='javascript_switch']"></xsl:template>

<!-- suppress bottom functions	-->
<xsl:template match="fieldset[@id='mobileTestBottomFunctions']"></xsl:template>

<!-- set mark image-->
<xsl:template match="img[@id='mobileTestMarkImage']">
	<xsl:if test="//input[@id='mobileTestResetMark']" >
			<xsl:copy>
				<xsl:copy-of select="@*" />
				<xsl:attribute name="src"><xsl:value-of select="//input[@id='mobileTestResetMark']/@src" /></xsl:attribute>
			</xsl:copy>
	</xsl:if>
</xsl:template>

<!-- 
	### Results screen ###
-->
<xsl:template match="div[@id='mobileTestResultsParticipants']">
	<final>
		<xsl:copy>
			<xsl:apply-templates select= "@*|*" />
		</xsl:copy>
	</final>
</xsl:template>

<!-- pass overview -->
<xsl:template match="div[@id='mobileTestResultsPassOverview']">
	<xsl:variable name="txtMarked" select="string(.//th[1])" />
	<xsl:variable name="txtPass" select="string(.//th[2])" />
	<xsl:variable name="txtDate" select="string(.//th[3])" />
	<xsl:variable name="txtQuestions" select="string(.//th[4])" />
	<xsl:variable name="txtPoints" select="string(.//th[5])" />
	<xsl:variable name="txtPercent" select="string(.//th[6])" />

	<xsl:if test="php:function('ilSkinTransformer::getUrlParameter','','pass') = ''" >
		<ul data-role="listview"> 
			<li data-role="list-divider">
				<xsl:value-of select="//h3" />
			</li>
			<xsl:for-each select=".//tr[not(@class)]">
				<li data-icon="false">
					<xsl:if test="td[1]/strong">
						<xsl:attribute name="data-icon">check</xsl:attribute>
					</xsl:if>
					
					<a rel="external" href="{td[7]//a/@href}">
						<p>									
							<strong>
								<xsl:value-of select="td[2]" />
								<xsl:text>. </xsl:text>
								<xsl:value-of select="$txtPass" />
								<xsl:text> </xsl:text>
								<xsl:value-of select="./td[3]" />
							</strong>
						</p>
						<p>
							<xsl:value-of select="$txtQuestions" />
							<xsl:text>: </xsl:text>
							<xsl:value-of select="./td[4]" />
						</p>
						<p>
							<xsl:value-of select="$txtPercent" />
							<xsl:text>: </xsl:text>
							<xsl:value-of select="./td[6]" />
						</p>
							
						<p class="ui-li-count">
							<!-- count bubble -->
							<xsl:value-of select="td[5]" />
						</p>
					</a>
				</li>
			</xsl:for-each>
		</ul>		
	</xsl:if>
</xsl:template>


<!-- pass details -->
<xsl:template match="div[@id='mobileTestResultsPassDetails']">
	<xsl:variable name="pass" select="php:function('ilSkinTransformer::getUrlParameter','','pass')" />
	<xsl:variable name="txtPass" select="php:function('ilSkinTransformer::getTxt','pass')" />

	<xsl:variable name="txtOrder" select="string(.//th[1])" />
	<xsl:variable name="txtTitle" select="string(.//th[2])" />
	<xsl:variable name="txtMaxpoints" select="string(.//th[3])" />
	<xsl:variable name="txtReachedPoints" select="string(.//th[4])" />
	<xsl:variable name="txtPercent" select="string(.//th[5])" />

	<ul data-role="listview"> 
		<li data-role="list-divider">
			<xsl:value-of select="$pass + 1" />
			<xsl:text>. </xsl:text>
			<xsl:value-of select="$txtPass" />
		</li>
		<xsl:for-each select=".//tr[not(@class)]">
			<li data-icon="false">				
				<a rel="external" href="{td[2]//a/@href}">
					<p>									
						<strong>
							<xsl:value-of select="td[1]" />
							<xsl:text>. </xsl:text>
							<xsl:value-of select="td[2]" />
						</strong>
					</p>
					<p>
						<xsl:value-of select="$txtPercent" />
						<xsl:text>: </xsl:text>
						<xsl:value-of select="./td[5]" />
					</p>
					<p>
						<xsl:value-of select="$txtMaxpoints" />
						<xsl:text>: </xsl:text>
						<xsl:value-of select="./td[3]" />
					</p>
						
					<p class="ui-li-count">
						<!-- count bubble -->
						<xsl:value-of select="td[4]" />
					</p>
				</a>
			</li>
		</xsl:for-each>
	</ul>		
</xsl:template>

<!-- 
	### Correct solution page ###
-->
<xsl:template match="div[@id='mobileTestCorrectSolutionOutput']">
	<final>
		<xsl:copy>
			<xsl:apply-templates select= "@*|*" />
		</xsl:copy>
	</final>
</xsl:template>

<!-- modify solution feedback -->
<xsl:template match="div[@class='solutionFeedback']/h2"> 
	<p><strong><xsl:copy-of select="*|text()" /></strong></p>
</xsl:template>

</xsl:stylesheet>