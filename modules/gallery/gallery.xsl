<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:template match="mod_gallery">
		<xsl:if test="//requests/get/ADMIN">

			<script type="text/javascript" src="/engine/js/jquery.uploadify.js" />
			<script type="text/javascript" src="/engine/js/swfobject.js" />
			<script type="text/javascript" src="/engine/modules/gallery/gallery.js" />
		</xsl:if>
		<xsl:choose>
			<xsl:when test="add or edit">
				<div class="forms" style="width:95%">
					<form method="post">
						<fieldset>
							<dl>
								<dt>
									<span class="star">*</span>
									Название:
								</dt>
								<dd>
									<input name="title" value="{edit/item/title}" class="input-text" />
								</dd>
								<dt>
									Сортировка:
								</dt>
								<dd>
									<input name="sort" value="{edit/item/sort}" class="input" />
								</dd>
								<dt> Анонс: </dt>
								<dd>
									<textarea name="anons" title="Small" rows="5">
										<xsl:value-of select="edit/item/anons" />
									</textarea>
								</dd>

								<dt>
									Описание:
								</dt>
								<dd>
									<textarea name="text" class="editor" title="Small">
										<xsl:value-of select="edit/item/text" />
									</textarea>
								</dd>
								<dt> Фото анонса: </dt>
								<dd>
									<xsl:call-template name="upload_photo">
										<xsl:with-param name="xpath" select="'edit/item/'" />
										<xsl:with-param name="field" select="'photo_anons'" />
										<xsl:with-param name="module" select="'gallery'" />
									</xsl:call-template>
								</dd>
								<dt> Фото: </dt>
								<dd style="width: 100%">
									<xsl:call-template name="upload_photo_multi">
										<xsl:with-param name="xpath" select="'edit/item/'" />
										<xsl:with-param name="field" select="'photo'" />
										<xsl:with-param name="module" select="'gallery'" />
									</xsl:call-template>
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
				<xsl:apply-templates select="item" mode="show_gallery" />


			</xsl:when>
			<xsl:when test="list_admin">
				<xsl:call-template name="list_admin" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="list" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="item" mode="show_gallery">
		<div class="clear" />
		<div>
			<h3>
				<xsl:value-of select="title" />
			</h3>
			<xsl:if test="text!=''">
				<ul class="article">
					<li>
						<xsl:value-of select="text" disable-output-escaping="yes" />
					</li>
				</ul>
			</xsl:if>
			<xsl:if test="count(photo/item)>0">
				<div class="gallery">
					<ul>
						<xsl:for-each select="photo/item">
							<li>
								<a href="/800x600/gallery/{name}" class="lb" style="width:100px;" title="{note}">
									<div style="text-align:center">
										<img src="/100x75/gallery/{name}" align="center" alt="{note}" />
									</div>
								</a>
								
<!--								<span class="photo"><span>-->
<!--								<a href="/800x600/gallery/{name}" class="lb" style="width:100px;" title="{note}">-->
<!--										<img src="/100x75/gallery/{name}" align="center" alt="{note}" />-->
<!--								</a>-->
<!--								-->
<!--								</span></span>-->
							</li>
						</xsl:for-each>
					</ul>
				</div>
			</xsl:if>
		</div>
	</xsl:template>


	<xsl:template name="list">
		<xsl:choose>
			<xsl:when test="count(list/item)=1">
				<xsl:apply-templates select="list/item" mode="show_gallery" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="list/item">
						<xsl:for-each select="list/item">
							<p>
								<xsl:if test="photo_anons!=''">
									<img src="/100x200/gallery/{photo_anons}" align="left" style="padding-right:10px;" />
								</xsl:if>
								<h3>
									<a href="/gallery/?ITEM={id}">
										<xsl:value-of select="title" />
									</a>
								</h3>
								<xsl:value-of select="anons" disable-output-escaping="yes" />
								<div class="clear"></div>
							</p>
							<br />
						</xsl:for-each>
					</xsl:when>
				</xsl:choose>
				<xsl:apply-templates select="pages" />
			</xsl:otherwise>


		</xsl:choose>
	</xsl:template>

	<xsl:template name="list_admin">
		<a href="{$get}?ADMIN&amp;ADD">Добавить</a>
		<br />
		<br />
		<xsl:apply-templates select="list_admin/pages" />
		<table class="list_admin" cellspacing="1">
			<xsl:variable name="cnt" select="count(list_admin/item)" />
			<xsl:for-each select="list_admin/item">
				<tr>
					<xsl:if test="position() mod 2 = 0">
						<xsl:attribute name="class">even</xsl:attribute>
					</xsl:if>
					<td>
						<xsl:value-of select="sort" />
					</td>
					<td width="300">
						<a>
							<xsl:choose>
								<xsl:when test="$cnt=1">
									<xsl:attribute name="href"><xsl:value-of select="$get" /></xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="href"><xsl:value-of select="concat($get,'?ITEM=',id)" /></xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>

							<xsl:value-of select="title" />
						</a>
					</td>
					<td>
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
						-
						[
						<a href="{$get}?ADMIN&amp;EDIT={id}" class="edit">редактировать</a>
						]
						-
						[
						<a href="{$get}?ADMIN&amp;DEL={id}" class="delete_ajax" title="Удалить">удалить</a>
						]
					</td>
				</tr>
			</xsl:for-each>
		</table>
		<xsl:apply-templates select="list_admin/pages" />
	</xsl:template>

</xsl:stylesheet>