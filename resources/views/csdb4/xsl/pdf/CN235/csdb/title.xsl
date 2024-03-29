<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <xsl:template match="title[parent::levelledPara]">
    <xsl:param name="level">
      <xsl:text>s</xsl:text>
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::checkLevel', parent::levelledPara, 1)"/>
    </xsl:param>

    <fo:block page-break-inside="avoid" page-break-after="avoid">
      <xsl:call-template name="style-title">
        <xsl:with-param name="level" select="$level"/>
      </xsl:call-template>

      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>

  <xsl:template match="title">
    <xsl:param name="prefix"/>
    <fo:block page-break-inside="avoid" page-break-after="avoid">
      <xsl:call-template name="style-title"/>
      <xsl:value-of select="$prefix"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>

  <xsl:template match="title[parent::table]">
    <xsl:apply-templates/>
  </xsl:template>


</xsl:stylesheet>