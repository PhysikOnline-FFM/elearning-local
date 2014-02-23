<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="html" version="4.0" encoding="UTF-8" />
	<xsl:template match="/">
		<final>
			<br></br>
			<xsl:copy-of select="." />
		</final>
	</xsl:template>
</xsl:stylesheet>
