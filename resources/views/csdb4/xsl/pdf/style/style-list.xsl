<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">
  
  <xsl:template name="style-listItem">
    <xsl:if test="parent::sequentialList or parent::definitionList">
      <xsl:attribute name="margin-top">11pt</xsl:attribute>
    </xsl:if>
  </xsl:template>

</xsl:transform>