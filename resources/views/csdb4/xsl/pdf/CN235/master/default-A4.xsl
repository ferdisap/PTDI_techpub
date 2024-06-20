<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template name="pageMasterByDefaultA4">
    <fo:layout-master-set>
      <!-- kalau tidak ditulis extent, bisa muncul warning di terminal, fo:region-before on
      page 1 exceed the available area in the block-progression direction by 42519
      millipoints. (See position 1:677) -->
      <!-- <fo:simple-page-master master-name="default-A4" page-height="29.7cm" page-width="21cm" -->
      <!-- <fo:simple-page-master master-name="tes" page-height="29.7cm" page-width="21cm" -->
        <!-- margin-top="1cm" margin-bottom="1cm" margin-left="2cm" margin-right="2cm"> -->
        <!-- <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.0cm" /> -->
        <!-- <fo:region-before region-name="header" extent="1.5cm" /> -->
        <!-- <fo:region-after region-name="footer" extent="2.0cm" /> -->
      <!-- </fo:simple-page-master> -->
  
      <fo:simple-page-master master-name="odd" page-height="{$height}" page-width="{$width}" margin-top="1cm" margin-bottom="1cm" margin-left="3cm" margin-right="1.5cm">
        <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.5cm"/>
        <fo:region-before region-name="header-odd" extent="1.5cm" />
        <fo:region-after region-name="footer-odd" extent="2.0cm" />
      </fo:simple-page-master>
      <fo:simple-page-master master-name="even" page-height="{$height}" page-width="{$width}" margin-top="1cm" margin-bottom="1cm" margin-left="1.5cm" margin-right="3cm">
        <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.5cm"/>
        <fo:region-before region-name="header-even" extent="1.5cm" />
        <fo:region-after region-name="footer-even" extent="2.0cm" />
      </fo:simple-page-master>
      <fo:simple-page-master master-name="left-blank" page-height="{$height}" page-width="{$width}" margin-top="1cm" margin-bottom="1cm" margin-left="1.5cm" margin-right="3cm">            
        <fo:region-body region-name="left_blank" margin-top="1.5cm" margin-bottom="2.0cm"/>
        <fo:region-before region-name="header-left_blank" extent="1.5cm" />
        <fo:region-after region-name="footer-left_blank" extent="2.0cm" />
      </fo:simple-page-master>
  
      <fo:page-sequence-master master-name="default-A4">
        <fo:repeatable-page-master-alternatives>
          <!-- untuk page between first and last -->
          <fo:conditional-page-master-reference master-reference="odd" page-position="rest" odd-or-even="odd"/>
          <fo:conditional-page-master-reference master-reference="even" page-position="rest" odd-or-even= "even"/>
  
          <!-- for the first page -->
          <fo:conditional-page-master-reference master-reference="odd" page-position="first" odd-or-even="odd"/>
  
          <!-- 
            for the end page 1. last, even, and blank akan mencetak intentionally left blank
            kalau 2. last, even, and not blank tidak akan mencetak intentionally left blank
           -->
          <fo:conditional-page-master-reference master-reference="left-blank" page-position="last" odd-or-even="even" blank-or-not-blank="blank"/>
          <fo:conditional-page-master-reference master-reference="even" page-position="last" odd-or-even="even"/>
  
        </fo:repeatable-page-master-alternatives>
      </fo:page-sequence-master>          
    </fo:layout-master-set>
  </xsl:template>

  <xsl:template name="header-odd-default-A4">
    <xsl:param name="entry"/>
    <fo:block-container width="100%" height="1.5cm">
      <fo:block>
        <!-- <fo:inline-container inline-progression-dimension="14.9%">
          <fo:block></fo:block>
        </fo:inline-container> -->
        <fo:inline-container inline-progression-dimension="64.9%" text-align="left">
          <xsl:call-template name="get_logo">
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
          <fo:block>
            <xsl:call-template name="getPmEntryTitle"/>
          </fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block text-align="right">
            <xsl:call-template name="getPMCode"/>
          </fo:block>
        </fo:inline-container>
      </fo:block>
      <fo:block border-bottom="1pt solid black"></fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="header-even-default-A4">
    <xsl:param name="entry"/>
    <fo:block-container width="100%" height="1.5cm">
      <fo:block>
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block text-align="left">
            <xsl:call-template name="getPMCode"/>
          </fo:block>
        </fo:inline-container>
        
        <fo:inline-container inline-progression-dimension="64.9%" text-align="right">
          <xsl:call-template name="get_logo">
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
          <fo:block>
            <xsl:call-template name="getPmEntryTitle"/>
          </fo:block>
        </fo:inline-container>
        <!-- <fo:inline-container inline-progression-dimension="24.9%">
          <fo:block></fo:block>
        </fo:inline-container> -->
      </fo:block>
      <fo:block border-bottom="1pt solid black"></fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="footer-odd-default-A4">
    <xsl:param name="id"/>
    <xsl:param name="entry"/>
    <fo:block-container width="100%">
      <fo:block border-top="1pt solid black"></fo:block>
      <fo:block>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="left">
            <xsl:text>Applicable to: </xsl:text>
            <xsl:call-template name="getApplicabilityOnFooter">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="right">
            <xsl:call-template name="getDMCode">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </fo:block>
        </fo:inline-container>
      </fo:block>
      <fo:block text-align="center">&#160;</fo:block>
      <fo:block text-align="center">
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block text-align="left">
            <xsl:call-template name="getDate">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
            <xsl:text> Page </xsl:text>
            <fo:page-number/>
            <!-- Date and Page <fo:page-number/> of <fo:page-number-citation-last ref-id="{$id}"/> -->
          </fo:block>
        </fo:inline-container>        
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block>
            <xsl:call-template name="getSecurity">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="29.9%">
          <fo:block></fo:block>
        </fo:inline-container>
      </fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="footer-even-default-A4">
    <xsl:param name="id"/>
    <xsl:param name="entry"/>
    <fo:block-container width="100%">
      <fo:block border-top="1pt solid black"></fo:block>
      <fo:block>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="left">
            <xsl:call-template name="getDMCode">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="49.9%">
          <fo:block text-align="right">
            <xsl:text>Applicable to: </xsl:text>
            <xsl:call-template name="getApplicabilityOnFooter">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </fo:block>
        </fo:inline-container>
      </fo:block>
      <fo:block text-align="center">&#160;</fo:block>
      <fo:block text-align="center">
        <fo:inline-container inline-progression-dimension="29.9%">
          <fo:block></fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block>
            <xsl:call-template name="getSecurity">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </fo:block>
        </fo:inline-container>
        <fo:inline-container inline-progression-dimension="34.9%">
          <fo:block text-align="right">
            <xsl:call-template name="getDate">
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
            <xsl:text> Page </xsl:text>
            <fo:page-number/>
            <!-- Date and Page <fo:page-number/> of <fo:page-number-citation-last ref-id="{$id}"/> -->
          </fo:block>
        </fo:inline-container>
      </fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="getPmEntryTitle">
    <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::get_pmEntryTitle')"/>
  </xsl:template>

  <!-- ada lagi template get_applicability, tapi dugnakan untuk content, bukan header/footer -->
  <xsl:template name="getApplicabilityOnFooter">
    <xsl:param name="entry"/>
    <xsl:choose>
      <xsl:when test="$entry">
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::getApplicability', $entry//identAndStatusSection/dmStatus/applic)"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::getApplicability', //identAndStatusSection/dmStatus/applic)"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template name="getDMCode">
    <xsl:param name="entry"/>
    <xsl:choose>
      <xsl:when test="$entry">
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmCode', $entry//identAndStatusSection/dmAddress/dmIdent/dmCode)"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmCode', //identAndStatusSection/dmAddress/dmIdent/dmCode)"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="getPMCode">
    <xsl:param name="pm"/>
    <xsl:choose>
      <xsl:when test="$pm">
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_pmCode', $entry//identAndStatusSection/pmAddress/pmIdent/pmCode)"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_pmCode', //identAndStatusSection/pmAddress/pmIdent/pmCode)"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="getDate">
    <xsl:param name="entry"/>
    <xsl:choose>
      <xsl:when test="$entry">
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_issueDate', $entry//identAndStatusSection/dmAddress/dmAddressItems/issueDate)"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_issueDate', //identAndStatusSection/dmAddress/dmAddressItems/issueDate)"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="getSecurity">
    <xsl:param name="entry"/>
    <xsl:choose>
      <xsl:when test="$entry">
        <xsl:apply-templates select="$entry//identAndStatusSection/dmStatus/security"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:apply-templates select="//identAndStatusSection/dmStatus/security"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="get_logo">
    <xsl:param name="entry"/>
    <xsl:variable name="infoEntityPath">
      <xsl:text>url('</xsl:text>
      <!-- <xsl:text>file:///D:/Temporary/tesimage.png</xsl:text> -->
      <!-- <xsl:text>file:\\\D:\Temporary\tesimage.png</xsl:text> -->
      <!-- <xsl:text>file:\\\D:\Temporary/tesimage.png</xsl:text> -->
      <xsl:choose>
        <xsl:when test="$entry">
          <xsl:value-of select="unparsed-entity-uri($entry//dmStatus/logo/symbol/@infoEntityIdent)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="unparsed-entity-uri(//dmStatus/logo/symbol/@infoEntityIdent)"/>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:text>')</xsl:text>
    </xsl:variable>
    
    <fo:block>
      <fo:external-graphic src="{$infoEntityPath}" content-width="scale-to-fit" width="2.5cm"/>
    </fo:block>
  </xsl:template>
  
</xsl:transform>