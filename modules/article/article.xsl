<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:template match="mod_article">
		<xsl:if test="//requests/get/ADMIN">
			<script type="text/javascript" src="/engine/js/jquery.uploadify.js" />
			<script type="text/javascript" src="/engine/js/swfobject.js" />
			<script type="text/javascript" src="/engine/modules/admin/interface.js" />
			<script type="text/javascript" src="/engine/modules/admin/tree.js" />
			<script type="text/javascript" src="/engine/modules/article/article.js" />
		</xsl:if>
		<xsl:choose>
			<xsl:when test="add or edit">
				<script language="javascript" type="text/javascript" src="/engine/tiny_mce/tiny_mce.js" />
				<script type="text/javascript" src="/engine/js/inittiny_admin.js" />
				<div class="forms">
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
								<dt> Анонс: </dt>
								<dd>
									<textarea name="anons" title="Small" rows="5">
										<xsl:value-of select="edit/item/anons" />
									</textarea>
								</dd>
								<dt> Автор: </dt>
								<dd>
									<input name="author" value="{edit/item/author}" class="input-text" />
								</dd>
								<xsl:if test="//requests/get/tags">
									<dt> Теги: </dt>
									<dd>
										<input name="tags" value="{edit/item/tags}" class="input-text" />
									</dd>
								</xsl:if>
								<dt>
									<span class="star">*</span>
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
										<xsl:with-param name="module" select="'article'" />
									</xsl:call-template>
								</dd>
								<dt> Фото: </dt>
								<dd>
									<xsl:call-template name="upload_photo_multi">
										<xsl:with-param name="xpath" select="'edit/item/'" />
										<xsl:with-param name="field" select="'photo'" />
										<xsl:with-param name="module" select="'article'" />
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
				<div class="article">
					<xsl:apply-templates select="item" mode="show_article" />
				</div>
			</xsl:when>
			<xsl:when test="list_admin">
				<xsl:apply-templates select="list_admin" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="item" mode="show_article">
		<div class="anonses">
			<div class="anonses_top">
				<div class="anonses_bot">
					<div class="book-text">
						<xsl:if test="count(../list/item)!=1">
							<h2>
								<xsl:value-of select="title" />
							</h2>
							<br />
						</xsl:if>
						<xsl:if test="author[text()!='']">
							<div style="text-align:right; color:gray; padding: 20px 20px 20px 0">
								Автор статьи:
								<b>
									<xsl:value-of select="author" />
								</b>
							</div>
						</xsl:if>

						<xsl:value-of select="text" disable-output-escaping="yes" />
					</div>
					<xsl:if test="count(//photo/item)>0">
						<div class="book-photo">
							<xsl:for-each select="//photo/item">
								<span>
									<a href="/800x600/article/{name}" class="lb" rel="lb" title="{note}">
										<img src="/120x80/article/{name}" alt="{note}" />
										<i class="lt"></i>
										<i class="rt"></i>
										<i class="lb"></i>
										<i class="rb"></i>
									</a>
								</span>
							</xsl:for-each>
						</div>
					</xsl:if>
				</div>
			</div>
		</div>
	</xsl:template>


	<xsl:template match="list">
		<xsl:choose>
			<xsl:when test="count(item)=1">
				<xsl:apply-templates select="item" mode="show_article" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="item">
						<xsl:for-each select="item">
							<p>
								<xsl:if test="photo_anons!=''">
									<img src="/100x200/article/{photo_anons}" align="left" style="padding-right:10px;" />
								</xsl:if>
								<h3 class="list">
									<xsl:value-of select="concat(position(), '. ')" />
									<a>
										<xsl:call-template name="translit">
											<xsl:with-param name="id" select="id" />
											<xsl:with-param name="alias" select="alias" />
										</xsl:call-template>


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


	<xsl:template match="list_admin">
		<a href="{$get}?ADMIN&amp;ADD">Добавить</a>
		<br />
		<br />
		<xsl:apply-templates select="pages" />
		<div id="div_tree">
			<ul id="tree">
				<xsl:variable name="cnt" select="count(item)" />
				<xsl:for-each select="item">
					<li class="sort" id="{id}">
						<a>
							<xsl:choose>
								<xsl:when test="alias!=''">
									<xsl:attribute name="href">
													<xsl:value-of select="concat($get, id,'-', alias)" />
												</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="href">
													<xsl:value-of select="concat($get, '?ITEM=', id)" />
												</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>

							<xsl:value-of select="title" />
						</a>

						<div style="width:212px; float: right;">
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
							<a href="{$get}?ADMIN&amp;EDIT={id}" class="edit">edit</a>
							]
							[
							<a href="{$get}?ADMIN&amp;DEL={id}" class="delete_ajax" title="Удалить">del</a>
							]
						</div>
					</li>
				</xsl:for-each>
			</ul>
		</div>
		<xsl:apply-templates select="pages" />
	</xsl:template>



</xsl:stylesheet>