<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:include href="./../xsl/templates.xsl" />
	<xsl:include href="#ROOT#xsl/templates.xsl" />

	<xsl:template match="mod_feedback">
		<xsl:apply-templates mode="feedback" />
	</xsl:template>

	<xsl:template match="*" mode="feedback" />

	<xsl:template match="form" mode="feedback">
		<xsl:choose>
			<xsl:when test="//messages/content/error">
				<xsl:call-template name="messages" />
				<xsl:apply-templates select="fieldset" mode="feedback" />
			</xsl:when>
			<xsl:when test="//messages/content/success">
				<xsl:call-template name="messages" />
			</xsl:when>
			<xsl:otherwise>
				<script type="text/javascript" src="/engine/js/jquery.form.js" />
				<div class="popup" style="display:none">
					<i class="close" />
					<div class="popup-fon">
						<div class="popup-in link-friend" style="width:500px;">
							<h2>Обратная связь</h2>
							<form action="__feedback" method="post" id="sendMess">
								<input type="hidden" name="responseType" value="html" />
								<xsl:apply-templates select="fieldset" mode="feedback" />
							</form>
						</div>
					</div>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="fieldset" mode="feedback">
		<fieldset class="forma">
			<span id="error_place" />
			<p>
				Поля, отмеченные
				<span class="star">* </span>
				обязательны для заполнения
			</p>
			<xsl:choose>
				<xsl:when test="login and email!=''">
					<input type="hidden" name="login" value="{login}" />
					<input type="hidden" name="email" value="{email}" />
				</xsl:when>
				<xsl:when test="login and email=''">
					<input type="hidden" name="login" value="{login}" />
					<dl>
						<dt>
							<label for="r16">
								<div style="width:100px; float:left">
									<span class="star">*</span>
									Ваш email
								</div>
							</label>
							<input type="text" id="r16" class="input-text" name="email" value="{email}" />
						</dt>
						<dd style="line-height: 5px;">
							&#160; </dd>
					</dl>
				</xsl:when>
				<xsl:otherwise>
					<dl>
						<dd>
							&#160; </dd>
						<dt>
							<label for="r15">
								<div style="width:100px; float:left">
									Ваше имя
								</div>
							</label>
							<input type="text" id="r15" class="input-text" name="name" value="{name}" />
						</dt>
						<dd style="line-height: 5px;">
							&#160; </dd>
						<dt>
							<label for="r16">
								<div style="width:100px; float:left">
									<span class="star">*</span>
									Ваш email
								</div>
							</label>
							<input type="text" id="r16" class="input-text" name="email" value="{email}" />
						</dt>
						<dd style="line-height: 5px;">
							&#160; </dd>
					</dl>
				</xsl:otherwise>
			</xsl:choose>

			<dl>
				<dt>
					<label for="r18">
						<span class="star">*</span>
						Сообщение
					</label>
				</dt>
				<dd>
					<label>
						<textarea rows="4" cols="" name="text" id="r18" limit="500" style="display:block">
							<xsl:value-of select="text" />
						</textarea>
					</label>
				</dd>
			</dl>
			<xsl:if test="not(login)">
				<dl>
					<dt>

						<label for="r19">
							<span class="star">*</span>
							Защитный код
						</label>
					</dt>
					<dd>
						<img id="fcpt" src="captcha.php?{time}" class="captcha" align="center" />
						<input type="text" id="r19" class="input-text" style="width: 100px;" name="captcha" />
						<br />
						<a href="" class="reload_captcha">Обновить картинку</a>
						<br />
					</dd>
				</dl>
			</xsl:if>
			<dl>
				<dt>
					<input id="url" type="hidden" name="url" value="{url}" />
				</dt>
				<dd style="text-align:center">
					<input type="submit" class="submit" title="Отправить" value="Отправить" src="/img/btn/b21.gif" />
				</dd>
			</dl>
			<p class="protect"></p>
		</fieldset>
	</xsl:template>

	<xsl:template match="item" mode="feedback">

		<xsl:variable name="id_parent">
			<xsl:value-of select="item/id" />
		</xsl:variable>
		<xsl:for-each select="item">
			<div class="feedback_view{view}">
				<xsl:value-of select="date" />
				<br />
				<xsl:call-template name="nl2br">
					<xsl:with-param name="string" select="text" />
				</xsl:call-template>
				<br />
				<div class="reply_{id}">
					<xsl:choose>
						<xsl:when test="reply!=''">
							<div style="margin-left:20px;">
								<i>
									<xsl:value-of select="reply_date" />
									<br />
									<xsl:call-template name="nl2br">
										<xsl:with-param name="string" select="reply" />
									</xsl:call-template>
								</i>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<a class="reply" title="{email}" href="" id="{id}">Ответить</a>
						</xsl:otherwise>
					</xsl:choose>
				</div>
				<hr />
			</div>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="reply_form" mode="feedback">
		<script type="text/javascript" src="/engine/js/jquery.form.js" />
		<form method="post" class="reply_form" id="{id}" title="{email}">
			<fieldset>
				<dt>
					<label for="reply_{id}">
						Ответ
					</label>
				</dt>
				<dd>
					<label>
						<textarea rows="4" cols="" name="text" id="reply_{id}" style="border:1px solid #000;" />
					</label>
				</dd>
			</fieldset>
			<input type="submit" class="btn" title="Ответить" value="Ответить" src="/img/btn/b21.gif" />
		</form>
	</xsl:template>
</xsl:stylesheet>