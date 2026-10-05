<?xml version="1.0" encoding="ISO-8859-1"?>

<!-- feuille de transformation xslt pour le connecteur entrant entrez
****************************************************************************************
© 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: pubmed_to_unimarc.xsl,v 1.12 2023/09/19 15:16:12 tsamson Exp $ -->

<xsl:stylesheet version = '1.0' 
	xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
	
<xsl:output method="xml" indent='yes'/>
	
<xsl:template match="/PubmedArticleSet">
	<xsl:element name="unimarc">
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>


<xsl:template match="PubmedArticle">
	<notice>
		<xsl:element name="rs">*</xsl:element>
		<xsl:element name="ru">*</xsl:element>
		<xsl:element name="el">1</xsl:element>
		<xsl:element name="bl">a</xsl:element>
		<xsl:element name="hl">2</xsl:element><!-- niveau hierarchique:  -->
		<xsl:element name="dt"><xsl:value-of select="./dt"/></xsl:element>
		
		<xsl:if test="MedlineCitation">
			<xsl:for-each select="MedlineCitation"> 
				<xsl:call-template name="parse_medlinecitation"/>
				<xsl:call-template name="url"/>
				
				<xsl:for-each select="Article[@PubModel='Print']"> 
					<xsl:call-template name="title"/>
					<xsl:call-template name="presentation"/>
					<xsl:call-template name="autorite"/>
					<xsl:call-template name="langue"/>
					<xsl:call-template name="journal_dateparution"/>
					<xsl:call-template name="journal_title"/>
					<xsl:call-template name="bulletin"/>
					<xsl:call-template name="perio"/>
					<xsl:call-template name="typedoc"/>
				</xsl:for-each>
				
				<xsl:for-each select="Article[@PubModel='Print-Electronic']"> 
					<xsl:call-template name="title"/>
					<xsl:call-template name="presentation"/>
					<xsl:call-template name="autorite"/>
					<xsl:call-template name="langue"/>
					<xsl:call-template name="journal_dateparution"/>
					<xsl:call-template name="journal_title"/>
					<xsl:call-template name="bulletin"/>
					<xsl:call-template name="perio"/>
					<xsl:call-template name="typedoc"/>
				</xsl:for-each>
				
				<xsl:for-each select="Article[@PubModel='Electronic']"> 
					<xsl:call-template name="title"/>
					<xsl:call-template name="presentation"/>
					<xsl:call-template name="autorite"/>
					<xsl:call-template name="langue"/>
					<xsl:call-template name="journal_dateparution"/>
					<xsl:call-template name="journal_title"/>
					<xsl:call-template name="article_affiliation"/>
					<xsl:call-template name="bulletin"/>
					<xsl:call-template name="perio"/>
					<xsl:call-template name="typedoc"/>
				</xsl:for-each>
				
				<xsl:for-each select="Article[@PubModel='Electronic-eCollection']"> 
					<xsl:call-template name="title"/>
					<xsl:call-template name="presentation"/>
					<xsl:call-template name="autorite"/>
					<xsl:call-template name="langue"/>
					<xsl:call-template name="journal_dateparution"/>
					<xsl:call-template name="journal_title"/>
					<xsl:call-template name="article_affiliation"/>
					<xsl:call-template name="bulletin"/>
					<xsl:call-template name="perio"/>
					<xsl:call-template name="typedoc"/>
				</xsl:for-each>
			</xsl:for-each>
		</xsl:if>
		
		<xsl:if test="PubmedData/ArticleIdList/ArticleId[@IdType='doi']">
			<xsl:element name="f">
			<xsl:attribute name="c">014</xsl:attribute>	
				<s c="a"><xsl:value-of select="PubmedData/ArticleIdList/ArticleId[@IdType='doi']"/></s>
				<s c="b"><xsl:text>DOI</xsl:text></s>	
			</xsl:element>
		</xsl:if>
	</notice>
</xsl:template>

