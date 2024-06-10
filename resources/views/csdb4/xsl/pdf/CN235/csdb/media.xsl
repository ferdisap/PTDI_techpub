<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <xsl:template name="get_logo">
    <xsl:variable name="infoEntityPath">
      <xsl:text>url('</xsl:text>
      <!-- <xsl:text>file:///D:/Temporary/tesimage.png</xsl:text> -->
      <!-- <xsl:text>file:\\\D:\Temporary\tesimage.png</xsl:text> -->
      <!-- <xsl:text>file:\\\D:\Temporary/tesimage.png</xsl:text> -->
      <xsl:value-of select="unparsed-entity-uri(//dmStatus/logo/symbol/@infoEntityIdent)"/>
      <xsl:text>')</xsl:text>
    </xsl:variable>
    
    <fo:block>
      <fo:external-graphic src="{$infoEntityPath}" content-width="scale-to-fit" width="2.5cm"/>
    </fo:block>
  </xsl:template>

  <xsl:template match="symbol">
    <fo:inline>
      <xsl:call-template name="add_applicability"/>
      <xsl:call-template name="add_controlAuthority"/>
      <fo:external-graphic src="url('{unparsed-entity-uri(@infoEntityIdent)}')" content-width="scale-to-fit" max-width="100%">
        <xsl:call-template name="style-icn"/>
      </fo:external-graphic>
    </fo:inline>
  </xsl:template>

  <xsl:template match="figure">
    <fo:block text-align="center" margin-bottom="11pt" page-break-inside="avoid">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="cgmark_begin"/>
      <xsl:call-template name="add_applicability"/>
      <xsl:call-template name="add_controlAuthority"/>
      <xsl:call-template name="add_security"/>
      <xsl:apply-templates select="graphic"/>
      <xsl:apply-templates select="legend"/>
      <xsl:apply-templates select="title">
        <xsl:with-param name="prefix">Fig <xsl:number level="any"/><xsl:text>&#160;&#160;</xsl:text></xsl:with-param>
      </xsl:apply-templates>
      <xsl:call-template name="cgmark_end"/>
    </fo:block>
  </xsl:template>

  <xsl:template match="graphic">
    <fo:block margin-bottom="11pt">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="cgmark_begin"/>
      <xsl:if test="@applicRefId or @controlAuthorityRefs or @securityClassification or @derivativeClassificationRefId or @commercialClassification or @caveat">
        <fo:block text-align="center">
          <xsl:call-template name="add_applicability"/>
          <xsl:call-template name="add_controlAuthority"/>
          <xsl:call-template name="add_security"/>
        </fo:block>
      </xsl:if>
      <fo:external-graphic src="url('{unparsed-entity-uri(@infoEntityIdent)}')" content-width="scale-to-fit" max-width="100%" max-height="90%">
        <xsl:call-template name="style-icn"/>
      </fo:external-graphic>
      <fo:block text-align="right"><xsl:value-of select="@infoEntityIdent"/></fo:block>
      <xsl:call-template name="cgmark_end"/>
    </fo:block>
  </xsl:template>

  <xsl:template match="graphic[parent::table]">
    <fo:block>
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="cgmark_begin"/>
      <xsl:if test="@applicRefId or @controlAuthorityRefs or @securityClassification or @derivativeClassificationRefId or @commercialClassification or @caveat">
        <fo:block text-align="center">
          <xsl:call-template name="add_applicability"/>
          <xsl:call-template name="add_controlAuthority"/>
          <xsl:call-template name="add_security"/>
        </fo:block>
      </xsl:if>
      <fo:external-graphic src="url('{unparsed-entity-uri(@infoEntityIdent)}')" content-width="scale-to-fit">
        <xsl:call-template name="setGraphicDimension"/>
      </fo:external-graphic>
      <fo:block text-align="right"><xsl:value-of select="@infoEntityIdent"/></fo:block>
      <xsl:call-template name="cgmark_end"/>
    </fo:block>
  </xsl:template>

</xsl:stylesheet>