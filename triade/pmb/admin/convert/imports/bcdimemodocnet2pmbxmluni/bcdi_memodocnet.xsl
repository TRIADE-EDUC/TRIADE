<?xml version="1.0" encoding="iso-8859-1"?>
<!--
****************************************************************************************
© 2002-2024 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: bcdi_memodocnet.xsl,v 1.2.14.1 2024/08/28 14:10:18 rtigero Exp $ -->

<!DOCTYPE stylesheet [
	<!ENTITY MAJUSCULE "ABCDEFGHIJKLMNOPQRSTUVWXYZ">
	<!ENTITY MINUSCULE "abcdefghijklmnopqrstuvwxyz">
	<!ENTITY MAJUS_EN_MINUS " '&MAJUSCULE;' , '&MINUSCULE;' ">
	<!ENTITY MINUS_EN_MAJUS " '&MINUSCULE;' , '&MAJUSCULE;' ">
]>
<xsl:stylesheet version = '1.0'
     xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>

<xsl:output method="xml" version="1.0" encoding="iso-8859-1" indent="yes"/>

<xsl:template match="/MEMO_NOTICES">
<unimarc>
		<xsl:apply-templates select="NOTICES/NOTICE_GENERALE"/>
		<xsl:apply-templates select="NOTICES/NOTICE_PARTIE/NOTICE_GENERALE"/>
		<xsl:apply-templates select="NOTICES/NOTICE_PARTIE"/>
</unimarc>
</xsl:template>


<!--  on crée toutes les notices : mère et fille => s'il y a doublons, elle sauteront à l'import -->
<xsl:template match="NOTICES/NOTICE_GENERALE">
	<xsl:call-template name="do_notice">
		<xsl:with-param name="notice" select="."/>
		<xsl:with-param name="mere" select="'oui'"/>
	</xsl:call-template>
</xsl:template>


<xsl:template match="NOTICES/NOTICE_PARTIE">
	<xsl:call-template name="do_notice">
		<xsl:with-param name="notice" select="."/>
		<xsl:with-param name="mere" select="'non'"/>
	</xsl:call-template>
</xsl:template>


