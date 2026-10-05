<?xml version="1.0" encoding="UTF-8"?>
<!-- Feuille de génération de documentation HTML de l'API PMB
****************************************************************************************
© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
****************************************************************************************
$Id: mache_doc_group_to_html.xsl,v 1.2 2016/03/22 09:01:20 cgil Exp $ 
Conception: Erwan Martin:
Design copié de la feuille de style wsdl-viewer.xsl, voir http://tomi.vanek.sk
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://rien" xmlns:func="http://exslt.org/functions"
extension-element-prefixes="func" xmlns:pmb="http://sigb.net/es/misc/" xmlns:exsl="http://exslt.org/common">

<xsl:output method="html"/>
<xsl:param name="external_services_basepath"></xsl:param>
<xsl:param name="catalog_file"></xsl:param>
<xsl:param name="working_group"></xsl:param>
<xsl:param name="lang">fr_FR</xsl:param>
<xsl:param name="navigation_base"></xsl:param>

<func:function name="pmb:msg">
	<xsl:param name="code"></xsl:param>
	<xsl:param name="group"></xsl:param>
	<xsl:choose>
		<xsl:when test="starts-with($code,'msg:')">
	  		<func:result select="document(concat($external_services_basepath, '/','/messages/',$group,'/',$lang, '.xml'))/XMLlist/entry[@code=substring-after($code, ':')]"/>
		</xsl:when>
		<xsl:otherwise>
			 <func:result select="$code"/>
		</xsl:otherwise>
	</xsl:choose>
</func:function>

<xsl:template name="lf2br">
		<!-- import $StringToTransform -->
		<xsl:param name="StringToTransform"/>
		<xsl:choose>
			<!-- string contains linefeed -->
			<xsl:when test="contains($StringToTransform,'&#xA;')">
				<!-- output substring that comes before the first linefeed -->
				<!-- note: use of substring-before() function means        -->
				<!-- $StringToTransform will be treated as a string,       -->
				<!-- even if it is a node-set or result tree fragment.     -->
				<!-- So hopefully $StringToTransform is really a string!   -->
				<xsl:value-of select="substring-before($StringToTransform,'&#xA;')"/>
				<!-- by putting a 'br' element in the result tree instead  -->
				<!-- of the linefeed character, a <br> will be output at   -->
				<!-- that point in the HTML                                -->
				<xsl:value-of select="'&lt;br&gt;'" disable-output-escaping="yes"/>
				<!-- repeat for the remainder of the original string -->
				<xsl:call-template name="lf2br">
					<xsl:with-param name="StringToTransform">
						<xsl:value-of select="substring-after($StringToTransform,'&#xA;')"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:when>
			<!-- string does not contain newline, so just output it -->
			<xsl:otherwise>
				<xsl:value-of select="$StringToTransform"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


