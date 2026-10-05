<?xml version="1.0" encoding="iso-8859-1"?>
<!-- $Id: bibtex.xsl,v 1.7 2013/03/13 16:50:02 mbertin Exp $ -->

<!-- http://fr.wikipedia.org/wiki/BibTeX#cite_note-1 -->
<xsl:stylesheet version = '1.0'
     xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>

<xsl:output method="text"/>

<xsl:template match="pmbmarc">
		<xsl:apply-templates select="notice"/>
</xsl:template>

<xsl:template match="unimarc">
		<xsl:apply-templates select="notice"/>
</xsl:template>

<xsl:template match="notice">
	<xsl:text>@</xsl:text>
	<xsl:call-template name="niveau"/>
	<xsl:text>{</xsl:text>	
    <xsl:call-template name="identification"/>
	<xsl:text>,</xsl:text>	
		
	<xsl:if test="f[@c='200']/s[@c='a']">
	    <xsl:call-template name="titre"/>
	</xsl:if>
	<xsl:if test="f[@c='700']/s[@c='a']">
	    <xsl:call-template name="auteur"/>
	</xsl:if>
	<xsl:if test="f[@c='210']/s[@c='c']">
	    <xsl:call-template name="editeur"/>
	</xsl:if>
	<xsl:if test="f[@c='210']/s[@c='d'] or f[@c='463']/s[@c='e'] or f[@c='463']/s[@c='d']">
	    <xsl:call-template name="annee"/>
	</xsl:if>
	<xsl:if test="f[@c='210']/s[@c='a']">
	    <xsl:call-template name="adresse"/>
	</xsl:if>
	<xsl:if test="f[@c='215']/s[@c='a']">
	    <xsl:call-template name="pages"/>
	</xsl:if>
	<xsl:if test="(bl = 'm' and hl = '0')">
		<xsl:if test="f[@c='461']/s[@c='t']">
			<xsl:call-template name="series"/>
		</xsl:if>
		<xsl:if test="f[@c='461']/s[@c='v']">
			<xsl:call-template name="volume"/>
		</xsl:if>
	</xsl:if>
	<xsl:if test="f[@c='330']/s[@c='a']">
	    <xsl:call-template name="resume"/>
	</xsl:if>
	<xsl:if test="f[@c='327']/s[@c='a']">
	    <xsl:call-template name="contenu"/>
	</xsl:if>
	<xsl:if test="f[@c='010']/s[@c='a']">
	    <xsl:call-template name="isbn"/>
	</xsl:if>
	<xsl:if test="f[@c='856']/s[@c='u']">
	    <xsl:call-template name="url"/>
	</xsl:if>
	<xsl:if test="(bl = 'a' and hl = '2') or (bl = 's' and hl = '2')">
		<xsl:if test="f[@c='461']/s[@c='t']">
	    	<xsl:call-template name="journal"/>
		</xsl:if>
		<xsl:if test="f[@c='463']/s[@c='v']">
			<xsl:call-template name="number"/>
		</xsl:if>
	</xsl:if>
	
	<xsl:if test="f[@c='300']/s[@c='a']">
	    <xsl:call-template name="note"/>
	</xsl:if>
	<xsl:text>
}
</xsl:text>
</xsl:template>

<xsl:template name="identification">
	<!-- composition de l'identifiant de la notice : 6 premiers caractères du titre, nom de famille de l'auteur -->
	<xsl:if test="f[@c='001']">
	    <xsl:value-of select="f[@c='001']"/>
	</xsl:if>
	<xsl:text>-</xsl:text>
	<xsl:if test="f[@c='700']/s[@c='a']">
	    <xsl:value-of select="translate(f[@c='700']/s[@c='a'],' ','_')"/>
		<xsl:text>-</xsl:text>
	</xsl:if>
	<xsl:choose>
		<xsl:when test="f[@c='210']/s[@c='d']">
			<xsl:value-of select="substring(translate(f[@c='210']/s[@c='d'],' ','_'),1,6)"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="substring(translate(f[@c='200']/s[@c='a'],' ','_'),1,6)"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="niveau">
<xsl:choose>
	<xsl:when test="bl='m'">
		<xsl:text>book</xsl:text>
	</xsl:when>
	<xsl:when test="bl='a'">
		<xsl:text>article</xsl:text>
	</xsl:when>
	<xsl:otherwise>
		<xsl:text>misc</xsl:text>
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template name="titre">
		<xsl:text>
    title = "</xsl:text>
	<xsl:value-of select="f[@c='200']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="auteur">
	<xsl:text>,
    author = "</xsl:text>
	<xsl:if test="f[@c='700']">
		<xsl:value-of select="f[@c='700']/s[@c='b']"/>	
		<xsl:text> </xsl:text>
		<xsl:value-of select="f[@c='700']/s[@c='a']"/>
	</xsl:if>
	<xsl:for-each select="./f[@c='701']">
		<xsl:if test="position()>1 or ../f[@c='700']">
			<xsl:text> and </xsl:text>
		</xsl:if>
		<xsl:value-of select="s[@c='b']"/>	
		<xsl:text> </xsl:text>
		<xsl:value-of select="s[@c='a']"/>
	</xsl:for-each>
	<xsl:for-each select="./f[@c='711']">
		<xsl:if test="position()>1 or ../f[@c='710'] or ../f[@c='701']">
			<xsl:text> and </xsl:text>
		</xsl:if>
		<xsl:value-of select="s[@c='b']"/>	
		<xsl:text> </xsl:text>
		<xsl:value-of select="s[@c='a']"/>
	</xsl:for-each>
			<xsl:text>"</xsl:text>	