<xsl:template name="do_notice">
	<xsl:param name="notice"/>
	<xsl:param name="mere"/>
	<notice>
		<xsl:element name="rs">*</xsl:element>
		<xsl:element name="ru">*</xsl:element>
		<xsl:element name="el">1</xsl:element>

		<!-- Niveau hiérarchique et bibliographique -->
		<xsl:if test="$notice/TYPE_NOTICE_N">
			<xsl:call-template name="type_notice">
				<xsl:with-param name="noeud" select="$notice/TYPE_NOTICE_N"/>
			</xsl:call-template>
		</xsl:if>

		<!-- doctype -->
		<xsl:choose>
			<xsl:when test="$notice/TYPE_DOC_N">
				<xsl:call-template name="type_doc">
					<xsl:with-param name="noeud" select="$notice/TYPE_DOC_N"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="$notice/NOTICE_GENERALE/TYPE_DOC_N">
				<xsl:call-template name="type_doc">
					<xsl:with-param name="noeud" select="$notice/NOTICE_GENERALE/TYPE_DOC_N"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>

		<!-- Dans l'ordre -->
		<!-- Numéro de référence -->
		<xsl:call-template name="ref">
			<xsl:with-param name="noeud" select="$notice/IDENTITE_N"/>
		</xsl:call-template>
		<!-- ISBN/PRIX-->
		<xsl:call-template name="isbn">
			<xsl:with-param name="noeud" select="$notice/ISBN_N"/>
		</xsl:call-template>
		<xsl:call-template name="issn">
			<xsl:with-param name="noeud" select="$notice/ISSN_N"/>
		</xsl:call-template>
		<!-- Langue -->
		<xsl:call-template name="langue">
			<xsl:with-param name="noeud" select="$notice/LANGUE_N"/>
		</xsl:call-template>
		<!-- Date de création -->
		<xsl:call-template name="date_creation">
			<xsl:with-param name="noeud" select="$notice/DATE_SAISIE_N"/>
		</xsl:call-template>
		<!-- Titres -->
		<xsl:call-template name="titre">
			<xsl:with-param name="noeud" select="$notice/TITRE_N"/>
		</xsl:call-template>
		<!-- Mention d'édition -->
		<xsl:call-template name="mention_edition">
			<xsl:with-param name="noeud" select="$notice/EDITION_N"/>
		</xsl:call-template>
		<!-- Editeur -->
		<xsl:call-template name="editeurs">
			<xsl:with-param name="noeud" select="$notice/EDITEURS"/>
		</xsl:call-template>
		<!-- Date edition -->
		<xsl:call-template name="date_edition">
			<xsl:with-param name="noeud" select="$notice/DATE_PARUTION_N"/>
		</xsl:call-template>
		<!-- Collation -->
		<xsl:call-template name="collation">
			<xsl:with-param name="noeud" select="$notice/COLLATION_N"/>
		</xsl:call-template>
		<!-- Collection -->
		<xsl:if test="$notice/SUPPORT_N!='Périodique'">
			<xsl:call-template name="collection">
				<xsl:with-param name="noeud" select="$notice/COLLECTIONS"/>
			</xsl:call-template>
		</xsl:if>
		<!-- Notes -->
		<xsl:call-template name="notes">
			<xsl:with-param name="noeud" select="$notice"/>
		</xsl:call-template>
		<!-- EAN -->
		<xsl:call-template name="ean">
			<xsl:with-param name="noeud" select="$notice/CODE_BARRE_N"/>
		</xsl:call-template>
		<!-- Série -->
		<!-- Périodiques -->
		<xsl:choose>
			<xsl:when test="$notice/SUPPORT_N='Périodique' or $notice/TYPE_NOTICE_N='Article'">
				<xsl:choose>
					<xsl:when test="$notice/NOTICE_GENERALE/COLLECTIONS/COLLECTION_C">
						<xsl:call-template name="periodiques">
							<xsl:with-param name="noeud" select="$notice/NOTICE_GENERALE"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="periodiques">
							<xsl:with-param name="noeud" select="$notice"/>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="($notice/TYPE_NOTICE_N='Partie' or $notice/TYPE_NOTICE_N='Contribution') and ($notice/NOTICE_GENERALE/IDENTITE_N != $notice/IDENTITE_N)">
				<xsl:call-template name="chapitre">
					<xsl:with-param name="noeud" select="$notice"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
		<!-- Descripteurs -->
		<xsl:call-template name="descripteurs">
			<xsl:with-param name="noeud" select="$notice/DESCRIPTEURS_N"/>
		</xsl:call-template>
		<!-- Mots clés -->
		<xsl:call-template name="mots_clefs">
			<xsl:with-param name="noeud" select="$notice/MOTS_CLES_N"/>
		</xsl:call-template>
		<!-- Dewey -->
		<!--<xsl:call-template name="dewey"/>-->
		<!-- Auteurs -->
		<xsl:if test="AUTEURS">
			<xsl:call-template name="construct_auteurs">
				<xsl:with-param name="compteur" select="1"/>
				<xsl:with-param name="fonctions" select="$notice/FONCTIONS_N"/>
				<xsl:with-param name="notc" select="$notice/AUTEURS"/>
			</xsl:call-template>
		</xsl:if>
		<!-- URL -->
		<xsl:call-template name="url">
			<xsl:with-param name="noeud" select="$notice/LIEN_N"/>
		</xsl:call-template>
		<!-- Champs persos -->
		<xsl:call-template name="persos">
			<xsl:with-param name="noeud" select="$notice"/>
		</xsl:call-template>
		<!-- Exemplaires -->
		<xsl:call-template name="exemplaires">
			<xsl:with-param name="n_ex" select="1"/>
			<xsl:with-param name="noeud" select="$notice/EXEMPLAIRES"/>
		</xsl:call-template>
	</notice>
</xsl:template>