<xsl:template match="PubmedBookArticle">
	<xsl:element name="notice">
		<xsl:element name="rs">*</xsl:element>
		<xsl:element name="ru">*</xsl:element>
		<xsl:element name="el">1</xsl:element>
		<xsl:element name="bl">m</xsl:element>
		<xsl:element name="hl">0</xsl:element><!-- niveau hierarchique:  -->
		<xsl:element name="dt">a</xsl:element>
		
		<xsl:if test="BookDocument/PMID">
			<f c="001"><xsl:value-of select="BookDocument/PMID"/></f>
			
			<xsl:element name="f">
				<xsl:attribute name="c">014</xsl:attribute>			
				<s c="a"><xsl:value-of select="BookDocument/PMID"/></s>
				<s c="b"><xsl:text>PMID</xsl:text></s>
			</xsl:element>
		</xsl:if>		
		
		<xsl:call-template name="isbn"/>
		<xsl:call-template name="title"/>
		<xsl:call-template name="collection"/>	
		<xsl:call-template name="publishers"/>
		<xsl:call-template name="parution_date"/>
		<xsl:call-template name="book_authors"/>
		<xsl:call-template name="resume"/>
		<xsl:call-template name="link"/>
		<xsl:call-template name="sections"/>	
		<xsl:if test="BookDocument">
			<xsl:for-each select="BookDocument"> 
				<xsl:call-template name="autorite"/>
				<xsl:call-template name="langue"/>
				<xsl:call-template name="journal_dateparution"/>
			</xsl:for-each>
		</xsl:if>
		
	</xsl:element>
</xsl:template>

<xsl:template name="isbn">
	<xsl:if test="BookDocument/Book/Isbn">
		<f c="010">
			<s c="a"><xsl:value-of select="BookDocument/Book/Isbn"/></s>
		</f>
	</xsl:if>	
</xsl:template>

	
<xsl:template name="parse_medlinecitation">
	<xsl:call-template name="record_identifier"/>
</xsl:template>
	
<xsl:template name="record_identifier">
	<xsl:if test="PMID">
		<f c="001"><xsl:value-of select="PMID"/></f>
		
		<xsl:element name="f">
			<xsl:attribute name="c">014</xsl:attribute>			
			<s c="a"><xsl:value-of select="PMID"/></s>
			<s c="b"><xsl:text>PMID</xsl:text></s>
		</xsl:element>
	</xsl:if>	
</xsl:template>

<xsl:template name="url">
	<xsl:if test="PMID">
		<xsl:element name="f">
			<xsl:attribute name="c">856</xsl:attribute>	
			<s c="u">
				<xsl:text>http://www.ncbi.nlm.nih.gov/pubmed/</xsl:text><xsl:value-of select="PMID"/>
			</s>	
		</xsl:element>	
	</xsl:if>	
</xsl:template>
	
<xsl:template name="title">
	<xsl:element name="f">
		<xsl:attribute name="c">200</xsl:attribute>	
		<xsl:choose>
			<xsl:when test="ArticleTitle">
				<xsl:if test="ArticleTitle">
					<s c="a"><xsl:value-of select="ArticleTitle"/></s>	
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="BookDocument/ArticleTitle">
						<s c="a"><xsl:value-of select="BookDocument/ArticleTitle"/></s>
						<s c="i"><xsl:value-of select="BookDocument/Book/BookTitle"/></s>	
					</xsl:when>
					<xsl:otherwise>
						<s c="a"><xsl:value-of select="BookDocument/Book/BookTitle"/></s>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>

	</xsl:element>	
</xsl:template>
	
<xsl:template name="presentation">
	<xsl:if test="Abstract/AbstractText">
		<xsl:for-each select="Abstract/AbstractText">
			<xsl:element name="f">
				<xsl:attribute name="c">330</xsl:attribute>	
				<s c="a"><xsl:value-of select="./@Label"/><xsl:text>: </xsl:text><xsl:value-of select="."/></s>
			</xsl:element>		
		</xsl:for-each>
	</xsl:if>
</xsl:template>