</xsl:template>

<xsl:template name="editeur">
		<xsl:text>,
    publisher = "</xsl:text>
	<xsl:value-of select="f[@c='210']/s[@c='c']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="annee">
		<xsl:text>,
    year = "</xsl:text>
	<xsl:choose>
		<xsl:when test="f[@c='210']/s[@c='d']">
			<xsl:value-of select="f[@c='210']/s[@c='d']"/>
		</xsl:when>
		<xsl:when test="f[@c='463']/s[@c='d']">
			<xsl:value-of select="substring-before(f[@c='210']/s[@c='d'], '-')"/>
		</xsl:when>
		<xsl:when test="f[@c='463']/s[@c='e']">
			<xsl:value-of select="normalize-space(f[@c='210']/s[@c='e'])"/>
		</xsl:when>
	</xsl:choose>
	
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="adresse">
		<xsl:text>,
    address = "</xsl:text>
	<xsl:value-of select="f[@c='210']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="pages">
		<xsl:text>,
    pages = "</xsl:text>
	<xsl:value-of select="f[@c='215']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>
	
<xsl:template name="series">
		<xsl:text>,
    series = "</xsl:text>
	<xsl:value-of select="f[@c='461']/s[@c='t']"/>
	<xsl:text>"</xsl:text>
</xsl:template>
	
<xsl:template name="volume">
		<xsl:text>,
    volume = "</xsl:text>
	<xsl:value-of select="f[@c='461']/s[@c='v']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="resume">
		<xsl:text>,
    abstract = "</xsl:text>
	<xsl:value-of select="f[@c='330']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="contenu">
		<xsl:text>,
    contents = "</xsl:text>
	<xsl:value-of select="f[@c='327']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="isbn">
		<xsl:text>,
    ISBN = "</xsl:text>
	<xsl:value-of select="f[@c='010']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="prix">
		<xsl:text>,
    price = "</xsl:text>
	<xsl:value-of select="f[@c='010']/s[@c='d']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<xsl:template name="url">
		<xsl:text>,
    URL = "</xsl:text>
	<xsl:value-of select="f[@c='856']/s[@c='u']"/>
	<xsl:text>"</xsl:text>
</xsl:template>
	
<xsl:template name="journal">
		<xsl:text>,
    journal = "</xsl:text>
	<xsl:value-of select="f[@c='461']/s[@c='t']"/>
	<xsl:text>"</xsl:text>
</xsl:template>
	
<xsl:template name="number">
	<xsl:choose>
		<xsl:when test="substring-after(normalize-space(f[@c='463']/s[@c='v']),'no. ')">
		<xsl:text>,
    number = "</xsl:text>
	<xsl:value-of select="substring-after(normalize-space(f[@c='463']/s[@c='v']),'no. ')"/>
	<xsl:text>"</xsl:text>
		</xsl:when>
		<xsl:when test="substring-after(normalize-space(f[@c='463']/s[@c='v']),'n° ')">	
		<xsl:text>,
    number = "</xsl:text>
	<xsl:value-of select="substring-after(normalize-space(f[@c='463']/s[@c='v']),'n° ')"/>
	<xsl:text>"</xsl:text>
		</xsl:when>
		<xsl:when test="normalize-space(f[@c='463']/s[@c='v'])">	
		<xsl:text>,
    number = "</xsl:text>
	<xsl:value-of select="snormalize-space(f[@c='463']/s[@c='v'])"/>
	<xsl:text>"</xsl:text>
		</xsl:when>
	</xsl:choose>
	<xsl:choose>
		<xsl:when test="substring-before(normalize-space(f[@c='463']/s[@c='v']),', ')">
		<xsl:text>,
    volume = "</xsl:text>
	<xsl:value-of select="substring-after(substring-before(normalize-space(f[@c='463']/s[@c='v']),', '),'vol. ')"/>
	<xsl:text>"</xsl:text>
		</xsl:when>
		<xsl:when test="substring-after(normalize-space(f[@c='463']/s[@c='v']),'vol. ')">
		<xsl:text>,
    volume = "</xsl:text>
	<xsl:value-of select="substring-after(normalize-space(f[@c='463']/s[@c='v']),'vol. ')"/>
	<xsl:text>"</xsl:text>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template name="note">
		<xsl:text>,
    note = "</xsl:text>
	<xsl:value-of select="f[@c='300']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>

<!-- pas d'export du titre de série pour l'instant
<xsl:template name="serie">
		<xsl:text>,
    series = "</xsl:text>
	<xsl:value-of select="f[@c='210']/s[@c='a']"/>
	<xsl:text>"</xsl:text>
</xsl:template>
-->

<xsl:template match="*"/>

</xsl:stylesheet>
