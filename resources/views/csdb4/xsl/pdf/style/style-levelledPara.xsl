<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="style-levelledPara">
    <xsl:param name="level">
      <xsl:text>s</xsl:text>
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::checkLevel', ., 1)"/>
    </xsl:param>
    <xsl:choose>
      <!-- compliance to S1000D v5.0 chap 6.2.2 page 5, table 3 colom 5 (leading text paragraph to the heading) -->
      <xsl:when test="following-sibling::levelledPara">
        <xsl:attribute name="margin-top">
          <xsl:choose>
            <xsl:when test="($level = 's0') or ($level = 's1') or ($level = 'c1') or ($level = 'c2')">27pt</xsl:when>
            <xsl:when test="$level = 's2'">25pt</xsl:when>
            <xsl:when test="$level = 's3'">23pt</xsl:when>
            <xsl:when test="$level = 's4'">23pt</xsl:when>
            <xsl:when test="$level = 's5'">19pt</xsl:when>
            <xsl:otherwise>0pt</xsl:otherwise>
          </xsl:choose>
        </xsl:attribute>
      </xsl:when>
      <!-- compliance to S1000D v5.0 chap 6.2.2 page 5, table 3 colom 3 (leading to next lower level of heading) -->
      <xsl:when test="parent::levelledPara">
        <xsl:attribute name="margin-top">
          <xsl:choose>
            <xsl:when test="($level = 's0') or ($level = 'c1') or ($level = 'c2')">28pt</xsl:when>
            <xsl:when test="$level = 's1'">14pt</xsl:when>
            <xsl:when test="($level = 's2') or ($level = 's3') or ($level = 's4')">11pt</xsl:when>
            <xsl:otherwise>0pt</xsl:otherwise>
          </xsl:choose>
        </xsl:attribute>
      </xsl:when>
    </xsl:choose>
  </xsl:template>
  
</xsl:transform>