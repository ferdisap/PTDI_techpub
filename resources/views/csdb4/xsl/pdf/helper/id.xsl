<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <!-- xsl:number mencari posisi sesuai urutannya pada data module -->
  <!-- position() mencari posisi sesuai urutan hasil matched="" atau select="" -->

  <xsl:template name="add_id">
    <xsl:param name="attributeName">id</xsl:param>
    <xsl:param name="id"/>
    <xsl:param name="force"/>
    <xsl:choose>
      <xsl:when test="string(@id) != ''">
        <xsl:attribute name="{$attributeName}"><xsl:value-of select="@id"/></xsl:attribute>
      </xsl:when>
      <xsl:when test="$id">
        <xsl:attribute name="{$attributeName}"><xsl:value-of select="$id"/></xsl:attribute>
      </xsl:when>
      <xsl:when test="$force = 'yes'">
        <xsl:if test="string(@id) = ''">
          <xsl:attribute name="{$attributeName}"><xsl:value-of select="generate-id(.)"/></xsl:attribute>
        </xsl:if>
      </xsl:when>
    </xsl:choose>
  </xsl:template>

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

</xsl:stylesheet>