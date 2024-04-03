<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="style-table">
    <xsl:param name="orient"/>
    <xsl:param name="level"/>
    <xsl:attribute name="margin-top">3pt</xsl:attribute>
    <xsl:if test="$orientation = 'port'">
      <xsl:if test="$orient = 'land'">
        <xsl:attribute name="reference-orientation">90</xsl:attribute>
      </xsl:if>
    </xsl:if>
    <xsl:if test="$orientation = 'land'">
      <xsl:if test="$orient = 'port'">
        <xsl:attribute name="reference-orientation">90</xsl:attribute>
      </xsl:if>
    </xsl:if>
    <xsl:choose>
      <xsl:when test="following-sibling::para">
        <xsl:attribute name="margin-bottom">18pt</xsl:attribute>
      </xsl:when>
      <xsl:when test="following-sibling::title">
        <xsl:attribute name="margin-bottom">22pt</xsl:attribute>
      </xsl:when>
      <!-- compliance to Chap 6.2.2 page 14 randomlist paragraph -->
      <xsl:when test="parent::*/following-sibling::levelledPara/title">
        <xsl:choose>
          <xsl:when test="$level = 's0' or $level = 's1'">
            <xsl:attribute name="margin-bottom">21pt</xsl:attribute>
          </xsl:when>
          <xsl:when test="$level = 's2'">
            <xsl:attribute name="margin-bottom">21pt</xsl:attribute>
          </xsl:when>
          <xsl:when test="$level = 's3' or $level = 's4'">
            <xsl:attribute name="margin-bottom">18pt</xsl:attribute>
          </xsl:when>
          <xsl:when test="$level = 's5'">
            <xsl:attribute name="margin-bottom">18pt</xsl:attribute>
          </xsl:when>
        </xsl:choose>
      </xsl:when>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template name="style-tgroup">
    <xsl:param name="pgwide"/>
    <xsl:param name="orient">
      <xsl:value-of select="string(parent::*/@orient)"/>
    </xsl:param>
    <xsl:param name="frame"/>

    <xsl:if test="($orient != 'land') and ($pgwide = '1')">
      <xsl:attribute name="table-layout">fixed</xsl:attribute>
      <xsl:attribute name="width">100%</xsl:attribute>
      <xsl:attribute name="start-indent">0cm</xsl:attribute>
      <xsl:attribute name="end-indent">0cm</xsl:attribute>
    </xsl:if>
    
    <xsl:if test="$frame = 'all'">
      <xsl:attribute name="border">1pt solid black</xsl:attribute>
    </xsl:if>
    <xsl:if test="$frame = 'sides'">
      <xsl:attribute name="border-left">1pt solid black</xsl:attribute>
      <xsl:attribute name="border-right">1pt solid black</xsl:attribute>
    </xsl:if>
    <xsl:if test="$frame = 'top'">
      <xsl:attribute name="border-top">1pt solid black</xsl:attribute>
    </xsl:if>
    <xsl:if test="$frame = 'topbot'">
      <xsl:attribute name="border-top">1pt solid black</xsl:attribute>
      <xsl:attribute name="border-bottom">1pt solid black</xsl:attribute>
    </xsl:if>
    <xsl:if test="$frame = 'bottom'">
      <xsl:attribute name="border-bottom">1pt solid black</xsl:attribute>
    </xsl:if>
    <xsl:if test="$frame = 'none'">
      <xsl:attribute name="border">none</xsl:attribute>
    </xsl:if>    
  </xsl:template>

  <xsl:template name="style-row">
    <xsl:if test="parent::tbody">
      <xsl:attribute name="padding-top">4pt</xsl:attribute>
      <xsl:attribute name="padding-bottom">4pt</xsl:attribute>
    </xsl:if>
    <xsl:if test="parent::thead">
      <xsl:attribute name="font-weight">bold</xsl:attribute>
    </xsl:if>
  </xsl:template>

  <xsl:template name="style-entry">
    <xsl:param name="rowsep"/>
    <xsl:param name="colsep"/>
    
    <xsl:if test="$rowsep = '1'">
      <xsl:attribute name="border-bottom">1pt solid black</xsl:attribute>
    </xsl:if>
    <xsl:if test="$colsep = '1'">
      <xsl:variable name="index"><xsl:number/></xsl:variable>
      <xsl:if test="$index = '1'">
        <xsl:attribute name="border-left">1pt solid black</xsl:attribute>
      </xsl:if>
      <xsl:attribute name="border-right">1pt solid black</xsl:attribute>
    </xsl:if>

    <xsl:attribute name="padding-top">4pt</xsl:attribute>
    <xsl:attribute name="padding-bottom">4pt</xsl:attribute>
    <xsl:attribute name="padding-right">4pt</xsl:attribute>
    <xsl:attribute name="padding-left">4pt</xsl:attribute>
  </xsl:template>
  
  <xsl:template name="style-tbody">
    <xsl:if test="tfoot or preceding-sibling::tfoot or descendant::footnote">
      <xsl:attribute name="border-bottom">1pt solid black</xsl:attribute>
    </xsl:if>
  </xsl:template>

</xsl:transform>