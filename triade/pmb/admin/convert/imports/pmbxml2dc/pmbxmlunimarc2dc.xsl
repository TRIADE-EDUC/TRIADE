<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dcterms="http://purl.org/dc/terms/" version="1.0">
<!-- Feuille de conversion pmb_xml_unimarc -> dublin core
****************************************************************************************
© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: pmbxmlunimarc2dc.xsl,v 1.6.18.1 2025/03/12 14:25:53 dgoron Exp $ -->

<xsl:output method="xml" indent="yes" encoding="utf-8"/>
<xsl:param name="notice_url_base"></xsl:param>

	<xsl:template match="/unimarc/notice">
		<oai_dc:dc xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
			<xsl:call-template name="identifier"/>
			<xsl:call-template name="language"/>
			<xsl:call-template name="title"/>
			<xsl:call-template name="publisher"/>
			<xsl:call-template name="date"/>
			<xsl:call-template name="collation"/>
			<xsl:call-template name="coverage"/>
			<xsl:call-template name="description"/>
			<xsl:call-template name="authors"/>
			<xsl:call-template name="category"/>
			<xsl:call-template name="relations"/>
			<xsl:call-template name="rights"/>
			<xsl:call-template name="type"/>
		</oai_dc:dc>
	</xsl:template>
	
	<xsl:template name="identifier">
		<!-- URL -->
		<xsl:if test="$notice_url_base!=''">
			<xsl:for-each select="f[@c=001]">
				<dc:identifier>
					<xsl:value-of select="$notice_url_base"/>index.php?lvl=notice_display&amp;id=<xsl:value-of select="."/>
				</dc:identifier>
			</xsl:for-each>		    
		</xsl:if>
		<!-- Notice ID -->
		<xsl:for-each select="f[@c=001]">
			<dc:identifier>
				<xsl:value-of select="."/>
			</dc:identifier>
		</xsl:for-each>
		<!-- ISBN -->
		<xsl:for-each select="f[@c=010]/s[@c='a']">
			<dc:identifier>urn:ISBN:<xsl:value-of select="."/></dc:identifier>
		</xsl:for-each>
		<!-- ISSN -->
		<xsl:for-each select="f[@c=011]/s[@c='a']">
			<dc:identifier>urn:ISSN:<xsl:value-of select="."/></dc:identifier>
		</xsl:for-each>
		<!-- Date de creation -->
		<xsl:for-each select="f[@c=100]/s[@c='a']">
			<dcterms:created><xsl:value-of select="substring(.,1,8)"/></dcterms:created>
		</xsl:for-each>
		<!-- Date de mise a jour -->
		<xsl:for-each select="f[@c=100]/s[@c='d']">
			<dcterms:modified><xsl:value-of select="substring(.,1,8)"/></dcterms:modified>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="language">
		<xsl:for-each select="f[@c=101]/s[@c='a']">
			<dc:language>
				<xsl:value-of select="."/>
			</dc:language>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="title">
		<!-- Titre propre -->
		<xsl:if test="f[@c=200]/s[@c='a']">
			<dc:title>
				<xsl:value-of select="f[@c=200]/s[@c='a']"/>
				<!-- Titre parallèle -->
				<xsl:if test="f[@c=200]/s[@c='d']">
					<xsl:text> = </xsl:text>
					<xsl:value-of select="f[@c=200]/s[@c='d']"/>
				</xsl:if>
				<!-- Complément du titre -->
				<xsl:if test="f[@c=200]/s[@c='e']">
					<xsl:text> : </xsl:text>
					<xsl:value-of select="f[@c=200]/s[@c='e']"/>
				</xsl:if>
				<!-- Titre propre d'un auteur différent -->
				<xsl:if test="f[@c=200]/s[@c='c']">
					<xsl:text> ; </xsl:text>
					<xsl:value-of select="f[@c=200]/s[@c='c']"/>
				</xsl:if>
			</dc:title>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="publisher">
		<xsl:for-each select="f[@c=210]/s[@c='c']">
			<dc:publisher>
				<xsl:value-of select="."/>
				<xsl:if test="../s[@c='a']">
					<xsl:text> (</xsl:text>
					<xsl:value-of select="../s[@c='a']"/>
					<xsl:text>)</xsl:text>
				</xsl:if>
			</dc:publisher>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="date">
		<xsl:for-each select="f[@c=009]/s[@c='a']">
			<dc:date>
				<xsl:value-of select="."/>
			</dc:date>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="collation">
		<!-- Importance matérielle (nombre de pages, d'éléments...) -->
		<xsl:for-each select="f[@c=215]/s[@c='a']">
			<dc:description>
				<xsl:value-of select="."/>
			</dc:description>
		</xsl:for-each>
		<!-- Autres caractéristiques matérielles (ill., ...) -->
		<xsl:for-each select="f[@c=215]/s[@c='c']">
			<dc:description>
				<xsl:value-of select="."/>
			</dc:description>
		</xsl:for-each>
		<!-- Matériel d'accompagnement -->
		<xsl:for-each select="f[@c=215]/s[@c='e']">
			<dc:description>
				<xsl:value-of select="."/>
			</dc:description>
		</xsl:for-each>
		<!-- Format -->
		<xsl:for-each select="f[@c=215]/s[@c='d']">
			<dc:format>
				<xsl:value-of select="."/>
			</dc:format>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="coverage">
		<xsl:for-each select="f[@c=300]/s[@c='a']">
			<dc:coverage>
				<xsl:value-of select="."/>
			</dc:coverage>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="description">
		<xsl:for-each select="f[@c=330]/s[@c='a']">
			<dc:description>
				<xsl:value-of select="."/>
			</dc:description>
		</xsl:for-each>
		<xsl:for-each select="f[@c=327]/s[@c='a']">
			<dc:description>
				<xsl:value-of select="."/>
			</dc:description>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="authors">
		<!-- Auteur principal -->
		<xsl:for-each select="f[@c=700]">
			<dc:creator>
				<xsl:value-of select="s[@c='a']"/>
				<xsl:if test="s[@c='b']">
					<xsl:text>, </xsl:text>
					<xsl:value-of select="s[@c='b']"/>
				</xsl:if>
			</dc:creator>
		</xsl:for-each>
		<!-- Auteur autre -->
		<xsl:for-each select="f[@c=701]">
			<dc:contributor>
				<xsl:value-of select="s[@c='a']"/>
				<xsl:if test="s[@c='b']">
					<xsl:text>, </xsl:text>
					<xsl:value-of select="s[@c='b']"/>
				</xsl:if>
			</dc:contributor>
		</xsl:for-each>
		<!-- Auteur secondaire -->
		<xsl:for-each select="f[@c=702]">
			<dc:contributor>
				<xsl:value-of select="s[@c='a']"/>
				<xsl:if test="s[@c='b']">
					<xsl:text>, </xsl:text>
					<xsl:value-of select="s[@c='b']"/>
				</xsl:if>
			</dc:contributor>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="category">
		<xsl:for-each select="f[@c=606]/s[@c='a']">
			<dc:subject>
				<xsl:value-of select="."/>
			</dc:subject>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="relations">
		<!-- Collections -->
		<xsl:for-each select="f[@c=225]/s[@c='a']">
			<dc:relation>
				<xsl:value-of select="."/>
			</dc:relation>
		</xsl:for-each>
		<!-- Sous collections -->
		<xsl:for-each select="f[@c=225]/s[@c='i']">
			<dc:relation>
				<xsl:value-of select="."/>
			</dc:relation>
		</xsl:for-each>
		<!-- Vignette -->
		<xsl:if test="f[@c=896]/s[@c='a']">
			<dc:relation>
				<xsl:text>Vignette : </xsl:text>
				<xsl:value-of select="f[@c=896]/s[@c='a']"/>
			</dc:relation>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="rights">
		<xsl:for-each select="f[@c=319]/s[@c='a']">
			<dc:rights>
				<xsl:value-of select="."/>
			</dc:rights>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="type">
		<xsl:choose>
			<xsl:when test="dt">
				<xsl:call-template name="gallica_type">
					<xsl:with-param name="entree" select="dt"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="gallica_type">
					<xsl:with-param name="entree" select="'a'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- Renvoyer le dc:type en fonction du dt -->
	<xsl:template name="gallica_type">
		<xsl:param name="entree"/>
 
		<!-- Pour les conversions directes -->
		<xsl:variable name="fichierSubst" select="document('../../includes/marc_tables/gallica_dctype_subst.xml')"/>
		<xsl:variable name="fichier" select="document('../../includes/marc_tables/gallica_dctype.xml')"/>
		
		<!-- Pour les conversions d'entrepots -->
		<xsl:variable name="fichierOaiSubst" select="document('../includes/marc_tables/gallica_dctype_subst.xml')"/>
		<xsl:variable name="fichierOai" select="document('../includes/marc_tables/gallica_dctype.xml')"/>
		
		<xsl:choose>
			<xsl:when test="$fichierSubst/gallica_dctype/entry[@code=$entree]">
				<xsl:copy-of select="$fichierSubst/gallica_dctype/entry[@code=$entree]/*" />
			</xsl:when>
			<xsl:when test="$fichier/gallica_dctype/entry[@code=$entree]">
				<xsl:copy-of select="$fichier/gallica_dctype/entry[@code=$entree]/*" />
			</xsl:when>
			<xsl:when test="$fichierOaiSubst/gallica_dctype/entry[@code=$entree]">
				<xsl:copy-of select="$fichierOaiSubst/gallica_dctype/entry[@code=$entree]/*" />
			</xsl:when>
			<xsl:when test="$fichierOai/gallica_dctype/entry[@code=$entree]">
				<xsl:copy-of select="$fichierOai/gallica_dctype/entry[@code=$entree]/*" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="$fichierSubst/gallica_dctype/entry[@code='a']">
						<xsl:copy-of select="$fichierSubst/gallica_dctype/entry[@code='a']/*" />
					</xsl:when>
					<xsl:when test="$fichier/gallica_dctype/entry[@code='a']">
						<xsl:copy-of select="$fichier/gallica_dctype/entry[@code='a']/*" />
					</xsl:when>
					<xsl:when test="$fichierOaiSubst/gallica_dctype/entry[@code='a']">
						<xsl:copy-of select="$fichierOaiSubst/gallica_dctype/entry[@code='a']/*" />
					</xsl:when>
					<xsl:when test="$fichierOai/gallica_dctype/entry[@code='a']">
						<xsl:copy-of select="$fichierOai/gallica_dctype/entry[@code='a']/*" />
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
</xsl:stylesheet>