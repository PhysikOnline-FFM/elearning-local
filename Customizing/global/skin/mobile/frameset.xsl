<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:php="http://php.net/xsl">

<xsl:output method="html" version="4.0" encoding="UTF-8"/>


<xsl:template match="/">
<html>
	<head>
		<meta http-equiv="refresh" content="0; URL={//frame[@id='rep_content']/@src}" /> 
	</head>
</html>
</xsl:template>

</xsl:stylesheet>
