<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:fox="http://xmlgraphics.apache.org/fop/extensions" xmlns:php="http://php.net/xsl">

  <xsl:template match="pm">
    <xsl:param name="masterReference"/>
    <xsl:variable name="id">
      <xsl:value-of select="generate-id(.)"/>
    </xsl:variable>
    <xsl:apply-templates select="content">
      <xsl:with-param name="masterReference" select="$masterReference"/>
    </xsl:apply-templates>
  </xsl:template>
  
  <xsl:template match="content[ancestor::pm]">
    <xsl:param name="masterReference"/>
    <xsl:apply-templates select="pmEntry">
      <xsl:with-param name="masterReference" select="$masterReference"/>
    </xsl:apply-templates>
  </xsl:template>

  <xsl:template match="pmEntry">
    <xsl:param name="masterReference"/>
    <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::set_pmEntryTitle', string(pmEntryTitle))"/>

    <xsl:variable name="idParentBookmark">
      <xsl:choose>
        <xsl:when test="preceding-sibling::dmRef">
          <!-- <xsl:value-of select="generate-id(preceding-sibling::dmRef/dmRefIdent)"/> -->
          <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmIdent', preceding-sibling::dmRef/dmRefIdent, '', '')"/>
        </xsl:when>
        <xsl:otherwise>
          <!-- nothing to do -->
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>

    <xsl:apply-templates select="dmRef|pmRef|pmEntry">
      <xsl:with-param name="masterReference" select="$masterReference"/>
      <xsl:with-param name="idParentBookmark" select="$idParentBookmark"/>
    </xsl:apply-templates>
  </xsl:template>

  <xsl:template match="dmRef[parent::pmEntry]">
    <xsl:param name="masterReference"/>
    <xsl:param name="idParentBookmark"/>
    <xsl:variable name="entry" select="php:function(
      'Ptdi\Mpub\Main\CSDBStatic::document',
      $csdb_path,
      php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmIdent', descendant::dmRefIdent)
      )"/>
    <fo:page-sequence master-reference="{$masterReference}" initial-page-number="auto-odd" force-page-count="even">
      <xsl:call-template name="getRegion">
        <xsl:with-param name="masterReference" select="$masterReference"/>
        <xsl:with-param name="entry" select="$entry"/>
      </xsl:call-template>
      <!-- flow-name merujuk ke simple-page-master/region-body/@region-name -->
      <fo:flow flow-name="body">
        <fo:block-container>
          <fo:block>
            <xsl:apply-templates select="$entry//content">
              <xsl:with-param name="idParentBookmark" select="$idParentBookmark"/>
            </xsl:apply-templates>
          </fo:block>
        </fo:block-container>
      </fo:flow>
    </fo:page-sequence>
  </xsl:template>

  <!-- 
    Kekuranganya adalah, jika kita mau ngeganti font, maka kita perlu menganulir global variable. 
    Agak sulit, namun nanti global variable akan menggunakan PHP saja.
  -->
  <xsl:template match="pmRef[parent::pmEntry]">
    <xsl:variable name="entry" select="php:function(
      'Ptdi\Mpub\Main\CSDBStatic::document',
      $csdb_path,
      php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_pmIdent', descendant::pmRefIdent)
      )"/>
    <xsl:variable name="masterName">
      <xsl:variable name="pt" select="string($entry/pm/@pmType)"/>
      <xsl:choose>
        <xsl:when test="$pt">
          <xsl:value-of select="$ConfigXML/config/pmGroup/pt[string(@type) = $pt]"/>
        </xsl:when>
        <xsl:otherwise>default-pm</xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <xsl:variable name="height" select="'30cm'"/>
    <xsl:apply-templates select="$entry/pm">
      <xsl:with-param name="masterReference" select="$masterName"/>
    </xsl:apply-templates>
  </xsl:template>
  
  <xsl:template match="pmRef[parent::externalPubRef]">

  </xsl:template>

  <xsl:template match="pmEntryTitle">
    <!-- nothing to do -->
  </xsl:template>

</xsl:transform>
