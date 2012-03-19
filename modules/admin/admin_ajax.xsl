<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />
	<xsl:include href="./../xsl/templates.xsl" />
	<xsl:include href="#ROOT#xsl/templates.xsl" />

	<xsl:template match="mod_admin">

		<xsl:call-template name="messages" />

		<xsl:if test="main">
			<div id="dialog_main">
				<fieldset>
					<label for="name-section">Название</label>
					<input type="text" id="name-section" name="name" value="{main/item/name}" />
					<label for="alias-section">Alias</label>
					<input type="text" id="alias-section" name="alias" value="{main/item/alias}" />
					Модуль для раздела
					<select name="module">
						<xsl:for-each select="modules/item">
							<option value="{.}">
								<xsl:if test="../../main/item/module=.">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="." />
							</option>
						</xsl:for-each>
					</select>
					Модуль для всех подразделов
					<select name="sub_module">
						<xsl:for-each select="modules/item">
							<option value="{.}">
								<xsl:if test="../../main/item/sub_module=.">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="." />
							</option>
						</xsl:for-each>
					</select>
					<xsl:variable name="id" select="main/item/id" />
					<xsl:variable name="param" select="//section_present/item/id2[text()=$id]/../param" />
					<label for="param-section">Параметры</label>
					<input type="hidden" name="section_present[{$id}]" value="{$id}" />
					<input type="text" id="param-section" name="section_present_param[{$id}]" value="{$param}" />

					<label for="present_anywhere">Везде</label>
					<input style="width:20px" id="present_anywhere" type="checkbox" name="present_anywhere" value="anywhere">
						<xsl:if test="main/item/present='anywhere'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>

					<label for="present_reset">Сбросить</label>
					<input style="width:20px" id="present_reset" type="checkbox" name="present_reset" value="reset" />
				</fieldset>
			</div>

			<div id="dialog_meta_tags">
				<fieldset>
					<label for="title-tag">Title</label>
					<textarea id="title-tag" name="title">
						<xsl:value-of select="meta_tags/item/title" />
					</textarea>
					
					<label for="description-tag">Description</label>
					<textarea id="title-tag" name="description">
            <xsl:value-of select="meta_tags/item/description" />
          </textarea>				
			
					<label for="keywords">Keywords</label>
					<textarea id="title-tag" name="keywords">
            <xsl:value-of select="meta_tags/item/keywords" />
          </textarea>					
				</fieldset>
			</div>

			<div id="dialog_options" style="overflow:auto;">
				<p>
					<b>Отметьте какие модули (из каких разделов) будут выполняться в
						текущем разделе
					</b>
				</p>
				<br />
				<br />
				<xsl:apply-templates select="sections/item" />
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="item">
		<xsl:choose>
			<xsl:when test="@id=0">
				<ul>
					<xsl:apply-templates select="item" />
				</ul>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="id" select="id" />
				<xsl:variable name="param" select="//section_present/item/id2[text()=$id]/../param" />
				<li>
					<table class="dialog_options">
						<tr>
							<td>
								<xsl:choose>
									<xsl:when test="//main/item/id[text()!=$id]">
										<div class="sections">
											<label for="sp{id}">
												<xsl:value-of select="name" />
											</label>
										</div>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="name" />
									</xsl:otherwise>
								</xsl:choose>
							</td>

							<td style="width:20px">
								<xsl:if test="//main/item/id[text()!=$id] or not(//main/item/id)">
									<input id="sp{id}" type="checkbox" name="section_present[{id}]" style="width:20px" value="{id}">
										<xsl:if test="//section_present/item/id2[text()=$id]">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
								</xsl:if>
							</td>

							<td style="width:200px; padding: 2px">
								<xsl:if test="//main/item/id[text()!=$id] or not(//main/item/id)">

									<input id="{id}" type="text" class="section_present_param" name="section_present_param[{id}]" value="{$param}" />
								</xsl:if>
							</td>
						</tr>
					</table>
					<xsl:apply-templates select="item" />
				</li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>