<!-- Construction de la liste des auteurs -->
<xsl:template name="construct_auteurs">
	<xsl:param name="compteur"/>
	<xsl:param name="fonctions"/>
	<xsl:param name="notc"/>

	<xsl:variable name="auteur_no" select="substring-before($fonctions,'/')"/>
	<xsl:element name="f">
		<!-- code unimarc : aut physique ou moral -->
		<xsl:attribute name="c">
			<xsl:choose>
				<xsl:when test="$compteur=1 and contains($notc[$compteur]/TYPE_AUTEUR_A,'Collec')">710</xsl:when>
				<xsl:when test="$compteur=1">700</xsl:when>
				<xsl:when test="$compteur!=1 and contains($notc[$compteur]/TYPE_AUTEUR_A,'Collec')">711</xsl:when>
				<xsl:otherwise>701</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:attribute name="ind"> 0</xsl:attribute>
		<xsl:if test="$notc[$compteur]">
			<xsl:apply-templates select="$notc[$compteur]/*"/>
		</xsl:if>
		<xsl:if test="$auteur_no">
			<xsl:element name="s">
				<xsl:attribute name="c">4</xsl:attribute>
				<xsl:choose>
					<xsl:when test="normalize-space($auteur_no)='Auteur'">
						<xsl:text>070</xsl:text>
					</xsl:when>
					<xsl:when test='normalize-space($auteur_no)="Chef d&apos;orchestre"'>
						<xsl:text>250</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Compositeur'">
						<xsl:text>230</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Directeur de la publication'">
						<xsl:text>651</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Graphiste'">
						<xsl:text>410</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Illustrateur'">
						<xsl:text>440</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Interprète'">
						<xsl:text>590</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Interviewé'">
						<xsl:text>460</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Intervieweur'">
						<xsl:text>470</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Parolier'">
						<xsl:text>520</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Photographe'">
						<xsl:text>600</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Réalisateur' or normalize-space($auteur_no)='Vidéaste'">
						<xsl:text>300</xsl:text>
					</xsl:when>
					<xsl:when test="normalize-space($auteur_no)='Traducteur'">
						<xsl:text>730</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>070</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:element>
		</xsl:if>
	</xsl:element>
	<xsl:if test="$notc[$compteur+1]">
		<xsl:call-template name="construct_auteurs">
			<xsl:with-param name="compteur" select="$compteur+1"/>
			<xsl:with-param name="fonctions" select="substring-after($fonctions,'/')"/>
			<xsl:with-param name="notc" select="$notc"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<xsl:template match="AUTEUR_A">
	<xsl:choose>
		<xsl:when test="contains(.,',')">
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="substring-before(.,',')"/>
			</xsl:element>
			<xsl:element name="s">
				<xsl:attribute name="c">b</xsl:attribute>
				<xsl:value-of select="normalize-space(substring-after(.,','))"/>
			</xsl:element>
		</xsl:when>
		<xsl:otherwise>
			<xsl:element name="s">
					<xsl:attribute name="c">a</xsl:attribute>
					<xsl:value-of select="."/>
			</xsl:element>
		</xsl:otherwise>
	</xsl:choose>
	<!-- Autres éléments -->
	<xsl:if test="../DATE_DE_NAISSANCE_A|../DATE_DE_DECES_A">
		<xsl:element name="s">
				<xsl:attribute name="c">f</xsl:attribute>
				<xsl:value-of select="concat(../DATE_DE_NAISSANCE_A,'-',../DATE_DE_DECES_A)"/>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- Identifiant unique IDENTITE_N -->
<xsl:template name="ref">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">001</xsl:attribute>
			<xsl:value-of select="$noeud"/>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- Type de notice et niveau hiérarchique TYPE_NOTICE_N -->
<xsl:template name="type_notice">
	<xsl:param name="noeud"/>
	<xsl:element name="bl">
		<xsl:choose>
			<xsl:when test="$noeud='Notice générale' or $noeud='Partie' or $noeud='Contribution'">
				<xsl:text>m</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>a</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:element>
	<xsl:element name="hl">
		<xsl:choose>
			<xsl:when test="$noeud='Article'">
				<xsl:text>2</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>0</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:element>
</xsl:template>

<!--LANGUE -->
<xsl:template name="langue">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:call-template name="construct_repeat">
			<xsl:with-param name="chaine" select="translate($noeud,&MAJUS_EN_MINUS;)"/>
			<xsl:with-param name="field_number" select="'101'"/>
			<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<!-- date de création -->
<xsl:template name="date_creation">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">100</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="substring-after(substring-after($noeud,'/'),'/')"/>
				<xsl:value-of select="substring-before(substring-after($noeud,'/'),'/')"/>
				<xsl:value-of select="substring-before($noeud,'/')"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>


<!--TITRE -->
<xsl:template name="titre">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">200</xsl:attribute>
			<xsl:attribute name="ind">
				<xsl:for-each select="$noeud/../SIGNIFICATIF_N">
					<xsl:if test='.="Oui"'>1 </xsl:if>
					<xsl:if test='.="Non"'>2 </xsl:if>
				</xsl:for-each>
			</xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- COLLATION -->
