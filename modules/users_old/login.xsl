<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_login" mode="login">
		<xsl:apply-templates mode="login" />
	</xsl:template>

	<xsl:template match="sub_login" mode="brief">
		<xsl:choose>
			<xsl:when test="user/id">
				<li class="first">
					<a href="/?logout">Выход</a>
				</li>
				<li class="last">
					<a href="/profile/">Профиль</a>
				</li>
				<li class="first">
					<a>
						<b>
							Здравствуйте,
							<xsl:value-of select="user/login" />
						</b>
					</a>
				</li>
			</xsl:when>
			<xsl:otherwise>
				<li class="first">
					<a href="/signup/">Регистрация</a>
				</li>
				<li class="last">
					<a href="/login/">Вход</a>
				</li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="form_login" mode="login">

		<div class="anonses">
			<div class="anonses_top">
				<div class="anonses_bot">
					<form method="post">
						<fieldset class="response">
							<p class="req">* поля обязательные для заполнения</p>

							<div class="form-item">
								<span class="star"> &#8727; </span>
								<label for="login"> Login</label>
								<input type="text" name="login" id="login" value="{//requests/post/login}" class="input-text" />
							</div>

							<div class="form-item">
								<span class='star'> &#8727; </span>
								<label for="password"> Пароль</label>
								<input type="password" name="password" id="password" class="input-text" />
							</div>

							<div class="form-item">
								<input name="remember" id="rem" type="checkbox" value="1">
									<xsl:if test="//requests/post/remember">
										<xsl:attribute name="checked" select="'1'" />
									</xsl:if>
								</input>
								<label for="rem"> Запомнить меня</label>
							</div>
							<xsl:if test="//config/ENABLE_CAPTCHA_SIGNUP=1">
								<div class="form-item">
									<div class="capcha">
										<img src="captcha.php" class="captcha" />
										<span>
											<a href="" class="reload_captcha">Обновить</a>
										</span>
										<span>Код подтверждения &#8727;</span>
										<input name="captcha" class="input-short" />
									</div>
								</div>
							</xsl:if>

							<div class="form-item">
								<input type="submit" name="save" value="Войти" class="input-submit" />
							</div>
						</fieldset>
					</form>
				</div>
			</div>
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