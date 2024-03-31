<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">
  
  <xsl:template name="style-warningcautionnote">
    <xsl:attribute name="margin-top">11pt</xsl:attribute>
    <xsl:choose>    
      <xsl:when test="name(.) = 'note'">
        <xsl:attribute name="border">6pt solid black</xsl:attribute>
        <xsl:attribute name="padding">8pt</xsl:attribute>
      </xsl:when>
      <xsl:when test="name(.) = 'caution'">
        <xsl:attribute name="border">6pt solid orange</xsl:attribute>
        <xsl:attribute name="padding">8pt</xsl:attribute>
      </xsl:when>
      <xsl:when test="name(.) = 'note'">
        <xsl:attribute name="border">6pt solid red</xsl:attribute>
        <xsl:attribute name="padding">8pt</xsl:attribute>
      </xsl:when>
    </xsl:choose>
  </xsl:template>

</xsl:transform>