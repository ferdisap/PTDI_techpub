<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="reducedPara">
    <fo:block page-break-before="avoid">
      <xsl:call-template name="style-para"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>

  <xsl:template match="para">
    <xsl:param name="level"/>
    <xsl:call-template name="add_applicability"/>
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>
    <fo:block page-break-before="avoid">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="style-para">
        <xsl:with-param name="level" select="$level"/>
      </xsl:call-template>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>
  
  <xsl:template match="simplePara">
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>
    <fo:block page-break-before="avoid">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="style-para"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>
  
  <!-- supaya inline, not block -->
  <xsl:template match="para[parent::footnote]">
    <xsl:apply-templates/>
  </xsl:template>
  
  <xsl:template match="notePara|warningAndCautionPara">
    <fo:block margin-top="11pt" text-align="left">
      <xsl:call-template name="style-warningcautionnotePara"/>
      <xsl:apply-templates/>     
    </fo:block>
  </xsl:template>

  <xsl:template match="para[parent::challenge] | para[parent::response]">
    <xsl:choose>
      <xsl:when test="following-sibling::*">
        <fo:block>
          <xsl:apply-templates/>
        </fo:block>
      </xsl:when>
      <xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:transform>