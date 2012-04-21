<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_login" mode="brief_login">
		<xsl:choose>
			<xsl:when test="user/id">
				<b><xsl:value-of select="concat(user/first_name,' ',user/last_name)" /></b>
				&#160;&#160;
					<a href="/?logout">Выход</a>
			</xsl:when>
			<xsl:otherwise>
				Войти:&#160;
				<script src="http://ulogin.ru/js/ulogin.js" />
				<div id="uLogin"
					x-ulogin-params="display=small&amp;fields=first_name,last_name,photo&amp;providers=vkontakte,odnoklassniki,mailru,facebook&amp;hidden=twitter,google,yandex,livejournal,openid&amp;redirect_uri=http%3A%2F%2Fazovskaya-riviera.com.ua" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="sub_login" mode="admin">
		<xsl:choose>
			<xsl:when test="user/nick!=''">
				<xsl:value-of select="user/nick" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="concat(user/first_name,' ', user/last_name)" />
			</xsl:otherwise>
		</xsl:choose>
		<span style="float:right;">
			<a href="/?logout">Выход</a>
		</span>
	</xsl:template>

	<xsl:template match="form_login" mode="login">
		<div class="login">
			<article class="module">
				<header>
					<h3>Вход</h3>
				</header>
				<form id="form_login" method="post" action="/login/">
					<div class="module_content">
						<fieldset>
							<label for="nick">Логин</label>
							<input id="nick" type="text" name="nick" />
							<label for="password">Пароль</label>
							<input type="password" name="password" id="password" />
						</fieldset>
						<div class="clear"></div>
					</div>
					<footer>
						<div class="submit_link">
							<input type="submit" value="Войти" name="save" />
						</div>
					</footer>
				</form>
			</article><!-- end of post new article -->
			<div class="spacer"></div>
		</div>
	</xsl:template>

	<xsl:template match="form_change_password" mode="login">
		<div class="frame">
			<div class="line">
				<h2>Восстановление доступа</h2>
			</div>
			<form method="post" id="signInForm">

				<div class="line">
					<label for="login" class="field-name">
						YPIN или Email:
					</label>
					<span class="field-value">
						<xsl:choose>
							<xsl:when test="//messages/login">
								<div class="messageError">
									<xsl:value-of select="//messages/login/error/item" />
								</div>
								<input name="login" id="login" value="{//requests/post/login}" type="text" class="inputField inputError" />
								&#160;
								<i class="ico-attention"></i>
							</xsl:when>
							<xsl:otherwise>
								<input name="login" id="login" value="{//requests/post/login}" type="text" class="inputField" />
								<xsl:if test="//requests/post/login">
									&#160;
									<i class="ico-ok"></i>
								</xsl:if>
							</xsl:otherwise>
						</xsl:choose>
					</span>
				</div>

				<div class="line">
					<span class="field-name"></span>
					<span class="field-value">
						<button type="submit" name="save" class="btn">Отправить</button>
					</span>
				</div>

				<div class="line">
					<span class="field-name"></span>
					<span class="field-value">
						На ваш email будет выслана ссылка для изменения
						пароля
					</span>
				</div>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="form_new_password" mode="login">
		<div class="frame">
			<div class="line">
				<h2>Смена пароля</h2>
			</div>
			<form method="post" id="signInForm">

				<div class="line">
					<label for="password" class="field-name">
						Пароль:
						<span class="star">*</span>
					</label>
					<xsl:choose>
						<xsl:when test="//messages/password">
							<span class="field-value">
								<div class="messageError">
									<xsl:value-of select="//messages/password/error/item" />
								</div>
								<input name="password" id="password" type="password" value="{//requests/post/password}" class="inputField inputError" />
								&#160;
								<i class="ico-attention"></i>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<span class="field-value">
								<input name="password" id="password" type="password" value="{//requests/post/password}" class="inputField" />
								<xsl:if test="//requests/post/password">
									&#160;
									<i class="ico-ok"></i>
								</xsl:if>
							</span>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<div class="line">
					<label for="repassword" class="field-name">
						Повтор пароля:
						<span class="star">*</span>
					</label>
					<xsl:choose>
						<xsl:when test="//messages/repassword">
							<span class="field-value">
								<div class="messageError">
									<xsl:value-of select="//messages/repassword/error/item" />
								</div>
								<input name="repassword" id="repassword" type="password" value="{//requests/post/repassword}" class="inputField inputError" />
								&#160;
								<i class="ico-attention"></i>
							</span>
						</xsl:when>
						<xsl:otherwise>
							<span class="field-value">
								<input name="repassword" id="repassword" type="password" value="{//requests/post/repassword}" class="inputField" />
								<xsl:if test="//requests/post/repassword">
									&#160;
									<i class="ico-ok"></i>
								</xsl:if>
							</span>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<div class="line">
					<span class="field-name"></span>
					<span class="field-value">
						<button type="submit" name="save" class="btn">Сменить</button>
					</span>
				</div>
			</form>
		</div>
	</xsl:template>

</xsl:stylesheet>