<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0" xmlns:dyn="http://exslt.org/dynamic" xmlns:str="http://exslt.org/strings"
	extension-element-prefixes="str">
	<xsl:variable name="get"	select="concat('/', //section/current_path, '/')" />
	<xsl:variable name="get_ajax"	select="concat('/__', //section/current_path, '/')" />

	<xsl:template match="*" mode="brief" />

	<xsl:template name="rusMonth">
		<xsl:param name="date" />
		<xsl:param name="date2" />
		<xsl:param name="leading-zero" select="1" />
		<xsl:choose>
			<xsl:when test="$leading-zero=1">
				<xsl:value-of select="concat(substring($date, 9,2), ' ')" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="substring($date, 9,1)='0'">
						<xsl:value-of select="concat(substring($date, 10,2), ' ')" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="concat(substring($date, 9,2), ' ')" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:call-template name="month">
			<xsl:with-param name="date" select="$date" />
		</xsl:call-template>
		<xsl:if test="substring($date, 0,5)!='0000'">
			<xsl:value-of select="concat(' ', substring($date, 0,5))" />
		</xsl:if>
		<xsl:if test="$date!=$date2 and $date2">
			<xsl:value-of select="concat(' - ', substring($date2, 9,2),' ')" />
			<xsl:call-template name="month">
				<xsl:with-param name="date" select="$date2" />
			</xsl:call-template>
			<xsl:value-of select="concat(' ', substring($date2, 0,5))" />
		</xsl:if>
	</xsl:template>

	<xsl:template name="month">
		<xsl:param name="date" />
		<xsl:param name="month" />
		<xsl:choose>
			<xsl:when test="$date!=''">
				<xsl:variable name="mon" select="substring($date, 6,2)" />
				<xsl:variable name="name">
					<xsl:choose>
						<xsl:when test="$mon='01'">
							января
						</xsl:when>
						<xsl:when test="$mon='02'">
							февраля
						</xsl:when>
						<xsl:when test="$mon='03'">
							марта
						</xsl:when>
						<xsl:when test="$mon='04'">
							апреля
						</xsl:when>
						<xsl:when test="$mon='05'">
							мая
						</xsl:when>
						<xsl:when test="$mon='06'">
							июня
						</xsl:when>
						<xsl:when test="$mon='07'">
							июля
						</xsl:when>
						<xsl:when test="$mon='08'">
							августа
						</xsl:when>
						<xsl:when test="$mon='09'">
							сентября
						</xsl:when>
						<xsl:when test="$mon='10'">
							октября
						</xsl:when>
						<xsl:when test="$mon='11'">
							ноября
						</xsl:when>
						<xsl:when test="$mon='12'">
							декабря
						</xsl:when>
					</xsl:choose>
				</xsl:variable>
				<xsl:value-of select="$name" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="name">
					<xsl:choose>
						<xsl:when test="$month='01'">
							январь
						</xsl:when>
						<xsl:when test="$month='02'">
							февраль
						</xsl:when>
						<xsl:when test="$month='03'">
							март
						</xsl:when>
						<xsl:when test="$month='04'">
							апрель
						</xsl:when>
						<xsl:when test="$month='05'">
							май
						</xsl:when>
						<xsl:when test="$month='06'">
							июнь
						</xsl:when>
						<xsl:when test="$month='07'">
							июль
						</xsl:when>
						<xsl:when test="$month='08'">
							август
						</xsl:when>
						<xsl:when test="$month='09'">
							сентябрь
						</xsl:when>
						<xsl:when test="$month='10'">
							октябрь
						</xsl:when>
						<xsl:when test="$month='11'">
							ноябрь
						</xsl:when>
						<xsl:when test="$month='12'">
							декабрь
						</xsl:when>
					</xsl:choose>
				</xsl:variable>
				<xsl:value-of select="$name" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!--Шаблон склонения существительных после числительных -->
	<xsl:template name="ending">
		<xsl:param name="number" select="0" />
		<xsl:param name="words" />
		<!-- Строка вариантов склонения (3 штуки), разделённых запятой. например
			"франшиза,франшизы,франшиз" -->
		<xsl:variable name="word1" select="substring-before($words,',')" />
		<xsl:variable name="word2"
			select="substring-before(substring-after($words,','),',')" />
		<xsl:variable name="word3"
			select="substring-after(substring-after($words,','),',')" />
		<xsl:choose>
			<xsl:when
				test="(($number mod 100) &gt;= 5) and (($number mod 100) &lt;= 20)">
				<xsl:value-of select="$word3" />
			</xsl:when>
			<xsl:when test="$number mod 10 = 1">
				<xsl:value-of select="$word1" />
			</xsl:when>
			<xsl:when test="(($number mod 10) &gt;= 2) and (($number mod 10) &lt;= 4)">
				<xsl:value-of select="$word2" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$word3" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="str_replace">
		<xsl:param name="text" />
		<xsl:param name="replace" />
		<xsl:param name="by" />
		<xsl:choose>
			<xsl:when test="contains($text, $replace)">
				<xsl:value-of select="substring-before($text,$replace)" />
				<xsl:value-of select="$by" />
				<xsl:call-template name="str_replace">
					<xsl:with-param name="text"
						select="substring-after($text,$replace)" />
					<xsl:with-param name="replace" select="$replace" />
					<xsl:with-param name="by" select="$by" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$text" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="nl2br">
		<xsl:param name="string" />
		<xsl:value-of select="normalize-space(substring-before($string,'&#10;'))" />
		<xsl:choose>
			<xsl:when test="contains($string,'&#10;')">
				<br />
				<xsl:call-template name="nl2br">
					<xsl:with-param name="string"
						select="substring-after($string,'&#10;')" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$string" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="translit">
		<xsl:param name="id" />
		<xsl:param name="alias" />
		<xsl:param name="query" />
		<xsl:param name="get" select="$get" />
		<xsl:choose>
			<xsl:when test="$alias!=''">
				<xsl:attribute name="href">
			<xsl:value-of select="concat($get, $id, '-', $alias, $query)" />
		</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="href">
				<xsl:value-of select="concat($get, '?ITEM=', $id)" />
			</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="substr">
		<xsl:param name="str" />
		<xsl:param name="num" select="500" />
		<xsl:param name="delim" select="' '" />
		<xsl:param name="end" select="'...'" />
		<xsl:choose>
			<xsl:when test="string-length($str) &gt; $num">
				<xsl:variable name="txt" select="substring($str, 1, $num)" />
				<xsl:for-each select="str:tokenize($txt, $delim)">
					<xsl:if test="position() &lt; last()">
						<xsl:value-of select="concat(., $delim )" />
					</xsl:if>
				</xsl:for-each>
				<xsl:value-of select="$end" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$str" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>