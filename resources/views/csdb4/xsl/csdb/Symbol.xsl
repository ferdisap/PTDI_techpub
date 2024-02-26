<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" omit-xml-declaration="yes"/>

  <xsl:template match="symbol">
    <xsl:variable name="infoEntityIdent">
      <xsl:text>/route/api.get_transform_csdb/?filename=</xsl:text>
      <xsl:value-of select="@infoEntityIdent"/>
      <xsl:text>&#38;project_name=</xsl:text>
      <xsl:value-of select="$repoName"/>
    </xsl:variable>
    <img src="{$infoEntityIdent}">
      <xsl:call-template name="cgmark"/>
      <xsl:if test="@reproductionWidth">
        <xsl:attribute name="width"><xsl:value-of select="@reproductionWidth"/></xsl:attribute>
      </xsl:if>
      <xsl:if test="@reproductionHeight">
        <xsl:attribute name="height"><xsl:value-of select="@reproductionHeight"/></xsl:attribute>
      </xsl:if>
    </img>
  </xsl:template>
  
</xsl:stylesheet>