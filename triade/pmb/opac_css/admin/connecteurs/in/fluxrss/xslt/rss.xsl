<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
<xsl:output method="xml" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>	

<xsl:template match="/rss/channel">
	<channel>
		<xsl:for-each select="item">
			<item>	
				<xsl:call-template name="get_guid"/>
				<xsl:call-template name="get_title"/>
				<xsl:call-template name="get_description"/>
				<xsl:call-template name="get_author"/>
				<xsl:call-template name="get_link"/>
				<xsl:call-template name="get_pubDate"/>
				<xsl:call-template name="get_category"/>
			</item>
		</xsl:for-each>				
	</channel>
</xsl:template>
	
<xsl:template name="get_guid">
	<xsl:if test="guid">
		<guid><xsl:value-of select="guid"/></guid>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_title">
	<xsl:if test="title">
		<title><xsl:value-of select="title"/></title>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_description">
	<xsl:if test="description">
		<description><xsl:value-of select="description"/></description>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_author">
	<xsl:if test="author">
		<author><xsl:value-of select="author"/></author>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_link">
	<xsl:if test="link">		
		<link><xsl:value-of select="link"/></link>
	</xsl:if>		
</xsl:template>

<xsl:template name="get_pubDate">
	<xsl:if test="pubDate">
		<pubDate><xsl:value-of select="pubDate"/></pubDate>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_category">
	<xsl:if test="category">
		<category><xsl:value-of select="category"/></category>
	</xsl:if>	
</xsl:template>

</xsl:stylesheet>