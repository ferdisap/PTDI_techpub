<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:output method="xml" omit-xml-declaration="yes"/>

<xsl:template match="productCrossRefTable">
  <hr/>
  <h1>PRODUCT CROSS REFERENCE TABLE</h1>
  <h1 style="text-align:center"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', //identAndStatusSection/descendant::dmTitle)"/></h1>
  <div>
    <xsl:apply-templates select="product"/>
  </div>
</xsl:template>

<xsl:template match="product">
  <div class="product">
    <xsl:apply-templates/>
  </div>
  <br/>
</xsl:template>

<xsl:template match="assign">
  <div>
    <xsl:number/>
    <xsl:text>.  </xsl:text>
    <xsl:value-of select="@applicPropertyType"/>
    <xsl:text>, </xsl:text>
    <xsl:value-of select="@applicPropertyIdent"/>
    <xsl:text>, </xsl:text>
    <xsl:value-of select="@applicPropertyValue"/>
    <xsl:text>, </xsl:text>
  </div>
</xsl:template>

</xsl:transform>