<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="getControlAuthority"> </xsl:template>

  <!-- 
    Outstanding:
    1. <dmRef> dan <controlAuthorityText> saat ini tidak digunakan karena untuk output pdf memang tidak diperlukan, beda jika ietm harus diprint pada sebuah identAndStatus file
   -->

  <xsl:template name="add_controlAuthority">
    <xsl:param name="id" select="@controlAuthorityRefs"/>
    <xsl:if test="$id">
      <fo:block font-weight="bold" text-align="left" font-size="8pt">
        <xsl:choose>
          <xsl:when test="//controlAuthority[@id = string($id)]/symbol">
            <xsl:apply-templates select="symbol"/>                          
          </xsl:when>
          <xsl:otherwise>
            <fo:external-graphic src="url('{$controlAutoritySymbolPath}')" content-width="scale-to-fit" max-width="100%"/>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:text> </xsl:text>
        <xsl:choose>
          <xsl:when test="not(@controlAuthorityValue)">
            <xsl:apply-templates select="$ConfigXML//controlAuthority[string(@type) = string(//controlAuthority[string(@id) = string($id)]/@controlAuthorityType)]"/>
          </xsl:when>
          <xsl:otherwise><xsl:value-of select="string(@controlAuthorityValue)"/></xsl:otherwise>
        </xsl:choose>
        <xsl:text> </xsl:text>
      </fo:block>
    </xsl:if>
  </xsl:template>

  <xsl:template name="add_inline_controlAuthority">
    <xsl:param name="id" select="@controlAuthorityRefs"/>
    <xsl:if test="$id">
      <fo:inline font-weight="bold" font-size="8pt">
        <xsl:choose>
          <xsl:when test="//controlAuthority[@id = string($id)]/symbol">
            <xsl:apply-templates select="symbol"/>                          
          </xsl:when>
          <xsl:otherwise>
            <fo:external-graphic src="url('{$controlAutoritySymbolPath}')" content-width="scale-to-fit" max-width="100%"/>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:text> </xsl:text>
        <xsl:choose>
          <xsl:when test="not(@controlAuthorityValue)">
            <xsl:apply-templates select="$ConfigXML//controlAuthority[string(@type) = string(//controlAuthority[string(@id) = string($id)]/@controlAuthorityType)]"/>
          </xsl:when>
          <xsl:otherwise><xsl:value-of select="string(@controlAuthorityValue)"/></xsl:otherwise>
        </xsl:choose>
        <xsl:text> </xsl:text>
      </fo:inline>
    </xsl:if>
  </xsl:template>
  
</xsl:transform>