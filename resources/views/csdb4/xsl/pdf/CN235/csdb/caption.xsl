<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <!-- 
    Outstanding:
    1. captionGroup@tableOfContentType belum difungsikan karena belum berencana bikin TOC untuk caption
   -->

  <xsl:template match="captionGroup">
    <xsl:call-template name="add_applicability"/>
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>

    <fo:block-container width="100%" page-break-before="avoid">
      <xsl:call-template name="add_id"/>
      
      <xsl:apply-templates select="captionBody|__cgmark"/>
    </fo:block-container>
  </xsl:template>

  <xsl:template match="captionBody">
    <xsl:param name="colsep">
      <xsl:choose>
        <xsl:when test="@colsep">
          <xsl:value-of select="string(@colsep)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="string(parent::captionGroup/@colsep)"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:param>
    <xsl:param name="rowsep">
      <xsl:choose>
        <xsl:when test="@rowsep">
          <xsl:value-of select="string(@rowsep)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="string(parent::captionGroup/@rowsep)"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:param>
    <!-- <xsl:param name="alignCaption">
      <xsl:choose>
        <xsl:when test="parent::captionGroup/@rowsep">
          <xsl:value-of select="string(parent::captionGroup/@alignCaption)"/>
        </xsl:when>
        <xsl:otherwise>left</xsl:otherwise>
      </xsl:choose>
    </xsl:param> -->
    <!-- <xsl:param name="valign">
      <xsl:choose>
        <xsl:when test="@valign">
          <xsl:value-of select="string(@valign)"/>
        </xsl:when>
        <xsl:otherwise>top</xsl:otherwise>
      </xsl:choose>
    </xsl:param> -->

    <xsl:call-template name="add_applicability"/>
    <xsl:call-template name="add_controlAuthority"/>
    <fo:table table-omit-footer-at-break="true" page-break-before="avoid">
      <xsl:for-each select="parent::captionGroup/colspec[@colnum and @colwidth]">
        <xsl:variable name="width" select="php:function('Ptdi\Mpub\Main\CSDBStatic::interpretDimension', string(@colwidth))"/>
        <fo:table-column column-number="{@colnum}" column-width="{$width}"/>
      </xsl:for-each>

      <fo:table-body>
        <xsl:apply-templates/>
      </fo:table-body>
    </fo:table>
  </xsl:template>

  <xsl:template match="captionRow">
    <xsl:if test="@applicRefId">
      <fo:table-row keep-together="always">
        <fo:table-cell number-columns-spanned="{string(ancestor::captionGroup/@cols)}" padding-top="4pt" padding-bottom="-4pt">
          <xsl:call-template name="add_applicability"/>
          <xsl:call-template name="add_controlAuthority"/> 
          <xsl:call-template name="add_security"/>
        </fo:table-cell>
      </fo:table-row>
    </xsl:if>
    <fo:table-row>
      <xsl:call-template name="add_id"/>
      <xsl:apply-templates/>
    </fo:table-row>
  </xsl:template>

  <xsl:template name="captionEntry">
    <xsl:param name="colname" select="string(@colname)"/>
    <xsl:param name="rowsep">
      <xsl:choose>
        <xsl:when test="@rowsep"><xsl:value-of select="string(@rowsep)"/></xsl:when>
        <xsl:when test="parent::captionRowrow/@rowsep"><xsl:value-of select="string(parent::captionRow/@rowsep)"/></xsl:when>
        <xsl:when test="($colname != '') and ancestor::captionGroup/colspec[string(@colname) = $colname]/@rowsep">
          <xsl:value-of select="string(ancestor::captionGroup/colspec[string(@colname) = $colname]/@rowsep)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="ancestor::*[string(@rowsep) != '']/@rowsep"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:param>
    <xsl:param name="colsep">
      <xsl:choose>
        <xsl:when test="@colsep"><xsl:value-of select="string(@colsep)"/></xsl:when>
        <xsl:when test="parent::captionRow/@colsep"><xsl:value-of select="string(parent::captionRow/@colsep)"/></xsl:when>
        <xsl:when test="($colname != '') and ancestor::captionGroup/colspec[string(@colname) = $colname]/@colsep">
          <xsl:value-of select="string(ancestor::captionGroup/colspec[string(@colname) = $colname]/@colsep)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="ancestor::*[string(@colsep) != '']/@colsep"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:param>
    
    <fo:table-cell width="from-table-column()">
      <xsl:call-template name="style-entry">
        <xsl:with-param name="rowsep" select="$rowsep"/>
        <xsl:with-param name="colsep" select="$colsep"/>
      </xsl:call-template>

      <xsl:if test="@morerows"><xsl:attribute name="number-rows-spanned"><xsl:value-of select="string(@morerows)"/></xsl:attribute></xsl:if>

      <xsl:if test="@spanname">
        <xsl:variable name="numberColumnsSpanned">
          <xsl:variable name="namestColname"><xsl:value-of select="string(ancestor::captionGroup/spanspec[@spanname = string(@spanname)]/@namest)"/></xsl:variable>
          <xsl:variable name="nameendColname"><xsl:value-of select="string(ancestor::captionGroup/spanspec[@spanname = string(@spanname)]/@nameend)"/></xsl:variable>
          <xsl:variable name="namestColnum"><xsl:value-of select="number(ancestor::captionGroup/colspec[@colname = $namestColname]/@colnum)"/></xsl:variable>
          <xsl:variable name="nameendColnum"><xsl:value-of select="number(ancestor::*/colspec[@colname = $nameendColname]/@colnum)"/></xsl:variable>
          <xsl:value-of select="number($nameendColnum - $namestColnum + 1)"/>
        </xsl:variable>
        <xsl:attribute name="number-columns-spanned"><xsl:value-of select="$numberColumnsSpanned"/></xsl:attribute>
      </xsl:if>

      <xsl:variable name="valign">
        <xsl:choose>
          <xsl:when test="ancestor-or-self::captionBody/@valign">
            <xsl:value-of select="string(ancestor-or-self::captionBody/@valign)"/>
          </xsl:when>
          <xsl:otherwise>top</xsl:otherwise>
        </xsl:choose>
      </xsl:variable>

      <xsl:choose>
        <xsl:when test="$valign = 'bottom'">
          <xsl:attribute name="display-align">after</xsl:attribute>
        </xsl:when>
        <xsl:when test="$valign = 'center'">
          <xsl:attribute name="display-align">middle</xsl:attribute>
        </xsl:when>
        <xsl:otherwise>
          <xsl:attribute name="display-align">top</xsl:attribute>
        </xsl:otherwise>
      </xsl:choose>
      
      <xsl:if test="@alignCaption"><xsl:attribute name="text-align"><xsl:value-of select="string(@alignCaption)"/></xsl:attribute></xsl:if>

      <fo:block-container>
        <xsl:if test="@id"><xsl:attribute name="id"><xsl:value-of select="string(@id)"/></xsl:attribute></xsl:if>
        <xsl:if test="@applicRefId">
          <xsl:call-template name="add_applicability">
            <xsl:with-param name="prefix"><xsl:text>This cell is applicable to: </xsl:text></xsl:with-param>
          </xsl:call-template>          
          <xsl:call-template name="add_controlAuthority"/>
        </xsl:if>
        <xsl:apply-templates/>
      </fo:block-container>
    </fo:table-cell>
  </xsl:template>

  <xsl:template match="caption">

  </xsl:template>

  <xsl:template match="captionLine">

  </xsl:template>

  <xsl:template match="captionText">
    
  </xsl:template>

</xsl:transform>