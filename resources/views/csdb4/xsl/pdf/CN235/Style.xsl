<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:include href="../style/style-para.xsl" />
  <xsl:include href="../style/style-title.xsl"/>
  <xsl:include href="../style/style-levelledPara.xsl"/>
  <xsl:include href="../style/style-table.xsl"/>
  <xsl:include href="../style/style-list.xsl"/>
  <xsl:include href="../style/style-icn.xsl"/>
  <xsl:include href="../style/style-warningcautionnote.xsl"/>

  <!-- 
    outstanding:
    1. belum melakukan compliance to S1000D v5.0 chap 6.2.2 page 5, table 3 (leading table footer end line to heading)
   -->

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

  <xsl:template name="numbered">
    <xsl:param name="parentName" select="name(parent::*)"/>
    <xsl:for-each select="..">
      <xsl:if test="name()=$parentName">
        <xsl:call-template name="checkParent"/>
        <xsl:number/>
        <xsl:text>&#160;&#160;&#160;</xsl:text>
      </xsl:if>
    </xsl:for-each>
  </xsl:template>  

</xsl:transform>