<xsl:template name="collation">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">215</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:if test="$noeud">
				<xsl:element name="s">
					<xsl:attribute name="c">a</xsl:attribute>
					<xsl:value-of select="$noeud"/>
				</xsl:element>
			</xsl:if>
			<xsl:if test="$noeud/../STANDARD_N">
				<xsl:element name="s">
					<xsl:attribute name="c">d</xsl:attribute>
					<xsl:value-of select="$noeud/../STANDARD_N"/>
				</xsl:element>
			</xsl:if>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- DOCUMENTS -->

<!-- Type de document -->
<xsl:template name="type_doc">
	<xsl:param name="noeud"/>
	<xsl:element name="dt">
		<xsl:choose>
			<xsl:when test="$noeud='Document projeté, vidéo'">
				<xsl:text>g</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Texte imprimé'">
				<xsl:text>a</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Enregistrement sonore'">
				<xsl:text>i</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Texte manuscrit'">
				<xsl:text>b</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Document cartographique'">
				<xsl:text>e</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Ressource électronique'">
				<xsl:text>l</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Ressource en ligne'">
				<xsl:text>l</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Document graphique'">
				<xsl:text>k</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Document multisupport'">
				<xsl:text>m</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Objet 3 dimensions'">
				<xsl:text>r</xsl:text>
			</xsl:when>
			<xsl:when test="$noeud='Autre'">
				<xsl:text>m</xsl:text>
			</xsl:when>
		</xsl:choose>
	</xsl:element>
</xsl:template>

<!-- EAN -->
<xsl:template name="ean">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">345</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">b</xsl:attribute>
				<xsl:value-of select="$noeud"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- ISBN -->
<xsl:template name="isbn">
	<xsl:param name="noeud"/>
	<xsl:if test="$noeud/../COUT_N | $noeud">
		<xsl:element name="f">
			<xsl:attribute name="c">010</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:if test="$noeud">
				<xsl:element name="s">
					<xsl:attribute name="c">a</xsl:attribute>
					<xsl:value-of select="normalize-space(translate($noeud,'/',''))"/>
				</xsl:element>
			</xsl:if>
			<xsl:if test="$noeud/../COUT_N">
				<xsl:element name="s">
					<xsl:attribute name="c">d</xsl:attribute>
					<xsl:value-of select="$noeud/../COUT_N"/>
				</xsl:element>
			</xsl:if>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- ISSN -->
<xsl:template name="issn">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)">
		<xsl:element name="f">
			<xsl:attribute name="c">011</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="normalize-space(translate($noeud,'/',''))"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- Mention d'édition -->
<xsl:template name="mention_edition">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">205</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!--EDITEUR -->
<xsl:template name="editeurs">
	<xsl:param name="noeud"/>
	<xsl:if test="$noeud!=''">
		<xsl:for-each select='$noeud'>
			<xsl:variable name="pos" select="position()"/>
			<xsl:variable name="dat" select="$noeud/../DATE_PARUTION_N"/>

			<xsl:if test="$pos &lt; '3'">
				<xsl:call-template name="do_editeur">
					<xsl:with-param name="noeud_editeur" select="."/>
					<xsl:with-param name="position" select="$pos"/>
					<xsl:with-param name="date" select="$dat"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:if>
</xsl:template>

<xsl:template name="do_editeur">
	<xsl:param name="noeud_editeur"/>
	<xsl:param name="position"/>
	<xsl:param name="date"/>

	<xsl:element name="f">
		<xsl:attribute name="c">210</xsl:attribute>
		<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
		<xsl:if test="$noeud_editeur/VILLE_E">
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud_editeur/VILLE_E"/>
			</xsl:element>
		</xsl:if>
		<xsl:if test="$noeud_editeur/EDITEUR_E">
			<xsl:element name="s">
				<xsl:attribute name="c">c</xsl:attribute>
				<xsl:value-of select="$noeud_editeur/EDITEUR_E"/>
			</xsl:element>
		</xsl:if>
		<xsl:if test="$noeud_editeur/PAYS_E">
			<xsl:element name="s">
				<xsl:attribute name="c">z</xsl:attribute>
				<xsl:value-of select="$noeud_editeur/PAYS_E"/>
			</xsl:element>
		</xsl:if>
		<xsl:if test="$noeud_editeur/ADRESSE_E|$noeud_editeur/CODE_POSTAL_E">
			<xsl:element name="s">
				<xsl:attribute name="c">b</xsl:attribute>
				<xsl:value-of select="normalize-space(concat($noeud_editeur/ADRESSE_E,' ',$noeud_editeur/CODE_POSTAL_E))"/>
			</xsl:element>
		</xsl:if>
		<xsl:if test="$position='1' and normalize-space($date)!=''">
			<xsl:element name="s">
				<xsl:attribute name="c">d</xsl:attribute>
				<xsl:value-of select="$date"/>
			</xsl:element>
		</xsl:if>
		<xsl:if test="normalize-space(substring-before($noeud_editeur/AUTRES_FORMES_E,'/'))!='' and normalize-space(substring-before($noeud_editeur/AUTRES_FORMES_E,'/'))!=normalize-space($noeud_editeur/EDITEUR_E)">
			<xsl:element name="s">
				<xsl:attribute name="c">n</xsl:attribute>
				<xsl:value-of select="normalize-space(substring-before($noeud_editeur/AUTRES_FORMES_E,'/'))"/>
			</xsl:element>
		</xsl:if>
	</xsl:element>
