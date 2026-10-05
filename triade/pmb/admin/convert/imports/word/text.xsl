<xsl:stylesheet version = '1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!--
****************************************************************************************
© 2002-2024 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: text.xsl,v 1.2.30.1 2024/08/28 14:10:17 rtigero Exp $ -->

<xsl:output method="text" encoding="iso-8859-1"/>

<xsl:template match="/">
			<xsl:apply-templates select="//notice"/>
</xsl:template>

<xsl:template match="notice">
		<xsl:apply-templates select="f[@c='200']"/>
		<xsl:text>;</xsl:text>
		<xsl:apply-templates select="f[@c='700' or @c='710' or @c='701' or @c='702' or @c='711' or @c='712']"/>
		<xsl:text>
</xsl:text>
</xsl:template>

<xsl:template match="f[@c='200']">
		<xsl:apply-templates select="s[@c='a']" mode="titre"/>
</xsl:template>

<xsl:template match="f[@c='700' or @c='710' or @c='701' or @c='702' or @c='711' or @c='712']">
		<xsl:apply-templates select="s[@c='a' or @c='b']" mode="auteur"/>
		<xsl:text>/</xsl:text>
</xsl:template>

<xsl:template match="s[@c='a']" mode="titre">
<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="s[@c='a']" mode="auteur">
<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="s[@c='b']" mode="auteur">
<xsl:text>, </xsl:text><xsl:value-of select="."/>
</xsl:template>

<xsl:template match="*"/>
</xsl:stylesheet>
