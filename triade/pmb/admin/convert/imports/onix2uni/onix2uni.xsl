<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE stylesheet [
	<!ENTITY MAJUSCULE "ABCDEFGHIJKLMNOPQRSTUVWXYZ">
	<!ENTITY MINUSCULE "abcdefghijklmnopqrstuvwxyz">
	<!ENTITY MAJUS_EN_MINUS " '&MAJUSCULE;' , '&MINUSCULE;' ">
	<!ENTITY MINUS_EN_MAJUS " '&MINUSCULE;' , '&MAJUSCULE;' ">
]>
<xsl:stylesheet version = '1.0'
     xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>

<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes"/>

<xsl:variable name="majuscules">ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ</xsl:variable>
<xsl:variable name="minuscules">abcdefghijklmnopqrstuvwxyzàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþ</xsl:variable>
<xsl:variable name="apos">'</xsl:variable>

<xsl:template match="/ONIXMessage">
	<unimarc>
		<xsl:apply-templates select="Product"/>
	</unimarc>
</xsl:template>

<xsl:template match="Product">
	<xsl:call-template name="notice"/>
</xsl:template>


<xsl:template name="notice">
	<notice>
		<xsl:element name="rs">*</xsl:element>
		<xsl:element name="ru">*</xsl:element>
		<xsl:element name="el">1</xsl:element>
		<xsl:element name="dt">l</xsl:element>		
		<xsl:element name="bl">m</xsl:element>		
		<xsl:element name="hl">0</xsl:element>
		<xsl:call-template name="mono"/>
	</notice>
</xsl:template>

<!-- mono -->
<xsl:template name='mono'>	
	<xsl:call-template name="identifier"/>
	<xsl:call-template name="titres"/>	
	<xsl:call-template name="publisher"/>	
	<xsl:call-template name="edition"/>
	<xsl:call-template name="resume"/>
	<xsl:call-template name="responsabilites"/>
	<xsl:call-template name="vignette"/>
</xsl:template>

<!-- identifier -->
<xsl:template name="identifier">
	<xsl:if test="./ProductIdentifier/IDValue!=''">
		<f c="010" ind="  ">
			<s c="a"><xsl:value-of select="./ProductIdentifier/IDValue"/></s>
		</f>			
	</xsl:if>				
</xsl:template>

<!-- titres -->
<xsl:template name="titres">
	<!-- titre / complément de titre -->
	<xsl:if test="normalize-space(./DescriptiveDetail/TitleDetail/TitleElement/TitleText)!=''">
		<f c="200" ind="  ">
			<s c="a"><xsl:value-of select="normalize-space(./DescriptiveDetail/TitleDetail/TitleElement/TitleText)"/></s>
			<xsl:if test="normalize-space(./DescriptiveDetail/TitleDetail/TitleElement/Subtitle)!=''">
				<s c="e"><xsl:value-of select="normalize-space(./DescriptiveDetail/TitleDetail/TitleElement/Subtitle)"/></s>
			</xsl:if>
		</f>
	</xsl:if>
</xsl:template>

<!-- Editeur  -->
<xsl:template name="publisher">
	<xsl:if test="./PublishingDetail/Publisher/PublisherName!=''">
		<f c="210" ind="  ">
			<s c="c"><xsl:value-of select="./PublishingDetail/Publisher/PublisherName"/></s>
		</f>
	</xsl:if>
</xsl:template>

<!-- Edition : la date est en timestamp => sera à mettre à jour au format date dans la finctin d'import -->
<xsl:template name="edition">
	<!-- date edition -->
	<xsl:if test="./PublishingDetail/Publisher/PublishingDate/Date!=''">
		<f c="210" ind="  ">
			<s c="d"><xsl:value-of select="./PublishingDetail/Publisher/PublishingDate/Date"/></s>
		</f>
	</xsl:if>
</xsl:template>

<!-- Résumé -->
<xsl:template name="resume">
	<xsl:if test="./CollateralDetail/TextContent/Text!=''">
		<f c="330" ind="  ">
			<s c="a"><xsl:value-of select="./CollateralDetail/TextContent/Text"/></s>
		</f>
	</xsl:if>
</xsl:template>

