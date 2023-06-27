<?xml version="1.0" ?>
<xsl:stylesheet version='1.0' 
  xmlns:xsl='http://www.w3.org/1999/XSL/Transform'
  xmlns:msxsl="urn:schemas-microsoft-com:xslt"
  xmlns:fmt="urn:p2plusfmt-xsltformats" 

  xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
  xmlns:s="http://www.w3.org/2001/XMLSchema"
  xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" 
  xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/">
  <!-- 
  /// 19.07.2005 optional documentation
  /// 20.07.2005 more datatypes and XML Documents 
  /// 20.07.2005 more datatypes and XML Documents fixed
  -->
  <xsl:strip-space elements="*" />
  <xsl:output method="text" version="4.0" />
  <xsl:param name="alias">
    <xsl:value-of select="wsdl:definitions/wsdl:service/@name" />
  </xsl:param>

  <xsl:template match="/">
    // javascript proxy for webservices
    // by Matthias Hertel
    /*<xsl:value-of select="wsdl:definitions/wsdl:documentation"/>*/
     <xsl:for-each select=
        "/wsdl:definitions/wsdl:service/wsdl:port[soap:address]">
       <xsl:call-template name="soapport" />
     </xsl:for-each>
  </xsl:template>

  <xsl:template name="soapport">
     proxies.<xsl:value-of select="$alias" /> = {
     url: "<xsl:value-of select="soap:address/@location" />",
     ns: "<xsl:value-of 
      select=
        "/wsdl:definitions/wsdl:types/s:schema/@targetNamespace"/>"
     } // proxies.<xsl:value-of select="$alias" />
     <xsl:text>
     </xsl:text>

     <xsl:for-each select="/wsdl:definitions/wsdl:binding[@name = 
                      substring-after(current()/@binding, ':')]">
       <xsl:call-template name="soapbinding11" />
     </xsl:for-each>
  </xsl:template>

  <xsl:template name="soapbinding11">
     <xsl:variable name="portTypeName" 
       select="substring-after(current()/@type, ':')" />
    <xsl:for-each select="wsdl:operation">
       <xsl:variable name="inputMessageName" 
        select="substring-after(/wsdl:definitions/wsdl:portType[@name = 
                $portTypeName]/wsdl:operation[@name = 
                current()/@name]/wsdl:input/@message, ':')" />
       <xsl:variable name="outputMessageName" 
        select="substring-after(/wsdl:definitions/wsdl:portType[@name = 
                $portTypeName]/wsdl:operation[@name = current()/@name]
                /wsdl:output/@message, ':')" />

       <xsl:for-each select="/wsdl:definitions/wsdl:portType[@name = 
                               $portTypeName]/wsdl:operation[@name = 
                               current()/@name]/wsdl:documentation">
        /** <xsl:value-of select="." /> */
       </xsl:for-each>
       proxies.<xsl:value-of 
        select="$alias" />.<xsl:value-of select="@name" /> 
        = function () { return(proxies.callSoap(arguments)); }
       proxies.<xsl:value-of 
        select="$alias" />.<xsl:value-of select="@name" />.fname
        = "<xsl:value-of select="@name" />";
       proxies.<xsl:value-of 
        select="$alias" />.<xsl:value-of select="@name" />.service
        = proxies.<xsl:value-of select="$alias" />;
       proxies.<xsl:value-of 
        select="$alias" />.<xsl:value-of select="@name" />.action
        = "<xsl:value-of select="soap:operation/@soapAction" />";
       proxies.<xsl:value-of 
        select="$alias" />.<xsl:value-of select="@name" />.params
        = [<xsl:for-each select="/wsdl:definitions/wsdl:message[@name 
        = $inputMessageName]">
        <xsl:call-template name="soapMessage" />
      </xsl:for-each>];
      proxies.<xsl:value-of select="$alias" />.
         <xsl:value-of select="@name" />.rtype 
         = [<xsl:for-each 
         select="/wsdl:definitions/wsdl:message[@name = 
                                    $outputMessageName]">
         <xsl:call-template name="soapMessage" />
         </xsl:for-each>];
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="soapMessage">
    <xsl:variable name="inputElementName" 
       select="substring-after(wsdl:part/@element, ':')" />
    <xsl:for-each select="/wsdl:definitions/wsdl:types/s:schema/s:element
                                   [@name=$inputElementName]//s:element">
      <xsl:choose>
        <xsl:when test="@type='s:string'">
          "<xsl:value-of select="@name" />"
        </xsl:when>
        <xsl:when test="@type='s:int' 
                  or @type='s:unsignedInt' or @type='s:short' 
                  or @type='s:unsignedShort' or @type='s:unsignedLong' 
                  or @type='s:long'">
          "<xsl:value-of select="@name" />:int"
        </xsl:when>
        <xsl:when test="@type='s:double' or @type='s:float'">
          "<xsl:value-of select="@name" />:float"
        </xsl:when>
        <xsl:when test="@type='s:dateTime'">
          "<xsl:value-of select="@name" />:date"
        </xsl:when>
        <xsl:when test="./s:complexType/s:sequence/s:any">
          "<xsl:value-of select="@name" />:x"
        </xsl:when>
        <xsl:otherwise>
          "<xsl:value-of select="@name" />"
        </xsl:otherwise>
      </xsl:choose>
      <xsl:if test="position()!=last()">,</xsl:if>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>