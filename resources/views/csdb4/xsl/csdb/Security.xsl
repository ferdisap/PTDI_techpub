<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:template match="security">
    <div class="security">
      <span class="securityClassification">
        <xsl:apply-templates select="@securityClassification"/>
      </span>
    </div>   
  </xsl:template>

  <xsl:template match="@securityClassification[parent::security]">
    <xsl:variable name="number">
      <xsl:value-of select="."/>      
    </xsl:variable>
    <xsl:choose>
      <xsl:when test="$number = '05'">Top Secret</xsl:when>
      <xsl:when test="$number = '04'">Secret</xsl:when>
      <xsl:when test="$number = '03'">Confidential</xsl:when>
      <xsl:when test="$number = '02'">Restricted</xsl:when>
      <xsl:when test="$number = '01'">Unclassified</xsl:when>
    </xsl:choose>
  </xsl:template>

</xsl:transform>