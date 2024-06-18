<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <xsl:template name="add_dmTitle">
    <fo:block font-size="16pt" text-align="center" font-weight="bold" margin-bottom="12pt"
      margin-top="6pt" start-indent="-{$stIndent}">
      <xsl:for-each select="//dmTitle">
        <xsl:value-of select="techName" />
            <xsl:if test="infoName">
          <xsl:text> - </xsl:text>
              <xsl:value-of select="infoName" />
        </xsl:if>
      </xsl:for-each>
    </fo:block>
  </xsl:template>
</xsl:transform>