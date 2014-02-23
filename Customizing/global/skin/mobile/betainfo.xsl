<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="*">
		<ul data-role="listview" data-theme="a" data-divider-theme="a">
			<li>
				<h1>ILIAS mobile Infos</h1>
				<xsl:for-each select="/BetaInfos/Info">
					<p style="white-space: normal;">
						<xsl:copy-of select="." />
					</p>
				</xsl:for-each>
			</li>
			<xsl:for-each select="/BetaInfos/Beta">
				<li data-role="list-divider">
					Version
					<xsl:value-of select="version" />
					<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">
						<xsl:value-of select="date" />
					</span>
				</li>
				<xsl:for-each select="features">
					<li>
						<xsl:copy-of select="." />
					</li>
				</xsl:for-each>
			</xsl:for-each>
		</ul>
	</xsl:template>
</xsl:stylesheet>