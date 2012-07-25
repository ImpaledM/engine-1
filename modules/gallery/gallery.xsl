<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />

	<xsl:template match="mod_gallery">
		<xsl:apply-templates mode="gallery" />
	</xsl:template>

	<xsl:template match="edit" mode="gallery">
		<script type="text/javascript" src="/engine/js/jquery.uploadify.js" />
		<script type="text/javascript" src="/engine/js/swfobject.js" />
		<script type="text/javascript" src="/modules/gallery/gallery_admin.js" />
		<div class="module_content">
			<form method="post">
				<fieldset>
					<label>
						<span class="star">*</span>
						Название альбома:
					</label>
					<input name="name" value="{item/name}" type="text" />

					<label>Обложка альбома:</label>
					<xsl:call-template name="upload_photo">
						<xsl:with-param name="xpath" select="'edit/item/'" />
						<xsl:with-param name="field" select="'photo_anons'" />
						<xsl:with-param name="module" select="'gallery'" />
					</xsl:call-template>

					<label>Фото:</label>
					<xsl:call-template name="upload_photo_multi">
						<xsl:with-param name="xpath" select="'edit/item/'" />
						<xsl:with-param name="field" select="'photo'" />
						<xsl:with-param name="module" select="'gallery'" />
					</xsl:call-template>
				</fieldset>

				<fieldset>
					<xsl:call-template name="saveButton">
						<xsl:with-param name="active" select="item/active" />
					</xsl:call-template>
				</fieldset>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="item" mode="gallery">
		<ul class="listing">
			<xsl:for-each select="photo/item">
				<li>
					<div class="pic">
						<a href="/800x600/gallery/{name}" class="cb" rel="gallery">
							<img src="/184x1000/gallery/{name}" />
						</a>
						<xsl:if test="note!=''">
							<div class="name">
								<a href="/800x600/gallery/{name}" class="cb" rel="gallery">
									<xsl:value-of select="note" />
								</a>
							</div>
						</xsl:if>
					</div>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>


	<xsl:template match="list" mode="gallery">
		<ul class="listing">
			<xsl:for-each select="item">
				<li>
					<div class="pic">

						<a href="/fotogalereya/?ITEM={id}">
							<img src="/184x1000/gallery/{photo_anons}" />
						</a>

						<div class="name">
							<a class="product_name" href="/fotogalereya/?ITEM={id}">
								<xsl:value-of select="name" />
							</a>
						</div>
					</div>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>

	<xsl:template match="list_admin" mode="gallery">
		<div class="control">
			<a href="{$get}?ADMIN&amp;ADD">
				<input type="submit" value="Добавить" />
			</a>
			<xsl:apply-templates select="pages" mode="digital" />
		</div>
		<xsl:if test="item">
			<div class="tab_container">
				<div class="tab_content" id="tab1" style="display: block;">
					<table cellspacing="0" class="tablesorter">
						<thead>
							<tr>
								<th style="width:200px;">Фото</th>
								<th>Заголовок</th>
								<th style="width:100px;">Действия</th>
							</tr>
						</thead>
						<tbody>
							<xsl:for-each select="item">
								<tr>
									<td>
										<img src="/1000x90/gallery/{photo_anons}" />
									</td>
									<td>
										<xsl:value-of select="name" />
									</td>
									<td>
										<xsl:choose>
											<xsl:when test="active=1">
												<a href="{$get}?ADMIN&amp;ACTIVE={id}" class="publish_ajax">
													<input type="image" title="Деактивировать" src="/engine/modules/admin/images/icn_pause.png" />
												</a>
											</xsl:when>
											<xsl:otherwise>
												<a href="{$get}?ADMIN&amp;ACTIVE={id}" class="publish_ajax">
													<input type="image" title="Активировать" src="/engine/modules/admin/images/icn_play.png" />
												</a>
											</xsl:otherwise>
										</xsl:choose>

										<a href="{$get}?ADMIN&amp;EDIT={id}" class="edit">
											<input type="image" title="Редактировать" src="/engine/modules/admin/images/icn_edit.png" />
										</a>
										<a href="{$get}?ADMIN&amp;DEL={id}" class="delete_ajax" title="Удалить">
											<input type="image" title="Удалить" src="/engine/modules/admin/images/icn_trash.png" />
										</a>
									</td>
								</tr>
							</xsl:for-each>
						</tbody>
					</table>
				</div>
			</div>
			<div class="control">
				<xsl:apply-templates select="pages" mode="digital" />
			</div>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>