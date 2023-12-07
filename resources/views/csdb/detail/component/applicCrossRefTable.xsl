<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:output method="xml" omit-xml-declaration="yes"/>

<xsl:template match="applicCrossRefTable">
  <hr/>
  <h1>APPLICABILITY CROSS REFERENCE TABLE</h1>
  <h1 style="text-align:center"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', //identAndStatusSection/descendant::dmTitle)"/></h1>
  <div>
    <xsl:apply-templates select="productAttributeList"/>
    <xsl:apply-templates select="condCrossRefTableRef"/>
    <xsl:apply-templates select="productCrossRefTableRef"/>
  </div>
</xsl:template>

<xsl:template match="productAttributeList">
  <div class="productAttributeList">
    <xsl:apply-templates/>
  </div>
</xsl:template>

<xsl:template match="productAttribute">
  <div class="productAttribute">
    <div>ID: <xsl:value-of select="@id"/></div>
    <div>Value Data Type: <xsl:value-of select="@valueDataType"/></div>
    <div>Value Pattern: <xsl:value-of select="@valuePattern"/></div>
    <div>Product Identifier: <xsl:value-of select="@productIdentifier"/></div>
    <div>Alias Flag: <xsl:value-of select="@aliasFlag"/></div>
    <div>Control Authority Ref ID: <xsl:value-of select="@controlAuthorityRefs"/></div>
    <div>Name: <xsl:value-of select="name"/></div>
    <div>Display Name: <xsl:value-of select="displayName"/></div>
    <div>Enumeration: <xsl:value-of select="enumeration/@applicPropertyValues"/>, <xsl:value-of select="enumeration/@enumerationLabel"/>, <xsl:value-of select="enumeration/@aliasFlag"/></div>
  </div>
  <br/>
</xsl:template>

<xsl:template match="condCrossRefTableRef">
  <div>
    <p>Condition Cross Reference Table Ref</p>
    Filename: <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', descendant::dmRefIdent)"/>
  </div>
  <br/>
</xsl:template>

<xsl:template match="productCrossRefTableRef">
  <div>
    <p>Product Cross Reference Table Ref</p>
    Filename: <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', descendant::dmRefIdent)"/>
  </div>
  <br/>
</xsl:template>

</xsl:transform>