<?xml version="1.0" encoding="utf-8"?>

<!-- 
****************************************************************************************
© 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: marc.xsl,v 1.4 2017/02/09 13:55:40 jpermanne Exp $ -->

<xsl:stylesheet version = '1.0'
     xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>

<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes"/>

<xsl:template match="pmbmarc">
<collection>
		<xsl:apply-templates select="notice"/>
</collection>
</xsl:template>

<xsl:template match="notice">
	<record>
		<xsl:call-template name="_100_9"/>
		<xsl:apply-templates select="./f"/>
	</record>
</xsl:template>

<xsl:template name="_100_9">
	<leader>00000n<xsl:value-of select="./dt"/><xsl:value-of select="./bl"/><xsl:value-of select="./hl"/>a00000001i 450 </leader>
</xsl:template>

<xsl:template match="notice/f">
	<xsl:choose>
		<xsl:when test="(@c='100') and (./s[@c='9'])">
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="./s">
					<datafield tag="{@c}" ind1="{substring(@ind,1,1)}" ind2="{substring(@ind,2,1)}">
						<xsl:apply-templates select="./s"/>
					</datafield>
				</xsl:when>
				<xsl:otherwise>
					<controlfield tag="{@c}"><xsl:value-of select="."/></controlfield>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="notice/f/s">
	<subfield code="{@c}"><xsl:value-of select="."/></subfield>
</xsl:template>

<xsl:template match="*"/>

</xsl:stylesheet>