<xsl:template name="autorite">
	<xsl:if test="Affiliation">
		<xsl:element name="f">
			<xsl:attribute name="c">
				<xsl:choose>
					<xsl:when test="not(../AuthorList/Author)">
						<xsl:text>710</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>711</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<s c="a"><xsl:value-of select="substring-before(Affiliation,', ')"/></s>
			<s c="e"><xsl:value-of select="substring-after(Affiliation,', ')"/></s>
		</xsl:element>
	</xsl:if>	
	<xsl:for-each select="AuthorList/Author">
	<xsl:element name="f">
		<xsl:attribute name="c">
			<xsl:choose>
				<xsl:when test="position()=1">
					<xsl:text>700</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>701</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>	
			<s c="a">
				<xsl:choose>
					<xsl:when test="ForeName">
						<xsl:value-of select="concat(LastName,' ',ForeName)"/>
					</xsl:when>
					<xsl:when test="FirstName">
						<xsl:value-of select="concat(LastName,' ',FirstName)"/>
					</xsl:when>
					<xsl:when test="MiddleName and FirstName">
						<xsl:value-of select="concat(LastName,' ',MiddleName,' ',FirstName)"/>
					</xsl:when>
					<xsl:when test="MiddleName and ForeName">
						<xsl:value-of select="concat(LastName,' ',MiddleName,' ',ForeName)"/>
					</xsl:when>
				</xsl:choose>
			</s>	
	</xsl:element>
	</xsl:for-each>	
</xsl:template>
	
<xsl:template name="langue">
	<xsl:if test="Language">
		<xsl:element name="f">
			<xsl:attribute name="c">101</xsl:attribute>	
			<s c="a">
				<xsl:value-of select="Language"/>
			</s>	
		</xsl:element>
	</xsl:if>
</xsl:template>
	
<xsl:template name="journal_dateparution">
	<xsl:if test="Journal/JournalIssue/PubDate">
		<xsl:element name="f">
			<xsl:attribute name="c">910</xsl:attribute>	
				<s c="a">
					<xsl:value-of select="Journal/JournalIssue/PubDate/Month"/><xsl:text> </xsl:text><xsl:value-of select="Journal/JournalIssue/PubDate/Year"/>
				</s>		
		</xsl:element>
	</xsl:if>
</xsl:template>	

<xsl:template name="journal_title">
	<xsl:if test="Journal/Title">
		<xsl:element name="f">
			<xsl:attribute name="c">205</xsl:attribute>	
				<s c="a">
					<xsl:value-of select="Journal/Title"/>
				</s>		
		</xsl:element>
	</xsl:if>
</xsl:template>	
	
	
<xsl:template name="article_affiliation">
	<xsl:element name="f">
		<xsl:attribute name="c">210</xsl:attribute>	
		<xsl:if test="Affiliation">
			<s c="a">
				<xsl:value-of select="Affiliation"/>
			</s>	
		</xsl:if>
	</xsl:element>	
</xsl:template>

