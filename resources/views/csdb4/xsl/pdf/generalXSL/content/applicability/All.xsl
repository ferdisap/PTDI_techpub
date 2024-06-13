<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on"
  xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <xsl:template name="add_applicability">
    <xsl:param name="id" select="@applicRefId"/>
    <xsl:param name="prefix"><xsl:text>Applicable to: </xsl:text></xsl:param>
    <xsl:if test="$id">
      <fo:block text-align="left" font-size="8pt"><xsl:value-of select="$prefix"/><xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::getApplicability', //@id[. = $id])"/></fo:block>
    </xsl:if>
  </xsl:template>

  <xsl:template name="add_inline_applicability">
    <xsl:param name="id" select="@applicRefId"/>
    <xsl:param name="prefix"><xsl:text>Applicable to: </xsl:text></xsl:param>
    <xsl:if test="$id">
      <fo:inline text-align="left" font-size="8pt"><xsl:value-of select="$prefix"/><xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::getApplicability', //@id[. = $id])"/></fo:inline>
    </xsl:if>
  </xsl:template>
  
</xsl:transform>