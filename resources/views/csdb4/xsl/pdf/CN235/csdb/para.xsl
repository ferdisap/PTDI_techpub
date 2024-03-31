<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="reducedPara">
    <fo:block page-break-before="avoid">
      <xsl:call-template name="style-para"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>

  <xsl:template match="para">
    <xsl:param name="level"/>
    <xsl:call-template name="add_applicability"/>
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>
    <fo:block page-break-before="avoid">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="style-para">
        <xsl:with-param name="level" select="$level"/>
      </xsl:call-template>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>
  
  <xsl:template match="simplePara">
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>
    <fo:block page-break-before="avoid">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="style-para"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>
  
  <!-- supaya inline, not block -->
  <xsl:template match="para[parent::footnote]">
    <xsl:apply-templates/>
  </xsl:template>

  <!-- untuk merender anotasi danjuga footnote di bottom page atau di paragraphnya -->
  <xsl:template match="footnote[not(ancestor::table)]">
    <xsl:param name="position"><xsl:number level="any"/></xsl:param>
    <xsl:variable name="mark">
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\Helper::get_footnote_mark', number($position), string(@footnoteMark))"/>
    </xsl:variable>
    <fo:footnote font-size="8pt">
      <fo:inline baseline-shift="super">
        <fo:basic-link text-decoration="underline" color="blue">
          <xsl:call-template name="add_id">
            <xsl:with-param name="force">yes</xsl:with-param>
            <xsl:with-param name="attributeName">internal-destination</xsl:with-param>
          </xsl:call-template>
          <xsl:value-of select="$mark"/>
        </fo:basic-link>
      </fo:inline>
      <fo:footnote-body>
        <fo:block text-indent="-8pt" start-indent="8pt">
          <xsl:call-template name="add_id">
            <xsl:with-param name="force">yes</xsl:with-param>
          </xsl:call-template>
          <xsl:value-of select="$mark"/><xsl:text>&#160;&#160;</xsl:text>
          <xsl:apply-templates/>
        </fo:block>
      </fo:footnote-body>
    </fo:footnote>
  </xsl:template>

  <!-- untuk merender anotasi footnote di table cell -->
  <xsl:template match="footnote[ancestor::table]|__cgmark[child::footnote]">
    <xsl:param name="position"><xsl:number level="any"/></xsl:param>
    <xsl:variable name="mark">
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\Helper::get_footnote_mark', number($position), string(@footnoteMark))"/>
    </xsl:variable>
    <fo:basic-link internal-destination="{@id}">
      <fo:inline text-decoration="underline" color="blue" baseline-shift="super" font-size="8pt"><xsl:value-of select="$mark"/></fo:inline>
    </fo:basic-link>
  </xsl:template>

  <!-- dipanggil di fo:table-footer select="descendant::footnote"-->
  <xsl:template name="add_footnote">
    <xsl:param name="position"><xsl:number level="any"/></xsl:param>
    <xsl:variable name="mark">
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\Helper::get_footnote_mark', number($position), string(@footnoteMark))"/>
    </xsl:variable>
    <fo:block text-indent="-8pt" start-indent="8pt" id="{@id}">
      <fo:inline><xsl:value-of select="$mark"/><xsl:text>&#160;&#160;</xsl:text></fo:inline>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>
  
  <xsl:template match="notePara|warningAndCautionPara">
    <fo:block margin-top="11pt">
      <xsl:call-template name="style-warningcautionnotePara"/>
      <xsl:apply-templates/>     
    </fo:block>
  </xsl:template>

</xsl:transform>