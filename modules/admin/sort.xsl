<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="list_admin" mode="sort">
		<xsl:if test="item">
			<script type="text/javascript" src="/engine/modules/admin/sort.js" />
			<ul id="sortable">
				<xsl:for-each select="item">
					<li class="ui-state-default" id="{id}">
						<xsl:value-of select="name" />
					</li>
				</xsl:for-each>
			</ul>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>