</xsl:template>

<!-- DATE EDITION -->
<xsl:template name="date_edition">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!='' and not($noeud/../EDITEURS)">
		<xsl:element name="f">
			<xsl:attribute name="c">210</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">d</xsl:attribute>
				<xsl:value-of select="$noeud"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- COLLECTIONS -->
<xsl:template name="collection">
	<xsl:param name="noeud"/>
	<xsl:if test="$noeud">
		<xsl:element name="f">
			<xsl:attribute name="c">225</xsl:attribute>
			<xsl:attribute name="ind">1 </xsl:attribute>
			<!-- NOM COLLECTION -->
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud/COLLECTION_C"/>
			</xsl:element>
			<!-- NUMERO DANS LA COLLECTION -->
			<xsl:if test="normalize-space(NO_COLLECTION_N)!=''">
				<xsl:element name="s">
					<xsl:attribute name="c">v</xsl:attribute>
					<xsl:value-of select="NO_COLLECTION_N"/>
				</xsl:element>
			</xsl:if>
			<!-- ISSN COLLECTION -->
			<xsl:if test="$noeud/ISSN_C_C">
				<xsl:element name="s">
					<xsl:attribute name="c">x</xsl:attribute>
					<xsl:value-of select="$noeud/ISSN_C_C"/>
				</xsl:element>
			</xsl:if>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- NOTES -->
<xsl:template name="notes">
	<xsl:param name="noeud"/>
	<!-- Notes générale -->
	<xsl:if test="normalize-space($noeud/NOTES_N)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">300</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud/NOTES_N"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
	<!-- Note de contenu -->
	<xsl:if test="normalize-space($noeud/DIVERS_N)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">327</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud/DIVERS_N"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
	<!-- Résumé -->
	<xsl:if test="normalize-space($noeud/RESUME_N)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">330</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud/RESUME_N"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- MOTS CLES -->
<xsl:template name="mots_clefs">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:call-template name="construct_repeat">
			<xsl:with-param name="chaine" select="normalize-space($noeud)"/>
			<xsl:with-param name="field_number" select="'610'"/>
			<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<!-- DESCRIPTEURS -->
<xsl:template name="descripteurs">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:call-template name="construct_repeat">
			<xsl:with-param name="chaine" select="normalize-space($noeud)"/>
			<xsl:with-param name="field_number" select="'606'"/>
			<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<!-- Dewey -->
<xsl:template name="dewey">
	<xsl:if test="DOCUMENTS/COTE_D">
		<xsl:element name="f">
			<xsl:attribute name="c">676</xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="DOCUMENTS/COTE_D"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- Champs persos -->
