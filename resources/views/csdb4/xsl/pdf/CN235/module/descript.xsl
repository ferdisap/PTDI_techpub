<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="content[name(child::*) = 'description']">
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="levelledPara">
    <xsl:param name="level">
      <xsl:text>s</xsl:text>
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::checkLevel', ., 1)"/>
    </xsl:param>
    <!-- <xsl:value-of select="php:function('dd', $level, string(.))"/> -->
    <fo:block>
      <xsl:call-template name="style-levelledPara">
        <xsl:with-param name="level" select="$level"/>
      </xsl:call-template>
      <xsl:apply-templates>
        <xsl:with-param name="level" select="$level"/>
      </xsl:apply-templates>
    </fo:block>
  </xsl:template>

  </xsl:transform>