<xsl:template match="/">
	<xsl:choose>
		<xsl:when test="$working_group = '' or  (boolean(document(concat($external_services_basepath, '/', $working_group,'_manifest.xml'))) = false)">
			<xsl:call-template name="list_groups"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="group_manifest" select="document(concat($external_services_basepath, '/', $working_group,'_manifest.xml'))"/>
			<xsl:call-template name="show_group">
				<xsl:with-param name="group_manifest" select="$group_manifest"/>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="list_groups">
	<xsl:variable name="catalog" select="document(concat($external_services_basepath, '/',$catalog_file))"/>
	<html><head>
		<title>
			Documentation des fonctions de template de PMB
		</title>
        <link type="text/css" rel="stylesheet" href="doc.css" />
	</head><body id="operations">
		<div id="outer_box">
            <div id="inner_box" onload="pagingInit()">
            	<div id="header">
					<h1>Template de PMB: Liste des groupes de fonctions</h1>
				</div>
				<h2 class="target">Liste des groupes</h2>
				<ul class="listDocs">
					<xsl:for-each select="$catalog/catalog/item">
						<xsl:choose>
							<xsl:when test="$navigation_base != ''">
								<li id="Doc_{@name}">
									<a href="{$navigation_base}group={@name}">
										<span class="titleDoc"><xsl:value-of select="@name"/></span>
										<span class="descDoc"><xsl:value-of select="@description"/></span>
									</a>
								</li>
							</xsl:when>
							<xsl:otherwise>
								<li><xsl:value-of select="@name"/></li>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</ul>
				<br />
			</div>
	      	<div id="footer">
			Le design de cette page a été copié sur celui de la feuille wsdl-viewer.xsl (<a href="http://tomi.vanek.sk/">http://tomi.vanek.sk</a>)
			</div>
		</div>
	</body>
	</html>

</xsl:template>

<xsl:template name="show_group">
	<xsl:param name="group_manifest"/>
	<html><head>
		<title>
			Documentation du groupe de fonction <xsl:value-of select="$group_manifest/manifest/name"/> des templates de PMB PMB
		</title>
        <link type="text/css" rel="stylesheet" href="doc.css" />
	</head><body id="operations">
		<div id="outer_box">
            <div id="inner_box" onload="pagingInit()">
            	<a name="top"/>
            	<div id="header">
					<h1>Template de PMB: Groupe <xsl:value-of select="$group_manifest/manifest/name"/></h1>
				</div>
				<div id="content">
					<div class="page">

						<xsl:if test="$navigation_base != ''">
							<a href="{$navigation_base}">Retour à la liste des groupes</a>
						</xsl:if>

						<h2 class="target">Informations sur le groupe</h2>
						<ul>
							<div class="label">Nom du groupe:</div> <div class="value"><xsl:value-of select="$group_manifest/manifest/name"/></div>
							<div class="label">Description:</div> <div class="value"><xsl:value-of select="pmb:msg($group_manifest/manifest/description, $working_group)"/></div>
							<xsl:if test="count($group_manifest/manifest/requirements/requirement) > 0">
								<div class="label">Nécessite les groupes suivant:</div> 
								<div class="value">
									<ul>
										<xsl:for-each select="$group_manifest/manifest/requirements/requirement">
											<xsl:choose>
												<xsl:when test="$navigation_base != ''">
													<li><a href="{$navigation_base}group={@group}"><xsl:value-of select="@group"/></a></li>
												</xsl:when>
												<xsl:otherwise>
													<li><xsl:value-of select="@group"/></li>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:for-each>
									</ul>
								</div>
							</xsl:if>
							<xsl:if test="count($group_manifest/manifest/types/type) > 0">
								<div class="label">Déclare ou fait référence aux types suivants:</div> 
								<div class="value">
									<ol>
										<xsl:for-each select="$group_manifest/manifest/types/type">
											<li><a href="#type_{@name}"><xsl:value-of select="@name"/></a></li>
										</xsl:for-each>
									</ol>
								</div>
							</xsl:if>
							<xsl:if test="count($group_manifest/manifest/methods/method) > 0">
								<div class="label">Déclare les fonctions suivantes:</div> 
								<div class="value">
									<xsl:for-each select="$group_manifest/manifest/methods">
										<h3><div class="value"><xsl:value-of select="pmb:msg(@comment, $working_group)"/></div></h3>
										<ol>
											<xsl:for-each select="method">
												<li><a href="#method_{@name}"><xsl:value-of select="@name"/></a></li>
											</xsl:for-each>
										</ol>
									</xsl:for-each>
								</div>
							</xsl:if>
						</ul>
					</div>
					<xsl:if test="count($group_manifest/manifest/types/type) > 0">
						<h2>Types déclarés ou référencés</h2>
						<xsl:call-template name="types">
							<xsl:with-param name="group_manifest" select="$group_manifest"/>
						</xsl:call-template>
					</xsl:if>
					<xsl:if test="count($group_manifest/manifest/methods/method) > 0">
						<xsl:for-each select="$group_manifest/manifest/methods">
							<h2><xsl:value-of select="pmb:msg(@comment, $working_group)"/></h2>
							<xsl:call-template name="methods">
								<xsl:with-param name="group_manifest" select="$group_manifest"/>
								<xsl:with-param name="level" select="position()"/>
							</xsl:call-template>
						</xsl:for-each>
						<!--  <h2>Fonctions</h2>
						<xsl:call-template name="methods">
							<xsl:with-param name="group_manifest" select="$group_manifest"/>
						</xsl:call-template>  -->
					</xsl:if> 
				</div>
			</div>
	      	<div id="footer">
			Le design de cette page a été copié sur celui de la feuille wsdl-viewer.xsl (<a href="http://tomi.vanek.sk/">http://tomi.vanek.sk</a>)
			</div>
		</div>
	</body>
	</html>
</xsl:template>

<xsl:template name="types">
	<xsl:param name="group_manifest"/>
	<ol>
		<xsl:for-each select="$group_manifest/manifest/types/type">
		<li class="operation">
		<a name="type_{@name}"/>
		<h3><b><xsl:value-of select="@name"/></b></h3>
			<ul>
				<xsl:choose>
					<xsl:when test="@imported">
						<xsl:variable name="temp_name" select="@name"/>
						<xsl:variable name="buffer2" select="document(concat($external_services_basepath, '/', @imported_from,'/manifest.xml'))/manifest/types/type[@name=$temp_name]"/>
						<div class="label">Nom du type:</div> <div class="value"><xsl:value-of select="@name"/></div>
						<div class="label">Description:</div> <div class="value"><xsl:value-of select="pmb:msg($buffer2/@description, @imported_from)"/></div>
						<div class="label">Localisation:</div> 
						<div class="value">Importé du groupe 
							<xsl:choose>
								<xsl:when test="$navigation_base != ''">
									<a href="{$navigation_base}group={@imported_from}#type_{@name}"><xsl:value-of select="@imported_from"/></a>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@imported_from"/>
								</xsl:otherwise>
							</xsl:choose>
						</div>
						<div class="label">Contenu:</div><div class="value"></div>
						<xsl:call-template name="params_or_results_typecontents">
						  <xsl:with-param name="node_name">part</xsl:with-param>
						  <xsl:with-param name="parent_node" select="$buffer2"/>
						  <xsl:with-param name="current_group" select="@imported_from"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<div class="label">Nom du type:</div> <div class="value"><xsl:value-of select="@name"/></div>
						<div class="label">Description:</div> <div class="value"><xsl:value-of select="pmb:msg(@description, $working_group)"/></div>
						<div class="label">Localisation:</div> <div class="value">local au groupe</div>
						<div class="label">Contenu:</div><div class="value"></div>
						<xsl:call-template name="params_or_results_typecontents">
						  <xsl:with-param name="node_name">part</xsl:with-param>
						  <xsl:with-param name="parent_node" select="."/>
						  <xsl:with-param name="current_group" select="$working_group"/>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
				<br clear="both"/>
				<div style="text-align: right">
					<a href="#top">Sommaire</a>
				</div>
			</ul>
		</li>
		</xsl:for-each>
	</ol>
</xsl:template>

<xsl:template name="methods">
	<xsl:param name="group_manifest"/>
	<xsl:param name="level"/>
	<ol>
		<xsl:for-each select="$group_manifest/manifest/methods[$level]/method">
		<li class="operation">
		<a name="method_{@name}"/>
		<h3><b><xsl:value-of select="@name"/></b></h3>
			<ul>
				<div class="label">Nom de la fonction:</div> <div class="value"><xsl:value-of select="@name"/></div>
				<div class="label">Description:</div> <div class="value"><xsl:value-of select="pmb:msg(@comment, $working_group)"/></div>
				<!--  <div class="label">Version:</div> <div class="value"><xsl:value-of select="@version"/></div> -->
				<xsl:if test="count(requirements/requirement) > 0">
					<div class="label">Nécessite les fonctions suivantes:</div> 
					<div class="value">
						<ul>
							<xsl:for-each select="requirements/requirement">
								<xsl:choose>
									<xsl:when test="$navigation_base != ''">
										<li><a href="{$navigation_base}group={@group}#method_{@name}"><xsl:value-of select="@name"/></a><span class="requirement_descripter"> du groupe </span><a href="{$navigation_base}group={@group}"><xsl:value-of select="@group"/></a><span class="requirement_descripter">, en version </span><xsl:value-of select="@version"/></li>
									</xsl:when>
									<xsl:otherwise>
										<li><xsl:value-of select="@name"/><span class="requirement_descripter"> du groupe </span><xsl:value-of select="@group"/><span class="requirement_descripter">, en version </span><xsl:value-of select="@version"/></li>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</ul>
					</div>
				</xsl:if>
				<div class="label">Description des paramètres:</div><div class="value"><pre><xsl:value-of select="pmb:msg(inputs/description, $working_group)"/></pre></div>
				  <xsl:call-template name="params_or_results_typecontents">
				    <xsl:with-param name="node_name">param</xsl:with-param>
				    <xsl:with-param name="parent_node" select="inputs"/>
				    <xsl:with-param name="current_group" select="$working_group"/>
				  </xsl:call-template>
				<div class="label">Exemples de retours:</div><div class="value"><pre><xsl:value-of select="pmb:msg(outputs/description, $working_group)"/></pre></div>
				  <xsl:call-template name="params_or_results_typecontents">
				    <xsl:with-param name="node_name">result</xsl:with-param>
				    <xsl:with-param name="parent_node" select="outputs"/>
				    <xsl:with-param name="current_group" select="$working_group"/>
				  </xsl:call-template>
			</ul>
			<br />
			<div style="text-align: right">
				<a href="#top">Sommaire</a>
			</div>
		</li>
		</xsl:for-each>
	</ol>
</xsl:template>

<xsl:template name="params_or_results_typecontents">
	<xsl:param name="node_name">param</xsl:param>
	<xsl:param name="parent_node"></xsl:param>
	<xsl:param name="current_group"></xsl:param>
	<xsl:variable name="current_group_manifest" select="document(concat($external_services_basepath, '/', $current_group,'_manifest.xml'))"/>
	<xsl:variable name="temp" select="$parent_node/*[local-name() = $node_name]"/>
	<xsl:for-each select="$temp">
		<div class="value box" style="margin-bottom: 3px;">
			<table width="100%">
				<tr>
					<td style="padding-right:50px;">
						<b>
							<xsl:value-of select="@name"/>
						</b>
						<xsl:choose>
							<xsl:when test="@type='scalar'">
								<span style="color:darkblue">
									<small> type </small>
									<xsl:value-of select="@dataType"/>
									<xsl:variable name="buffer" select="@dataType"/>
									<xsl:choose>
										<xsl:when test="count($current_group_manifest/manifest/types/type[@name=$buffer])>0">
											<xsl:choose>
												<xsl:when test="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported != ''">
													<xsl:variable name="buffer2" select="document(concat($external_services_basepath, '/', $current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from,'/manifest.xml'))/manifest/types"/>
													<xsl:variable name="type_parent_node" select="$buffer2/type[@name=$buffer]"/>
													<xsl:call-template name="type_to_list">
												    	<xsl:with-param name="node_name">part</xsl:with-param>
												    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
												    	<xsl:with-param name="current_group" select="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from"/>
													</xsl:call-template>
												</xsl:when>
												<xsl:otherwise>
													<xsl:variable name="type_parent_node" select="$current_group_manifest/manifest/types/type[@name=$buffer]"/>
													<xsl:call-template name="type_to_list">
												    	<xsl:with-param name="node_name">part</xsl:with-param>
												    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
												    	<xsl:with-param name="current_group" select="$current_group"/>
													</xsl:call-template>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
									</xsl:choose>
								</span>
							</xsl:when>
							<xsl:when test="@type='array'">
								<span style="color:darkblue">
									<small> type tableau de </small>
									<xsl:variable name="temp2" select="*[local-name() = $node_name]"/>
									<xsl:choose>
										<xsl:when test="count($temp2)=1">
											<xsl:value-of select="$temp2/@dataType"/>
											<xsl:variable name="buffer" select="$temp2/@dataType"/>
												<xsl:choose>
												<xsl:when test="count($current_group_manifest/manifest/types/type[@name=$buffer])>0">
													<xsl:choose>
														<xsl:when test="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported != ''">
															<xsl:variable name="buffer2" select="document(concat($external_services_basepath, '/', $current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from,'/manifest.xml'))/manifest/types"/>
															<xsl:variable name="type_parent_node" select="$buffer2/type[@name=$buffer]"/>
															<xsl:call-template name="type_to_list">
														    	<xsl:with-param name="node_name">part</xsl:with-param>
														    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
														    	<xsl:with-param name="current_group" select="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from"/>
															</xsl:call-template>
														</xsl:when>
														<xsl:otherwise>
															<xsl:variable name="type_parent_node" select="$current_group_manifest/manifest/types/type[@name=$buffer]"/>
															<xsl:call-template name="type_to_list">
														    	<xsl:with-param name="node_name">part</xsl:with-param>
														    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
														    	<xsl:with-param name="current_group" select="$current_group"/>
															</xsl:call-template>
														</xsl:otherwise>
													</xsl:choose>
												</xsl:when>
											</xsl:choose>
										</xsl:when>
										<xsl:otherwise>
											la structure suivante:
											  <xsl:call-template name="type_to_list">
											    <xsl:with-param name="node_name"><xsl:copy-of select="$node_name"/></xsl:with-param>
											    <xsl:with-param name="parent_node" select="."/>
											    <xsl:with-param name="current_group" select="$current_group"/>
											  </xsl:call-template>
										</xsl:otherwise>
									</xsl:choose>
								</span>
							</xsl:when>
							<xsl:when test="@type='structure'">
								<span style="color:darkblue">
									<small> type </small>
									structure
									<xsl:variable name="temp2" select="*[local-name() = $node_name]"/>
									<xsl:choose>
										<xsl:when test="count($temp2)=1">
											<xsl:value-of select="$temp2/@dataType"/>
											<xsl:variable name="buffer" select="$temp2/@dataType"/>
												<xsl:choose>
												<xsl:when test="count($current_group_manifest/manifest/types/type[@name=$buffer])>0">
													<xsl:choose>
														<xsl:when test="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported != ''">
															<xsl:variable name="buffer2" select="document(concat($external_services_basepath, '/', $current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from,'/manifest.xml'))/manifest/types"/>
															<xsl:variable name="type_parent_node" select="$buffer2/type[@name=$buffer]"/>
															<xsl:call-template name="type_to_list">
														    	<xsl:with-param name="node_name">part</xsl:with-param>
														    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
														    	<xsl:with-param name="current_group" select="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from"/>
															</xsl:call-template>
														</xsl:when>
														<xsl:otherwise>
															<xsl:variable name="type_parent_node" select="$current_group_manifest/manifest/types/type[@name=$buffer]"/>
															<xsl:call-template name="type_to_list">
														    	<xsl:with-param name="node_name">part</xsl:with-param>
														    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
														    	<xsl:with-param name="current_group" select="$current_group"/>
															</xsl:call-template>
														</xsl:otherwise>
													</xsl:choose>
												</xsl:when>
											</xsl:choose>
										</xsl:when>
										<xsl:otherwise>
											  <xsl:call-template name="type_to_list">
											    <xsl:with-param name="node_name"><xsl:copy-of select="$node_name"/></xsl:with-param>
											    <xsl:with-param name="parent_node" select="."/>
											    <xsl:with-param name="current_group" select="$current_group"/>
											  </xsl:call-template>
										</xsl:otherwise>
									</xsl:choose>
								</span>
							</xsl:when>
						</xsl:choose>
					</td>
					<td align="right" class="input_description">
				    <xsl:call-template name="lf2br">
	            		<xsl:with-param name="StringToTransform" select="pmb:msg(@description, $current_group)"/>
	    			</xsl:call-template>
					</td>
				</tr>
			</table>
		</div>
	</xsl:for-each>
</xsl:template>

<xsl:template name="type_to_list">
	<xsl:param name="node_name">param</xsl:param>
	<xsl:param name="parent_node"></xsl:param>
	<xsl:param name="current_group"></xsl:param>
	<xsl:variable name="current_group_manifest" select="document(concat($external_services_basepath, '/', $current_group,'_manifest.xml'))"/>
	<xsl:variable name="temp" select="$parent_node/*[local-name() = $node_name]"/>
	<xsl:for-each select="$temp">
		<ul type="square">
			<li>
				<span style="color:black"><xsl:value-of select="@name"/></span>
				<xsl:choose>
				<xsl:when test="@type='scalar'">
					<span style="color:darkblue">
						<small> type </small>
						<xsl:variable name="buffer" select="@dataType"/>
						<xsl:choose>
							<xsl:when test="(count(ancestor::*[@name = $buffer]) = 0) and count($current_group_manifest/manifest/types/type[@name=$buffer])>0">
								<xsl:value-of select="$current_group_manifest/manifest/types/type/@imported"/>
								<xsl:choose>
									<xsl:when test="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported != ''">
										<xsl:variable name="buffer2" select="document(concat($external_services_basepath, '/', $current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from,'/manifest.xml'))/manifest/types"/>
										<xsl:variable name="type_parent_node" select="$buffer2/type[@name=$buffer]"/>
										<xsl:call-template name="type_to_list">
									    	<xsl:with-param name="node_name">part</xsl:with-param>
									    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
									    	<xsl:with-param name="current_group" select="$current_group_manifest/manifest/types/type[@name=$buffer]/@imported_from"/>
										</xsl:call-template>
									</xsl:when>
									<xsl:otherwise>
										<xsl:variable name="type_parent_node" select="$current_group_manifest/manifest/types/type[@name=$buffer]"/>
										<xsl:call-template name="type_to_list">
									    	<xsl:with-param name="node_name">part</xsl:with-param>
									    	<xsl:with-param name="parent_node" select="$type_parent_node"/>
									    	<xsl:with-param name="current_group" select="$current_group"/>
										</xsl:call-template>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="@dataType"/>	&#xA0;&#xA0;&#xA0;&#xA0;&#xA0;&#xA0;&#xA0;<i class="subfield_description"><xsl:value-of select="pmb:msg(@description, $current_group)"/></i>
							</xsl:otherwise>
						</xsl:choose>
					</span>
				</xsl:when>
				<xsl:when test="@type='array'">
					<span style="color:darkblue">
						<small> type tableau de </small>
						<xsl:variable name="temp2" select="*[local-name() = $node_name]"/>
						<xsl:choose>
							<xsl:when test="count($temp2)=1">
								<xsl:value-of select="$temp2/@dataType"/>
							</xsl:when>
							<xsl:otherwise>
								la structure suivante: &#xA0;&#xA0;&#xA0;&#xA0;&#xA0;&#xA0;&#xA0;<i clear="both" class="subfield_description"><xsl:value-of select="pmb:msg(@description, $current_group)"/></i>
								  <xsl:call-template name="type_to_list">
								    <xsl:with-param name="node_name"><xsl:copy-of select="$node_name"/></xsl:with-param>
								    <xsl:with-param name="parent_node" select="."/>
								    <xsl:with-param name="current_group" select="$current_group"/>
								  </xsl:call-template>
							</xsl:otherwise>
						</xsl:choose>
					</span>
				</xsl:when>
				<xsl:when test="@type='structure'">
					<span style="color:darkblue">
						<small> type </small>
						structure &#xA0;&#xA0;&#xA0;&#xA0;&#xA0;&#xA0;&#xA0;<i clear="both" class="subfield_description"><xsl:value-of select="pmb:msg(@description, $current_group)"/></i>
						<xsl:variable name="temp2" select="*[local-name() = $node_name]"/>
						<xsl:choose>
							<xsl:when test="count($temp2)=1">
								<xsl:value-of select="$temp2/@dataType"/>
							</xsl:when>
							<xsl:otherwise>
								  <xsl:call-template name="type_to_list">
								    <xsl:with-param name="node_name"><xsl:copy-of select="$node_name"/></xsl:with-param>
								    <xsl:with-param name="parent_node" select="."/>
								    <xsl:with-param name="current_group" select="$current_group"/>
								  </xsl:call-template>
							</xsl:otherwise>
						</xsl:choose>
					</span>
				</xsl:when>
			</xsl:choose>
			</li>
		</ul>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>