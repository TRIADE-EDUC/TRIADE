<?xml version="1.0" encoding="utf-8"?>
<!--
****************************************************************************************
 © 2002-2024 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: onix2uni.xsl,v 1.5.8.1.2.1 2025/05/02 12:00:29 dbellamy Exp $ -->

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
	<xsl:call-template name="resume"/>
	<xsl:call-template name="responsabilites"/>
	<xsl:call-template name="vignette"/>
</xsl:template>

<!-- identifier -->
<xsl:template name="identifier">

    <xsl:variable name="vEAN13">
	    <xsl:for-each select="./ProductIdentifier">
	        <xsl:if test="ProductIDType='03'">
                <xsl:value-of select="normalize-space(IDValue)"/>
            </xsl:if>
	    </xsl:for-each>
    </xsl:variable>

    <xsl:variable name="vISBN13">
        <xsl:for-each select="./ProductIdentifier">
            <xsl:if test="ProductIDType='15'">
                <xsl:value-of select="normalize-space(IDValue)"/>
            </xsl:if>
        </xsl:for-each>
    </xsl:variable>

    <xsl:choose>
        <xsl:when test="$vEAN13!=''">
	        <f c="010" ind="  ">
	            <s c="a"><xsl:value-of select="$vEAN13"/></s>
	        </f>
        </xsl:when>
        <xsl:when test="$vISBN13!=''">
	         <f c="010" ind="  ">
	             <s c="a"><xsl:value-of select="$vISBN13"/></s>
	         </f>
        </xsl:when>
     </xsl:choose>

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

	<xsl:variable name="vPublisherName">
		<xsl:for-each select="./PublishingDetail/Publisher" >
			<xsl:if test="PublishingRole='01'">
				<xsl:value-of select="PublisherName"/>
			</xsl:if>
		</xsl:for-each>
	</xsl:variable>

	<xsl:variable name="vPublishingDate">
		<xsl:for-each select="./PublishingDetail/PublishingDate" >
			<xsl:if test="PublishingDateRole='01'">
				<xsl:value-of select="Date" />
			</xsl:if>
		</xsl:for-each>
	</xsl:variable>

	<xsl:variable name="vDateFormat">
		<xsl:for-each select="./PublishingDetail/PublishingDate" >
			<xsl:if test="PublishingDateRole='01'">
				<xsl:choose>
					<xsl:when test="Date/@dateformat!=''">
						<xsl:value-of select="Date/@dateformat" />
					</xsl:when>
					<xsl:when test="DateFormat!=''">
						<xsl:value-of select="DateFormat" />
					</xsl:when>
					<xsl:otherwise>00</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</xsl:for-each>
	</xsl:variable>

	<xsl:variable name="vFormatedDate">
		<xsl:call-template name="format_onix_date">
			<xsl:with-param name="date_value"><xsl:value-of select="$vPublishingDate" /></xsl:with-param>
			<xsl:with-param name="date_format"><xsl:value-of select="$vDateFormat" /></xsl:with-param>
		</xsl:call-template>
	</xsl:variable>

	<xsl:if test="$vPublisherName!='' or $vFormatedDate!=''">
		<f c="210" ind="  ">
			<xsl:if test="$vPublisherName!=''" >
				<s c="c"><xsl:value-of select="$vPublisherName"/></s>
			</xsl:if>
			<xsl:if test="$vFormatedDate!=''" >
				<s c="d"><xsl:value-of select="$vFormatedDate"/></s>
			</xsl:if>
		</f>
	</xsl:if>
</xsl:template>

