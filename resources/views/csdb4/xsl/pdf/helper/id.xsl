<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <!-- xsl:number mencari posisi sesuai urutannya pada data module -->
  <!-- position() mencari posisi sesuai urutan hasil matched="" atau select="" -->

  <xsl:template name="add_id">
    <xsl:param name="id"/>
    <xsl:choose>
      <xsl:when test="@id">
        <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
      </xsl:when>
      <xsl:when test="$id">
        <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
      </xsl:when>
      <xsl:otherwise>
        <xsl:attribute name="id"><xsl:value-of select="php:function('rand')"/></xsl:attribute>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="add_applicability">
    <xsl:param name="id" select="@applicRefId"/>
    <xsl:if test="$id">
      <fo:block text-align="left">Applicable to: <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::getApplicability', //@id[. = $id])"/></fo:block>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>