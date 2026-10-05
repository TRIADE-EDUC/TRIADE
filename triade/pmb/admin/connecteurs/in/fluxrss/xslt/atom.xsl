<?xml version="1.0" encoding="utf-8"?>


<xsl:stylesheet 
	version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:a="http://www.w3.org/2005/Atom">
<xsl:output method="xml" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>	

<xsl:template match="/">
	<channel>
		<xsl:for-each select="a:feed/a:entry">
			<item>	
				<xsl:call-template name="get_guid"/>
				<xsl:call-template name="get_title"/>
				<xsl:call-template name="get_description"/>
				<xsl:call-template name="get_author"/>
				<xsl:call-template name="get_link"/>
				<xsl:call-template name="get_pubDate"/>
			</item>
		</xsl:for-each>				
	</channel>
</xsl:template>
	
<xsl:template name="get_guid">
	<xsl:if test="a:id">
		<guid><xsl:value-of select="a:id"/></guid>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_title">
	<xsl:if test="a:title">
		<title><xsl:value-of select="a:title"/></title>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_description">
	<xsl:if test="a:summary">
		<description><xsl:value-of select="a:summary"/></description>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_author">
	<xsl:if test="a:author/a:name">
		<author><xsl:value-of select="a:author/a:name"/></author>
	</xsl:if>	
</xsl:template>

<xsl:template name="get_link">
	<xsl:for-each select="a:link">    
		<xsl:if test="@href">
			<link><xsl:value-of select="@href"/></link>
		</xsl:if>
	</xsl:for-each>		
</xsl:template>

<xsl:template name="get_pubDate">
	<xsl:if test="a:updated">
		<pubDate><xsl:value-of select="a:updated"/></pubDate>
	</xsl:if>	
</xsl:template>

</xsl:stylesheet>