<!-- responsabilites -->
<xsl:template name="responsabilites">	
	<xsl:for-each select="./DescriptiveDetail/Contributor">
		<xsl:if test="normalize-space(PersonName)!=''">
			<xsl:choose>
				<xsl:when test="normalize-space(ContributorRole)='A01'">
					<xsl:call-template name="do_auteur">
						<xsl:with-param name="string" select="PersonName"/>
						<xsl:with-param name="separateur" select="'\n'"/>
						<xsl:with-param name="compteur" select="1"/>
						<xsl:with-param name="code_function" select="'070'"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="do_auteur">
						<xsl:with-param name="string" select="PersonName"/>
						<xsl:with-param name="separateur" select="'\n'"/>
						<xsl:with-param name="compteur" select="2"/>
						<xsl:with-param name="code_function" select="''"/>
						
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:if>		
	</xsl:for-each>	
</xsl:template>


<!-- creation des responsabilites -->
<xsl:template name="do_auteur">
	<xsl:param name="string"/>
	<xsl:param name="separateur"/>
	<xsl:param name="compteur"/>
	<xsl:param name="code_function"/>
	
	<!-- auteur principale ou autre auteur -->
	<xsl:variable name="code">
		<xsl:choose>
			<xsl:when test="$compteur='1'"><xsl:text>700</xsl:text></xsl:when>
			<xsl:otherwise><xsl:text>701</xsl:text></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>	
	<xsl:if test="normalize-space($string)">
		<xsl:choose>
			<xsl:when test="contains($string,$separateur)">
				<xsl:if test="normalize-space(substring-before($string,$separateur))!=''">
					<f c="{$code}" ind="  ">
						<s c="a"><xsl:value-of select="normalize-space(substring-before($string,$separateur))"/></s>
					</f>
				</xsl:if>
				<xsl:call-template name="do_auteur">
					<xsl:with-param name="string" select="substring-after($string,$separateur)"/>
					<xsl:with-param name="separateur" select="$separateur"/>
					<xsl:with-param name="compteur" select="$compteur+1"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<f c="{$code}" ind="  ">
					<s c="a"><xsl:value-of select="normalize-space($string)"/></s>
				</f>	
				<f c="{$code}" ind="  ">
					<s c="4"><xsl:text>070</xsl:text></s>
				</f>				
			</xsl:otherwise>
		</xsl:choose>	
	</xsl:if>	
</xsl:template>

<!-- FONCTION UTILES -->

<!-- decoupage de champs multivalue -->
<xsl:template name="decoupe_chaine">
	<xsl:param name="value"/>
	<xsl:param name="sep"/>
	<xsl:param name="field"/>
	<xsl:param name="subfield"/>
	
	<xsl:if test="normalize-space($value)">
		<xsl:choose>
			<xsl:when test="contains($value,$sep)">
				<xsl:if test="normalize-space(substring-before($value,$sep))!=''">
					<f c="{$field}" ind="  ">
						<s c="a"><xsl:value-of select="normalize-space(substring-before($value,$sep))"/></s>
						<xsl:if test="$field='900'">
							<s c="n"><xsl:value-of select="$subfield"/></s>						
						</xsl:if>						
					</f>
				</xsl:if>
				
				<xsl:call-template name="decoupe_chaine">
					<xsl:with-param name="value" select="substring-after($value,$sep)"/>
					<xsl:with-param name="sep" select="$sep"/>
					<xsl:with-param name="field" select="$field"/>
					<xsl:with-param name="subfield" select="$subfield"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<f c="{$field}" ind="  ">
					<s c="a"><xsl:value-of select="normalize-space($value)"/></s>
					<xsl:if test="$field='900'">
						<s c="n"><xsl:value-of select="$subfield"/></s>						
					</xsl:if>
				</f>			
			</xsl:otherwise>
		</xsl:choose>	
	</xsl:if>	
</xsl:template>

<!-- Vignette -->
<xsl:template name="vignette">	
	<xsl:for-each select="./CollateralDetail/SupportingResource">
		<xsl:if test="ResourceContentType='01'">
			<f c="896" ind="  ">
				<s c="a"><xsl:value-of select="ResourceVersion/ResourceLink"/></s>
			</f>				
		</xsl:if>
	</xsl:for-each>	
</xsl:template>

<xsl:template match="*" />

</xsl:stylesheet>