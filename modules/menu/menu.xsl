<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="mod_menu">
		<xsl:apply-templates mode="top" />
	</xsl:template>

	<xsl:template match="list" mode="top">
		<div id="templatemo_menu">
			<ul>
				<xsl:for-each select="item/item">
					<xsl:if test="contains(param,'top_menu')">
						<li>
							<a href="/{path}">
								<xsl:value-of select="name" />
							</a>
						</li>
					</xsl:if>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>

	<xsl:template match="list" mode="bottom">
		<xsl:for-each select="item/item">
			<xsl:if test="contains(param,'bottom_menu')">
				<xsl:if test="position()!=1">
					&#160;|&#160;
				</xsl:if>
				<a href="/{path}">
					<xsl:value-of select="name" />
				</a>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>

</xsl:stylesheet>
