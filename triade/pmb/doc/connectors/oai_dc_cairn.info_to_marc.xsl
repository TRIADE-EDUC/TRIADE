<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
	version="1.0">
	
	<xsl:output method="xml" indent="yes"/>
	
	<xsl:template match="/record">
		<unimarc>
			<notice>
				<xsl:element name="rs">*</xsl:element>
				<xsl:element name="ru">*</xsl:element>
				<xsl:element name="el">1</xsl:element>
				<xsl:element name="bl">a</xsl:element>
				<xsl:element name="hl">2</xsl:element>
				<xsl:element name="dt">a</xsl:element>
				<f c="001">
					<xsl:value-of select="header/identifier"/>
				</f>
				<xsl:for-each select="metadata/oai_dc:dc">
					<xsl:call-template name="language"/>
					<xsl:call-template name="title"/>
					<xsl:call-template name="publisher"/>
					<xsl:call-template name="source"/>
					<xsl:call-template name="notes"/>
					<xsl:call-template name="subject"/>
					<xsl:call-template name="responsabilities"/>
					<xsl:call-template name="eresource"/>
				</xsl:for-each>
			</notice>
		</unimarc>
	</xsl:template>
	
	<xsl:template name="language">
		<xsl:for-each select="dc:language">
			<f c="101">
				<s c="a"><xsl:value-of select="."/></s>
			</f>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="title">
		<f c="200">
			<xsl:for-each select="dc:title">
				<xsl:choose>
					<xsl:when test="position()=1">
							<s c="a"><xsl:value-of select="."/></s>
					</xsl:when>
					<xsl:otherwise>
							<s c="e"><xsl:value-of select="."/></s>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</f>
	</xsl:template>
	
	<xsl:template name="publisher">
		<xsl:if test="dc:publisher!='' or dc:date!=''">
			<f c="210">
				<xsl:if test="dc:publisher"><s c="c"><xsl:value-of select="dc:publisher"/></s></xsl:if>
				<xsl:if test="dc:date"><s c="d"><xsl:value-of select="dc:date"/></s></xsl:if>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="source">
		<xsl:if test="dc:source!=''">
			<xsl:call-template name="split_source">
				<xsl:with-param name="input" select="dc:source"/>
				<xsl:with-param name="source" select="dc:source"/>
				<xsl:with-param name="reste" select="normalize-space(' ')" />
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="notes">
		<xsl:if test="dc:source!='' or dc:coverage!='' or dc:rights!=''">
			<f c="300">
				<xsl:for-each select="dc:source">
					<s c="a"><xsl:value-of select="."/></s>
				</xsl:for-each>
				<xsl:for-each select="dc:coverage">
					<s c="a"><xsl:value-of select="."/></s>
				</xsl:for-each>
				<xsl:for-each select="dc:rights">
					<s c="a"><xsl:value-of select="."/></s>
				</xsl:for-each>
			</f>
		</xsl:if>
		<xsl:if test="dc:description">
			<f c="330">
				<xsl:for-each select="dc:description">
					<s c="a"><xsl:value-of select="."/></s>
				</xsl:for-each>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="subject">
		<xsl:if test="dc:subject!=''">
			<xsl:for-each select="dc:subject">
				<f c="606">
					<s c="a"><xsl:value-of select="."></xsl:value-of></s>
				</f>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="responsabilities">
		<xsl:if test="dc:creator">
			<xsl:for-each select="dc:creator">
				<xsl:choose>
					<xsl:when test="position()=1">
						<f c="700">
							<s c="a"><xsl:value-of select="."/></s>
						</f>
					</xsl:when>
					<xsl:otherwise>
						<f c="701">
							<s c="a"><xsl:value-of select="."/></s>
						</f>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="dc:contributors">
			<f c="702">
				<xsl:for-each select="dc:contributors">
					<s c="a"><xsl:value-of select="."/></s>
				</xsl:for-each>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="eresource">
		<xsl:if test="dc:identifier[1]">
			<f c="856">
				<s c="u"><xsl:value-of select="dc:identifier[1]"/></s>
				<xsl:if test="dc:format[1]">
					<s c="q"><xsl:value-of select="dc:format[1]"/></s>
				</xsl:if>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="split_source">
		<xsl:param name="input" />
		<xsl:param name="source" />
		<xsl:param name="reste" />
		<xsl:param name="index" select="'1'"/>
	 
		<xsl:choose>
			<xsl:when test="contains($input, '|')">				
				<xsl:call-template name="split_source">
					<xsl:with-param name="input" select="substring-after($input, '|')"/>
					<xsl:with-param name="source" select="$source"/>
					<xsl:with-param name="reste" select="$reste"/>
					<xsl:with-param name="index" select="$index"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="$index = 1">	
						<f c="461">
							<s c="x"><xsl:value-of select="normalize-space($input)"/></s>
						</f>
					</xsl:when>
					<xsl:when test="$index = 2">	
						<f c="215">
							<s c="a"><xsl:value-of select="normalize-space($input)"/></s>
						</f>
					</xsl:when>
					<xsl:when test="$index = 3">
						<f c="463">
							<s c="d"><xsl:value-of select="normalize-space($input)"/></s>
						</f>
					</xsl:when>
					<xsl:when test="$index = 4">	
						<!-- ignore -->
					</xsl:when>
					<xsl:when test="$index = 5">
						<xsl:if test="normalize-space($input)!='-'">
							<f c="463">
								<s c="v"><xsl:value-of select="normalize-space($input)"/></s>
							</f>
						</xsl:if>
					</xsl:when>
					<xsl:when test="$index = 6">	
						<f c="461">
							<s c="t"><xsl:value-of select="normalize-space($input)"/></s>
						</f>
						
					</xsl:when>
				</xsl:choose>
				<xsl:if test="$index &gt; 0 and $index &lt; 7">
					<xsl:call-template name="split_source">
						<xsl:with-param name="index" select="number($index)+1"/>
						<xsl:with-param name="source" select="$source"/>
						<xsl:with-param name="reste" select="concat('|',$input,$reste)"/>
						<xsl:with-param name="input" select="substring-before($source, concat('|',$input,$reste))"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
		
</xsl:stylesheet>