<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:output method="xml" omit-xml-declaration="yes"/>

<xsl:template match="condCrossRefTable">
  <hr/>
  <h1>CONDITION CROSS REFERENCE TABLE</h1>
  <h1 style="text-align:center"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', //identAndStatusSection/descendant::dmTitle)"/></h1>
  <div>
    <xsl:apply-templates select="condTypeList"/>
  </div>
</xsl:template>

<xsl:template match="condTypeList">
  <div class="condTypeList">
    <xsl:apply-templates/>
  </div>
</xsl:template>

<xsl:template match="condType">
  <div class="condType">
    <div>ID: <xsl:value-of select="@id"/></div>
    <div>Name: <xsl:value-of select="name"/></div>
    <div>Description: <xsl:value-of select="escr"/></div>
    <div>Enumeration: <xsl:value-of select="enumeration/@applicPropertyValues"/>, <xsl:value-of select="enumeration/@enumerationLabel"/>, <xsl:value-of select="enumeration/@aliasFlag"/></div>
    <div>Value Data Type: <xsl:value-of select="@valueDataType"/></div>
    <div>Value Pattern: <xsl:value-of select="@valuePattern"/></div>
  </div>
  <br/>
</xsl:template>
</xsl:transform>