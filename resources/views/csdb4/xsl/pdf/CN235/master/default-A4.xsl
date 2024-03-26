<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="header-odd-default-A4">
    <fo:block-container width="100%" height="1.5cm">
      <fo:block>
        <fo:inline-container inline-progression-dimension="24.9%">
          <fo:block></fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="50%" text-align="center">
          <xsl:call-template name="get_logo"/>
          <fo:block>
            <xsl:text>PM Entry Title</xsl:text>
          </fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="24.9%">
          <fo:block text-align="right">PM Code</fo:block>
        </fo:inline-container>
      </fo:block>
      <fo:block border-bottom="1pt solid black"></fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="header-even-default-A4">
    <fo:block-container width="100%" height="1.5cm">
      <fo:block>
        <fo:inline-container inline-progression-dimension="24.9%">
          <fo:block text-align="left">PM Code</fo:block>
        </fo:inline-container>
        
        <fo:inline-container inline-progression-dimension="50%" text-align="center">
          <xsl:call-template name="get_logo"/>
          <fo:block>
            <xsl:text>PM Entry Title</xsl:text>
          </fo:block>
        </fo:inline-container>
        
        <fo:inline-container inline-progression-dimension="24.9%">
          <fo:block></fo:block>
        </fo:inline-container>
      </fo:block>
      <fo:block border-bottom="1pt solid black"></fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="footer-odd-default-A4">
    <xsl:param name="id"/>
    <fo:block-container width="100%">
      <fo:block border-top="1pt solid black"></fo:block>
      <fo:block>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="left">Applicable to: <xsl:call-template name="getApplicabilityOnFooter"/></fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="right"><xsl:call-template name="getDMCodeOnFooter"/></fo:block>
        </fo:inline-container>
      </fo:block>
      <fo:block text-align="center">&#160;</fo:block>
      <fo:block text-align="center">
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block text-align="left">
            <xsl:call-template name="getDateOnFooter"/> Page <fo:page-number/>
            <!-- Date and Page <fo:page-number/> of <fo:page-number-citation-last ref-id="{$id}"/> -->
          </fo:block>
        </fo:inline-container>        
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block><xsl:apply-templates select="//identAndStatusSection/dmStatus/security"/></fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="29.9%">
          <fo:block></fo:block>
        </fo:inline-container>
      </fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="footer-even-default-A4">
    <xsl:param name="id"/>
    <fo:block-container width="100%">
      <fo:block border-top="1pt solid black"></fo:block>
      <fo:block>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="left"><xsl:call-template name="getDMCodeOnFooter"/></fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="right">Applicable to: <xsl:call-template name="getApplicabilityOnFooter"/></fo:block>
        </fo:inline-container>
      </fo:block>
      <fo:block text-align="center">&#160;</fo:block>
      <fo:block text-align="center">
        <fo:inline-container inline-progression-dimension="29.9%">
          <fo:block></fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block><xsl:apply-templates select="//identAndStatusSection/dmStatus/security"/></fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block text-align="right">
            <xsl:call-template name="getDateOnFooter"/> Page <fo:page-number/>
            <!-- Date and Page <fo:page-number/> of <fo:page-number-citation-last ref-id="{$id}"/> -->
          </fo:block>
        </fo:inline-container>
      </fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="getApplicabilityOnFooter">
    <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::getApplicability', //identAndStatusSection/dmStatus/applic)"/>
  </xsl:template>
  <xsl:template name="getDMCodeOnFooter">
    <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmCode', //identAndStatusSection/dmAddress/dmIdent/dmCode)"/>
  </xsl:template>
  <xsl:template name="getDateOnFooter">
    <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_issueDate', //identAndStatusSection/dmAddress/dmAddressItems/issueDate)"/>
  </xsl:template>
  
</xsl:transform>