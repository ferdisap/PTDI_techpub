<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <xsl:template match="sequentialList">
    <fo:block margin-top="11pt">
      <fo:block><xsl:apply-templates select="title|__cgmark"/></fo:block>
      <fo:list-block provisional-distance-between-starts="0.7cm" provisional-label-separation="0.15cm">
        <xsl:apply-templates select="listItem|__cgmark"/>
      </fo:list-block>
    </fo:block>
  </xsl:template>

  <xsl:template match="randomList">
    <fo:block margin-top="11pt">
      <fo:block><xsl:apply-templates select="title|__cgmark"/></fo:block>
      <fo:list-block provisional-distance-between-starts="0.5cm" provisional-label-separation="0.5cm">
        <xsl:apply-templates select="listItem|__cgmark">
          <xsl:with-param name="listItemPrefix" select="string(@listItemPrefix)"/>
        </xsl:apply-templates>
      </fo:list-block>
    </fo:block>
  </xsl:template>
  
  <xsl:template match="listItem[parent::sequentialList]">
    <fo:list-item>
      <xsl:call-template name="style-listItem"/>
      <fo:list-item-label end-indent="label-end()">
        <fo:block>
          <xsl:number/><xsl:text>.</xsl:text>
        </fo:block>
      </fo:list-item-label>
      <fo:list-item-body start-indent="body-start()">
        <xsl:apply-templates/>
      </fo:list-item-body>
    </fo:list-item>
  </xsl:template>

  <xsl:template match="listItem[parent::randomList]">
    <xsl:param name="listItemPrefix"/>
    <fo:list-item>
      <xsl:call-template name="style-listItem"/>
      <fo:list-item-label end-indent="label-end()">
        <fo:block>
          <xsl:choose>
            <xsl:when test="$listItemPrefix != ''">
              <xsl:value-of select="$listItemPrefix"/>
            </xsl:when>
            <xsl:when test="count(ancestor::randomList) mod 2">
              <xsl:text>-</xsl:text>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>&#x2022;</xsl:text>
            </xsl:otherwise>
          </xsl:choose>
        </fo:block>
      </fo:list-item-label>
      <fo:list-item-body start-indent="body-start()">
        <xsl:apply-templates/>
      </fo:list-item-body>
    </fo:list-item>
  </xsl:template>

  <xsl:template match="reducedRandomList">
    <fo:block><xsl:apply-templates select="title|__cgmark"/></fo:block>
    <fo:list-block provisional-distance-between-starts="0.5cm" provisional-label-separation="0.5cm">
      <xsl:apply-templates select="reducedRandomListItem|__cgmark"/>
    </fo:list-block>
  </xsl:template>
  <xsl:template match="reducedRandomListItem">
    <fo:list-item start-indent="0.5cm">
      <fo:list-item-label end-indent="label-end()">
        <fo:block>-</fo:block>
      </fo:list-item-label>
      <fo:list-item-body start-indent="body-start()">
        <xsl:apply-templates select="reducedListItemPara|__cgmark"/>
      </fo:list-item-body>
    </fo:list-item>
  </xsl:template>
  <xsl:template match="reducedListItemPara">
    <fo:block text-align="justify">
      <xsl:apply-templates select="__cgmark|node()"/>
    </fo:block>
  </xsl:template>
  
</xsl:stylesheet>