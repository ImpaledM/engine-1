<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />
	<xsl:template match="mod_admin">
		<xsl:if test="not(//requests/get/REFRESH)">
			<script type="text/javascript" src="/engine/js/jquery.form.js" />
			<script type="text/javascript" src="/engine/modules/admin/interface.js" />
			<script type="text/javascript" src="/engine/modules/admin/tree.js" />
			<script type="text/javascript" src="/engine/modules/admin/jquery.contextMenu.js" />
			<script type="text/javascript" src="/engine/modules/admin/admin.js" />
			<link rel="stylesheet" href="/engine/modules/admin/jquery.contextMenu.css"	type="text/css" />
			<link rel="stylesheet" href="/engine/modules/admin/admin.css" type="text/css" />
			<link rel="stylesheet" href="/css/ui.custom.css" type="text/css" />
		</xsl:if>
		<xsl:apply-templates select="sections" />
	</xsl:template>

	<xsl:template match="sections">
		<xsl:if test="not(//requests/get/REFRESH)">
			<div id="dialog" style="display:none;">
				<form id="options-form" method="post">
					<div id="tabs">
						<ul>
							<li>
								<a href="#dialog_main">
									<span>Основные</span>
								</a>
							</li>
							<li>
								<a href="#dialog_options">
									<span>Настройка</span>
								</a>
							</li>
							<li>
								<a href="#dialog_meta_tags">
									<span>Мета тэги</span>
								</a>
							</li>
						</ul>
						<div id="dialog_message" />
					</div>
				</form>
			</div>
		</xsl:if>

		<div id="div_tree">
			<ul id="tree">
				<li id="0" class="sort">
					<div class="sections">Корневой раздел</div>
					<xsl:apply-templates select="item" />
				</li>
			</ul>
		</div>

		<xsl:if test="not(//requests/get/REFRESH)">
			<div id="fon" class="ui-widget-overlay" style="display:none"></div>
			<ul id="myMenu" class="contextMenu">
				<li>
					&#160;
					<b class="name">name</b>
					<hr />
				</li>
				<li class="edit">
					<a href="#edit">Редактировать</a>
				</li>
				<li class="copy">
					<a href="#new">Создать</a>
				</li>
				<li class="delete">
					<a href="#delete">Удалить</a>
				</li>
				<li class="quit separator">
					<a href="#cancel">Отменить</a>
				</li>
			</ul>
		</xsl:if>
	</xsl:template>

	<xsl:template match="item">
		<xsl:choose>
			<xsl:when test="@id=0">
				<ul id="tree">
					<xsl:apply-templates select="item" />
				</ul>
			</xsl:when>
			<xsl:otherwise>
				<li class="sort" id="{id}" alt="{name}">
					<div class="sections">
						<xsl:value-of select="name" />
					</div>
					<xsl:apply-templates select="item" />
				</li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>