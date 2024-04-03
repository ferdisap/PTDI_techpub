<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="emphasis">
    <fo:inline>
      <xsl:call-template name="setEmphasisAttribute"/>
      <xsl:apply-templates/>
    </fo:inline>
  </xsl:template>

  <xsl:template name="setEmphasisAttribute">
    <xsl:param name="emphasisType" select="string(@emphasisType)"/>
    <xsl:variable name="type" select="string($ConfigXML//emphasis[string(@type) = $emphasisType])"/>
    <xsl:choose>
      <xsl:when test="$type = 'bold'">
        <xsl:attribute name="font-weight">bold</xsl:attribute>
      </xsl:when>
      <xsl:when test="$type = 'italic'">
        <xsl:attribute name="font-style">italic</xsl:attribute>
      </xsl:when>
      <xsl:when test="$type = 'underline'">
        <xsl:attribute name="text-decoration">underline</xsl:attribute>
      </xsl:when>
      <xsl:when test="$type = 'strikethrough'">
        <xsl:attribute name="text-decoration">line-through</xsl:attribute>
      </xsl:when>
      <xsl:when test="$type = 'underline-bold'">
        <xsl:attribute name="text-decoration">underline</xsl:attribute>
        <xsl:attribute name="font-weight">bold</xsl:attribute>
      </xsl:when>
      <xsl:when test="$type = 'underline-italic'">
        <xsl:attribute name="text-decoration">underline</xsl:attribute>
        <xsl:attribute name="font-style">italic</xsl:attribute>
      </xsl:when>
      <xsl:when test="$type = 'bold-italic'">
        <xsl:attribute name="font-weight">bold</xsl:attribute>
        <xsl:attribute name="font-style">italic</xsl:attribute>
      </xsl:when>
      <xsl:when test="$type = 'bold-italic-underline'">
        <xsl:attribute name="font-weight">bold</xsl:attribute>
        <xsl:attribute name="font-style">italic</xsl:attribute>
        <xsl:attribute name="text-decoration">underline</xsl:attribute>
      </xsl:when>
    </xsl:choose>
  </xsl:template>

</xsl:transform>