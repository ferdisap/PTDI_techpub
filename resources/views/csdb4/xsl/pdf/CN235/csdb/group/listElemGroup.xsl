<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">


  <xsl:template match="sequentialList">
    <fo:block><xsl:value-of select="title"/></fo:block>
    <fo:list-block provisional-distance-between-starts="0.7cm" provisional-label-separation="0.15cm">
      <xsl:apply-templates select="listItem">
        <xsl:with-param name="listItemType">ol</xsl:with-param>
      </xsl:apply-templates>
    </fo:list-block>
  </xsl:template>

  <xsl:template match="randomList">
    <fo:block><xsl:value-of select="title"/></fo:block>
    <fo:list-block provisional-distance-between-starts="0.5cm" provisional-label-separation="0.5cm">
      <xsl:apply-templates select="listItem">
        <xsl:with-param name="listItemType">ul</xsl:with-param>
      </xsl:apply-templates>
    </fo:list-block>
  </xsl:template>
  
  <xsl:template match="listItem">
    <xsl:param name="listItemType"/>
    <fo:list-item start-indent="0.5cm">
      <fo:list-item-label end-indent="label-end()">
        <fo:block>
          <xsl:call-template name="getListItemLabel">
            <xsl:with-param name="listItemType" select="$listItemType"/>
          </xsl:call-template>
        </fo:block>
      </fo:list-item-label>
      <fo:list-item-body start-indent="body-start()">
        <xsl:apply-templates/>
      </fo:list-item-body>
    </fo:list-item>
  </xsl:template>

  <xsl:template match="reducedRandomList">
    <fo:block><xsl:value-of select="title"/></fo:block>
    <fo:list-block provisional-distance-between-starts="0.5cm" provisional-label-separation="0.5cm">
      <xsl:apply-templates select="reducedRandomListItem"/>
    </fo:list-block>
  </xsl:template>
  <xsl:template match="reducedRandomListItem">
    <fo:list-item start-indent="0.5cm">
      <fo:list-item-label end-indent="label-end()">
        <fo:block>-</fo:block>
      </fo:list-item-label>
      <fo:list-item-body start-indent="body-start()">
        <xsl:apply-templates select="reducedListItemPara"/>
      </fo:list-item-body>
    </fo:list-item>
  </xsl:template>
  <xsl:template match="reducedListItemPara">
    <fo:block text-align="justify">
      <xsl:apply-templates/>
    </fo:block>
  </xsl:template>

  <!-- return '&#x2022;' or '-', or '<number>.' -->
  <xsl:template name="getListItemLabel">
    <xsl:param name="listItemType"/>
    <xsl:choose>
      <xsl:when test="$listItemType = 'ul'">
        <xsl:choose>
          <xsl:when test="ancestor::randomList">
            <xsl:text>&#x2022;</xsl:text>
          </xsl:when>
          <xsl:otherwise>-</xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:number/><xsl:text>.</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>



  <!-- <ul class="randomList">
    <xsl:call-template name="cgmark"/>
    <xsl:apply-templates/>
  </ul>          -->

  <!-- <xsl:template match="listItem">
    <li class="listItem">
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </li>
  </xsl:template>
  
  <xsl:template match="definitionList">
    <br/>
    <dl class="definitionList">
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates select="definitionListItem"/>
    </dl>
  </xsl:template>

  <xsl:template match="definitionListItem">
    <div class="definitionListItem">
      <dt>
        <b><xsl:apply-templates select="listItemTerm"/></b>
        <br/>
      </dt>
      <dd>
        <xsl:apply-templates select="listItemDefinition"/>
        <br/>
      </dd>
    </div>
  </xsl:template> -->


</xsl:stylesheet>