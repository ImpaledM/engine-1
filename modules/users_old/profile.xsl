<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_profile">
		<xsl:apply-templates mode="profile" />
	</xsl:template>

	<xsl:template match="edit" mode="profile">
		<script type="text/javascript" src="/engine/js/swfobject.js" />
		<script type="text/javascript" src="/engine/js/jquery.uploadify.js" />
		<script type="text/javascript" src="/engine/modules/users/profile.js" />
		<div class="anonses">
			<div class="anonses_top">
				<div class="anonses_bot">
					<h3>
						Профиль пользователя
          </h3>
					<form method="post">
						<fieldset class="response">
							<p class="req">* поля обязательные для заполнения</p>

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
								<input type="text" name="signature" id="signature" value="{../edit/item/signature}" class="input-text" limit="100" />
							</div>

							<div class="form-item">
								<span class='star'> &#160;&#160; </span>
								<label> Аватар</label>
								<xsl:call-template name="upload_photo">
									<xsl:with-param name="xpath" select="'sub_profile/edit/item/'" />
									<xsl:with-param name="field" select="'foto'" />
									<xsl:with-param name="module" select="'users'" />
									<xsl:with-param name="path" select="item/path" />
								</xsl:call-template>
							</div>

							<div class="form-item">
								<input type="hidden" name="EDIT" value="{//mod_users/sub_login/user/id}" />
								<input type="submit" name="save" value="продолжить" class="input-submit" />
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>