<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="getControlAuthority"> </xsl:template>

  <xsl:template name="add_controlAuthority">
    <xsl:param name="id" select="@controlAuthorityRefs" />
    <xsl:param name="prefix"><xsl:text>Controlled By: </xsl:text></xsl:param>

    <xsl:if test="$id">
      <xsl:variable name="dmRef" select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmIdent', controlAuthority[@id = string($id)]/dmRef, '', '')"/>
      <!-- <xsl:variable name="symbol" select="//controlAuthority[@id = string($id)]/symbol/@infoEntityIdent"/> -->

      <fo:block text-align="left" font-size="8pt">
        <xsl:value-of select="$prefix" />

        <xsl:if test="//controlAuthority[@id = string($id)]/symbol">
          <fo:inline-container>
            <xsl:for-each select="//controlAuthority[@id = string($id)]/symbol">
              <fo:external-graphic src="url('{unparsed-entity-uri(@infoEntityIdent)}')" content-width="scale-to-fit">
                <xsl:call-template name="setGraphicDimension"/>
              </fo:external-graphic>
            </xsl:for-each>
          </fo:inline-container>
        </xsl:if>

        <fo:inline-container>
          <xsl:apply-templates select="//controlAuthority[@id = string($id)]/controlAuthorityText"/>
        </fo:inline-container>
        
      </fo:block>
    </xsl:if>
  </xsl:template>

</xsl:transform>