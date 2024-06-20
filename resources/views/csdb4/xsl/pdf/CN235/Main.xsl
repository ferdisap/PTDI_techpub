<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <xsl:output method="xml" />
  
  <xsl:param name="filename"/>

  <xsl:include href="./master/default-A4.xsl" />
  <xsl:include href="./master/default-pm.xsl" />
  <xsl:include href="./master/region.xsl" />
  <xsl:include href="./dmodule.xsl" />
  <xsl:include href="./pm.xsl"/>
  <xsl:include href="../generalXSL/other/other.xsl" />
  
  <xsl:include href="../generalXSL/identStatus/All-Authority.xsl" />
  <xsl:include href="../generalXSL/identStatus/All-Enterprise.xsl" />
  <xsl:include href="../generalXSL/identStatus/All-Security.xsl" />
  <xsl:include href="../generalXSL/identStatus/dmTitle.xsl" />
  <xsl:include href="../generalXSL/content/content.xsl" />
  <xsl:include href="../generalXSL/content/applicability/All.xsl" />
  <xsl:include href="../generalXSL/content/captionGroups/All.xsl" />
  <xsl:include href="../generalXSL/content/changeMarking/All.xsl" />
  <xsl:include href="../generalXSL/content/commonInformation/commonInfo.xsl" />
  <xsl:include href="../generalXSL/content/commonInformationRepository/All-AccessPointRepository.xsl" />
  <xsl:include href="../generalXSL/content/commonInformationRepository/All-CircuitBreakerRepository.xsl" />
  <xsl:include href="../generalXSL/content/commonInformationRepository/All-ControlIndicatorRepository.xsl" />
  <xsl:include href="../generalXSL/content/commonInformationRepository/All-ZoneRepository.xsl" />
  <xsl:include href="../generalXSL/content/commonInformationRepository/comrep.xsl" />
  <xsl:include href="../generalXSL/content/crewOperatorInformation/All.xsl" />
  <xsl:include href="../generalXSL/content/descriptiveInformation/Style-levelledPara.xsl" />
  <xsl:include href="../generalXSL/content/descriptiveInformation/description.xsl" />
  <xsl:include href="../generalXSL/content/descriptiveInformation/levelledPara.xsl" />
  <xsl:include href="../generalXSL/content/figuresMultimediaFoldouts/All.xsl" />
  <xsl:include href="../generalXSL/content/figuresMultimediaFoldouts/Style-icn.xsl" />
  <xsl:include href="../generalXSL/content/frontMatter/All-FrontMatterTitlePage.xsl" />
  <xsl:include href="../generalXSL/content/frontMatter/All-FrontMatterList.xsl" />
  <xsl:include href="../generalXSL/content/frontMatter/Highlights.xsl" />
  <xsl:include href="../generalXSL/content/frontMatter/HighlightsAndUpdating.xsl" />
  <xsl:include href="../generalXSL/content/frontMatter/Leodm.xsl" />
  <xsl:include href="../generalXSL/content/frontMatter/Updating.xsl" />
  <xsl:include href="../generalXSL/content/frontMatter/frontMatter.xsl" />
  <xsl:include href="../generalXSL/content/lists/All.xsl" />
  <xsl:include href="../generalXSL/content/lists/Style-list.xsl" />
  <xsl:include href="../generalXSL/content/referencing/dmRef.xsl" />
  <xsl:include href="../generalXSL/content/referencing/externalPubRef.xsl" />
  <xsl:include href="../generalXSL/content/referencing/functionalItemRef.xsl" />
  <xsl:include href="../generalXSL/content/referencing/internalRef.xsl" />
  <xsl:include href="../generalXSL/content/referencing/pmRef.xsl" />
  <xsl:include href="../generalXSL/content/referencing/refs.xsl" />
  <xsl:include href="../generalXSL/content/tables/All.xsl" />
  <xsl:include href="../generalXSL/content/tables/Style-table.xsl" />
  <xsl:include href="../generalXSL/content/textElements/All.xsl" />
  <xsl:include href="../generalXSL/content/textElements/Style-para.xsl" />
  <xsl:include href="../generalXSL/content/textElements/accessPointRef.xsl" />
  <xsl:include href="../generalXSL/content/textElements/circuitBreakerRef.xsl" />
  <xsl:include href="../generalXSL/content/textElements/controlIndicatorRef.xsl" />
  <xsl:include href="../generalXSL/content/textElements/footnoteRef.xsl" />
  <xsl:include href="../generalXSL/content/textElements/name.xsl" />
  <xsl:include href="../generalXSL/content/textElements/para.xsl" />
  <xsl:include href="../generalXSL/content/textElements/partRef.xsl" />
  <xsl:include href="../generalXSL/content/textElements/zoneRef.xsl" />
  <xsl:include href="../generalXSL/content/titles/All.xsl" />
  <xsl:include href="../generalXSL/content/titles/Style-title.xsl" />
  <xsl:include href="../generalXSL/content/warningCautionNote/All.xsl" />
  <xsl:include href="../generalXSL/content/warningCautionNote/Attention.xsl" />
  <xsl:include href="../generalXSL/content/warningCautionNote/Style-warningcautionnote.xsl" />
  <xsl:include href="../generalXSL/content/wiringInformation/amperage.xsl" />
  <xsl:include href="../generalXSL/other/Style.xsl" />
  <xsl:include href="../generalXSL/other/id.xsl" />
  <xsl:include href="../generalXSL/other/position.xsl" />
  
  <xsl:variable name="ConfigXML" select="document('../../Config.xml')"/>

  <xsl:variable name="masterName">
    <xsl:choose>
      <xsl:when test="pm">
        <xsl:variable name="pt" select="$ConfigXML/config/pmGroup/pt[string(@type) = string(pm/@pmType)]" />
        <xsl:choose>
          <xsl:when test="$pt">
            <xsl:value-of select="$pt"/>
          </xsl:when>
          <xsl:otherwise>default-pm</xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>default-A4</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <xsl:variable name="orientation" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@orientation)"/>
  <xsl:variable name="width" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@width)"/>
  <xsl:variable name="height" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@height)"/>
  <xsl:variable name="mt" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@margin-top)"/>
  <xsl:variable name="mb" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@margin-bottom)"/>
  <xsl:variable name="ml" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@margin-left)"/>
  <xsl:variable name="mr" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@margin-right)"/>
  <xsl:variable name="rb" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@region-before)"/>
  <xsl:variable name="ra" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@region-after)"/>
  <xsl:variable name="stIndent" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@start-indent)"/>
  <xsl:variable name="cgmarkIndent" select="string($ConfigXML/config/output/layout[@master-name = $masterName]/@cgmark-indent)"/>
  <xsl:variable name="titleNumberWidth">
    <xsl:choose>
      <xsl:when test="boolean($stIndent) or $stIndent != ''">
        <xsl:text>0</xsl:text>
      </xsl:when>
      <xsl:otherwise>1.5cm</xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <xsl:variable name="blockIndent">0cm</xsl:variable>

  <xsl:param name="alertPathBackground"/>
  <xsl:variable name="warningPath"><xsl:value-of select="$alertPathBackground"/>/warningBackground.png</xsl:variable>
  <xsl:variable name="cautionPath"><xsl:value-of select="$alertPathBackground"/>/cautionBackground.png</xsl:variable>
  <xsl:variable name="controlAutoritySymbolPath"><xsl:value-of select="$alertPathBackground"/>/controlAuthoritySymbol.png</xsl:variable>
  
  <xsl:variable name="csdb_path"><xsl:value-of select="php:function('storage_path', 'csdb')"/></xsl:variable>

  
  <xsl:template match="/">
    <fo:root font-family="Arial">
      <xsl:call-template name="setPageMaster">
        <xsl:with-param name="masterName" select="$masterName" />
      </xsl:call-template>

      <xsl:call-template name="setBookmark">
        <xsl:with-param name="masterName" select="$masterName" />
      </xsl:call-template>

      <xsl:call-template name="setPageSequence">
        <xsl:with-param name="masterName" select="$masterName" />
      </xsl:call-template>
    </fo:root>    
  </xsl:template>

  <xsl:template name="setPageMaster">
    <xsl:param name="masterName" />
    <xsl:choose>
      <xsl:when test="$masterName = 'default-A4'">
        <xsl:call-template name="pageMasterByDefaultA4"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:call-template name="pageMasterByPt">
          <xsl:with-param name="masterName" select="$masterName" />
        </xsl:call-template>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="setPageSequence">
    <xsl:param name="masterName"/>
    <xsl:choose>
      <xsl:when test="name(*) = 'pm'">
        <xsl:apply-templates select="pm">
          <xsl:with-param name="masterReference" select="$masterName"/>
        </xsl:apply-templates>
      </xsl:when>
      <xsl:when test="name(*) = 'dmodule'">
        <xsl:apply-templates select="dmodule">
          <xsl:with-param name="masterReference" select="$masterName"/>
        </xsl:apply-templates>
      </xsl:when>
      <xsl:otherwise>
        <fo:page-sequence master-reference="default-A4">
          <fo:flow flow-name="body">
            <fo:block>Nothing to displayed of &#60;<xsl:value-of select="name(*)"/>&#62;</fo:block>
          </fo:flow>
        </fo:page-sequence>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="setBookmark">
    <xsl:param name="masterName"/>
    <fo:bookmark-tree/>
    <!-- <xsl:text>##insert_bookmark_here##</xsl:text> -->
    <!-- <fo:bookmark-tree>
      <fo:bookmark internal-destination="block-001">
        <fo:bookmark-title>Any text</fo:bookmark-title>
      </fo:bookmark>
      <fo:bookmark internal-destination="block-002">
        <fo:bookmark-title>Any text</fo:bookmark-title>
      </fo:bookmark>
    </fo:bookmark-tree> -->
  </xsl:template>

</xsl:transform>