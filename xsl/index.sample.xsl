<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<!-- <xsl:output cdata-section-elements="script" doctype-public="-//W3C//DTD 
		XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" 
		encoding="UTF-8" indent="yes" media-type="html" method="html" standalone="no" 
		/> -->
	<xsl:output method="html" doctype-system="about:legacy-compat"
		encoding="utf-8" />
	<xsl:include href="./xsl/head.xsl" />
	<xsl:include href="./engine/xsl/templates.xsl" />
	<xsl:include href="./xsl/templates.xsl" />
	<!--include modules -->

	<xsl:template match="root">
		<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif] -->
		<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif] -->
		<!--[if IE 8]> <html class="no-js lt-ie9" lang="en"> <![endif] -->
		<!--[if gt IE 8]> <! -->
		<html>
			<head>
				<base href="{domain}" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<xsl:call-template name="head" />
			</head>
			<body>

				<xsl:if test="//requests/get/d">
					<xsl:attribute name="style">background-color:#ffffff;</xsl:attribute>
				</xsl:if>
				<xsl:if test="DEBUG=1">
					<xsl:attribute name="rel">debug</xsl:attribute>
				</xsl:if>
				<xsl:apply-templates select="//mod_admin_menu" />
				<xsl:call-template name="body" />
			</body>
		</html>
	</xsl:template>

	<xsl:template match="content">
		<xsl:apply-templates select="//messages" />
		<xsl:call-template name="current" />
	</xsl:template>

	<xsl:template name="current">
		<xsl:apply-templates select="CURRENT" mode="CLASS" />

	</xsl:template>

</xsl:stylesheet>