<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />

	<xsl:template match="mod_client" mode="head">
		<title>
			<xsl:value-of select="//mod_meta_tags/title" />
		</title>
		<meta name="description">
			<xsl:attribute name="content">
              <xsl:value-of select="//mod_meta_tags/description" />
            </xsl:attribute>
		</meta>

		<meta name="keywords" content="#KEYWORDS#" />

		<link rel="stylesheet" href="/css/reset.css" type="text/css" media="screen, projection" />
		<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen, projection" />

		<xsl:comment><![CDATA[[if lt IE 9]><link rel="stylesheet" href="/css/ie.css" type="text/css" media="screen, projection" /><![endif]]]></xsl:comment>

		<link href="/css/colorbox/example4/colorbox.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="/css/add.css" type="text/css" />

		<script type="text/javascript" src="/engine/js/jquery.js" />
		<script type="text/javascript" src="/engine/js/jquery.colorbox.js" />
		<script type="text/javascript" src="/engine/js/jquery.livequery.js" />
		<script type="text/javascript" src="/engine/js/engine.js" />
		<script type="text/javascript" src="/modules/client/client.js" />
	</xsl:template>

	<xsl:template match="mod_client">
		<xsl:apply-templates mode="client" />
	</xsl:template>

	<xsl:template match="*" mode="client" />

</xsl:stylesheet>
