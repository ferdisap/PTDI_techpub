<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">
  
  <xsl:template name="style-warningcautionnote">
    <xsl:attribute name="margin-top">11pt</xsl:attribute>
    <xsl:attribute name="margin-bottom">8pt</xsl:attribute>
    <xsl:choose>
      <xsl:when test="name(.) = 'caution'">
        <xsl:attribute name="padding">0.5cm</xsl:attribute>
        <xsl:attribute name="background-image">url('<xsl:value-of select="$cautionPath"/>')</xsl:attribute>
      </xsl:when>
      <xsl:when test="name(.) = 'warning'">
        <xsl:attribute name="padding">0.5cm</xsl:attribute>
        <xsl:attribute name="background-image">url('<xsl:value-of select="$warningPath"/>')</xsl:attribute>
      </xsl:when>
    </xsl:choose>
  </xsl:template>

</xsl:transform>