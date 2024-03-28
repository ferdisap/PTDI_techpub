<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

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

  <xsl:template name="style-para">
    <!-- compliance to S1000D v5.0 chap 6.2.2 page 7, para 2.4 par1 (sidehead 4 atau sidehead 5 itu leading 11pt)-->
    <xsl:if test="(name(parent::*) = 'listItem' and following-sibling::para) or following-sibling::para">
      <xsl:attribute name="margin-bottom">11pt</xsl:attribute>
    </xsl:if>
    <!-- compliance to S1000D v5.0 chap 6.2.2 page 7, para 2.4 par3-->
    <xsl:if test="following-sibling::para">
      <xsl:attribute name="margin-bottom">20pt</xsl:attribute>
    </xsl:if>
  </xsl:template>
  
  <xsl:template name="style-title">
    <xsl:param name="level"/>
    <xsl:choose>
      <!-- compliance to S1000D v5.0 chap 6.2.2 page 8, para 2.6.1 par2-->
      <xsl:when test="parent::randomList or parent::sequentialList or parent::definitionList or parent::attentionSequentialList or parent::attentionRandomList">
        <xsl:attribute name="font-weight">bold</xsl:attribute>
        <xsl:attribute name="margin-bottom">4pt</xsl:attribute> <!-- leading 14pt setara 4pt karena fontsize 10pt -->
      </xsl:when>
      <!-- compliance to S1000D v5.0 chap 6.2.2 page 5, table 2 dan table 3 (leading text paragraph to the heading)  -->
      <xsl:when test="$level = 'c1'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">14pt</xsl:attribute>
          <xsl:attribute name="font-weight">bold</xsl:attribute>
          <xsl:attribute name="margin-bottom">17pt</xsl:attribute>
          <xsl:attribute name="text-align">center</xsl:attribute>
        </xsl:if>
      </xsl:when>
      <xsl:when test="$level = 'c2'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">14pt</xsl:attribute>
          <xsl:attribute name="font-weight">bold</xsl:attribute>
          <xsl:attribute name="font-style">italic</xsl:attribute>
          <xsl:attribute name="margin-bottom">17pt</xsl:attribute>
          <xsl:attribute name="text-align">center</xsl:attribute>
        </xsl:if>
      </xsl:when>
      <xsl:when test="$level = 's0'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">14pt</xsl:attribute>
          <xsl:attribute name="font-weight">bold</xsl:attribute>
          <xsl:attribute name="margin-bottom">17pt</xsl:attribute>
          <xsl:attribute name="text-align">left</xsl:attribute>
        </xsl:if>
      </xsl:when>
      <xsl:when test="$level = 's1'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">14pt</xsl:attribute>
          <xsl:attribute name="font-weight">bold</xsl:attribute>
          <xsl:attribute name="margin-bottom">15pt</xsl:attribute>
          <xsl:attribute name="text-align">left</xsl:attribute>
        </xsl:if>
        <xsl:call-template name="numbered"/>
      </xsl:when>
      <xsl:when test="$level = 's2'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">12pt</xsl:attribute>
          <xsl:attribute name="font-weight">bold</xsl:attribute>
          <xsl:attribute name="margin-bottom">12pt</xsl:attribute>
          <xsl:attribute name="text-align">left</xsl:attribute>
        </xsl:if>
        <xsl:call-template name="numbered"/>
      </xsl:when>
      <xsl:when test="$level = 's2'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">10pt</xsl:attribute>
          <xsl:attribute name="font-weight">bold</xsl:attribute>
          <xsl:attribute name="text-align">left</xsl:attribute>
          <xsl:attribute name="margin-bottom">12pt</xsl:attribute>
        </xsl:if>
        <xsl:call-template name="numbered"/>
      </xsl:when>
      <xsl:when test="$level = 's3'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">10pt</xsl:attribute>
          <xsl:attribute name="font-weight">bold</xsl:attribute>
          <xsl:attribute name="text-align">left</xsl:attribute>
          <xsl:attribute name="margin-bottom">11pt</xsl:attribute>
        </xsl:if>
        <xsl:call-template name="numbered"/>
      </xsl:when>
      <xsl:when test="$level = 's4'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">10pt</xsl:attribute>
          <xsl:attribute name="text-align">left</xsl:attribute>
          <xsl:attribute name="margin-bottom">11pt</xsl:attribute>
        </xsl:if>
        <xsl:call-template name="numbered"/>
      </xsl:when>
      <xsl:when test="$level = 's5'">
        <xsl:if test="following-sibling::para or following-sibling::figure or following-sibling::table or following-sibling::levelledPara">
          <xsl:attribute name="font-size">10pt</xsl:attribute>
          <xsl:attribute name="text-align">left</xsl:attribute>
          <xsl:attribute name="font-style">italic</xsl:attribute>
          <xsl:attribute name="margin-bottom">11pt</xsl:attribute>
        </xsl:if>
        <xsl:call-template name="numbered"/>
      </xsl:when>
      <xsl:otherwise>
        <!-- <xsl:value-of select="php:function('dd',string(.))"/> -->
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

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

  <xsl:template name="style-table">
    <xsl:param name="orient"/>
    <xsl:param name="level"/>
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
    <xsl:if test="following-sibling::para">
      <xsl:attribute name="margin-bottom">28pt</xsl:attribute>
    </xsl:if>
    <xsl:if test="following-sibling::title">
      <xsl:attribute name="margin-bottom">32pt</xsl:attribute>
    </xsl:if>
    <xsl:if test="parent::*/following-sibling::levelledPara/title">
      <xsl:if test="$level = 's0' or $level = 's1'">
        <xsl:attribute name="margin-bottom">35pt</xsl:attribute>
      </xsl:if>
      <xsl:if test="$level = 's2'">
        <xsl:attribute name="margin-bottom">34pt</xsl:attribute>
      </xsl:if>
      <xsl:if test="$level = 's3' or $level = 's4'">
        <xsl:attribute name="margin-bottom">32pt</xsl:attribute>
      </xsl:if>
      <xsl:if test="$level = 's5'">
        <xsl:attribute name="margin-bottom">28pt</xsl:attribute>
      </xsl:if>
    </xsl:if>
    <!-- <xsl:variable name="pos"><xsl:number/></xsl:variable> -->
    <!-- <xsl:variable name="total"><xsl:value-of select="count(parent::*/child::*)"/></xsl:variable> -->
    <!-- <xsl:value-of select="php:function('dd',$pos, position(), number($total))"/> -->
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
