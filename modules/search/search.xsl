<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" standalone="no"
		media-type="html" cdata-section-elements="script" indent="yes" />
	<xsl:template match="mod_search">
		<div class="front">
			<div class="anonses">
				<div class="anonses_top">
					<div class="anonses_bot">
						<xsl:apply-templates mode="search" />
					</div>
				</div>
			</div>
		</div>
	</xsl:template>

	<xsl:template match="list" mode="search">
		<h3 style="text-align: right; font-size: 14px; color:#777">
			Всего найдено -
			<xsl:value-of select="../allSearch" />
			&#160;
			<xsl:call-template name="ending">
				<xsl:with-param name="number" select="../allSearch" />
				<xsl:with-param name="words">
					документ,документа,документов
				</xsl:with-param>
			</xsl:call-template>
			из -
			<xsl:value-of select="../allIndex" />
		</h3>
		<br />
		<xsl:apply-templates select="pages" />

		<xsl:for-each select="item">
			<div class="anons">
				<xsl:if test="highlighting >= //date_time/unix">
					<xsl:attribute name="class">anons active</xsl:attribute>
				</xsl:if>
				<h3>
					<a target="_blank" href="{link}">
						<xsl:value-of select="title" disable-output-escaping="yes" />
					</a>
				</h3>

				<xsl:if test="//mod_users/sub_login/user/role=1">
					<div class="type">
						<span class="control">
							<xsl:choose>
								<xsl:when test="active=1">
									[
									<a href="/{table}/?ADMIN&amp;ACTIVE={id}" class="publish_ajax">скрыть</a>
									]
								</xsl:when>
								<xsl:otherwise>
									[
									<a href="/{table}/?ADMIN&amp;ACTIVE={id}" class="publish_ajax">отобразить</a>
									]
								</xsl:otherwise>
							</xsl:choose>
							[
							<a href="/{table}/?ADMIN&amp;EDIT={id}">редактировать</a>
							]

							[
							<a href="/{table}/?ADMIN&amp;DEL={id}" class="delete_ajax">удалить</a>
							]
						</span>
					</div>
				</xsl:if>

				<div class="anons_body">
					<a target="_blank" href="{link}" style="color:#008CD2">
						<xsl:if test="photo_anons!=''">
							<img src="/120x90/{table}/{photo_anons}" />
						</xsl:if>
					</a>

					<div class="anons">
						<a href="{link}">
							<xsl:value-of select="pagetext" disable-output-escaping="yes" />
						</a>
					</div>
				</div>
			</div>

			<xsl:if test="(position() mod 4)=0 and position()!=12">

				<div class="anons">
					<h3>
						<a>реклама</a>
					</h3>
					<div class="anons_body">
						<div class="anons">
							<xsl:call-template name="banner_context" />
						</div>
					</div>
				</div>

			</xsl:if>


		</xsl:for-each>
		<xsl:apply-templates select="pages" />

	</xsl:template>



	<xsl:template match="stat" mode="search">
		<table style=" margin: auto;">
			<tr>
				<th>
					найденные (
					<xsl:value-of select="count(searched/item)" />
					)
				</th>
				<th width="50px">&#160;</th>
				<th>
					ненайденные (
					<xsl:value-of select="count(notsearched/item)" />
					)
				</th>
			</tr>
			<tr>
				<td style="vertical-align:top">
					<xsl:apply-templates select="searched" mode="stat" />
				</td>
				<td>&#160;</td>
				<td style="vertical-align:top">
					<xsl:apply-templates select="notsearched" mode="stat" />
				</td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="searched" mode="stat">
		<xsl:apply-templates match="item" mode="stat" />
	</xsl:template>

	<xsl:template match="notsearched" mode="stat">
		<xsl:apply-templates match="item" mode="stat" />
	</xsl:template>

	<xsl:template match="item" mode="stat">
		<xsl:value-of select="key" />
		<div style="width: 30px; float:left">
			<xsl:value-of select="hit" />
		</div>
		<br />
	</xsl:template>


	<xsl:template match="google_search" mode="search">

		Если вы ничего не нашли у нас на сайте, вы можете поискать в google:
		<br />
		<br />
		<xsl:choose>
			<xsl:when test="//domain_clear='otdyh-ua.net'">
				<form action="http://otdyh-ua.net/search/" id="cse-search-box">
					<div>
						<input type="hidden" name="cx" value="partner-pub-4339194722181635:5096784731" />
						<input type="hidden" name="cof" value="FORID:10" />
						<input type="hidden" name="ie" value="UTF-8" />
						<input type="text" name="q" size="55" />
						<input type="submit" name="sa" value="&#x041f;&#x043e;&#x0438;&#x0441;&#x043a;" />
					</div>
				</form>

				<script type="text/javascript" src="http://www.google.com.ua/coop/cse/brand?form=cse-search-box&amp;lang=ru" />

				<br />
				<br />

				<div id="cse-search-results"></div>
				<script type="text/javascript">
					var googleSearchIframeName = "cse-search-results";
					var googleSearchFormName = "cse-search-box";
					var googleSearchFrameWidth = 795;
					var googleSearchDomain = "www.google.com.ua";
					var
					googleSearchPath = "/cse";
