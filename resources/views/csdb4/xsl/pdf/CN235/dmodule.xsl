<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <!-- 
    Outstanding
    1. <foldout>, <multimedia>, <....alts> belum difungsikan/dicoba
   -->

  <!-- 
    param 'id' diperlukan jika ingin pakai page 1 of <total>
   -->
  <xsl:template match="dmodule">
    <xsl:param name="masterReference"/>
    <xsl:variable name="id">
      <xsl:value-of select="php:function('rand')"/>
    </xsl:variable>
    
    <fo:page-sequence master-reference="{$masterReference}" initial-page-number="auto-odd" force-page-count="even">
      <xsl:call-template name="getRegion">
        <xsl:with-param name="masterReference" select="$masterReference"/>
        <xsl:with-param name="id" select="$id"/>
      </xsl:call-template>
      <fo:flow flow-name="body">
        <xsl:call-template name="body"/>
      </fo:flow>
    </fo:page-sequence>
  </xsl:template>
  
  <xsl:template match="content">
    <xsl:variable name="dmIdent" select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmIdent', //identAndStatusSection/dmAddress/dmIdent, '', '')"/>
    <fo:block-container id="{$dmIdent}" start-indent="{$stIndent}">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="add_controlAuthority"/>
      <xsl:apply-templates select="crew|description|commonRepository"/>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="getRegion">
      <xsl:param name="masterReference"/>
      <xsl:param name="id"/>
      <fo:static-content flow-name="header-odd">
        <xsl:call-template name="header">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'odd'"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="header-even">
        <xsl:call-template name="header">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="footer-odd">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'odd'"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="footer-even">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="header-left_blank">
        <xsl:call-template name="header">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
        </xsl:call-template>
        <fo:block-container position="fixed" top="14cm" left="7cm">
          <fo:block>Intentionally left blank</fo:block>
        </fo:block-container>
      </fo:static-content>
      <fo:static-content flow-name="footer-left_blank">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="xsl-footnote-separator">
        <fo:block>---------------</fo:block>
      </fo:static-content>
  </xsl:template>
  
</xsl:transform>