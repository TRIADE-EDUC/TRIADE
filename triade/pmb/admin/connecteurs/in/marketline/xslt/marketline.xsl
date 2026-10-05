<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl" 
>
	
<xsl:output method="xml" encoding="UTF-8" indent="yes"/>
<xsl:variable name="lowercase">ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ</xsl:variable>
<xsl:variable name="uppercase">abcdefghijklmnopqrstuvwxyzàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþ</xsl:variable>
<xsl:variable name="apos">'</xsl:variable>
	
	<xsl:template match="/*">
		<unimarc>
			<xsl:for-each select="AnalystInsights">
				<xsl:call-template name="doAnalystInsights" />
			</xsl:for-each>
			<xsl:for-each select="Analyst_Insights">
				<xsl:call-template name="doAnalystInsights" />
			</xsl:for-each>
			<xsl:for-each select="Case">
				<xsl:call-template name="doCase" />
			</xsl:for-each>
			<xsl:for-each select="Chart">
				<xsl:call-template name="doChart" />
			</xsl:for-each>
			<xsl:for-each select="City">
				<xsl:call-template name="doCity" />
			</xsl:for-each>
			<xsl:for-each select="Country">
				<xsl:call-template name="doCountry" />
			</xsl:for-each>
			<xsl:for-each select="Company">
				<xsl:call-template name="doCompany" />
			</xsl:for-each>
			<xsl:for-each select="Industry">
				<xsl:call-template name="doIndustry" />
			</xsl:for-each>
			<xsl:for-each select="Thematic">
				<xsl:call-template name="doThematic" />
			</xsl:for-each>
			<xsl:for-each select="ValueSupply">
				<xsl:call-template name="doValueSupply" />
			</xsl:for-each>
			<xsl:for-each select="Value_Supply">
				<xsl:call-template name="doValueSupply" />
			</xsl:for-each>
		</unimarc>
	</xsl:template>
	
	<xsl:template name="doAnalystInsights">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublicationDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>
	
	<xsl:template name="doCase">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublicationDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>
	
	<xsl:template name="doChart">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublicationDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>

	<xsl:template name="doCountry">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublicationDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>
	
	<xsl:template name="doCity">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublicationDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>

	<xsl:template name="doCompany">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Company" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Company" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@CompanyName" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
 			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublishedDate" />
				</xsl:with-param>
			</xsl:call-template>
 			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@OverviewOne" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@OverviewTwo" />
				</xsl:with-param>
			</xsl:call-template>
 			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Products" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="mainIndusties" >
				<xsl:with-param name="value">
					<xsl:value-of select="@MainIndusties" />
				</xsl:with-param>
			</xsl:call-template>	
  			<xsl:call-template name="address" />
		</notice>
	</xsl:template>

	<xsl:template name="doIndustry">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublishedDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>
	
	<xsl:template name="doThematic">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublicationDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>

	<xsl:template name="doValueSupply">
		<notice>
			<xsl:element name="rs">*</xsl:element>
			<xsl:element name="ru">*</xsl:element>
			<xsl:element name="el">1</xsl:element>
			<xsl:element name="bl">m</xsl:element>
			<xsl:element name="hl">0</xsl:element>
			<xsl:element name="dt">a</xsl:element>
			<xsl:call-template name="identifier" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="code" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ReportCode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="language" />
			<xsl:call-template name="title">
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductTitle" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="url">
				<xsl:with-param name="value">
					<xsl:value-of select="@UrlNode" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="publicationDate">
				<xsl:with-param name="value">
					<xsl:value-of select="@PublicationDate" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="content" >
				<xsl:with-param name="value">
					<xsl:value-of select="@Synopsis" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="taxonomy" >
				<xsl:with-param name="value">
					<xsl:value-of select="@TaxonomyBreadcrumb" />
				</xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="productType" >
				<xsl:with-param name="value">
					<xsl:value-of select="@ProductType" />
				</xsl:with-param>
			</xsl:call-template>
		</notice>
	</xsl:template>
	
	<xsl:template name="identifier">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<f c="001">
				<xsl:value-of select="normalize-space($value)"/>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="code">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<f c="010">
				<s c="a"><xsl:value-of select="normalize-space($value)"/></s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="language">
		<f c="101">
			<s c="a">eng</s>
		</f>
	</xsl:template>
	
	<xsl:template name="title">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<f c="200">
				<s c="a"><xsl:value-of select="normalize-space($value)"/></s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="url">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<f c="856">
				<s c="u"><xsl:value-of select="normalize-space($value)"/></s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="publicationDate">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<f c="210">
				<s c="d">
					<xsl:call-template name="formatYear">
						<xsl:with-param name="date"><xsl:value-of select="$value"/></xsl:with-param>
					</xsl:call-template>
				</s>
			</f>		
			<f c="219">
				<s c="d">
					<xsl:call-template name="formatDate">
						<xsl:with-param name="date"><xsl:value-of select="$value"/></xsl:with-param>
					</xsl:call-template>
				</s>
			</f>
			</xsl:if>
	</xsl:template>

	<xsl:template name="formatYear">
		<xsl:param name="date" />
		<xsl:choose>
			<!-- DateTime ISO8601-->
			<xsl:when test="substring($date,11,1)='T' ">
				<xsl:value-of select="substring($date,1,4)"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="month_after" ><xsl:value-of select="substring-after($date,'/')" /></xsl:variable>
				<xsl:variable name="day_after" ><xsl:value-of select="substring-after($month_after,'/')" /></xsl:variable>
				<xsl:variable name="year" ><xsl:value-of select="substring($day_after, 1, 4)" /></xsl:variable>
				<xsl:value-of select="$year" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="formatDate">
		<xsl:param name="date" />
		<xsl:choose>
			<!-- DateTime ISO8601-->
			<xsl:when test="substring($date,11,1)='T' ">
				<xsl:value-of select="concat(substring($date,9,2), '/', substring($date,6,2), '/', substring($date,1,4))"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="month"><xsl:value-of select="substring-before($date,'/')" /></xsl:variable>
				<xsl:variable name="month_after" ><xsl:value-of select="substring-after($date,'/')" /></xsl:variable>
				<xsl:variable name="day" ><xsl:value-of select="substring-before($month_after,'/')" /></xsl:variable>
				<xsl:variable name="day_after" ><xsl:value-of select="substring-after($month_after,'/')" /></xsl:variable>
				<xsl:variable name="year" ><xsl:value-of select="substring($day_after, 1, 4)" /></xsl:variable>
				<xsl:variable name="day2" >
					<xsl:choose>
						<xsl:when test="string-length($day)=2">	
							<xsl:value-of select="$day" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="concat('0', $day)" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<xsl:variable name="month2" >
					<xsl:choose>
						<xsl:when test="string-length($month)=2">	
							<xsl:value-of select="$month" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="concat('0', $month)" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<xsl:value-of select="concat($day2,'/',$month2,'/',$year)" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="content">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<f c="327">
				<s c="a"><xsl:value-of select="normalize-space($value)"/></s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="customField">
		<xsl:param name="name" />
		<xsl:param name="value" />
		<xsl:param name="label" />
		<xsl:if test="$name and $value">
			<f c="900">
				<s c="n"><xsl:value-of select="$name"/></s>
				<s c="a"><xsl:value-of select="$value"/></s>
				<s c="l"><xsl:value-of select="$label"/></s>
			</f>
		</xsl:if>
	</xsl:template>	

	<xsl:template name="productType">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<xsl:call-template name="customField">
				<xsl:with-param name="name">cp_product_type</xsl:with-param>
				<xsl:with-param name="label">productType</xsl:with-param>
				<xsl:with-param name="value"><xsl:value-of select="normalize-space($value)"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="address">
		<xsl:variable name="s1"><xsl:value-of select="normalize-space(@AddressOne)" /></xsl:variable>
		<xsl:variable name="s2"><xsl:value-of select="normalize-space(@AddressTwo)" /></xsl:variable>

		<xsl:variable name="s12">
			<xsl:if test="($s1!='')">
				<xsl:value-of select="$s1" />
			</xsl:if>
			<xsl:if test="($s1!='') and  ($s2!='')">
				<xsl:value-of select="'&#xD;&#xA;'" />
			</xsl:if>
			<xsl:if test="($s2!='')">
				<xsl:value-of select="$s2" />
			</xsl:if>
		</xsl:variable>
		
		<xsl:variable name="s3"><xsl:value-of select="normalize-space(@AddressThree)" /></xsl:variable>
		
		<xsl:variable name="s123">
			<xsl:if test="($s12!='')">
				<xsl:value-of select="$s12" />
			</xsl:if>
			<xsl:if test="($s12!='') and  ($s3!='')">
				<xsl:value-of select="'&#xD;&#xA;'" />
			</xsl:if>
			<xsl:if test="($s3!='')">
				<xsl:value-of select="$s3" />
			</xsl:if>
		</xsl:variable>
		
		<xsl:variable name="s4"><xsl:value-of select="normalize-space(@AddressFour)" /></xsl:variable>
		
		<xsl:variable name="s1234">
			<xsl:if test="($s123!='')">
				<xsl:value-of select="$s123" />
			</xsl:if>
			<xsl:if test="($s123!='') and  ($s4!='')">
				<xsl:value-of select="'&#xD;&#xA;'" />
			</xsl:if>
			<xsl:if test="($s4!='')">
				<xsl:value-of select="$s4" />
			</xsl:if>
		</xsl:variable>
		
		<xsl:variable name="s5"><xsl:value-of select="normalize-space(@AddressFive)" /></xsl:variable>
		
		<xsl:variable name="s12345">
			<xsl:if test="($s1234!='')">
				<xsl:value-of select="$s1234" />
			</xsl:if>
			<xsl:if test="($s1234!='') and  ($s5!='')">
				<xsl:value-of select="'&#xD;&#xA;'" />
			</xsl:if>
			<xsl:if test="($s5!='')">
				<xsl:value-of select="$s5" />
			</xsl:if>
		</xsl:variable>
		
		<xsl:variable name="s6"><xsl:value-of select="normalize-space(@City)" /></xsl:variable>
		
		<xsl:variable name="s123456">
			<xsl:if test="($s12345!='')">
				<xsl:value-of select="$s12345" />
			</xsl:if>
			<xsl:if test="($s12345!='') and  ($s6!='')">
				<xsl:value-of select="'&#xD;&#xA;'" />
			</xsl:if>
			<xsl:if test="($s6!='')">
				<xsl:value-of select="$s6" />
			</xsl:if>
		</xsl:variable>
		
		<xsl:variable name="s7"><xsl:value-of select="normalize-space(@State)" /></xsl:variable>
		
		<xsl:variable name="s1234567">
			<xsl:if test="($s123456!='')">
				<xsl:value-of select="$s123456" />
			</xsl:if>
			<xsl:if test="($s123456!='') and  ($s7!='')">
				<xsl:value-of select="'&#xD;&#xA;'" />
			</xsl:if>
			<xsl:if test="($s7!='')">
				<xsl:value-of select="$s7" />
			</xsl:if>
		</xsl:variable>
		
		<xsl:variable name="s8"><xsl:value-of select="normalize-space(@Country)" /></xsl:variable>
		
		<xsl:variable name="s12345678">
			<xsl:if test="($s1234567!='')">
				<xsl:value-of select="$s1234567" />
			</xsl:if>
			<xsl:if test="($s1234567!='') and  ($s8!='')">
				<xsl:value-of select="'&#xD;&#xA;'" />
			</xsl:if>
			<xsl:if test="($s8!='')">
				<xsl:value-of select="$s8" />
			</xsl:if>
		</xsl:variable>
		
		<xsl:if test="$s12345678!=''">	
			<xsl:call-template name="customField">
				<xsl:with-param name="name">cp_address</xsl:with-param>
				<xsl:with-param name="label">Address</xsl:with-param>
				<xsl:with-param name="value"><xsl:value-of select="$s12345678"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="taxonomy">
		<xsl:param name="value" />
		<xsl:if test="$value">
			<xsl:variable name="formated_value">
				<xsl:call-template name="formatTaxonomyValue">
	    			<xsl:with-param name="value" select="$value" />
	  			</xsl:call-template>
			</xsl:variable>
			
			<xsl:call-template name="tokenizeToField">
				<xsl:with-param name="string"><xsl:value-of select="$formated_value"/></xsl:with-param>
				<xsl:with-param name="separator"><xsl:value-of select="';'" /></xsl:with-param>
				<xsl:with-param name="field">606</xsl:with-param>
				<xsl:with-param name="subfield">a</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="formatTaxonomyValue">
		<xsl:param name="value" />
		<xsl:variable name="s1">
			<xsl:call-template name="replace">
				<xsl:with-param name="text" select="$value" />
    			<xsl:with-param name="replace" select="'Root &amp;gt;'" />
    			<xsl:with-param name="by" select="''" />
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="s2">
			<xsl:call-template name="replace">
				<xsl:with-param name="text" select="$s1" />
    			<xsl:with-param name="replace" select="'&amp;gt;'" />
    			<xsl:with-param name="by" select="'&gt;'" />
			</xsl:call-template>
		</xsl:variable>
		<xsl:variable name="s3">
			<xsl:call-template name="replace">
				<xsl:with-param name="text" select="$s2" />
    			<xsl:with-param name="replace" select="'&amp;amp;'" />
    			<xsl:with-param name="by" select="'&amp;'" />
			</xsl:call-template>
		</xsl:variable>
		<xsl:value-of select="$s3" />
	</xsl:template>
	
	<xsl:template name="replace">
		<xsl:param name="text" />
		<xsl:param name="replace" />
		<xsl:param name="by" />
		<xsl:choose>
			<xsl:when test="contains($text, $replace)">
				<xsl:value-of select="substring-before($text,$replace)" />
				<xsl:value-of select="$by" />
				<xsl:call-template name="replace">
					<xsl:with-param name="text" select="substring-after($text,$replace)" />
					<xsl:with-param name="replace" select="$replace" />
					<xsl:with-param name="by" select="$by" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
			  <xsl:value-of select="$text" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="mainIndusties">
		<xsl:param name="value" />
		<xsl:if test="$value">
		<xsl:variable name="formated_value">
			<xsl:call-template name="formatTaxonomyValue">
	    			<xsl:with-param name="value" select="$value" />
	  			</xsl:call-template>
			</xsl:variable>
			<xsl:call-template name="tokenizeToField">
				<xsl:with-param name="string"><xsl:value-of select="$formated_value"/></xsl:with-param>
				<xsl:with-param name="separator">;</xsl:with-param>
				<xsl:with-param name="field">606</xsl:with-param>
				<xsl:with-param name="subfield">a</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="tokenizeToField">
		<xsl:param name="string" />
		<xsl:param name="separator"/>
		<xsl:param name="field" />
		<xsl:param name="subfield" />
		<xsl:choose>
			<xsl:when test="substring-before($string,$separator)">
				<f c="{$field}">
					<s c="{$subfield}"><xsl:value-of select="normalize-space(substring-before($string,$separator))"/></s>
				</f>
				<xsl:call-template name="tokenizeToField">
					<xsl:with-param name="string"><xsl:value-of select="substring-after($string,$separator)"/></xsl:with-param>
					<xsl:with-param name="separator"><xsl:value-of select="$separator"/></xsl:with-param>
					<xsl:with-param name="field"><xsl:value-of select="$field"/></xsl:with-param>
					<xsl:with-param name="subfield"><xsl:value-of select="$subfield"/></xsl:with-param>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<f c="{$field}">
					<s c="{$subfield}"><xsl:value-of select="normalize-space($string)"/></s>
				</f>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	 
</xsl:stylesheet>
