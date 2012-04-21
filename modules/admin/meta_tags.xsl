<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output indent="yes" />

	<xsl:template match="sub_meta_tags" mode="meta_tags">
		<xsl:apply-templates select="*" mode="meta_tags" />
	</xsl:template>

	<xsl:template match="edit" mode="meta_tags">
		<div class="module_content">
			<form method="post">
				<fieldset>
					<label>META Title:</label>
					<input type="text" name="title" value="{item/title}" />

					<label>META Description:</label>
					<textarea name="description" title="Small" rows="5">
						<xsl:value-of select="item/description" />
					</textarea>

					<label>META Keywords:</label>
					<textarea name="keywords" title="Small" rows="5">
						<xsl:value-of select="item/keywords" />
					</textarea>
				</fieldset>
				<fieldset>
					<div class="saveButtons">
						<input type="submit" value="Сохранить" class="submit" name="save" />
					</div>
				</fieldset>
			</form>
		</div>
	</xsl:template>


</xsl:stylesheet>