<!--  formatage dates selon ONIX Code Lists Issue 49, March 2020 -->
<xsl:template name="format_onix_date">

	<xsl:param name="date_value" />
	<xsl:param name="date_format" />

	<xsl:choose>

		<!-- YYYYMMDD (Année, mois, jour) -->
		<xsl:when test="$date_format='00'" >
			<xsl:value-of select="concat(substring($date_value,7,2), '/', substring($date_value,5,2), '/', substring($date_value,1,4) )" />
		</xsl:when>

		<!-- YYYYMM (Année, mois) -->
		<xsl:when test="$date_format='01'" >
			<xsl:value-of select="concat(substring($date_value,5,2), '/', substring($date_value,1,4) )" />
		</xsl:when>

		<!-- YYYYWW (Année, semaine) -->
		<xsl:when test="$date_format='02'" >
			<xsl:value-of select="concat('Sem.', substring($date_value,5,2),  ' ', substring($date_value,1,4) )" />
		</xsl:when>

		<!-- YYYYQ (Année, trimestre  avec Q=[1,2,3,4] et 1=Jan to Mar) -->
		<xsl:when test="$date_format='03'" >
			<xsl:variable name="vQuarter">
				<xsl:call-template name="getQuarter">
					<xsl:with-param name="quarter_number"><xsl:value-of select="substring($date_value,5,1)" /></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:value-of select="concat($vQuarter, ' ', substring($date_value,1,4) )" />
		</xsl:when>

		<!-- YYYYS (Année, saison avec S=[1,2,3,4] et 1=Printemps) -->
		<xsl:when test="$date_format='04'" >
			<xsl:variable name="vSeason">
				<xsl:call-template name="getSeason">
					<xsl:with-param name="season_number"><xsl:value-of select="substring($date_value,5,1)" /></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:value-of select="concat($vSeason, ' ', substring($date_value,1,4))" />
		</xsl:when>

		<!-- YYYY (Année) -->
		<xsl:when test="$date_format='05'" >
			<xsl:value-of select="$date_value" />
		</xsl:when>

		<!-- YYYYMMDDYYYYMMDD (Plage Années, mois, jours) -->
		<xsl:when test="$date_format='06'" >
			<xsl:value-of select="concat(substring($date_value,7,2), '/', substring($date_value,5,2), '/', substring($date_value,1,4) , ' - ',  substring($date_value,15,2), '/', substring($date_value,13,2), '/', substring($date_value,9,4) )" />
		</xsl:when>

		<!-- YYYYMMYYYYMM (Plage Années, mois) -->
		<xsl:when test="$date_format='07'" >
			<xsl:value-of select="concat(substring($date_value,5,2), '/', substring($date_value,1,4) , ' - ', substring($date_value,11,2), '/', substring($date_value,7,4) )" />
		</xsl:when>

		<!-- YYYYWWYYYYWW (Plage Années, semaines) -->
		<xsl:when test="$date_format='08'" >
			<xsl:value-of select="concat('Sem.', substring($date_value,5,2),  ' ', substring($date_value,1,4), ' - ',  'Sem.', substring($date_value,11,2),  ' ', substring($date_value,7,4) )" />
		</xsl:when>

		<!-- YYYYQYYYYQ (Plage Années, trimestre) -->
		<xsl:when test="$date_format='09'" >
			<xsl:variable name="vQuarter1">
				<xsl:call-template name="getQuarter">
					<xsl:with-param name="quarter_number"><xsl:value-of select="substring($date_value,5,1)" /></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="vQuarter2">
				<xsl:call-template name="getQuarter">
					<xsl:with-param name="quarter_number"><xsl:value-of select="substring($date_value,10,1)" /></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:value-of select="concat($vQuarter1,  ' ', substring($date_value,1,4) , ' - ',  $vQuarter2,  ' ', substring($date_value,6,4) )" />
		</xsl:when>

		<!-- YYYYSYYYYS (Plage Années, saisons)-->
		<xsl:when test="$date_format='10'" >
			<xsl:variable name="vSeason1">
				<xsl:call-template name="getSeason">
					<xsl:with-param name="season_number"><xsl:value-of select="substring($date_value,5,1)" /></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="vSeason2">
				<xsl:call-template name="getSeason">
					<xsl:with-param name="season_number"><xsl:value-of select="substring($date_value,10,1)" /></xsl:with-param>
				</xsl:call-template>
			</xsl:variable>
			<xsl:value-of select="concat($vSeason1, ' ', substring($date_value,1,4), ' - ', $vSeason2, ' ', substring($date_value,6,4) )" />
		</xsl:when>

		<!-- YYYYYYYY (Plage Années) -->
		<xsl:when test="$date_format='11'" >
			<xsl:value-of select="concat(substring($date_value,1,4), '-', substring($date_value,5,4) )" />
		</xsl:when>

		<!-- Text string -->
		<xsl:when test="$date_format='12'" >
			<xsl:value-of select="$date_value" />
		</xsl:when>

		<!-- YYYYMMDDThhmm -->
		<xsl:when test="$date_format='13'" >
			<xsl:value-of select="concat(substring($date_value,7,2), '/', substring($date_value,5,2), '/', substring($date_value,1,4) )" />
		</xsl:when>

		<!-- YYYYMMDDThhmmss -->
		<xsl:when test="$date_format='14'" >
			<xsl:value-of select="concat(substring($date_value,7,2), '/', substring($date_value,5,2), '/', substring($date_value,1,4) )" />
		</xsl:when>

		<!-- YYYYMMDD (H) (Calendrier Hégirien) -->
		<xsl:when test="$date_format='20'" >
			<xsl:value-of select="concat(substring($date_value,7,2), '/', substring($date_value,5,2), '/', substring($date_value,1,4), ' (H)' )" />
		</xsl:when>

		<!-- YYYYMM (H) (Calendrier Hégirien) -->
		<xsl:when test="$date_format='21'" >
			<xsl:value-of select="concat(substring($date_value,5,2), '/', substring($date_value,1,4), ' (H)' )" />
		</xsl:when>

		<!-- YYYY (H) (Calendrier Hégirien) -->
		<xsl:when test="$date_format='25'" >
			<xsl:value-of select="concat($date_value, ' (H)' )" />
		</xsl:when>

		<!-- Text string (H) (Calendrier Hégirien) -->
		<xsl:when test="$date_format='32'" >
			<xsl:value-of select="concat($date_value, ' (H)' )" />
		</xsl:when>

		<xsl:otherwise>
			<xsl:value-of select="concat(substring($date_value,7,2), '/', substring($date_value,5,2), '/', substring($date_value,1,4) )" />
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="getQuarter">
	<xsl:param name="quarter_number" />
	<xsl:variable name="vQuarterNumberSuffix">
		<xsl:choose>
			<xsl:when test="$quarter_number=1">er</xsl:when>
			<xsl:otherwise>e</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:value-of select="concat($quarter_number, $vQuarterNumberSuffix, ' trim.')" />
