<?xml version="1.0" encoding="ISO-8859-1"?>

<!-- feuille de transformation xslt pour le connecteur entrant zotero
****************************************************************************************
© 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: zotero_atom_json.xsl,v 1.4 2018/01/09 14:05:11 jpermanne Exp $ -->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
	
	<xsl:output method="xml" indent="yes"/>
	
	<xsl:template match="/record">
		<unimarc>
			<notice>
				<xsl:choose>
					<xsl:when test="itemType='artwork'" >
						<xsl:call-template name="do_itemType_artwork"/>
					</xsl:when>
					<xsl:when test="itemType='audioRecording'" >
						<xsl:call-template name="do_itemType_audioRecording"/>
					</xsl:when>
					<xsl:when test="itemType='bill'" >
						<xsl:call-template name="do_itemType_bill"/>
					</xsl:when>
					<xsl:when test="itemType='blogPost'" >
						<xsl:call-template name="do_itemType_blogPost"/>
					</xsl:when>
					<xsl:when test="itemType='book'" >
						<xsl:call-template name="do_itemType_book"/>
					</xsl:when>
					<xsl:when test="itemType='bookSection'" >
						<xsl:call-template name="do_itemType_bookSection"/>
					</xsl:when>
					<xsl:when test="itemType='case'" >
						<xsl:call-template name="do_itemType_case"/>
					</xsl:when>
					<xsl:when test="itemType='computerProgram'" >
						<xsl:call-template name="do_itemType_computerProgram"/>
					</xsl:when>
					<xsl:when test="itemType='conferencePaper'" >
						<xsl:call-template name="do_itemType_conferencePaper"/>
					</xsl:when>
					<xsl:when test="itemType='dictionaryEntry'" >
						<xsl:call-template name="do_itemType_dictionaryEntry"/>
					</xsl:when>
					<xsl:when test="itemType='document'" >
						<xsl:call-template name="do_itemType_document"/>
					</xsl:when>
					<xsl:when test="itemType='email'" >
						<xsl:call-template name="do_itemType_email"/>
					</xsl:when>
					<xsl:when test="itemType='encyclopediaArticle'" >
						<xsl:call-template name="do_itemType_encyclopediaArticle"/>
					</xsl:when>
					<xsl:when test="itemType='film'" >
						<xsl:call-template name="do_itemType_film"/>
					</xsl:when>
					<xsl:when test="itemType='forumPost'" >
						<xsl:call-template name="do_itemType_forumPost"/>
					</xsl:when>
					<xsl:when test="itemType='hearing'" >
						<xsl:call-template name="do_itemType_hearing"/>
					</xsl:when>
					<xsl:when test="itemType='instantMessage'" >
						<xsl:call-template name="do_itemType_instantMessage"/>
					</xsl:when>
					<xsl:when test="itemType='interview'" >
						<xsl:call-template name="do_itemType_interview"/>
					</xsl:when>
					<xsl:when test="itemType='journalArticle'" >
						<xsl:call-template name="do_itemType_journalArticle"/>
					</xsl:when>
					<xsl:when test="itemType='letter'" >
						<xsl:call-template name="do_itemType_letter"/>
					</xsl:when>
					<xsl:when test="itemType='magazineArticle'" >
						<xsl:call-template name="do_itemType_magazineArticle"/>
					</xsl:when>
					<xsl:when test="itemType='manuscript'" >
						<xsl:call-template name="do_itemType_manuscript"/>
					</xsl:when>
					<xsl:when test="itemType='map'" >
						<xsl:call-template name="do_itemType_map"/>
					</xsl:when>
					<xsl:when test="itemType='newspaperArticle'" >
						<xsl:call-template name="do_itemType_newspaperArticle"/>
					</xsl:when>
					<xsl:when test="itemType='patent'" >
						<xsl:call-template name="do_itemType_patent"/>
					</xsl:when>
					<xsl:when test="itemType='podcast'" >
						<xsl:call-template name="do_itemType_podcast"/>
					</xsl:when>
					<xsl:when test="itemType='presentation'" >
						<xsl:call-template name="do_itemType_presentation"/>
					</xsl:when>
					<xsl:when test="itemType='radioBroadcast'" >
						<xsl:call-template name="do_itemType_radioBroadcast"/>
					</xsl:when>
					<xsl:when test="itemType='report'" >
						<xsl:call-template name="do_itemType_report"/>
					</xsl:when>
					<xsl:when test="itemType='statute'" >
						<xsl:call-template name="do_itemType_statute"/>
					</xsl:when>
					<xsl:when test="itemType='tvBroadcast'" >
						<xsl:call-template name="do_itemType_tvBroadcast"/>
					</xsl:when>
					<xsl:when test="itemType='thesis'" >
						<xsl:call-template name="do_itemType_thesis"/>
					</xsl:when>
					<xsl:when test="itemType='videoRecording'" >
						<xsl:call-template name="do_itemType_videoRecording"/>
					</xsl:when>
					<xsl:when test="itemType='webpage'" >
						<xsl:call-template name="do_itemType_webpage"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="do_noitemType"/>
					</xsl:otherwise>
				</xsl:choose>
			</notice>
		</unimarc>
	</xsl:template>

	<xsl:template name="do_itemType_artwork">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_duration">
			<xsl:with-param name="duration" select="artworkSize"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="artworkMedium"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_audioRecording">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="label"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_duration">
			<xsl:with-param name="duration" select="runningTime"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="audioRecordingFormat"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="seriesTitle"/>
			<xsl:with-param name="collection_volume" select="volume"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_bill">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="billNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="codePages"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="code"/>
			<xsl:with-param name="collection_volume" select="codeVolume"/>
			<xsl:with-param name="subcollection_title" select="section"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">legislative_body</xsl:with-param>
 			<xsl:with-param name="content" select="legislativeBody"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">session</xsl:with-param>
 			<xsl:with-param name="content" select="session"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">history</xsl:with-param>
 			<xsl:with-param name="content" select="history"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_blogPost">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="blogTitle"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">website_type</xsl:with-param>
 			<xsl:with-param name="content" select="websiteType"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_book">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_edition">
			<xsl:with-param name="statement" select="edition"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="numPages"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="series"/>
			<xsl:with-param name="collection_number" select="seriesNumber"/>
			<xsl:with-param name="collection_volume" select="volume"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_bookSection">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="bookTitle"/>
			<xsl:with-param name="subtitle" select="title"/>
		</xsl:call-template>
		<xsl:call-template name="do_edition">
			<xsl:with-param name="statement" select="edition"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="series"/>
			<xsl:with-param name="collection_number" select="seriesNumber"/>
			<xsl:with-param name="collection_volume" select="volume"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_case">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="docketNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="caseName"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="court"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="dateDecided"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="firstPage"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="reporter"/>
			<xsl:with-param name="collection_volume" select="reporterVolume"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">history</xsl:with-param>
 			<xsl:with-param name="content" select="history"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_computerProgram">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_edition">
			<xsl:with-param name="statement" select="version"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="company"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="seriesTitle"/>
			<xsl:with-param name="collection_number" select="seriesNumber"/>
			<xsl:with-param name="collection_volume" select="volume"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>
		<xsl:call-template name="do_programming_language"/>
		<xsl:call-template name="do_system"/>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
  		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_conferencePaper">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="series"/>
			<xsl:with-param name="collection_volume" select="volume"/>
		</xsl:call-template>		
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_doi">
 			<xsl:with-param name="doi" select="DOI"/>
 		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">proceedings_title</xsl:with-param>
 			<xsl:with-param name="content" select="proceedingsTitle"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">conference_name</xsl:with-param>
 			<xsl:with-param name="content" select="conferenceName"/>
  		</xsl:call-template>
  		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_dictionaryEntry">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
			<xsl:with-param name="serie_title" select="dictionaryTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_edition">
			<xsl:with-param name="statement" select="edition"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="series"/>
			<xsl:with-param name="collection_number" select="seriesNumber"/>
			<xsl:with-param name="collection_volume" select="volume"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_document">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_encyclopediaArticle">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
			<xsl:with-param name="serie_title" select="encyclopediaTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_edition">
			<xsl:with-param name="statement" select="edition"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="numPages"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="series"/>
			<xsl:with-param name="collection_number" select="seriesNumber"/>
			<xsl:with-param name="collection_volume" select="volume"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_email">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="subject"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_film">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="distributor"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_duration">
			<xsl:with-param name="duration" select="runningTime"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="videoRecordingFormat"/>
		</xsl:call-template>
		<xsl:call-template name="do_genre"/>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
	</xsl:template>

	<xsl:template name="do_itemType_forumPost">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="forumTitle"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">post_type</xsl:with-param>
 			<xsl:with-param name="content" select="postType"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_others"/>
	</xsl:template>


	<xsl:template name="do_itemType_hearing">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_volume" select="documentNumber"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">committee</xsl:with-param>
 			<xsl:with-param name="content" select="committee"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">legislative_body</xsl:with-param>
 			<xsl:with-param name="content" select="legislativeBody"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">session</xsl:with-param>
 			<xsl:with-param name="content" select="session"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">history</xsl:with-param>
 			<xsl:with-param name="content" select="history"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_instantMessage">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="websiteTitle"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">website_type</xsl:with-param>
 			<xsl:with-param name="content" select="websiteType"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_interview">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_journalArticle">
		<xsl:call-template name="do_header">
			<xsl:with-param name="type">article</xsl:with-param>
		</xsl:call-template>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
 		<xsl:call-template name="do_serial">
 			<xsl:with-param name="serial_title" select="publicationTitle"/>
 			<xsl:with-param name="serial_issn" select="ISSN"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_issue">
 			<xsl:with-param name="issue_volume" select="volume"/>
 			<xsl:with-param name="issue_number" select="issue"/>
 			<xsl:with-param name="issue_date" select="date"/>
 		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_doi">
 			<xsl:with-param name="doi" select="DOI"/>
 		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">series</xsl:with-param>
 			<xsl:with-param name="content" select="series"/>
  		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">series_title</xsl:with-param>
 			<xsl:with-param name="content" select="seriesTitle"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">series_text</xsl:with-param>
 			<xsl:with-param name="content" select="seriesText"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">journal_abbreviation</xsl:with-param>
 			<xsl:with-param name="content" select="journalAbbreviation"/>
  		</xsl:call-template>
  		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_letter">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">letterType</xsl:with-param>
 			<xsl:with-param name="content" select="letterType"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_magazineArticle">
		<xsl:call-template name="do_header">
			<xsl:with-param name="type">article</xsl:with-param>
		</xsl:call-template>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
 		<xsl:call-template name="do_serial">
 			<xsl:with-param name="serial_title" select="publicationTitle"/>
 			<xsl:with-param name="serial_issn" select="ISSN"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_issue">
 			<xsl:with-param name="issue_volume" select="volume"/>
 			<xsl:with-param name="issue_number" select="issue"/>
 			<xsl:with-param name="issue_date" select="date"/>
 		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
  		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_manuscript">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="numPages"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">manuscriptType</xsl:with-param>
 			<xsl:with-param name="content" select="manuscriptType"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_map">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_edition">
			<xsl:with-param name="statement" select="edition"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="publisher"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="scale"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="seriesTitle"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">mapType</xsl:with-param>
 			<xsl:with-param name="content" select="mapType"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_patent">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="patentNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="issuingAuthority"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="country" select="country"/>
			<xsl:with-param name="date" select="issueDate"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">assignee</xsl:with-param>
 			<xsl:with-param name="content" select="assignee"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name" >applicationNumber</xsl:with-param>
 			<xsl:with-param name="content" select="applicationNumber"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name" >priorityNumbers</xsl:with-param>
 			<xsl:with-param name="content" select="priorityNumbers"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name" >references</xsl:with-param>
 			<xsl:with-param name="content" select="references"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name" >legalStatus</xsl:with-param>
 			<xsl:with-param name="content" select="legalStatus"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_podcast">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="seriesTitle"/>
			<xsl:with-param name="collection_number" select="episodeNumber"/>
		</xsl:call-template>		
		<xsl:call-template name="do_duration">
			<xsl:with-param name="duration" select="runningTime"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="audioFileType"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_presentation">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
 		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
			<xsl:with-param name="subtitle" select="meetingName"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="presentationType"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_radioBroadcast">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="network"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="programTitle"/>
			<xsl:with-param name="collection_number" select="episodeNumber"/>
		</xsl:call-template>		
		<xsl:call-template name="do_duration">
			<xsl:with-param name="duration" select="runningTime"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="audioRecordingFormat"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_report">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="institution"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="seriesTitle"/>
			<xsl:with-param name="collection_number" select="reportNumber"/>
		</xsl:call-template>		
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">reportType</xsl:with-param>
 			<xsl:with-param name="content" select="reportType"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_newspaperArticle">
		<xsl:call-template name="do_header">
			<xsl:with-param name="type">article</xsl:with-param>
		</xsl:call-template>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
 		<xsl:call-template name="do_serial">
 			<xsl:with-param name="serial_title" select="publicationTitle"/>
 			<xsl:with-param name="serial_issn" select="ISSN"/>
 		</xsl:call-template>
 		<xsl:call-template name="do_issue">
  			<xsl:with-param name="issue_date" select="date"/>
 		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">place</xsl:with-param>
 			<xsl:with-param name="content" select="place"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">edition</xsl:with-param>
 			<xsl:with-param name="content" select="edition"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">section</xsl:with-param>
 			<xsl:with-param name="content" select="section"/>
 		</xsl:call-template>		
  		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_statute">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="publicLawNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="nameOfAct"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="date" select="dateEnacted"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="pages"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="code"/>
			<xsl:with-param name="collection_number" select="codeNumber"/>
			<xsl:with-param name="subcollection_title" select="section"/>
		</xsl:call-template>		
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">legislative_body</xsl:with-param>
 			<xsl:with-param name="content" select="legislativeBody"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">session</xsl:with-param>
 			<xsl:with-param name="content" select="session"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">history</xsl:with-param>
 			<xsl:with-param name="content" select="history"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_thesis">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="university"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_pages">
			<xsl:with-param name="pages" select="numPages"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
 			<xsl:with-param name="name">thesisType</xsl:with-param>
 			<xsl:with-param name="content" select="thesisType"/>
 		</xsl:call-template>		
 		<xsl:call-template name="do_others"/>
	</xsl:template>
	
	<xsl:template name="do_itemType_tvBroadcast">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="network"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="programTitle"/>
			<xsl:with-param name="collection_number" select="episodeNumber"/>
		</xsl:call-template>		
		<xsl:call-template name="do_duration">
			<xsl:with-param name="duration" select="runningTime"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="videoRecordingFormat"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_videoRecording">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
 		<xsl:call-template name="do_isbn">
			<xsl:with-param name="isbn" select="ISBN"/>
		</xsl:call-template>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="studio"/>
			<xsl:with-param name="place" select="place"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_collection">
			<xsl:with-param name="collection_title" select="seriesTitle"/>
			<xsl:with-param name="collection_volume" select="volume"/>
			<xsl:with-param name="collection_number_of_volumes" select="numberOfVolumes"/>
		</xsl:call-template>		
		<xsl:call-template name="do_duration">
			<xsl:with-param name="duration" select="runningTime"/>
		</xsl:call-template>
		<xsl:call-template name="do_format">
			<xsl:with-param name="format" select="videoRecordingFormat"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
		<xsl:call-template name="do_dewey" >
			<xsl:with-param name="index" select="callNumber"/>
		</xsl:call-template>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>	
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_itemType_webpage">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
			<xsl:with-param name="parallel_title" select="shortTitle"/>
		</xsl:call-template>
		<xsl:call-template name="do_publishing">
			<xsl:with-param name="name" select="websiteTitle"/>
			<xsl:with-param name="date" select="date"/>
		</xsl:call-template>
		<xsl:call-template name="do_notes"/>
		<xsl:call-template name="do_abstract" >
			<xsl:with-param name="abstract" select="abstractNote"/>
		</xsl:call-template>
		<xsl:call-template name="do_subjects"/>
 		<xsl:call-template name="do_authors">
			<xsl:with-param name="itemtype" select="itemType"/>
		</xsl:call-template>
 		<xsl:call-template name="do_url"/>
 		<xsl:call-template name="do_attachments"/>
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">websiteType</xsl:with-param>
 			<xsl:with-param name="content" select="websiteType"/>
  		</xsl:call-template>
 		<xsl:call-template name="do_others"/>
	</xsl:template>

	<xsl:template name="do_noitemType">
		<xsl:call-template name="do_header"/>
		<xsl:call-template name="do_identifier"/>
		<xsl:call-template name="do_doctype"/>
		<xsl:call-template name="do_language">
			<xsl:with-param name="lang" select="language"/>
		</xsl:call-template>
		<xsl:call-template name="do_title">
			<xsl:with-param name="title" select="title"/>
		</xsl:call-template>		
 		<xsl:call-template name="do_custom">
			<xsl:with-param name="name">item_type</xsl:with-param>
 			<xsl:with-param name="content" select="concat(itemType,' (non géré)')"/>
  		</xsl:call-template>
	</xsl:template>

	<xsl:template name="do_header">
		<xsl:param name="type"/>
		<xsl:element name="rs">*</xsl:element>
		<xsl:element name="ru">*</xsl:element>
		<xsl:element name="el">1</xsl:element>
		<xsl:choose>
			<xsl:when test="$type='serial'" >
				<xsl:element name="bl">s</xsl:element>
				<xsl:element name="hl">1</xsl:element>
			</xsl:when>
			<xsl:when test="$type='issue'" >
				<xsl:element name="bl">s</xsl:element>
				<xsl:element name="hl">2</xsl:element>
			</xsl:when>
			<xsl:when test="$type='article'" >
				<xsl:element name="bl">a</xsl:element>
				<xsl:element name="hl">2</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="bl">m</xsl:element>
				<xsl:element name="hl">0</xsl:element>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="do_identifier">
		<f c="001"><xsl:value-of select="@key"/></f>
	</xsl:template>
	
	<xsl:template name="do_doctype">
		<xsl:choose>
			<xsl:when test="itemType='artwork'" >
				<xsl:element name="dt">k</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='audioRecording'" >
				<xsl:element name="dt">j</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='blogPost'" >
				<xsl:element name="dt">l</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='book'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='bookSection'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='bill'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='case'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='computerProgram'" >
				<xsl:element name="dt">l</xsl:element>
			</xsl:when>
				<xsl:when test="itemType='conferencePaper'" >
			<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='dictionaryEntry'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='document'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='encyclopediaArticle'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='email'" >
				<xsl:element name="dt">l</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='film'" >
				<xsl:element name="dt">g</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='forumPost'" >
				<xsl:element name="dt">l</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='hearing'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='instantMessage'" >
				<xsl:element name="dt">l</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='interview'" >
				<xsl:element name="dt">i</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='journalArticle'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='letter'" >
				<xsl:element name="dt">b</xsl:element>
			</xsl:when>			
			<xsl:when test="itemType='magazineArticle'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='manuscript'" >
				<xsl:element name="dt">b</xsl:element>
			</xsl:when>			
			<xsl:when test="itemType='map'" >
				<xsl:element name="dt">e</xsl:element>
			</xsl:when>			
			<xsl:when test="itemType='newspaperArticle'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='presentation'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>			
			<xsl:when test="itemType='patent'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>			
			<xsl:when test="itemType='podcast'" >
				<xsl:element name="dt">i</xsl:element>
			</xsl:when>			
			<xsl:when test="itemType='report'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='radioBroadcast'" >
				<xsl:element name="dt">i</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='statute'" >
				<xsl:element name="dt">i</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='thesis'" >
				<xsl:element name="dt">a</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='tvBroadcast'" >
				<xsl:element name="dt">g</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='videoRecording'" >
				<xsl:element name="dt">g</xsl:element>
			</xsl:when>
			<xsl:when test="itemType='webpage'" >
				<xsl:element name="dt">l</xsl:element>
			</xsl:when>
			<xsl:otherwise>
 				<xsl:element name="dt">a</xsl:element>
 			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	
	
	<xsl:template name="do_isbn">
		<xsl:param name="isbn"/>
		<xsl:if test="$isbn!=''">
			<f c="010">
				<s c="a"><xsl:value-of select="$isbn"/></s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_issn">
		<xsl:param name="issn"/>
		<xsl:if test="$issn!=''">
			<f c="011">
				<s c="a"><xsl:value-of select="$issn"/></s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_language">
		<xsl:param name="lang"/>
		<xsl:if test="$lang!=''">
			<f c="101">
				<s c="a">
					<xsl:choose>
						<xsl:when test="$lang='anglais'">eng</xsl:when>
						<xsl:otherwise>fre</xsl:otherwise>
					</xsl:choose>
				</s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_title">
		<xsl:param name="title"/>
		<xsl:param name="parallel_title"/>
		<xsl:param name="subtitle"/>
		<xsl:param name="serie_title"/>
		<xsl:param name="serie_number"/>
		<f c="200">
			<s c="a">
				<xsl:choose>
					<xsl:when test="$title!=''" >
						<xsl:value-of select="$title"/>
					</xsl:when>
					<xsl:otherwise>No title</xsl:otherwise>
				</xsl:choose>
			</s>
			<xsl:if test="$parallel_title!=''" >
				<s c="d"><xsl:value-of select="$parallel_title"/></s>
			</xsl:if>
			<xsl:if test="$subtitle!=''" >
				<s c="e"><xsl:value-of select="$subtitle"/></s>
			</xsl:if>
			<xsl:if test="$serie_title!=''" >
				<s c="i"><xsl:value-of select="$serie_title"/></s>
			</xsl:if>
			<xsl:if test="$serie_number!=''" >
				<s c="h"><xsl:value-of select="$serie_number"/></s>
			</xsl:if>
 		</f>
	</xsl:template>

	<xsl:template name="do_edition">
		<xsl:param name="statement"/>
		<xsl:if test="$statement!=''">
			<f c="205">
				<s c="a">
					<xsl:value-of select="$statement"/>
				</s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_publishing">
		<xsl:param name="name"/>
		<xsl:param name="place"/>
		<xsl:param name="country"/>
		<xsl:param name="date"/>
		<xsl:if test="$name!='' or $place!='' or $country!='' or $date!='' ">
			<f c="210">
				<xsl:if test="$place!='' or $country!=''">
					<s c="a">
						<xsl:choose>
							<xsl:when test="$place!='' and $country!=''" >
								<xsl:value-of select="concat($place,'(',$country,')')"/>  
							</xsl:when>
							<xsl:when test="$place!='' and $country=''" >
								<xsl:value-of select="$place"/>
							</xsl:when>
							<xsl:when test="$place='' and $country!=''" >
								<xsl:value-of select="$country"/>
							</xsl:when>
						</xsl:choose>
					</s>
				</xsl:if>
				<xsl:if test="$name!=''">
					<s c="c"><xsl:value-of select="$name"/></s>
				</xsl:if>
				<xsl:if test="$date!=''">
					<s c="d"><xsl:value-of select="$date"/></s>
				</xsl:if>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_pages">
		<xsl:param name="pages"/>
		<xsl:if test="$pages!=''">
			<f c="215">
				<s c="a">
					<xsl:value-of select="$pages"/>
				</s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_duration">
		<xsl:param name="duration"/>
		<xsl:if test="$duration!=''">
			<f c="215">
				<s c="a">
					<xsl:value-of select="$duration"/>
				</s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_format">
		<xsl:param name="format"/>
		<xsl:if test="$format!=''">
			<f c="215">
				<s c="d">
					<xsl:value-of select="$format"/>
				</s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_collection">
		<xsl:param name="collection_title"/>
		<xsl:param name="collection_number"/>
		<xsl:param name="collection_volume"/>
		<xsl:param name="collection_number_of_volumes"/>
		<xsl:param name="collection_issn"/>
		<xsl:param name="subcollection_title"/>
		<xsl:if test="$collection_title!='' or $collection_number!='' or $collection_volume!='' or $collection_number_of_volumes!=''">
			<f c="225">
				<xsl:if test="$collection_title!=''">
					<s c="a"><xsl:value-of select="$collection_title"/></s>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="$collection_number!=''">
						<s c="v"><xsl:value-of select="$collection_number"/></s>
					</xsl:when>
					<xsl:when test="$collection_volume!=''">
						<s c="v">
							<xsl:value-of select="$collection_volume"></xsl:value-of>
							<xsl:if test="$collection_number_of_volumes!=''"> / <xsl:value-of select="$collection_number_of_volumes"/></xsl:if>
						</s>
					</xsl:when>
				</xsl:choose>
				<xsl:if test="$subcollection_title!=''">
					<s c="i"><xsl:value-of select="$subcollection_title"/></s>
				</xsl:if>
				<xsl:if test="$collection_issn!=''">
					<s c="x"><xsl:value-of select="$collection_issn"/></s>
				</xsl:if>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_notes">
		<xsl:if test="attachments">
			<xsl:for-each select="attachments/attachment">
				<xsl:if test="itemType='note'">
					<f c="300">
						<s c="a"><xsl:value-of select="note"/></s>
					</f>
				</xsl:if>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_programming_language">
		<xsl:if test="programmingLanguage!=''">
			<f c="300">
				<s c="a">Language de programmation : <xsl:value-of select="programmingLanguage"/></s>
			</f>			
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_system">
		<xsl:if test="system!=''">
			<f c="300">
				<s c="a">Système : <xsl:value-of select="system"/></s>
			</f>			
		</xsl:if>
	</xsl:template>
		
	<xsl:template name="do_genre">
		<xsl:if test="genre!=''">
			<f c="300">
				<s c="a">Genre : <xsl:value-of select="genre"/></s>
			</f>			
		</xsl:if>
	</xsl:template>
		
	<xsl:template name="do_abstract">
		<xsl:param name="abstract"/>
		<xsl:if test="$abstract!=''">
			<f c="330">
				<s c="a"><xsl:value-of select="$abstract"/></s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_serial">
		<xsl:param name="serial_title"/>
 		<xsl:param name="serial_issn"/>
 		<f c="461">
			<s c="t">
				<xsl:choose>
					<xsl:when test="$serial_title!=''">
						<xsl:value-of select="$serial_title"/>
					</xsl:when>
					<xsl:otherwise>No title</xsl:otherwise>
				</xsl:choose>
			</s>
			<xsl:if test="$serial_issn!=''">
				<s c="x">
					<xsl:value-of select="$serial_issn"/>
				</s>
			</xsl:if>
			<s c="9">lnk:perio</s>
		</f>
	</xsl:template>
	
	<xsl:template name="do_issue">
		<xsl:param name="issue_title"/>
		<xsl:param name="issue_volume"/>
		<xsl:param name="issue_number"/>
		<xsl:param name="issue_date"/>
		<f c="463">
			<xsl:if test="$issue_title!=''">
				<s c="t"><xsl:value-of select="$issue_title"/></s>
			</xsl:if>
			<s c="v">
				<xsl:choose>
					<xsl:when test="$issue_volume!='' and $issue_number!=''">
						<xsl:value-of select="concat($issue_volume,' - ',$issue_number)"/>
					</xsl:when>
					<xsl:when test="$issue_volume!='' and $issue_number=''">
						<xsl:value-of select="$issue_volume"/>
					</xsl:when>
					<xsl:when test="$issue_volume='' and $issue_number!=''">
						<xsl:value-of select="$issue_number"/>
					</xsl:when>
					<xsl:otherwise>No number</xsl:otherwise>
				</xsl:choose>
			</s>
			<xsl:if test="$issue_date!=''">
 				<s c="d">
 					<xsl:value-of select="$issue_date"/>
  				</s>
  				
 				<s c="e">
 					<xsl:value-of select="$issue_date"/>
 				</s>
			</xsl:if>
			<s c="9">lnk:bull</s>
		</f>
	</xsl:template>
	
	<xsl:template name="do_display_date">
		<xsl:param name="date"/>
		<xsl:choose>
			<xsl:when test="$date!=''">
				<xsl:value-of select="$date"/>
			</xsl:when>
			<xsl:otherwise><xsl:value-of select="php:function('date','d/m/Y')"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="do_machine_date">
		<xsl:param name="date"/>
		<xsl:choose>
			<xsl:when test="$date!=''">
				<xsl:value-of select="concat(substring($date,7,4),'-',substring($date,4,2),'-',substring($date,1,2))"/>
			</xsl:when>
			<xsl:otherwise><xsl:value-of select="php:function('date','Y-m-d')"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="do_subjects">
		<xsl:if test="tags">
			<xsl:for-each select="tags">
				<xsl:if test="tag!=''">
					<f c="610">
						<s c="a">
							<xsl:value-of select="tag"/>
						</s>
					</f>
				</xsl:if>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_dewey">
		<xsl:param name="index"/>
		<xsl:if test="$index!=''">
			<f c="676">
				<s c="a"><xsl:value-of select="$index"/></s>
			</f>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_authors">
		<xsl:param name="itemtype"/>
		<xsl:if test="creators">
			<xsl:for-each select="creators">
				<xsl:choose>
					<xsl:when test="position()=1">
						<f c="700">
						<xsl:call-template name="do_author">
							<xsl:with-param name="name" select="name"/>
							<xsl:with-param name="firstname" select="firstName"/>
							<xsl:with-param name="lastname" select="lastName"/>
							<xsl:with-param name="function" select="creatorType"/>
							<xsl:with-param name="itemtype" select="$itemtype"/>
						</xsl:call-template>
						</f>
					</xsl:when>
					<xsl:otherwise>
						<f c="701">
						<xsl:call-template name="do_author">
							<xsl:with-param name="name" select="name"/>
							<xsl:with-param name="firstname" select="firstName"/>
							<xsl:with-param name="lastname" select="lastName"/>
							<xsl:with-param name="function" select="creatorType"/>
							<xsl:with-param name="itemtype" select="$itemtype"/>
						</xsl:call-template>
						</f>
					</xsl:otherwise>				
				</xsl:choose>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_author" >
		<xsl:param name="name"/>
		<xsl:param name="firstname"/>
		<xsl:param name="lastname"/>
		<xsl:param name="function"/>
		<xsl:param name="itemtype"/>
		<xsl:choose>
			<xsl:when test="$name!=''" >
				<s c="a"><xsl:value-of select="$name"/></s>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="$lastname!=''">
					<s c="a"><xsl:value-of select="$lastname"/></s>
				</xsl:if> 
				<xsl:if test="$firstname!=''">
					<s c="b"><xsl:value-of select="$firstname"/></s>
				</xsl:if> 
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="$function!=''">
			<xsl:call-template name="do_function">
				<xsl:with-param name="function" select="$function"/>
				<xsl:with-param name="itemtype" select="$itemtype"/>
			</xsl:call-template> 
		</xsl:if>
	</xsl:template>	

	<xsl:template name="do_function">
		<xsl:param name="function"/>
		<xsl:param name="itemtype"/>
		<xsl:choose>
			<xsl:when test="$function='artist'">
				<s c="4">040</s>
			</xsl:when>
			<xsl:when test="$function='author'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='bookAuthor'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='cartographer'">
				<s c="4">180</s>
			</xsl:when>
			<xsl:when test="$function='castMember'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='commenter'">
				<s c="4">210</s>
			</xsl:when>
			<xsl:when test="$function='composer'">
				<s c="4">230</s>
			</xsl:when>
			<xsl:when test="$function='contributor'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='cosponsor'">
				<s c="4">723</s>
			</xsl:when>
			<xsl:when test="$function='counsel'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='director'">
				<s c="4">300</s>
			</xsl:when>
			<xsl:when test="$function='editor'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='guest'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='interviewee'">
				<s c="4">460</s>
			</xsl:when>
			<xsl:when test="$function='interviewer'">
				<s c="4">470</s>
			</xsl:when>
			<xsl:when test="$function='inventor'">
				<s c="4">584</s>
			</xsl:when>
			<xsl:when test="$function='performer'">
				<s c="4">590</s>
			</xsl:when>			
			<xsl:when test="$function='podcaster'">
				<s c="4">070</s>
			</xsl:when>			
			<xsl:when test="$function='presenter'">
				<s c="4">605</s>
			</xsl:when>			
			<xsl:when test="$function='producer'">
				<s c="4">630</s>
			</xsl:when>
			<xsl:when test="$function='programmer'">
				<s c="4">635</s>
			</xsl:when>			
			<xsl:when test="$function='recipient'">
				<s c="4">660</s>
			</xsl:when>			
			<xsl:when test="$function='reviewedAuthor'">
				<s c="4">070</s>
			</xsl:when>			
			<xsl:when test="$function='scriptwriter'">
				<s c="4">690</s>
			</xsl:when>
			<xsl:when test="$function='seriesEditor'">
				<s c="4">070</s>
			</xsl:when>
			<xsl:when test="$function='sponsor'">
				<s c="4">723</s>
			</xsl:when>
			<xsl:when test="$function='translator'">
				<s c="4">730</s>
			</xsl:when>
			<xsl:when test="$function='wordsBy'">
				<s c="4">520</s>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="do_url">
		<xsl:if test="url!=''" >
			<f c="856" >
				<s c="u"><xsl:value-of select="./url"/></s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_attachments">
		<xsl:if test="attachments">
			<xsl:for-each select="attachments/attachment">
				<xsl:if test="itemType='attachment'" >
				<xsl:choose>
					<xsl:when test="linkMode='imported_file'">
						<xsl:call-template name="do_attachment">
							<xsl:with-param name="url" select="url"/>
							<xsl:with-param name="name" select="title"/>
						</xsl:call-template> 
					</xsl:when>
					<xsl:when test="linkMode='linked_file'">
					</xsl:when>
					<xsl:when test="linkMode='linked_url'">
						<xsl:call-template name="do_attachment">
							<xsl:with-param name="url" select="url"/>
							<xsl:with-param name="name" select="title"/>
						</xsl:call-template> 
					</xsl:when>
					<xsl:when test="linkMode='imported_url'">
						<xsl:call-template name="do_attachment">
							<xsl:with-param name="url" select="url"/>
							<xsl:with-param name="name" select="title"/>
						</xsl:call-template> 
					</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
				</xsl:if>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_attachment">
		<xsl:param name="url"/>
		<xsl:param name="name"/>
		<xsl:if test="$url!='' and $name!=''" >
			<f c="897">
				<s c="a"><xsl:value-of select="$url"/></s>
				<s c="b"><xsl:value-of select="$name"/></s>
			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_doi">
		<xsl:param name="doi"/>
		<xsl:if test="$doi!=''">
			<xsl:call-template name="do_custom">
				<xsl:with-param name="name">doi</xsl:with-param>
				<xsl:with-param name="label">doi</xsl:with-param>
				<xsl:with-param name="content" select="$doi"/>
				<xsl:with-param name="type">doi</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="do_custom" >
		<xsl:param name="name"/>
		<xsl:param name="label"/>
		<xsl:param name="content"/>
		<xsl:param name="type"/>
		<xsl:if test="$name!='' and $content!=''">
			<f c="900">
				<s c="a"><xsl:value-of select="$content"/></s>
				<s c="n"><xsl:value-of select="$name"/></s>
				<s c="l">
					<xsl:choose>
						<xsl:when test="$label!=''" >
							<xsl:value-of select="$label"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="$name"/>
						</xsl:otherwise>
					</xsl:choose>
				</s>
				<xsl:if test="$type!=''">
					<s c="t"><xsl:value-of select="$type"/></s>
				</xsl:if>
 			</f>
		</xsl:if>
	</xsl:template>

	<xsl:template name="do_others" >
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >access_date</xsl:with-param>
			<xsl:with-param name="content" select="accessDate"/>
		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >archive</xsl:with-param>
			<xsl:with-param name="content" select="archive"/>
		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >archive_location</xsl:with-param>
			<xsl:with-param name="content" select="archiveLocation"/>
		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >library_catalog</xsl:with-param>
			<xsl:with-param name="content" select="libraryCatalog"/>
		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >rights</xsl:with-param>
			<xsl:with-param name="content" select="rights"/>
		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >extra</xsl:with-param>
			<xsl:with-param name="content" select="extra"/>
		</xsl:call-template>
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >zotero_item_type</xsl:with-param>
			<xsl:with-param name="content" select="itemType"/>
		</xsl:call-template>	
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >zotero_key</xsl:with-param>
			<xsl:with-param name="content" select="@key"/>
		</xsl:call-template>	
		<xsl:call-template name="do_custom">
			<xsl:with-param name="name" >zotero_version</xsl:with-param>
			<xsl:with-param name="content" select="@version"/>
		</xsl:call-template>	
	</xsl:template>
	
</xsl:stylesheet>