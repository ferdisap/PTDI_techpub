<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="security">
    <xsl:apply-templates select="@securityClassification"/>
  </xsl:template>

  <xsl:template match="@securityClassification[parent::security]">
    <xsl:param name="useInterpreter" select="'yes'"/>
    <xsl:variable name="number">
      <xsl:value-of select="."/>
    </xsl:variable>
    <xsl:choose>
      <xsl:when test="$useInterpreter = 'yes'">
        <xsl:call-template name="interpretSC">
          <xsl:with-param name="scCode" select="$number"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$number"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="interpretSC">
    <xsl:param name="scCode"/>
    <xsl:if test="$scCode">
      <xsl:value-of select="document('../../Config.xml')/config/security/securityClassification[@code = $scCode]"/>
    </xsl:if>
  </xsl:template>

</xsl:transform>