<xsl:template name="bulletin">
	<xsl:element name="f">
		<xsl:if test="Journal/JournalIssue">
			<xsl:attribute name="c">463</xsl:attribute>	
				<xsl:variable name="vol">	
					<xsl:if test="Journal/JournalIssue/Volume">
						<xsl:value-of select="Journal/JournalIssue/Volume"/>	
					</xsl:if>
				</xsl:variable>
				<xsl:variable name="issue">	
					<xsl:if test="Journal/JournalIssue/Issue">
						<xsl:value-of select="Journal/JournalIssue/Issue"/>	
					</xsl:if>
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="$issue!='' and $vol!=''">
						<s c="v">
							<xsl:value-of select="concat('vol. ',$vol,', no. ',$issue)"/>
						</s>
					</xsl:when>
					<xsl:when test="$issue!='' and $vol=''">
						<s c="v">
							<xsl:value-of select="concat('no. ',$issue)"/>
						</s>
					</xsl:when>
					<xsl:when test="$issue='' and $vol!=''">
						<s c="v">
							<xsl:value-of select="concat('vol. ',$vol)"/>
						</s>
					</xsl:when>
				</xsl:choose>
			<s c="9">lnk:bull</s>	
		</xsl:if>
		<xsl:variable name="day">
			<xsl:if test="Journal/JournalIssue/PubDate/Day">
				<xsl:value-of select="Journal/JournalIssue/PubDate/Day"/>	
			</xsl:if>
		</xsl:variable>
		<xsl:variable name="month">
			<xsl:choose>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Jan'">01</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Feb'">02</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Mar'">03</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Apr'">04</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'May'">05</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Jun'">06</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Jul'">07</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Aug'">08</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Sep'">09</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Oct'">10</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Nov'">11</xsl:when>
				<xsl:when test="Journal/JournalIssue/PubDate/Month = 'Dec'">12</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="Journal/JournalIssue/PubDate/Month" />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="year">
			<xsl:if test="Journal/JournalIssue/PubDate/Year">
				<xsl:value-of select="Journal/JournalIssue/PubDate/Year"/>	
			</xsl:if>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$month!='' and $day !='' and $year !=''">
				<s c="d"><xsl:value-of select="concat($year,'-',$month,'-',$day)"/></s>
				<s c="e"><xsl:value-of select="concat($day,' ',Journal/JournalIssue/PubDate/Month,' ',$year)"/></s>
			</xsl:when>
			<xsl:when test="$month='' and $day ='' and $year !=''">
				<s c="d"><xsl:value-of select="concat($year,'-','01','-','01')"/></s>
				<s c="e"><xsl:value-of select="Journal/JournalIssue/PubDate/Month"/></s>
			</xsl:when>
			<xsl:when test="$month!='' and $day ='' and $year !=''">
				<s c="d"><xsl:value-of select="concat($year,'-',$month,'-','01')"/></s>
				<s c="e"><xsl:value-of select="concat(Journal/JournalIssue/PubDate/Month,' ',$year)"/></s>
			</xsl:when>
		</xsl:choose>
	</xsl:element>	
</xsl:template>


<xsl:template name="perio">
	<xsl:if test="Journal">
	<xsl:element name="f">
		<xsl:attribute name="c">461</xsl:attribute>	
			<xsl:if test="Journal/Title">
				<s c="t">
					<xsl:value-of select="Journal/Title"/>
				</s>
			</xsl:if>	
			<xsl:if test="Journal/ISSN">
				<s c="x">
					<xsl:value-of select="Journal/ISSN"/>
				</s>	
			</xsl:if>
			<s c="9">lnk:perio</s>	
	</xsl:element>	
	</xsl:if>
</xsl:template>

<xsl:template name="typedoc">
	<xsl:if test="PublicationTypeList">
	<xsl:element name="f">
		<xsl:attribute name="c">900</xsl:attribute>
			<xsl:variable name="doctype">	
				<xsl:choose>	
					<xsl:when test="PublicationTypeList/PublicationType = 'Abstracts'">Abstract</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Meeting Abstracts'">Abstract</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Academic Dissertations'">Thesis</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Annual Reports'">Report</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Technical Report'">Report</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Book Reviews'">Review</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Review'">Review</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Classical Article'">Article</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Corrected and Republished Article'">Article</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Journal Article'">Article</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Newspaper Article'">Article</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Comment'">Erratum</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Published Erratum'">Erratum</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Congresses'">Conference proceedings</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Database'">Database</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Dictionary'">Dictionary</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Directory'">Directory</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Editorial'">Editorial</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Encyclopedias'">Encyclopedia</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Letter'">Letter</xsl:when>
					<xsl:when test="PublicationTypeList/PublicationType = 'Unpublished Works'">Preprint</xsl:when>
					<xsl:otherwise>Article</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<s c="a"><xsl:value-of select="$doctype"/></s>
			<s c="l"><xsl:text>Sub-Type</xsl:text></s>
			<s c="n"><xsl:text>subtype</xsl:text></s>
	</xsl:element>	
	</xsl:if>
</xsl:template>	

<xsl:template name="publishers">
	<xsl:element name="f">
		<xsl:attribute name="c">210</xsl:attribute>	
		<xsl:if test="BookDocument/Book/Publisher/PublisherLocation">
			<s c="a">
				<xsl:value-of select="BookDocument/Book/Publisher/PublisherLocation"/>
			</s>	
		</xsl:if>	
		<xsl:if test="BookDocument/Book/Publisher/PublisherName">
			<s c="c">
				<xsl:value-of select="BookDocument/Book/Publisher/PublisherName"/>
			</s>	
		</xsl:if>
	</xsl:element>	
