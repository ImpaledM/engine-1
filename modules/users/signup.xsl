<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_signup">
		<xsl:apply-templates mode="signup" />
	</xsl:template>

	<xsl:template match="edit" mode="signup">
		<script type="text/javascript" src="/engine/modules/users/signup.js" />
		<form method="post" id="form_login_basic">
			<fieldset>
				<i>
					<span class="star"> &#8727; </span>
					поля обязательные для заполнения
				</i>
				<dl>
					<dt>
						<label for="nick"> Ник :</label>
						<span id="nick_status"></span>
					</dt>
					<dd>
						<input type="text" name="nick" id="nick" value="{//requests/post/nick}" />
					</dd>

					<dt>
						<label for="login">
							E-mail
							<span class="star"> &#8727; </span>
							:
						</label>
					</dt>
					<dd>
						<input type="text" name="email" id="email" value="{//requests/post/email}" />
					</dd>

					<dt>

						<label for="password">
							Пароль
							<span class='star'> &#8727; </span>
							:
						</label>
					</dt>
					<dd>
						<input type="password" name="password" id="password" />
					</dd>

					<dt>
						<label for="repassword">
							Пароль (повтор)
							<span class='star'> &#8727; </span>
							:
						</label>
					</dt>
					<dd>
						<input type="password" name="repassword" id="repassword" />
					</dd>
					<dt></dt>
					<dd>
						<input type="submit" name="save" value="Зарегистрироваться" />
					</dd>
				</dl>
			</fieldset>
		</form>
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