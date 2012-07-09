<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_signup">
		<xsl:apply-templates mode="signup" />
	</xsl:template>

	<xsl:template match="edit" mode="signup">
		<script type="text/javascript" src="/engine/modules/users/signup.js" />
		<div class="anonses">
			<div class="anonses_top">
				<div class="anonses_bot">
					<form method="post">
						<fieldset class="response">
							<p class="req">* поля обязательные для заполнения</p>
							<div class="form-item">
								<span class="star"> &#8727; </span>
								<label for="login"> Логин (от 3 симв)</label>
								<span id="login_status"></span>
								<input type="text" name="login" id="login" value="{//requests/post/login}" class="input-text" />
							</div>

							<div class="form-item">
								<span class="star"> &#8727; </span>
								<label for="login"> E-mail</label>
								<input type="text" name="email" id="email" value="{//requests/post/email}" class="input-text" />
							</div>

							<div class="form-item">
								<span class='star'> &#8727; </span>
								<label for="password"> Пароль</label>
								<input type="password" name="password" id="password" class="input-text" />
							</div>

							<div class="form-item">
								<span class='star'> &#8727; </span>
								<label for="repassword"> Пароль (повтор)</label>
								<input type="password" name="repassword" id="repassword" class="input-text" />
							</div>

							<div class="form-item">
								<span class='star'> &#160;&#160; </span>
								<label for="signature"> Подпись в отзывах</label>
								<input type="text" name="signature" id="signature" value="{//requests/post/signature}" class="input-text" limit="100" />
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
								<input type="submit" name="save" value="продолжить" class="input-submit" />
							</div>
						</fieldset>
					</form>

				</div>
			</div>
		</div>
	</xsl:template>

	<xsl:template match="signup_info" mode="signup">
		<div class="mcontainer">
			<ul class="message notice">
				<li>
					Для завершения регистрации
					<i class="pen">
						нажмите на ссылку в письме, которое мы отправили на
						<b>
							<xsl:value-of select="." />
						</b>
					</i>
				</li>
				<li>
					<p class="small">
						<b>Не получили письмо?</b>
						Проверьте папку "Спам/Сомнительные" вашего почтового ящика - наше письмо могло по ошибке попасть туда. Если это так,
						отметьте его как "Не спам" и
						оно автоматически перейдет в папку "Входящие".
					</p>
					<i>Если все таки не удалось получить письмо, свяжитесь с поддержкой</i>
				</li>
			</ul>
		</div>

	</xsl:template>

</xsl:stylesheet>