</xsl:template>

<xsl:template name="parution_date">
	<xsl:element name="f">
		<xsl:attribute name="c">210</xsl:attribute>
		<xsl:if test="PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']">
			<s c="d">
				<xsl:if test="PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']/Day">
					<xsl:if test="string-length(PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']/Day) = 1">
						<xsl:text>0</xsl:text>
					</xsl:if>
					<xsl:value-of select="PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']/Day" />
					<xsl:text>/</xsl:text>
				</xsl:if>
				<xsl:if test="PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']/Month">
					<xsl:if test="string-length(PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']/Month) = 1">
						<xsl:text>0</xsl:text>
					</xsl:if>
					<xsl:value-of select="PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']/Month" />
					<xsl:text>/</xsl:text>
				</xsl:if>
				<xsl:value-of select="PubmedBookData/History/PubMedPubDate[@PubStatus='pubmed']/Year" />
			</s>
		</xsl:if>
	</xsl:element>	
</xsl:template>

<xsl:template name="book_authors">
	<xsl:for-each select="BookDocument/Book/AuthorList/Author">
		<xsl:value-of select="."/>
		<xsl:element name="f">
			<xsl:attribute name="c">
				<xsl:choose>
					<xsl:when test="position()=1">
						<xsl:choose>
							<xsl:when test="CollectiveName">
								<xsl:text>710</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:text>700</xsl:text>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="CollectiveName">
								<xsl:text>711</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:text>701</xsl:text>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>	
			</xsl:attribute>
				<xsl:choose>
					<xsl:when test="CollectiveName">
						<s c="a"><xsl:value-of select="CollectiveName"/></s>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="LastName">
							<s c="a"><xsl:value-of select="LastName"/></s>
						</xsl:if>
						<xsl:if test="ForeName">
							<s c="b"><xsl:value-of select="ForeName"/></s>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="../@Type = 'authors'">
					<s c="4"><xsl:text>070</xsl:text></s>
				</xsl:if>				
				<xsl:if test="../@Type = 'editors'">
					<s c="4"><xsl:text>340</xsl:text></s>
				</xsl:if>
		</xsl:element>
	</xsl:for-each>
</xsl:template>

<xsl:template name="resume">
	<xsl:if test="BookDocument/Abstract/AbstractText">
		<xsl:for-each select="BookDocument/Abstract/AbstractText">
			<xsl:element name="f">
				<xsl:attribute name="c">330</xsl:attribute>	
				<s c="a">
					<xsl:if test="./@Label">
						<xsl:value-of select="./@Label"/><xsl:text>: </xsl:text>
					</xsl:if>
					<xsl:value-of select="."/>
				</s>
			</xsl:element>		
		</xsl:for-each>
	</xsl:if>
</xsl:template>

<xsl:template name="sections">
	<xsl:if test="BookDocument/Sections/Section">
		<f c="327">
			<s c="a">
				<xsl:for-each select="BookDocument/Sections/Section">
					<xsl:if test="SectionTitle">
						<xsl:if test="LocationLabel">
							<xsl:value-of select="LocationLabel" /><xsl:text>. </xsl:text>
						</xsl:if>
						<xsl:value-of select="SectionTitle" />
						<xsl:text>
</xsl:text>
					</xsl:if>
				</xsl:for-each>
			</s>
		</f>
	</xsl:if>
</xsl:template>

<xsl:template name="collection">
	<xsl:if test="BookDocument/Book/CollectionTitle">
		<f c="225">
			<s c="a"><xsl:value-of select="BookDocument/Book/CollectionTitle"/></s>
		</f>
	</xsl:if>
</xsl:template>

<xsl:template name="link">
	<xsl:if test="BookDocument/ArticleIdList/ArticleId[@IdType= 'bookaccession']">
		<f c="856">
			<s c="u"><xsl:text>http://www.ncbi.nlm.nih.gov/books/</xsl:text><xsl:value-of select="BookDocument/ArticleIdList/ArticleId[@IdType= 'bookaccession']"></xsl:value-of></s>
		</f>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>