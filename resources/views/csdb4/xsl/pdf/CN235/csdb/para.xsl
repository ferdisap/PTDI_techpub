<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="reducedPara">
    <fo:block>
      <xsl:call-template name="style-para"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>

  <xsl:template match="para">
    <xsl:call-template name="add_applicability"/>
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>
    <fo:block>
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="style-para"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>
  
  <xsl:template match="simplePara">
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>
    <fo:block>
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="style-para"/>
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>

  <xsl:template name="add_footnote">
    <xsl:param name="textOnly"/>
    <xsl:param name="mark"/>
    <xsl:variable name="position">
      <xsl:choose>
        <xsl:when test="$mark = 'sym'">
          <xsl:text>*</xsl:text>
        </xsl:when>
        <xsl:when test="$mark = 'alpha'">
          <xsl:text>&#945;</xsl:text>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::next_footnotePosition', $filename, boolean(1))"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <fo:block text-indent="-8pt" start-indent="8pt" id="{generate-id(.)}">
      <fo:inline><xsl:value-of select="$position"/><xsl:text>&#160;&#160;</xsl:text></fo:inline>
      <xsl:apply-templates>
        <xsl:with-param name="render">yes</xsl:with-param>
      </xsl:apply-templates>
    </fo:block>
  </xsl:template>

  <xsl:template match="para[parent::footnote]">
    <xsl:param name="render">no</xsl:param>
    <xsl:choose>
      <xsl:when test="$render = 'yes'">
        <xsl:apply-templates/>
      </xsl:when>
      <xsl:otherwise>
      <!-- tambahkan text seperti [1] atau [*] di paragraphnya jika ketemu footnote -->
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!-- <xsl:template match="footnote">
    <xsl:param name="pos">
      <xsl:if test="ancestor::table">
        
      </xsl:if>
    </xsl:param>
    <fo:inline vertical-align="sup" padding-left="-3pt" baseline-shift="8pt" font-size="8pt">
      i
    </fo:inline>
  </xsl:template> -->

  <xsl:template match="paras[parent::footnote/ancestor::table]">
    <xsl:param name="prefix"/>
    <xsl:param name="textOnly"/>
    <!-- <xsl:if test="$textOnly = 'yes'">
      <xsl:call-template name="add_applicability"/>
      <xsl:call-template name="add_controlAuthority"/>
      <xsl:call-template name="add_security"/>
      <fo:block text-indent="-8pt" start-indent="8pt" >
        <fo:inline><xsl:value-of select="$prefix"/></fo:inline>
        <xsl:apply-templates/>
      </fo:block>
    </xsl:if> -->

  </xsl:template>

</xsl:transform>