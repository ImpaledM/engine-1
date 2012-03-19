<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:template match="mod_configs">
		<xsl:if test="//requests/get/ADMIN">
			<xsl:apply-templates select="defines" mode="configs" />
		</xsl:if>
	</xsl:template>
	<xsl:template match="defines" mode="configs">
		<div class="forms cfg">
			<form method="post">
				<fieldset>
					<dl>
						<xsl:for-each select="child::*">
							<div>
								<dt>
									<xsl:value-of select="name()" />
								</dt>
								<xsl:choose>
									<xsl:when test="count(item)>0">
										<dd>
											<input type="text" name="DEF[{name()}]" value="{item[@id=0]}">
												<xsl:if test="string-length(item[@id=0])&gt;25">
													<xsl:attribute name="style">width: 300px</xsl:attribute>
												</xsl:if>
											</input>
											&#160;
											<xsl:value-of select="item[@id=1]" />
											<input type="hidden" name="DEF_HID[{name()}]" value="{item[@id=1]}" />
										</dd>
									</xsl:when>
									<xsl:otherwise>
										<dd>
											<input type="text" name="DEF[{name()}]" value="{.}">
												<xsl:if test="string-length(.)>25">
													<xsl:attribute name="style">width: 300px</xsl:attribute>
												</xsl:if>
											</input>
										</dd>
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</xsl:for-each>
						<div>
							<dt></dt>
							<dd>
								<input type="submit" name="saveConfig" value="Сохранить настройки" />
							</dd>
						</div>
					</dl>
				</fieldset>
			</form>
		</div>
	</xsl:template>
</xsl:stylesheet>