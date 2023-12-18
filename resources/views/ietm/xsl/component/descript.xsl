<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:output method="xml" omit-xml-declaration="yes"/>

<xsl:param name="dmOwner"/>

<xsl:template match="description">
  <div class="descript">
    <h1><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', //identAndStatusSection/descendant::dmTitle)"/></h1>
    <xsl:apply-templates select="levelledPara"/>
  </div>
</xsl:template>

</xsl:transform>