</xsl:template>

<xsl:template name="getSeason">
	<xsl:param name="season_number" />
	<xsl:choose>
		<xsl:when test="$season_number=1">Printemps</xsl:when>
		<xsl:when test="$season_number=2">Eté</xsl:when>
		<xsl:when test="$season_number=3">Automne</xsl:when>
		<xsl:when test="$season_number=4">Hiver</xsl:when>
	</xsl:choose>
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

			<xsl:variable name="vKeyNames">
				<xsl:choose>
					<xsl:when test="normalize-space(KeyNames)!=''" >
						<xsl:value-of select="normalize-space(KeyNames)" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="''" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>

			<xsl:variable name="vNamesBeforeKey">
				<xsl:choose>
					<xsl:when test="normalize-space(NamesBeforeKey)!=''" >
						<xsl:value-of select="normalize-space(NamesBeforeKey)" />
					</xsl:when>
						<xsl:otherwise>
					<xsl:value-of select="''" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>

			<xsl:variable name="vPersonName">
				<xsl:choose>
					<xsl:when test="$vNamesBeforeKey='' or $vKeyNames=''" >
						<xsl:value-of select="PersonName" />
					</xsl:when>
						<xsl:otherwise>
					<xsl:value-of select="concat($vKeyNames, ',', $vNamesBeforeKey)" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>

			<xsl:choose>
				<xsl:when test="normalize-space(ContributorRole)='A01'">
					<xsl:call-template name="do_auteur">
						<xsl:with-param name="string" select="$vPersonName" />
						<xsl:with-param name="separateur" select="','" />
						<xsl:with-param name="compteur" select="1" />
						<xsl:with-param name="code_function" select="'070'" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="do_auteur">
						<xsl:with-param name="string" select="$vPersonName" />
						<xsl:with-param name="separateur" select="','" />
						<xsl:with-param name="compteur" select="2" />
						<xsl:with-param name="code_function" select="''" />
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:for-each>
</xsl:template>


<!-- creation auteur -->
<xsl:template name="do_auteur">
	<xsl:param name="string"/>
	<xsl:param name="separateur"/>
	<xsl:param name="compteur"/>
	<xsl:param name="code_function"/>

	<!-- auteur principal ou autre auteur -->
	<xsl:variable name="code">
		<xsl:choose>
			<xsl:when test="$compteur='1'">700</xsl:when>
			<xsl:otherwise>701</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:if test="normalize-space($string)">
		<xsl:choose>
			<xsl:when test="contains($string,$separateur)">
				<xsl:if test="normalize-space(substring-before($string,$separateur))!=''">
					<f c="{$code}" ind="  ">
						<s c="a"><xsl:value-of select="normalize-space(substring-before($string,$separateur))"/></s>
						<s c="b"><xsl:value-of select="normalize-space(substring-after($string,$separateur))"/></s>
						<xsl:if test="$code_function!=''">
							<s c="4"><xsl:value-of select="$code_function" /></s>
						</xsl:if>
					</f>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<f c="{$code}" ind="  ">
					<s c="a"><xsl:value-of select="normalize-space($string)"/></s>
					<xsl:if test="$code_function!=''">
						<s c="4"><xsl:value-of select="$code_function" /></s>
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