<xsl:template name="persos">
	<xsl:param name="noeud"/>
	<!-- Thèmes -->
	<xsl:if test="$noeud/GENRES_N">
		<xsl:call-template name="construct_repeat">
			<xsl:with-param name="chaine" select="$noeud/GENRES_N"/>
			<xsl:with-param name="field_number" select="'900'"/>
			<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>

	<!-- Disciplines -->
	<xsl:if test="$noeud/DISCIPLINES_N">
		<xsl:call-template name="construct_repeat">
				<xsl:with-param name="chaine" select="$noeud/DISCIPLINES_N"/>
				<xsl:with-param name="field_number" select="'902'"/>
				<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>

	<!-- Genre -->
	<xsl:if test="$noeud/NATURES_N">
		<xsl:call-template name="construct_repeat">
				<xsl:with-param name="chaine" select="$noeud/NATURES_N"/>
				<xsl:with-param name="field_number" select="'901'"/>
				<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>

	<!-- Année de péremption -->
	<xsl:if test="$noeud/DATE_PEREMPTION_N">
		<xsl:element name="f">
			<xsl:attribute name="c">903</xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="$noeud/DATE_PEREMPTION_N"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>

	<!-- Date de saisie -->
	<xsl:if test="$noeud/DATE_SAISIE_N">
		<xsl:element name="f">
			<xsl:attribute name="c">904</xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">a</xsl:attribute>
				<xsl:value-of select="substring-after(substring-after($noeud/DATE_SAISIE_N,'/'),'/')"/>
				<xsl:text>-</xsl:text>
				<xsl:value-of select="substring-before(substring-after($noeud/DATE_SAISIE_N,'/'),'/')"/>
				<xsl:text>-</xsl:text>
				<xsl:value-of select="substring-before($noeud/DATE_SAISIE_N,'/')"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>

	<!-- Type de nature -->
	<xsl:if test="$noeud/TYPES_NATURE_N">
		<xsl:call-template name="construct_repeat">
				<xsl:with-param name="chaine" select="$noeud/TYPES_NATURE_N"/>
				<xsl:with-param name="field_number" select="'905'"/>
				<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>

	<!-- Niveau -->
	<xsl:if test="$noeud/NIVEAUX_N">
		<xsl:call-template name="construct_repeat">
				<xsl:with-param name="chaine" select="$noeud/NIVEAUX_N"/>
				<xsl:with-param name="field_number" select="'906'"/>
				<xsl:with-param name="subfield_number" select="'a'"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<!-- Traitement d'une chaine séparée par des '/' -->
<xsl:template name="construct_repeat">
	<xsl:param name="chaine"/>
	<xsl:param name="field_number"/>
	<xsl:param name="subfield_number"/>

	<xsl:if test="normalize-space(substring-before($chaine,'/'))">
		<xsl:variable name="chaine_no" select="substring-before($chaine,'/')"/>
		<xsl:element name="f">
			<xsl:attribute name="c"><xsl:value-of select="$field_number"/></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c"><xsl:value-of select="$subfield_number"/></xsl:attribute>
				<xsl:value-of select="normalize-space($chaine_no)"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>

	<xsl:if test="not(normalize-space(substring-before($chaine,'/'))) and normalize-space($chaine)">
		<xsl:variable name="chaine_no" select="$chaine"/>
		<xsl:element name="f">
			<xsl:attribute name="c"><xsl:value-of select="$field_number"/></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c"><xsl:value-of select="$subfield_number"/></xsl:attribute>
				<xsl:value-of select="normalize-space($chaine_no)"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>

	<xsl:if test="substring-after($chaine,'/')">
		<xsl:call-template name="construct_repeat">
			<xsl:with-param name="chaine" select="substring-after($chaine,'/')"/>
			<xsl:with-param name="field_number" select="$field_number"/>
			<xsl:with-param name="subfield_number" select="$subfield_number"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<!-- URL -->
<xsl:template name="url">
	<xsl:param name="noeud"/>
	<xsl:if test="normalize-space($noeud)!=''">
		<xsl:element name="f">
			<xsl:attribute name="c">856</xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">u</xsl:attribute>
				<xsl:value-of select="normalize-space($noeud)"/>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- Périodiques -->
