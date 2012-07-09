<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:template match="mod_sape_article">

		<xsl:choose>
			<xsl:when test="item">
				<xsl:apply-templates select="item" mode="show_article" />
			</xsl:when>
			<xsl:otherwise>
				<!--xsl:apply-templates select="list" mode="show_sape"/-->
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="item" mode="show_article">
		<div>
	<xsl:value-of select="." disable-output-escaping="yes" />
		</div>
	</xsl:template>

<xsl:template match="list" mode="anons">
    <div>
                <xsl:value-of select="item" disable-output-escaping="yes" />
    </div>
</xsl:template>


</xsl:stylesheet>