<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="add_dmTitle">
    <xsl:param name="idParentBookmark"/>
    <xsl:param name="idBookmark" select="generate-id(.)"/>

    <xsl:variable name="chapter">
      <xsl:call-template name="get_chapter"/>
    </xsl:variable>

    <xsl:variable name="title">
      <xsl:value-of select="//dmAddressItems/dmTitle/techName" />
      <xsl:if test="//dmAddressItems/dmTitle/infoName">
        <xsl:text> - </xsl:text>
        <xsl:value-of select="//dmAddressItems/dmTitle/infoName" />
      </xsl:if>
    </xsl:variable>

    <fo:block font-size="16pt" text-align="center" font-weight="bold" margin-bottom="12pt" margin-top="6pt">
      <xsl:attribute name="start-indent">
        <xsl:text>-</xsl:text>
        <xsl:call-template name="get_stIndent">
          <xsl:with-param name="masterName" select="php:function('Ptdi\Mpub\Main\CSDBStatic::get_PDF_MasterName')"/>
        </xsl:call-template>
        <xsl:call-template name="get_layout_unit_length">
          <xsl:with-param name="masterName" select="php:function('Ptdi\Mpub\Main\CSDBStatic::get_PDF_MasterName')"/>
        </xsl:call-template>
      </xsl:attribute>
      
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::fillBookmark', string($idBookmark), concat($chapter, ' ', $title), string($idParentBookmark) )"/>

      <xsl:if test="$chapter">
        <fo:block margin-bottom="6pt">
          <xsl:value-of select="$chapter"/>
        </fo:block>
      </xsl:if>
      <fo:block>
        <xsl:value-of select="$title"/>
      </fo:block>
    </fo:block>
  </xsl:template>

  <xsl:template name="get_chapter">
    <xsl:variable name="systemCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@systemCode)"/>
    </xsl:variable>
    <xsl:variable name="subSystemCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@subSystemCode)"/>
    </xsl:variable>
    <xsl:variable name="subSubSystemCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@subSubSystemCode)"/>
    </xsl:variable>
    <xsl:variable name="assyCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@assyCode)"/>
    </xsl:variable>
    <xsl:variable name="disassyCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@disassyCode)"/>
    </xsl:variable>

    <xsl:variable name="combined">
      <xsl:value-of select="$systemCode"/>
      <xsl:text>.</xsl:text>
      <xsl:value-of select="number(concat($subSystemCode, $subSubSystemCode))"/>
      <xsl:text>.</xsl:text>
      <xsl:value-of select="$assyCode"/>
      <xsl:text>.</xsl:text>
      <xsl:value-of select="$disassyCode"/>
    </xsl:variable>

    <xsl:variable name="final_combined" select="php:function('preg_replace', '/0?(.0)+$/', '', $combined)"/>

    <xsl:if test="$final_combined">
      <xsl:text>Chapter </xsl:text>
      <xsl:value-of select="$final_combined"/>
    </xsl:if>
  </xsl:template>
  
  <!-- <xsl:template name="add_chapter">
    <xsl:variable name="systemCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@systemCode)"/>
    </xsl:variable>
    <xsl:variable name="subSystemCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@subSystemCode)"/>
    </xsl:variable>
    <xsl:variable name="subSubSystemCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@subSubSystemCode)"/>
    </xsl:variable>
    <xsl:variable name="assyCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@assyCode)"/>
    </xsl:variable>
    <xsl:variable name="disassyCode">
      <xsl:value-of select="number(//dmAddress/dmIdent/dmCode/@disassyCode)"/>
    </xsl:variable>

    <xsl:variable name="combined">
      <xsl:value-of select="$systemCode"/>
      <xsl:text>.</xsl:text>
      <xsl:value-of select="number(concat($subSystemCode, $subSubSystemCode))"/>
      <xsl:text>.</xsl:text>
      <xsl:value-of select="$assyCode"/>
      <xsl:text>.</xsl:text>
      <xsl:value-of select="$disassyCode"/>
    </xsl:variable>

    <xsl:variable name="final_combined" select="php:function('preg_replace', '/0?(.0)+$/', '', $combined)"/>

    <xsl:if test="$final_combined">
      <fo:block margin-bottom="6pt" >
        <xsl:text>Chapter </xsl:text>
        <xsl:value-of select="$final_combined"/>
      </fo:block>
    </xsl:if>
  </xsl:template> -->
</xsl:transform>