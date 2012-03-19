<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />

	<xsl:template match="mod_feedback" />

	<xsl:template match="mod_feedback" mode="anywhere">
		<script type="text/javascript" src="/engine/modules/feedback/feedback.js" />
		<xsl:apply-templates mode="feedback" />
	</xsl:template>

	<xsl:template match="list_admin" mode="feedback">
		<table class="list_admin" cellspacing="1">
			<xsl:variable name="cnt" select="count(item)" />
			<xsl:for-each select="item">
				<tr>
					<xsl:if test="position() mod 2 = 0">
						<xsl:attribute name="class">even</xsl:attribute>
					</xsl:if>					
					<td width="300">
						<a class="feedback_message" href="" title="{email}" id="{id}">
							<xsl:value-of select="name" />
						</a>
					</td>
					<td width="300">
						<a class="feedback_message" href="" title="{email}" id="{id}">
							<xsl:value-of select="email" />
						</a>
						<xsl:variable name="count">
							<xsl:choose>
								<xsl:when test="not(../../curent_count)">
									<xsl:value-of select="count" />
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="../../curent_count" />
								</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>				
						<xsl:if test="$count!=0">
							<span id="count_{id}">
								(
								<b>
									<xsl:value-of select="$count" />
								</b>
								новых)
							</span>
						</xsl:if>
					</td>
					<td style="white-space:nowrap;">
						[
						<a href="/{//requests/get/path}?ADMIN&amp;DEL={id}" class="delete_ajax" title="Удалить">удалить</a>
						]
					</td>
				</tr>
				<tr>
					<td id="message_{id}" class="feedback" colspan="3" />
				</tr>
			</xsl:for-each>
		</table>
		<xsl:apply-templates select="pages" />

	</xsl:template>
</xsl:stylesheet>