<xsl:template name="periodiques">
	<xsl:param name="noeud"/>
	<xsl:if test="$noeud">
		<xsl:element name="f">
			<xsl:attribute name="c">464</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>

			<!-- info perio -->
			<xsl:choose>
				<xsl:when test="normalize-space($noeud/COLLECTIONS)">
					<xsl:choose>
						<xsl:when test="normalize-space($noeud/COLLECTIONS/COLLECTION_C)">
							<xsl:element name="s">
								<xsl:attribute name="c">t</xsl:attribute>
								<xsl:value-of select="normalize-space($noeud/COLLECTIONS/COLLECTION_C)"/>
							</xsl:element>
						</xsl:when>
						<xsl:otherwise>
							<xsl:element name="s">
								<xsl:attribute name="c">t</xsl:attribute>
								<xsl:text>Notice Sans Titre (Notice de Periodique)</xsl:text>
							</xsl:element>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="$noeud/COLLECTIONS/ISSN_C_C">
						<xsl:element name="s">
							<xsl:attribute name="c">x</xsl:attribute>
							<xsl:value-of select="$noeud/COLLECTIONS/ISSN_C_C"/>
						</xsl:element>
					</xsl:if>
				</xsl:when>
				<xsl:when test="$noeud/TITRE_NG_N">
					<xsl:element name="s">
						<xsl:attribute name="c">t</xsl:attribute>
						<xsl:value-of select="normalize-space($noeud/TITRE_NG_N)"/>
					</xsl:element>
				</xsl:when>
				<xsl:otherwise>
					<xsl:element name="s">
						<xsl:attribute name="c">t</xsl:attribute>
						<xsl:text>Notice Sans Titre (Notice de Periodique)</xsl:text>
					</xsl:element>
				</xsl:otherwise>
			</xsl:choose>

			<!-- info bulletin -->
			<xsl:if test="$noeud/NO_COLLECTION_N|$noeud/NOTICE_GENERALE/NO_COLLECTION_N">
				<xsl:variable name="num_bulletin">
					<xsl:choose>
						<xsl:when test="normalize-space($noeud/NO_COLLECTION_N)!=''"><xsl:value-of select="$noeud/NO_COLLECTION_N"/></xsl:when>
						<xsl:when test="normalize-space($noeud/NOTICE_GENERALE/NO_COLLECTION_N)!=''"><xsl:value-of select="$noeud/NOTICE_GENERALE/NO_COLLECTION_N"/></xsl:when>
					</xsl:choose>
				</xsl:variable>
				<xsl:if test="$num_bulletin!=''">
					<xsl:element name="s">
						<xsl:attribute name="c">v</xsl:attribute>
						<xsl:value-of select="$num_bulletin"/>
					</xsl:element>
				</xsl:if>
			</xsl:if>

			<!-- date de bulletin -->
			<xsl:if test="$noeud/DATE_PARUTION_N">
				<xsl:element name="s">
					<xsl:attribute name="c">d</xsl:attribute>
					<xsl:value-of select="$noeud/DATE_PARUTION_N"/>
				</xsl:element>
			</xsl:if>

			<!-- titre de bulletin -->
			<xsl:if test="$noeud/TITRE_NG_N">
				<xsl:element name="s">
					<xsl:attribute name="c">a</xsl:attribute>
					<xsl:value-of select="$noeud/TITRE_NG_N"/>
				</xsl:element>
			</xsl:if>

			<!-- nb pages -->
			<xsl:if test="$noeud/COLLATION_N">
				<xsl:element name="s">
					<xsl:attribute name="c">p</xsl:attribute>
					<xsl:value-of select="$noeud/COLLATION_N"/>
				</xsl:element>
			</xsl:if>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- Chapitre -->
<xsl:template name="chapitre">
	<xsl:param name="noeud"/>
	<!--  on integre la notice mere -->
	<xsl:if test="normalize-space($noeud/NOTICE_GENERALE/TITRE_N) != ''">
		<xsl:element name="f">
		<xsl:attribute name="c">463</xsl:attribute>
		<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<xsl:element name="s">
				<xsl:attribute name="c">t</xsl:attribute>
				<xsl:value-of select="$noeud/NOTICE_GENERALE/TITRE_N"/>
			</xsl:element>
			<xsl:element name="s">
				<xsl:attribute name="c">x</xsl:attribute>
				<xsl:value-of select="$noeud/NOTICE_GENERALE/IDENTITE_N"/>
			</xsl:element>
			<xsl:element name="s">
				<xsl:attribute name="c">m</xsl:attribute>
				<xsl:text>d</xsl:text>
			</xsl:element>
			<xsl:element name="s">
				<xsl:attribute name="c">9</xsl:attribute>
				<xsl:value-of select="concat('id:',$noeud/NOTICE_GENERALE/IDENTITE_N)"/>
			</xsl:element>
			<xsl:if test="normalize-space($noeud/NOTICE_GENERALE/ISBN_N) != ''">
				<xsl:element name="s">
					<xsl:attribute name="c">y</xsl:attribute>
					<xsl:value-of select="$noeud/NOTICE_GENERALE/ISBN_N"/>
				</xsl:element>
			</xsl:if>
			<s c='9'>bl:m0</s>
			<s c='9'>type_lnk:d</s>
			<s c='9'>lnk:parent</s>
		</xsl:element>
	</xsl:if>
</xsl:template>

