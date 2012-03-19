<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:template match="mod_counters">
		<xsl:choose>
			<xsl:when test="add or edit">
				<div class="forms">
					<form method="post">
						<input type="hidden" name="safetyCounters" value="1" />
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
									<span class="star">*</span>
									Код счетчика:
								</dt>
								<dd>
									<textarea name="text" rows="10" cols="52">
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
			<xsl:otherwise>
				<xsl:apply-templates mode="counters" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list_admin" mode="counters">
		<script type="text/javascript" src="/engine/modules/admin/interface.js" />
		<script type="text/javascript" src="/engine/modules/admin/tree.js" />
		<script type="text/javascript" src="/engine/modules/counters/counters.js" />
		<a href="/counters/?ADMIN&amp;ADD">Добавить</a>
		<br />
		<br />
		<div id="div_tree">
			<ul id="tree">
				<xsl:variable name="cnt" select="count(item)" />
				<xsl:for-each select="item">
					<li class="sort" id="{id}">
						<xsl:value-of select="title" />

						<div style="width:212px; float: right;">
							<xsl:choose>
								<xsl:when test="active=1">
									[
									<a href="/counters/?ADMIN&amp;ACTIVE={id}" class="publish_ajax">скрыть</a>
									]
								</xsl:when>
								<xsl:otherwise>
									[
									<a href="/counters/?ADMIN&amp;ACTIVE={id}" class="publish_ajax">отобразить</a>
									]
								</xsl:otherwise>
							</xsl:choose>
							[
							<a href="/counters/?ADMIN&amp;EDIT={id}" class="edit">edit</a>
							]
							[
							<a href="/counters/?ADMIN&amp;DEL={id}" class="delete_ajax" title="Удалить">del</a>
							]
						</div>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>

	<xsl:template match="show">
		<xsl:if test="//DEBUG!=1">
			<noindex>
				<xsl:for-each select="item">
					<xsl:value-of select="text" disable-output-escaping="yes" />
				</xsl:for-each>
			</noindex>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>