<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />
	<xsl:include href="#ROOT#/xsl/templates.xsl" />

	<xsl:template match="mod_login">
		<xsl:apply-templates mode="login" />
	</xsl:template>

	<xsl:template match="*" mode="login" />

	<xsl:template match="messages" mode="login">
		<xsl:apply-templates select="." />
	</xsl:template>

	<xsl:template match="form_login" mode="login">
		<div id="login_overlay" action="">
			<div id="login_message" />
			<form id="form_login_overlay" method="post">
				<fieldset>
					<dl>
						<dt> Email: </dt>
						<dd>
							<input type="text" name="email" value="" />
						</dd>
						<dt> Пароль: </dt>
						<dd>
							<input type="password" name="password" value="" />
						</dd>
						<dt></dt>
						<dd>
							<input id="enter" type="button" name="save" value="Войти" />
						</dd>
					</dl>
				</fieldset>
			</form>
		</div>
	</xsl:template>

</xsl:stylesheet>