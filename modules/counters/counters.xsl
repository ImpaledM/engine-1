<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">
	<xsl:output indent="yes" />

	<xsl:template match="mod_counters">
		<xsl:apply-templates select="brief" mode="brief_counters" />
	</xsl:template>

	<xsl:template match="brief" mode="brief_counters">
		<xsl:if test="//DEBUG!=1">
			<xsl:comment>
				<xsl:text>noindex</xsl:text>
			</xsl:comment>
            <ul class="counters">
			    <xsl:for-each select="item">
			        <li>
				      <xsl:value-of select="text" disable-output-escaping="yes" />
		        	</li>
			    </xsl:for-each>
            </ul>
			<xsl:comment>
				<xsl:text>/noindex</xsl:text>
			</xsl:comment>
		</xsl:if>
	</xsl:template>

	<xsl:template match="edit" mode="counters">
		<div class="module_content">
			<form method="post">
				<fieldset>
					<label>
						<span class="star">*</span>
						Название:
					</label>
					<input type="text" name="name" value="{item/name}" />
					<label>
						<span class="star">*</span>
						Код счетчика:
					</label>
					<textarea name="text" rows="5">
						<xsl:value-of select="item/text" />
					</textarea>
				</fieldset>

				<fieldset>
					<xsl:call-template name="saveButton">
						<xsl:with-param name="active" select="item/active" />
					</xsl:call-template>
				</fieldset>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="list_admin" mode="counters">
		<div class="control">
			<a href="/counters/?ADMIN&amp;ADD">
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
								<th style="width:200px;">Счетчик</th>
								<th>Код</th>
								<th style="width:200px;">Действия</th>
							</tr>
						</thead>
						<tbody>
							<xsl:for-each select="item">
								<tr>
									<td>
										<xsl:value-of select="name" />
									</td>
									<td>
									<xsl:value-of select="text" disable-output-escaping="yes" />
									</td>
									<td>
										<xsl:choose>
											<xsl:when test="active=1">
												<a href="/counters/?ADMIN&amp;ACTIVE={id}" class="publish_ajax">
													<input type="image" title="Деактивировать" src="/engine/modules/admin/images/icn_pause.png" />
												</a>
											</xsl:when>
											<xsl:otherwise>
												<a href="/counters/?ADMIN&amp;ACTIVE={id}" class="publish_ajax">
													<input type="image" title="Активировать" src="/engine/modules/admin/images/icn_play.png" />
												</a>
											</xsl:otherwise>
										</xsl:choose>

										<a href="/counters/?ADMIN&amp;EDIT={id}" class="edit">
											<input type="image" title="Редактировать" src="/engine/modules/admin/images/icn_edit.png" />
										</a>
										<a href="/counters/?ADMIN&amp;DEL={id}" class="delete_ajax" title="Удалить">
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