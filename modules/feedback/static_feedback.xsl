<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output cdata-section-elements="script" doctype-public="-//W3C//DTD HTML 4.01//EN" doctype-system="http://www.w3.org/TR/html4/strict.dtd" encoding="UTF-8" indent="yes" media-type="html"
		method="html" standalone="no" />
	<xsl:template name="messages" match="messages">
		<xsl:if test="count(/root/messages/content/error)>0">
			<div class="mcontainer">
				<ul class="message error">
					<li style="padding-bottom: 10px !important;">
						<strong>ОШИБКА!</strong>
					</li>
					<xsl:for-each select="/root/messages/content/error/item">
						<li>
							<xsl:value-of select="." disable-output-escaping="yes" />
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</xsl:if>

		<xsl:if test="count(/root/messages/content/success)>0">
			<div class="mcontainer">
				<ul class="message success">
					<xsl:for-each select="/root/messages/content/success/item">
						<li>
							<xsl:value-of select="." disable-output-escaping="yes" />
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</xsl:if>

		<xsl:if test="count(/root/messages/content/notice)>0">
			<div class="mcontainer">
				<ul class="message notice">
					<xsl:for-each select="/root/messages/content/notice/item">
						<li>
							<xsl:value-of select="." disable-output-escaping="yes" />
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template match="root/form">
		<div style="width:650px; margin: auto;">
		<form action="/contacts#feedback_form" method="post">
			<div class="feedbackForm">
				<xsl:call-template name="messages" />
				<p>
					Поля, отмеченные
					<span class="star">*</span>
					обязательны для заполнения
				</p>
				<dl>

					<xsl:choose>
						<xsl:when test="fieldset/login">
							<input type="hidden" name="login" value="{fieldset/login}" />
							<input type="hidden" name="email" value="{fieldset/email}" />
						</xsl:when>
						<xsl:otherwise>
							<dt>Ваше имя</dt>
							<dd>
								<input type="text" id="r15" class="input-text" name="name" value="{fieldset/name}" />
							</dd>
							<dt>
								Ваш e-mail
								<span class="star">*</span>
							</dt>
							<dd>
								<input type="text" id="r16" class="input-text" name="email" value="{fieldset/email}" />
							</dd>
						</xsl:otherwise>
					</xsl:choose>

					<dt>
						Текст сообщения
						<span class="star">*</span>
					</dt>
					<dd>
						<textarea rows="5" name="text" limit="500">
							<xsl:value-of select="fieldset/text" />
						</textarea>
					</dd>

					<xsl:if test="not(fieldset/login!='')">
						<br />
						<dt>
							Защитный код
							<span class="star">*</span>
						</dt>
						<dd>
							<img id="fcpt" src="captcha.php?{time}" class="captcha" align="center" />
							<br />
							<a href="" class="reload_captcha">Обновить картинку</a>
							<br />
							<input type="text" id="r19" class="input-text" style="width: 100px;" name="captcha" />
						</dd>
					</xsl:if>
					<br />
					<dt>
						<input id="url" type="hidden" name="url" value="{fieldset/url}" />
					</dt>
					<dd style="text-align:center">
						<input type="submit" name="submit" class="submit" title="Отправить" value="Отправить сообщение" src="/img/btn/b21.gif" />
					</dd>
				</dl>
				<div class="clear"></div>
			</div>
		</form>
		</div>
	</xsl:template>
	<xsl:template match="//messages" />
</xsl:stylesheet>