</script>
				<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js" />

				<script type="text/javascript" src="http://www.google.com/cse/query_renderer.js" />
				<div id="queries"></div>
				<script src="http://www.google.com/cse/api/partner-pub-4339194722181635/cse/5096784731/queries/js?oe=UTF-8&amp;callback=(new+PopularQueryRenderer(document.getElementById(%22queries%22))).render" />
			</xsl:when>
			<xsl:when test="//domain_clear='otdyh-ru.net'">
				<form action="http://otdyh-ru.net/search/" id="cse-search-box">
					<div>
						<input type="hidden" name="cx" value="partner-pub-4339194722181635:8483646568" />
						<input type="hidden" name="cof" value="FORID:10" />
						<input type="hidden" name="ie" value="UTF-8" />
						<input type="text" name="q" size="55" />
						<input type="submit" name="sa" value="&#x041f;&#x043e;&#x0438;&#x0441;&#x043a;" />
					</div>
				</form>

				<script type="text/javascript" src="http://www.google.com.ua/coop/cse/brand?form=cse-search-box&amp;lang=ru" />
				<br />
				<br />
				<div id="cse-search-results"></div>
				<script type="text/javascript">
					var googleSearchIframeName = "cse-search-results";
					var googleSearchFormName = "cse-search-box";
					var googleSearchFrameWidth = 795;
					var googleSearchDomain = "www.google.ru";
					var
					googleSearchPath = "/cse";
</script>
				<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js" />

				<script type="text/javascript" src="http://www.google.com/cse/query_renderer.js" />
				<div id="queries"></div>
				<script src="http://www.google.com/cse/api/partner-pub-4339194722181635/cse/8483646568/queries/js?oe=UTF-8&amp;callback=(new+PopularQueryRenderer(document.getElementById(%22queries%22))).render" />
			</xsl:when>
			<xsl:when test="//domain_clear='otdyh-eu.net'">
				<form action="http://otdyh-eu.net/search/" id="cse-search-box">
					<div>
						<input type="hidden" name="cx" value="partner-pub-4339194722181635:1009908983" />
						<input type="hidden" name="cof" value="FORID:10" />
						<input type="hidden" name="ie" value="UTF-8" />
						<input type="text" name="q" size="55" />
						<input type="submit" name="sa" value="&#x041f;&#x043e;&#x0438;&#x0441;&#x043a;" />
					</div>
				</form>

				<script type="text/javascript" src="http://www.google.com.ua/coop/cse/brand?form=cse-search-box&amp;lang=ru" />
				<br />
				<br />

				<div id="cse-search-results"></div>
				<script type="text/javascript">
					var googleSearchIframeName = "cse-search-results";
					var googleSearchFormName = "cse-search-box";
					var googleSearchFrameWidth = 795;
					var googleSearchDomain = "www.google.com";
					var
					googleSearchPath = "/cse";
</script>
				<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js" />

				<script type="text/javascript" src="http://www.google.com/cse/query_renderer.js" />
				<div id="queries"></div>
				<script src="http://www.google.com/cse/api/partner-pub-4339194722181635/cse/1009908983/queries/js?oe=UTF-8&amp;callback=(new+PopularQueryRenderer(document.getElementById(%22queries%22))).render" />


			</xsl:when>
			<xsl:when test="//domain_clear='medik-ua.net'">
				<form action="http://medik-ua.net/search/" id="cse-search-box">
					<div>
						<input type="hidden" name="cx" value="partner-pub-4339194722181635:7583734418" />
						<input type="hidden" name="cof" value="FORID:10" />
						<input type="hidden" name="ie" value="UTF-8" />
						<input type="text" name="q" size="55" />
						<input type="submit" name="sa" value="&#x041f;&#x043e;&#x0438;&#x0441;&#x043a;" />
					</div>
				</form>

				<script type="text/javascript" src="http://www.google.com.ua/coop/cse/brand?form=cse-search-box&amp;lang=ru" />
				<br />
				<br />
				<div id="cse-search-results"></div>
				<script type="text/javascript">
					var googleSearchIframeName = "cse-search-results";
					var googleSearchFormName = "cse-search-box";
					var googleSearchFrameWidth = 795;
					var googleSearchDomain = "www.google.com.ua";
					var
					googleSearchPath = "/cse";
</script>
				<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js" />

				<script type="text/javascript" src="http://www.google.com/cse/query_renderer.js" />
				<div id="queries"></div>
				<script src="http://www.google.com/cse/api/partner-pub-4339194722181635/cse/7583734418/queries/js?oe=UTF-8&amp;callback=(new+PopularQueryRenderer(document.getElementById(%22queries%22))).render" />
			</xsl:when>
			<xsl:otherwise>

			</xsl:otherwise>
		</xsl:choose>
		<br />
	</xsl:template>

	<xsl:template match="*" mode="search" />
</xsl:stylesheet>
