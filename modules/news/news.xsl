<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:template match="mod_news">
		<script type="text/javascript" src="/engine/modules/news/news.js" />
		<xsl:choose>
			<xsl:when test="add or edit">
				<script language="javascript" type="text/javascript" src="/engine/tiny_mce/tiny_mce.js" />
				<script type="text/javascript" src="/engine/js/inittiny_admin.js" />
				<div class="forms">
					<form method="post">
						<fieldset>
							<dl>
								<dt>Новость видна:</dt>
								<dd>
									<input type="checkbox" name="sendall" value="1" id="sendall">
										<xsl:if test="edit/item/role=15 or not(edit/item/role)">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
									<label for="sendall"> Всем</label>
									&#160;&#160;&#160;
									&#160;&#160;&#160;
									<input type="checkbox" name="senduser" value="1" id="senduser">
										<xsl:if test="edit/item/role!=15 and ( edit/item/role mod ( 2 * 2 ) ) - ( edit/item/role mod ( 2 ) )">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
									<label for="senduser"> Пользователям</label>
									&#160;&#160;&#160;
									<input type="checkbox" name="sendnotuser" value="1" id="sendnotuser">
										<xsl:if test="edit/item/role!=15 and ( edit/item/role mod ( 8 * 2 ) ) - ( edit/item/role mod ( 8 ) )">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
									<label for="sendnotuser"> Гостям</label>
								</dd>



								<dt>
									<span class="star">*</span>
									Название:
								</dt>
								<dd>
									<input name="title" value="{edit/item/title}" class="input-text" />
								</dd>
								<dt>
									<span class="star">*</span>
									Анонс:
								</dt>
								<dd>
									<textarea name="description" class="noeditor" title="Small">
										<xsl:value-of select="edit/item/description" />
									</textarea>
								</dd>
								<dt>Дата: </dt>
								<dd>
									<input name="pubDate" class="date">
										<xsl:if test="edit/item/pubDate">
											<xsl:attribute name="value"> <xsl:value-of select="concat(substring(edit/item/pubDate,9,2), '.', substring(edit/item/pubDate,6,2), '.', substring(edit/item/pubDate,1,4))"></xsl:value-of> </xsl:attribute>
										</xsl:if>
									</input>
								</dd>
								<dt>
									<span class="star">*</span>
									Описание:
								</dt>
								<dd>
									<textarea name="text" class="editor" title="Small">
										<xsl:value-of select="edit/item/text" />
									</textarea>
								</dd>
								<dt>
								</dt>
								<dd>
									<xsl:call-template name="saveButton">
										<xsl:with-param name="active" select="edit/item/active" />
									</xsl:call-template>

								</dd>
							</dl>
						</fieldset>
					</form>
				</div>
			</xsl:when>
			<xsl:when test="item">
				<div>
					<h2>
						<xsl:value-of select="item/title" />
					</h2>
					<dl class="news">
						<dt>
						</dt>
						<dd>
							<xsl:value-of select="item/text" disable-output-escaping="yes" />
						</dd>
					</dl>
				</div>
			</xsl:when>

			<xsl:when test="list_admin">
				<xsl:apply-templates select="list_admin" mode="news" />
			</xsl:when>



			<xsl:otherwise>
				<xsl:apply-templates select="list" mode="news" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list_admin" mode="news">
		<a href="{$get}?ADMIN&amp;ADD">Добавить</a>

		<table class="list_admin" cellspacing="1" width="99%">
			<xsl:for-each select="item">
				<tr>
					<xsl:if test="position() mod 2 = 0">
						<xsl:attribute name="class">even</xsl:attribute>
					</xsl:if>
					<td>
						<a href="{$get}?ITEM={id}">
							<xsl:value-of select="title" />
						</a>
					</td>
					<td width="260">
						<xsl:choose>
							<xsl:when test="active=1">
								[
								<a href="{$get}?ADMIN&amp;ACTIVE={id}" class="publish_ajax">скрыть</a>
								]
							</xsl:when>
							<xsl:otherwise>
								[
								<a href="{$get}?ADMIN&amp;ACTIVE={id}" class="publish_ajax">отобразить</a>
								]
							</xsl:otherwise>
						</xsl:choose>

						[
						<a href="{$get}?ADMIN&amp;EDIT={id}" class="edit">редактировать</a>
						]

						[
						<a href="{$get}?ADMIN&amp;DEL={id}" class="delete_ajax" title="Удалить">удалить</a>
						]
					</td>
				</tr>
			</xsl:for-each>
		</table>
		<xsl:apply-templates select="pages" />
	</xsl:template>

	<xsl:template match="list" mode="news">
		<xsl:choose>
			<xsl:when test="item">
				<xsl:for-each select="item">
					<a>
						<xsl:call-template name="translit">
							<xsl:with-param name="id" select="id" />
							<xsl:with-param name="alias" select="alias" />
						</xsl:call-template>
						<h3>
					<span style="font-size:10px; color: #aaa; padding-right: 10px;">
						<xsl:call-template name="rusDate">
							<xsl:with-param name="date" select="pubDate" />
						</xsl:call-template>
					</span>
							<xsl:value-of select="title" />
						</h3>
					</a>
					<br />
				</xsl:for-each>
			</xsl:when>
		</xsl:choose>
		<xsl:apply-templates select="pages" />
	</xsl:template>

	<xsl:template name="brief_news">
		<ul>
			<xsl:for-each select="//mod_news/brief_list/item">

				<li>
					<span class="date">
						<xsl:call-template name="rusDate">
							<xsl:with-param name="date" select="pubDate" />
						</xsl:call-template>
					</span>
					<a>
						<xsl:call-template name="translit">
							<xsl:with-param name="id" select="id" />
							<xsl:with-param name="alias" select="alias" />
							<xsl:with-param name="get" select="'/news/'" />
						</xsl:call-template>
							<xsl:value-of select="title" />
					</a>
				</li>

			</xsl:for-each>
			<div class="clear"></div>
		</ul>
	</xsl:template>
</xsl:stylesheet>