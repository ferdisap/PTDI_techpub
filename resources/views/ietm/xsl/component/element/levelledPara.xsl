<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

  <xsl:include href="para.xsl"/>
  <xsl:include href="title.xsl"/>
  <xsl:include href="figure.xsl"/>
  <xsl:include href="table.xsl"/>

  <xsl:output method="xml" omit-xml-declaration="yes"/>

  <xsl:template match="levelledPara">
    <div>
    <!-- <div style="border:1px solid red"> -->
      <xsl:call-template name="id"/>
      <xsl:call-template name="cgmark"/>

      <xsl:variable name="numberedPar">
        <xsl:call-template name="checkParent"/>
        <xsl:number/>
      </xsl:variable>
      <xsl:variable name="level">
          <xsl:variable name="l" select="php:function('preg_replace', '/\w+/', '?', $numberedPar)"/>
          <xsl:variable name="s" select="php:function('preg_replace', '/\./', '', $l)"/>
          <xsl:value-of select="string-length($s)"/>
      </xsl:variable>
      
      <!-- set the padding left for different level levelledPara -->
      <!-- <xsl:attribute name="paddingleft">
        <xsl:choose>
          <xsl:when test="$level = '1'">
            <xsl:value-of select="$padding_levelPara_1"/>
          </xsl:when>
          <xsl:when test="$level = '2'">
            <xsl:value-of select="$padding_levelPara_2"/>
          </xsl:when>
          <xsl:when test="$level = '3'">
            <xsl:value-of select="$padding_levelPara_3"/>
          </xsl:when>
          <xsl:when test="$level = '4'">
            <xsl:value-of select="$padding_levelPara_4"/>
          </xsl:when>
          <xsl:when test="$level = '5'">
            <xsl:value-of select="$padding_levelPara_5"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="'0'"/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:attribute> -->

      <!-- <xsl:if test="$level = '1'"> -->
        <!-- <xsl:attribute name="addIntentionallyLeftBlank">true</xsl:attribute> -->
        <!-- <xsl:variable name="pos"><xsl:number/></xsl:variable> -->
        <!-- <xsl:value-of select="php:function('Ptdi\Mpub\Pdf2\DMC::set_lastnumberoflevelledpara1', 1)"/> -->
      <!-- </xsl:if> -->

      <xsl:apply-templates/>
      <!-- <xsl:apply-templates select="title"/> -->
    </div>
  </xsl:template>


</xsl:stylesheet>