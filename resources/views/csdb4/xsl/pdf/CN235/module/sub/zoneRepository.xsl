<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="zoneRepository">
    <fo:block font-size="14pt" font-weight="bold" margin-bottom="6pt" margin-top="6pt"
      text-align="center">Common Information Repository - Zone Repository
    </fo:block>
    <fo:block>
      <xsl:call-template name="add_id" />
      <xsl:call-template name="add_controlAuthority" />
      <xsl:call-template name="add_security" />

      <fo:table width="100%">
        <fo:table-column column-number="1" column-width="15%" />
        <fo:table-column column-number="1" column-width="85%" />
        <fo:table-header>
          <fo:table-row>
            <fo:table-cell number-columns-spanned="2" text-align="center" padding-top="2pt">
              <fo:block font-weight="bold">Major Zone</fo:block>
            </fo:table-cell>
          </fo:table-row>
          <fo:table-row>
            <fo:table-cell padding="4pt" padding-left="0pt">
              <fo:block>Zone</fo:block>
            </fo:table-cell>
            <fo:table-cell padding="4pt" padding-left="0pt">
              <fo:block>Description</fo:block>
            </fo:table-cell>
          </fo:table-row>
        </fo:table-header>
        <fo:table-body>
          <xsl:apply-templates select="zoneSpec" />
        </fo:table-body>
      </fo:table>
    </fo:block>
  </xsl:template>

  <xsl:template match="zoneSpec">
    <xsl:if test="@controlAuthorityRefs">
      <fo:table-row keep-together="always">
        <fo:table-cell number-columns-spanned="2">
          <xsl:call-template name="add_controlAuthority" />
        </fo:table-cell>
      </fo:table-row>
    </xsl:if>

    <xsl:if test="@securityClassification or @commercialClassification or @caveat">
      <fo:table-row keep-together="always">
        <fo:table-cell number-columns-spanned="2">
          <xsl:call-template name="add_security"/>
        </fo:table-cell>
      </fo:table-row>
    </xsl:if>

    <xsl:apply-templates select="zoneAlts/zone"/>
  </xsl:template>
  
  <xsl:template match="zone">
    <fo:table-row>
      <xsl:call-template name="add_id"/>
      <fo:table-cell>
        <fo:block text-align="left">
          <xsl:call-template name="style-para"/>
          <xsl:value-of select="ancestor::zoneSpec/zoneIdent/@zoneNumber"/>
        </fo:block>
      </fo:table-cell>
      <fo:table-cell>
        <fo:block text-align="left">
          <xsl:call-template name="style-para"/>
          <xsl:call-template name="add_id"/>
          <xsl:call-template name="add_applicability"/>
          <xsl:call-template name="add_controlAuthority"/>
          <xsl:call-template name="add_security"/>

          <xsl:apply-templates select="itemDescr"/>
        </fo:block>
      </fo:table-cell>
    </fo:table-row>
  </xsl:template>

  <xsl:template match="itemDescr">
    <fo:block>
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="add_controlAuthority"/>
      <xsl:call-template name="add_security"/>
      <xsl:value-of select="."/>
    </fo:block>
  </xsl:template>


</xsl:transform>