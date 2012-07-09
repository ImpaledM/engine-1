<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />

	<xsl:template match="mod_admin_menu">
		<xsl:if test="( //mod_users/sub_login/user/role mod ( 1 * 2 ) ) - ( //mod_users/sub_login/user/role mod ( 1 ) ) and admin_menu='show'">
			<link rel="stylesheet" href="/engine/modules/admin_menu/admin_menu.css" type="text/css" />
			<link rel="stylesheet" type="text/css" href="/css/jquery.slidemenu.css" />
			<!--[if lte IE 7]> <style type="text/css"> html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/ </style> <![endif] -->
			<script type="text/javascript" src="/engine/js/jquery.slidemenu.js" />
			<script type="text/javascript" src="/engine/js/jquery.cookie.js" />
			<script type="text/javascript" src="/engine/modules/admin_menu/admin_menu.js" />
			<div class="admin_menu">
				<div class="cont_menu">
					<div id="myslidemenu" class="jqueryslidemenu">
						<ul>
							<li>
								<xsl:choose>
									<xsl:when test="//requests/get/ADMIN">
										<a href="{$get}">
											<xsl:choose>
												<xsl:when test="$get='/1/'">
													<xsl:attribute name="href">/</xsl:attribute>
												</xsl:when>
												<xsl:otherwise>
													<xsl:choose>
														<xsl:when test="//requests/get/EDIT">
															<xsl:attribute name="href"><xsl:value-of select="concat($get, '?ITEM=', //requests/get/EDIT)" /></xsl:attribute>
														</xsl:when>
														<xsl:otherwise>
															<xsl:attribute name="href"><xsl:value-of select="$get" /></xsl:attribute>
														</xsl:otherwise>
													</xsl:choose>
												</xsl:otherwise>
											</xsl:choose>
											На сайт
										</a>
									</xsl:when>
									<xsl:otherwise>
										<xsl:choose>
											<xsl:when test="//requests/get/ITEM">
												<a href="{$get}?ADMIN&amp;EDIT={//requests/get/ITEM}">Редактировать</a>
											</xsl:when>
											<xsl:otherwise>
												<a href="{$get}?ADMIN">Редактировать</a>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:otherwise>
								</xsl:choose>

							</li>
							<xsl:if test="//mod_users/sub_login/user/position='superadmin'">
								<li>
									<a href="/admin/?ADMIN">Разделы</a>
								</li>
								<li>
									<a href="/configs/?ADMIN">Настройки</a>
								</li>
							</xsl:if>
							<li>
								<a href="/counters/?ADMIN">Счетчики</a>
							</li>
							<li>
								<a href="#">Страницы</a>
								<ul>
									<xsl:for-each select="article_section/item">
										<li>
											<a href="/{path}/?ADMIN">
												<xsl:value-of select="name" />
											</a>
										</li>
									</xsl:for-each>
								</ul>
							</li>
							<xsl:apply-templates select="menu_item" />
							<li>
								<a href="/?logout" title="Выход">
									<img src="/img/logout.png" width="11" alt="Выход" />
								</a>
							</li>
							<!-- <li style="font-weight: normal"> <xsl:value-of select="concat('&#160;', //date_time/date,' ', //date_time/time)" /> </li> -->
						</ul>
					</div>
				</div>
				<dl>
					<dd id="but">&#160;</dd>
				</dl>
			</div>
			<div id="admin_pad" class="admin_pad_"></div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="menu_item">
		<xsl:apply-templates mode="menu_item" />
	</xsl:template>

	<xsl:template match="item" mode="menu_item">
		<li>
			<xsl:choose>
				<xsl:when test="count(item)>0">
					<a href="#">
						<xsl:value-of select="name" />
					</a>
					<ul>
						<xsl:apply-templates select="item" mode="menu_item" />
					</ul>
				</xsl:when>

				<xsl:otherwise>
					<a href="{url}">
						<xsl:value-of select="name" />
					</a>
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>
</xsl:stylesheet>