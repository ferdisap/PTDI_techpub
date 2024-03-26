<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="setGraphicDimension">
    <xsl:if test="graphic/@reproductionHeight">
      <xsl:attribute name="height"><xsl:value-of select="graphic/@reproductionHeight"/></xsl:attribute>
    </xsl:if>
    <xsl:if test="graphic/@reproductionWidth">
      <xsl:attribute name="height"><xsl:value-of select="graphic/@reproductionWidth"/></xsl:attribute>
    </xsl:if>
  </xsl:template>

  <xsl:attribute-set name="fmIntroName">
    <xsl:attribute name="font-size">18pt</xsl:attribute>
    <xsl:attribute name="font-weight">bold</xsl:attribute>
    <xsl:attribute name="margin-top">32pt</xsl:attribute>
  </xsl:attribute-set>

  <xsl:attribute-set name="fmPmTitle">
    <xsl:attribute name="font-size">24pt</xsl:attribute>
    <xsl:attribute name="font-weight">bold</xsl:attribute>
    <xsl:attribute name="margin-top">28pt</xsl:attribute>
  </xsl:attribute-set>

  <xsl:attribute-set name="fmShortPmTitle">
    <xsl:attribute name="font-size">14pt</xsl:attribute>
    <xsl:attribute name="font-weight">bold</xsl:attribute>
  </xsl:attribute-set>

  <xsl:attribute-set name="fmPmCode">
    <xsl:attribute name="font-size">14pt</xsl:attribute>
    <xsl:attribute name="font-weight">bold</xsl:attribute>
    <xsl:attribute name="margin-top">28pt</xsl:attribute>
  </xsl:attribute-set>

  <xsl:attribute-set name="fmPmIssueInfo">
    <xsl:attribute name="font-size">14pt</xsl:attribute>
    <xsl:attribute name="font-weight">bold</xsl:attribute>
  </xsl:attribute-set>

  <xsl:attribute-set name="fmPmIssueDate">
    <xsl:attribute name="font-size">14pt</xsl:attribute>
    <xsl:attribute name="font-weight">bold</xsl:attribute>
  </xsl:attribute-set>

  <xsl:attribute-set name="h1">
    <xsl:attribute name="font-size">14pt</xsl:attribute>
    <xsl:attribute name="font-weight">bold</xsl:attribute>
    <xsl:attribute name="margin-top">9pt</xsl:attribute>
  </xsl:attribute-set>

  <xsl:attribute-set name="reducedPara">
    <xsl:attribute name="font-size">10pt</xsl:attribute>
    <xsl:attribute name="margin-top">6pt</xsl:attribute>
  </xsl:attribute-set>
</xsl:transform>
