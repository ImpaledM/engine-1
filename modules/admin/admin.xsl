<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />

	<xsl:template name="admin_body">
		<header id="header">
			<hgroup>
				<h1 class="site_title">
					<a href="/">
						<xsl:value-of select="//content/mod_admin/meta/item/title" />
					</a>
				</h1>
			</hgroup>
		</header>
		<xsl:choose>
			<xsl:when test="//mod_users/sub_login/user/id">
				<section id="secondary_bar">
					<div class="user">
						<p>
							<xsl:apply-templates select="//sub_login" mode="admin" />
						</p>
					</div>
					<div class="breadcrumbs_container">
					<xsl:if test="//DEBUG=1">
						<article class="breadcrumbs">
							<xsl:choose>
								<xsl:when test="not(//requests/get/sort) and not(//requests/get/meta)">
									<a class="current">Редактирование</a>
								</xsl:when>
								<xsl:otherwise>
									<a href="{$get}?ADMIN">Редактирование</a>
								</xsl:otherwise>
							</xsl:choose>
							<div class="breadcrumb_divider"></div>
							<xsl:choose>
								<xsl:when test="//requests/get/sort">
									<a class="current">Сортировка</a>
								</xsl:when>
								<xsl:otherwise>
									<a href="{$get}?ADMIN&amp;sort">Сортировка</a>
								</xsl:otherwise>
							</xsl:choose>
							<div class="breadcrumb_divider"></div>
							<xsl:choose>
								<xsl:when test="//requests/get/meta">
									<a class="current">Мета-теги</a>
								</xsl:when>
								<xsl:otherwise>
									<a href="{$get}?ADMIN&amp;meta">Мета-теги</a>
								</xsl:otherwise>
							</xsl:choose>
						</article>
						</xsl:if>
					</div>
				</section>
				<aside id="sidebar" class="column">
					<hr />
					<xsl:apply-templates select="//content/mod_admin/sections" />
					<footer>
						<hr />
						<p>
							<strong>
								<xsl:value-of select="//content/mod_initialize/footer/item/copyright" disable-output-escaping="yes" />
							</strong>
						</p>
					</footer>
				</aside>
				<section id="main" class="column">
					<article class="module width_full">
						<xsl:choose>
							<xsl:when test="//requests/get/path!='admin'">
								<header>
									<h3 class="tabs_involved">
										<xsl:value-of select="//section/current_name" />
									</h3>
								</header>
								<xsl:apply-templates select="content" />
							</xsl:when>
							<xsl:otherwise>
								<header>
									<h3 class="tabs_involved">
										Добро пожаловать в панель управления
									</h3>
									<xsl:apply-templates select="content" />
								</header>
								<div class="control">
									Чтобы добавить \ редактировать \ удалить раздел, нажмите ПРАВУЮ кнопку мыши на списке разделов.
									<br />
									Выберите раздел ЛЕВОЙ кнопкой мыши для работы с ним.
								</div>
							</xsl:otherwise>
						</xsl:choose>
					</article>
					<div class="spacer"></div>
				</section>
			</xsl:when>
			<xsl:otherwise>
				<section>
					<xsl:apply-templates select="content" />
				</section>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="mod_admin" mode="head">
		<link href="/css/jquery.alerts.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="/engine/modules/admin/css/reset.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="/engine/modules/admin/css/layout.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="/engine/modules/admin/css/add.css" type="text/css" media="screen" />
		<xsl:comment>
<![CDATA[[if lt IE 9]>
    <link rel="stylesheet" href="/engine/modules/admin/css/ie.css" type="text/css" media="screen" />
    <script src="/engine/modules/admin/js/html5.js"></script>
    <![endif]]]></xsl:comment>
		<script type="text/javascript" src="/engine/js/jquery.js" />
		<script type="text/javascript" src="/engine/js/jquery.livequery.js" />
		<script type="text/javascript" src="/engine/js/jquery.alerts.js" />
		<script type="text/javascript" src="/engine/js/engine.js" />
		<script type="text/javascript" src="/engine/js/jquery.custom.js" />
		<script type="text/javascript" src="/engine/modules/admin/js/jquery.equalHeight.js" />
		<script type="text/javascript" src="/engine/modules/admin/js/admin.js" />
		<script type="text/javascript" src="/engine/modules/admin/js/custom.js" />
		<xsl:if test="//requests/get/path='admin'">
			<script type="text/javascript" src="/engine/modules/admin/js/interface.js" />
			<script type="text/javascript" src="/engine/modules/admin/js/tree.js" />
			<script type="text/javascript" src="/engine/modules/admin/js/jquery.contextMenu.js" />
			<script type="text/javascript" src="/engine/js/jquery.cookie.js" />
			<script type="text/javascript" src="/engine/modules/admin/js/admin_tree.js" />
			<script type="text/javascript" src="/engine/js/jquery.form.js" />
			<link rel="stylesheet" href="/engine/modules/admin/jquery.contextMenu.css" type="text/css" />
			<link rel="stylesheet" href="/engine/modules/admin/css/ui.custom.css" type="text/css" />
		</xsl:if>
	</xsl:template>

	<xsl:template match="mod_admin">
		<xsl:if test="//requests/get/REFRESH">
			<xsl:apply-templates select="sections" />
		</xsl:if>
	</xsl:template>

	 <xsl:template match="*" mode="admin"/>

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

		<xsl:if test="not(//requests/get/REFRESH) and //requests/get/path='admin'">
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
						<xsl:choose>
							<xsl:when test="//section/current_id=id">
								<a href="{path}/?ADMIN">
									<b>
										<xsl:value-of select="name" />
									</b>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<a href="{path}/?ADMIN">
									<xsl:value-of select="name" />
								</a>
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<xsl:apply-templates select="item" />
				</li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>