<!-- Exemplaires -->
<xsl:template name="exemplaires">
	<xsl:param name="n_ex"/>
	<xsl:param name="noeud"/>

	<xsl:if test="$noeud[$n_ex]">
		<xsl:element name="f">
			<xsl:attribute name="c">995</xsl:attribute>
			<xsl:attribute name="ind"><xsl:text>  </xsl:text></xsl:attribute>
			<!-- Localisation -->
			<xsl:if test="$noeud[$n_ex]/EMPLACEMENT_X">
				<xsl:element name="s">
					<xsl:attribute name="c">a</xsl:attribute>
					<xsl:value-of select="$noeud[$n_ex]/EMPLACEMENT_X"/>
				</xsl:element>
			</xsl:if>
			<!-- Code barre -->
			<xsl:call-template name="code_barre">
				<xsl:with-param name="n_ex" select="$n_ex"/>
				<xsl:with-param name="noeud_expl" select="$noeud"/>
			</xsl:call-template>

			<!-- Cote -->
			<xsl:choose>
				<xsl:when test="$noeud[$n_ex]/COTE_E_X">
					<xsl:element name="s">
						<xsl:attribute name="c">k</xsl:attribute>
						<xsl:value-of select="$noeud[$n_ex]/COTE_E_X"/>
					</xsl:element>
				</xsl:when>
				<xsl:otherwise>
					<xsl:element name="s">
						<xsl:attribute name="c">k</xsl:attribute>
						<xsl:text>ARCHIVES</xsl:text>
					</xsl:element>
				</xsl:otherwise>
			</xsl:choose>

			<!-- Support -->
			<xsl:if test="$noeud/../SUPPORT_N">
				<xsl:element name="s">
					<xsl:attribute name="c">r</xsl:attribute>
					<xsl:value-of select="$noeud/../SUPPORT_N"/>
				</xsl:element>
			</xsl:if>

			<!-- Section -->
			<xsl:if test="$noeud/../PUBLIC_N">
				<xsl:element name="s">
					<xsl:attribute name="c">q</xsl:attribute>
					<xsl:value-of select="$noeud/../PUBLIC_N"/>
				</xsl:element>
			</xsl:if>

			<!-- Statut -->
			<xsl:if test="normalize-space($noeud[$n_ex]/STATUT_X)!=''">
				<xsl:element name="s">
					<xsl:attribute name="c">o</xsl:attribute>
					<xsl:choose>
						<xsl:when test="$noeud[$n_ex]/STATUT_X='En-service' or $noeud[$n_ex]/STATUT_X='Autre'"><xsl:text>Empruntable</xsl:text></xsl:when>
						<xsl:when test="$noeud[$n_ex]/STATUT_X='Hors-Prêt'"><xsl:text>Consultable sur place</xsl:text></xsl:when>
						<xsl:when test="$noeud[$n_ex]/STATUT_X='Mis au pilon'"><xsl:text>Pilonné</xsl:text></xsl:when>
						<xsl:otherwise><xsl:value-of select="$noeud[$n_ex]/STATUT_X"/></xsl:otherwise>
					</xsl:choose>
				</xsl:element>
			</xsl:if>

			<!-- Prix -->
			<xsl:if test="$noeud[$n_ex]/COUT_E_X">
				<xsl:element name="s">
					<xsl:attribute name="c">p</xsl:attribute>
					<xsl:value-of select="$noeud[$n_ex]/COUT_E_X"/>
				</xsl:element>
			</xsl:if>

			<!-- Commentaire non bloquant -->
			<xsl:if test="$noeud[$n_ex]/DIVEXE_X">
				<xsl:element name="s">
					<xsl:attribute name="c">u</xsl:attribute>
					<xsl:value-of select="$noeud[$n_ex]/DIVEXE_X"/>
				</xsl:element>
			</xsl:if>



		</xsl:element>
		<xsl:if test="$noeud[$n_ex+1]">
			<xsl:call-template name="exemplaires">
				<xsl:with-param name="n_ex" select="$n_ex+1"/>
				<xsl:with-param name="noeud" select="$noeud"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:if>
</xsl:template>

<!-- Numéro d'exemplaire -->
<xsl:template name="code_barre">
		<xsl:param name="n_ex"/>
		<xsl:param name="noeud_expl"/>
		<xsl:choose>
			<xsl:when test="$noeud_expl[$n_ex]/CODE_EXEMPLAIRE_X">
				<xsl:element name="s">
					<xsl:attribute name="c">f</xsl:attribute>
					<xsl:value-of select="$noeud_expl[$n_ex]/CODE_EXEMPLAIRE_X"/>
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="s">
					<xsl:attribute name="c">f</xsl:attribute>
					<xsl:text>INCONNU</xsl:text>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>
</xsl:template>


<xsl:template match="*"/>

</xsl:stylesheet>
