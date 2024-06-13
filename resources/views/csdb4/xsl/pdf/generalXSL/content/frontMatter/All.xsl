<?xml version="1.0" encoding="UTF-8"?>

<!-- 
  Outstanding:
  - <dataRestriction> belum dibuat
  - @frontMatterInfoType (fmi-xx) belum dibuat
-->

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <!-- <xsl:template match="content[name(child::*) = 'frontMatter']">
    <xsl:variable name="dmIdent" select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_dmIdent', //identAndStatusSection/dmAddress/dmIdent, '', '')"/>
    <fo:block-container id="{$dmIdent}">
      <xsl:call-template name="add_id"/>
      <xsl:call-template name="add_controlAuthority"/>
      <xsl:apply-templates/>
    </fo:block-container>
  </xsl:template> -->

  <xsl:template match="frontMatter">
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="frontMatterTitlePage">
    <xsl:apply-templates select="productIntroName"/>
    <xsl:apply-templates select="productAndModel"/>
    <xsl:apply-templates select="pmTitle"/>
    <xsl:apply-templates select="shortPmTitle"/>

    <fo:block>
      <xsl:apply-templates select="pmCode"/>
      <fo:inline-container inline-progression-dimension="30%">
        <xsl:apply-templates select="issueInfo"/>
      </fo:inline-container>
      <fo:inline-container>
        <xsl:apply-templates select="issueDate"/>
      </fo:inline-container>
    </fo:block>

    <xsl:apply-templates select="productIllustration"/>
    <xsl:call-template name="copyright"/>

    <!-- externalPubCode -->
    <fo:block-container font-size="8pt" margin-top="3pt">
      <fo:block>External Publication related:</fo:block>
      <xsl:for-each select="externalPubCode">
        <fo:block>
          <xsl:value-of select="@pubCodingScheme"/><xsl:text>:</xsl:text><xsl:apply-templates/> <xsl:text>   </xsl:text>
        </fo:block>
      </xsl:for-each>
    </fo:block-container>

    <!-- dervative classification here -->

    <!-- manufacturer -->
    <fo:block-container margin-top="11pt">
      <fo:block font-size="8pt">Manufacturer:</fo:block>
      <fo:block font-size="8pt">
        <fo:table table-layout="fixed" width="100%">
          <fo:table-body>
            <fo:table-row>
              <fo:table-cell width="2cm">
                <xsl:apply-templates select="enterpriseLogo"/>
              </fo:table-cell>
              <fo:table-cell display-align="top">
                <xsl:apply-templates select="enterpriseSpec"/>
              </fo:table-cell>
            </fo:table-row>
          </fo:table-body>
        </fo:table>
      </fo:block>
    </fo:block-container>

    <!-- publisher -->
    <fo:block-container margin-top="11pt">
      <fo:block font-size="8pt">Publisher:</fo:block>
      <fo:block font-size="8pt">
        <fo:table table-layout="fixed" width="100%">
          <fo:table-body>
            <fo:table-row>
              <fo:table-cell width="2cm">
                <xsl:apply-templates select="publisherLogo"/>
                <fo:block></fo:block>
              </fo:table-cell>
              <fo:table-cell display-align="top">
                <fo:block>
                  <xsl:call-template name="add_id">
                    <xsl:with-param name="id" select="responsiblePartnerCompany/@id"/>
                  </xsl:call-template>
                  <xsl:value-of select="string(responsiblePartnerCompany/enterpriseName)"/>
                </fo:block>
              </fo:table-cell>
            </fo:table-row>
          </fo:table-body>
        </fo:table>
      </fo:block>
    </fo:block-container>

    <xsl:apply-templates select="security"/>
    <xsl:apply-templates select="barCode"/>
    
    <fo:block-container break-before="page" font-size="8pt">
      <fo:block margin-top="6pt">
        <fo:block margin-top="3pt"><xsl:apply-templates select="dataRestrictions/restrictionInfo/policyStatement"/></fo:block>
        <fo:block margin-top="3pt"><xsl:apply-templates select="dataRestrictions/restrictionInfo/dataConds"/></fo:block>
      </fo:block>
      <fo:block margin-top="6pt">
        <fo:block margin-top="3pt"><xsl:apply-templates select="dataRestrictions/restrictionInstructions/dataDistribution"/></fo:block>
        <fo:block margin-top="3pt"><xsl:apply-templates select="dataRestrictions/restrictionInstructions/exportControl"/></fo:block>
        <fo:block margin-top="3pt"><xsl:apply-templates select="dataRestrictions/restrictionInstructions/dataHandling"/></fo:block>
        <fo:block margin-top="3pt"><xsl:apply-templates select="dataRestrictions/restrictionInstructions/dataDestruction"/></fo:block>
        <fo:block margin-top="3pt"><xsl:apply-templates select="dataRestrictions/restrictionInstructions/dataDisclosure"/></fo:block>
      </fo:block>
      <fo:block margin-top="3pt">
        <xsl:apply-templates select="frontMatterInfo"/>
      </fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template match="productIntroName[ancestor::frontMatterTitlePage]">
    <fo:block xsl:use-attribute-sets="fmIntroName">
      <xsl:apply-templates select="name" />
    </fo:block>
  </xsl:template>

  <xsl:template match="productAndModel[parent::frontMatterTitlePage]">
    <xsl:if test="productName">
      <fo:block class="productName"><xsl:apply-templates select="productName"/></fo:block>
    </xsl:if>
    <xsl:for-each select="productModel">
      <fo:block-container>
        <fo:block>
          <fo:inline>Model: <xsl:apply-templates select="modelName"/></fo:inline>
          <xsl:if test="natoStockNumber">
            <xsl:text>   </xsl:text>
            <fo:inline>NSN: <xsl:apply-templates select="natoStockNumber"/></fo:inline>
          </xsl:if>
          <xsl:if test="identNumber">
            <xsl:text>   </xsl:text>
            <fo:inline>Manufacture Code: <xsl:apply-templates select="identNumber/manufacturerCode"/></fo:inline>
          </xsl:if>
        </fo:block>
      </fo:block-container>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="pmTitle[parent::frontMatterTitlePage]">
    <fo:block xsl:use-attribute-sets="fmPmTitle">
      <xsl:apply-templates />
    </fo:block>
  </xsl:template>

  <xsl:template match="shortPmTitle[parent::frontMatterTitlePage]">
    <fo:block xsl:use-attribute-sets="fmShortPmTitle">
      <xsl:apply-templates />
    </fo:block>
  </xsl:template>

  <xsl:template match="pmCode[parent::frontMatterTitlePage]">
    <fo:block xsl:use-attribute-sets="fmPmCode">
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_pmCode', .)" />
    </fo:block>
  </xsl:template>

  <xsl:template match="issueInfo[parent::frontMatterTitlePage]">
    <fo:block xsl:use-attribute-sets="fmPmIssueInfo">
      Issue No.: <xsl:value-of select="@issueNumber"/>
    </fo:block>
  </xsl:template>
  
  <xsl:template match="issueDate[parent::frontMatterTitlePage]">
    <fo:block xsl:use-attribute-sets="fmPmIssueDate">
      Issue Date: <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_issueDate', .)"/>
    </fo:block>
  </xsl:template>

  <xsl:template match="productIllustration">
    <xsl:for-each select="graphic">
      <fo:block text-align="center">
        <fo:external-graphic src="url('{unparsed-entity-uri(@infoEntityIdent)}')" content-width="scale-to-fit">
          <xsl:call-template name="setGraphicDimension"/>
        </fo:external-graphic>
      </fo:block>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="enterpriseLogo">
    <fo:block>
      <xsl:for-each select="symbol">
        <fo:external-graphic src="url('{unparsed-entity-uri(@infoEntityIdent)}')" content-width="scale-to-fit" width="1.5cm"/>
      </xsl:for-each>
    </fo:block>
  </xsl:template>
  
  <xsl:template match="publisherLogo">
    <fo:block>
      <xsl:for-each select="symbol">
        <fo:external-graphic src="url('{unparsed-entity-uri(@infoEntityIdent)}')" content-width="scale-to-fit" width="1.5cm"/>
      </xsl:for-each>
    </fo:block>
  </xsl:template>

  <xsl:template match="frontMatterInfo">
    <fo:block-container margin-top="6pt">
      <fo:block xsl:use-attribute-sets="h1">
        <xsl:value-of select="title"/>
      </fo:block>
      <xsl:apply-templates select="*[name() != 'title']"/>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="copyright">
    <xsl:call-template name="add_controlAuthority"/>
    <xsl:call-template name="add_security"/>
    <fo:block-container font-size="8pt" margin-top="8pt">
      <xsl:apply-templates select="dataRestrictions/restrictionInfo/copyright/copyrightPara"/>
    </fo:block-container>
  </xsl:template>

  <xsl:template match="copyrightPara">
    <xsl:call-template name="add_applicability"/>
    <fo:block margin-top="0pt">
      <xsl:apply-templates>
        <xsl:with-param name="listElemMarginTop">0pt</xsl:with-param>
      </xsl:apply-templates>
    </fo:block>
  </xsl:template>
  

  
</xsl:transform>