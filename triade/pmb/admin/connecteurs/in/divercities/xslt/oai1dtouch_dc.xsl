<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
	xmlns:oai1dtouch_dc="https://export.divercities.eu/OAI/2.0/oai1dtouch_dc/"
>

	<xsl:output method="xml" encoding="UTF-8" indent="yes"/>
	<xsl:variable name="lowercase">ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ</xsl:variable>
	<xsl:variable name="uppercase">abcdefghijklmnopqrstuvwxyzàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþ</xsl:variable>
	<xsl:variable name="apos">'</xsl:variable>


	<xsl:template match="/record">
		<unimarc>
			<notice>
				<xsl:element name="rs">*</xsl:element>
				<xsl:element name="ru">*</xsl:element>
				<xsl:element name="el">1</xsl:element>
				<xsl:element name="bl">m</xsl:element>
				<xsl:element name="hl">0</xsl:element>
				<xsl:element name="dt">l</xsl:element>
				<f c="001">
					<xsl:value-of select="header/identifier" />
				</f>
				<xsl:for-each select="metadata/oai1dtouch_dc:oai1dtouch_dc">
					<xsl:call-template name="title" />
					<xsl:call-template name="studios" />
					<xsl:call-template name="dateOfPublication" />
					<xsl:call-template name="requirements" />
					<xsl:call-template name="description" />
					<xsl:call-template name="styles" />
					<xsl:call-template name="artists" />
					<xsl:call-template name="url" />
					<xsl:call-template name="cover_url" />
				</xsl:for-each>
				<xsl:call-template name="serviceAndPath" />
			</notice>
		</unimarc>
	</xsl:template>

	<xsl:template name="title">
		<f c="200">
			<s c="a">
				<xsl:value-of select="oai1dtouch_dc:title" />
			</s>
		</f>
	</xsl:template>
	
	<xsl:template name="studios">
		<xsl:if test="oai1dtouch_dc:studios">
			<xsl:for-each select="oai1dtouch_dc:studios/oai1dtouch_dc:studio">
				<f c="210">
					<s c="a">
						<xsl:value-of select="oai1dtouch_dc:name" />
					</s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="dateOfPublication">
		<f c="210">
			<s c="d">
				<xsl:value-of select="substring(oai1dtouch_dc:dateOfPublication,1,4)" />
			</s>
		</f>
		<f c="219">
			<s c="d">
				<xsl:value-of select="concat(substring(oai1dtouch_dc:dateOfPublication,9,2), '/',  substring(oai1dtouch_dc:dateOfPublication,6,2), '/', substring(oai1dtouch_dc:dateOfPublication,1,4)) " />
			</s>
		</f>
	</xsl:template>

	<xsl:template name="requirements">
		<xsl:if test="oai1dtouch_dc:requirements!=''">
			<f c="327">
				<s c="a">
					<xsl:value-of select="oai1dtouch_dc:requirements" />
				</s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="description">
		<xsl:choose>
			<xsl:when test="oai1dtouch_dc:description/oai1dtouch_dc:fr!=''">
				<f c="330">
					<s c="a">
						<xsl:value-of select="oai1dtouch_dc:description/oai1dtouch_dc:fr" />
					</s>
				</f>
			</xsl:when>
			<xsl:when test="oai1dtouch_dc:description/oai1dtouch_dc:default!=''">
				<f c="330">
					<s c="a">
						<xsl:value-of select="oai1dtouch_dc:description/oai1dtouch_dc:default" />
					</s>
				</f>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="styles">
		<xsl:for-each select="oai1dtouch_dc:styles/oai1dtouch_dc:style">
			<f c="606">
				<s c="a">
					<xsl:value-of select="." />
				</s>
			</f>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="artists">
		<xsl:if test="oai1dtouch_dc:artists">
			<xsl:for-each select="oai1dtouch_dc:artists/oai1dtouch_dc:artist">
				<xsl:choose>
					<xsl:when test="position()=1">
						<f c="700">
							<s c="a">
								<xsl:value-of select="." />
							</s>
						</f>
					</xsl:when>
					<xsl:otherwise>
						<f c="701">
							<s c="a">
								<xsl:value-of select="." />
							</s>
						</f>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>

	<xsl:template name="url">
		<f c="856">
			<s c="u">
				<xsl:value-of select="oai1dtouch_dc:url" />
			</s>
		</f>
	</xsl:template>
	
	<xsl:template name="cover_url">
		<f c="896">
			<s c="a">
				<xsl:value-of select="oai1dtouch_dc:cover_url" />
			</s>
		</f>
	</xsl:template>

	<xsl:template name="serviceAndPath">
		<f c="901">
			<s c="a">
				<xsl:value-of select="header/setSpec" />
			</s>
			<s c="n">service</s>
		</f>
		<f c="902">
			<s c="a">
				<xsl:value-of select="metadata/oai1dtouch_dc:oai1dtouch_dc/oai1dtouch_dc:path" />
			</s>
			<s c="n">path</s>
		</f>
	</xsl:template>
	


	
</xsl:stylesheet>
