<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
****************************************************************************************
© 2002-2024 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: clean.xsl,v 1.1.38.1 2024/08/28 14:10:17 rtigero Exp $ -->

<xsl:stylesheet version = '1.0'
     xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
		<xsl:output method="xml" indent='yes'/>

<xsl:template match="@*">
	<xsl:copy/>
</xsl:template>

<xsl:template match="text()">
	<xsl:choose>
		<xsl:when test="substring(.,string-length(.))='/'">
			<xsl:value-of select="normalize-space(substring(.,1,string-length(.)-1))"></xsl:value-of>
		</xsl:when>
		<xsl:when test="substring(.,string-length(.))=':'">
			<xsl:value-of select="normalize-space(substring(.,1,string-length(.)-1))"></xsl:value-of>
		</xsl:when>
		<xsl:when test="substring(.,string-length(.))=','">
			<xsl:value-of select="normalize-space(substring(.,1,string-length(.)-1))"></xsl:value-of>
		</xsl:when>
		<xsl:when test="substring(.,string-length(.))=';'">
			<xsl:value-of select="normalize-space(substring(.,1,string-length(.)-1))"></xsl:value-of>
		</xsl:when>
		<xsl:when test="substring(.,string-length(.))='.'">
			<xsl:value-of select="normalize-space(substring(.,1,string-length(.)-1))"></xsl:value-of>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="normalize-space(.)"></xsl:value-of>
		</xsl:otherwise>
	</xsl:choose>

</xsl:template>

<xsl:template match="*">
	<xsl:element name="{name()}">
		<xsl:apply-templates select="* | text() | @*">
		</xsl:apply-templates>
	</xsl:element>
</xsl:template>


</xsl:stylesheet>
