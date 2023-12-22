<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

  <xsl:output method="xml" omit-xml-declaration="yes" />

  <!-- <xsl:param name="absolute_path_csdbInput"></xsl:param> -->

  <xsl:template match="figure">
    <xsl:variable name="current" select="."/>
    <xsl:variable name="figTitle">
      <xsl:value-of select="title"/>
    </xsl:variable>
    <xsl:variable name="qtyGra">
      <xsl:value-of select="count((graphic))"/>
    </xsl:variable>
    <xsl:variable name="index">
      <xsl:for-each select="//figure">
        <xsl:if test=". = $current">
          <xsl:value-of select="position()"/>
        </xsl:if>
      </xsl:for-each>
    </xsl:variable>

    <div style="text-align:center;page-break-inside: avoid;">
      <xsl:call-template name="id"/>
      <xsl:call-template name="cgmark"/>

      <xsl:for-each select="graphic">
        <xsl:variable name="graIndex"><xsl:number/></xsl:variable>
        <xsl:variable name="infoEntityIdent">
          <xsl:text>/route/get_transform_csdb/?filename=</xsl:text>
          <xsl:value-of select="@infoEntityIdent"/>
        </xsl:variable>
        
        <table style="text-align:center;">
          <tr>
            <td>
              <div class="flex justify-center">
                <img src="{$infoEntityIdent}">
                  <xsl:attribute name="onclick">
                    ietm.clickImg('<xsl:value-of select="$infoEntityIdent"/>')
                  </xsl:attribute>
                  <xsl:call-template name="cgmark"/>
                  <xsl:if test="@reproductionWidth">
                    <xsl:attribute name="width"><xsl:value-of select="@reproductionWidth"/></xsl:attribute>
                  </xsl:if>
                  <xsl:if test="@reproductionHeight">
                    <xsl:attribute name="height"><xsl:value-of select="@reproductionHeight"/></xsl:attribute>
                  </xsl:if>
                </img>
              </div>
            </td>
          </tr>
          <tr>
            <td style="text-align:right">
              <span style="font-size:6" paddingleft="5">
                <xsl:value-of select="php:function('preg_replace', '/.[\w]+$/', '', string(@infoEntityIdent))"/>
              </span>
            </td>
          </tr>
          <tr>
            <td>
              <span>
                <xsl:if test="parent::figure/title/@changeMark = '1'">
                  <xsl:call-template name="cgmark">
                    <xsl:with-param name="changeMark" select="parent::figure/title/@changeMark"/>
                    <xsl:with-param name="changeType" select="parent::figure/title/@changeType"/>
                    <xsl:with-param name="reasonForUpdateRefIds" select="parent::figure/title/@reasonForUpdateRefIds"/>
                  </xsl:call-template>
                </xsl:if>
                <xsl:text>Fig.&#160;</xsl:text>
                <xsl:value-of select="$index"/>&#160;<xsl:value-of select="$figTitle"/>
              </span>
              <xsl:if test="$qtyGra > 1">
                <span>
                  <xsl:text>&#160;&#40;sheet&#160;</xsl:text>
                  <xsl:value-of select="$graIndex"/>
                  <xsl:text>&#160;of&#160;</xsl:text>
                  <xsl:value-of select="$qtyGra"/>
                  <xsl:text>&#41;</xsl:text>
                </span>          
              </xsl:if>
            </td>
          </tr>
        </table>
      </xsl:for-each>

    </div>
    <br/>
  </xsl:template>
</xsl:stylesheet>