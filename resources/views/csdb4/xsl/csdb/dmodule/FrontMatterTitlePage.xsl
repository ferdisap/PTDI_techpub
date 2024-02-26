<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:output method="html" media-type="text/html" omit-xml-declaration="yes" />

  

  <xsl:template match="frontMatterTitlePage">
    <div class="frontMatterTitlePage">
      <xsl:apply-templates select="productIntroName"/>
      <xsl:apply-templates select="pmTitle"/>
      <xsl:apply-templates select="shortPmTitle"/>
      <xsl:apply-templates select="pmCode"/>
      <xsl:apply-templates select="issueInfo"/>
      <xsl:apply-templates select="issueDate"/>
      <xsl:apply-templates select="issueDate"/>
      <div class="div-externalPubCode">
        External Publication required to read:
        <ul>
          <xsl:for-each select="externalPubCode">
            <li><xsl:value-of select="@pubCodingScheme"/><xsl:text>:</xsl:text><xsl:apply-templates/></li>
          </xsl:for-each>
        </ul>
      </div>
      <xsl:apply-templates select="productAndModel"/>
      <div class="securityClassification">
        <xsl:apply-template select="security/@securityClassification"/>
      </div>
      <!-- dervative classification here -->
      <xsl:apply-templates select="dataRestrictions"/>
      <xsl:apply-templates select="productIllustration"/>
      <xsl:apply-templates select="enterpriseSpec"/>
      <xsl:apply-templates select="enterpriseLogo"/>
      <div class="responsiblePartnerCompany">
        Responsible Company: <xsl:apply-templates select="responsiblePartnerCompany/enterpriseName"/>
      </div>
      
    </div>
  </xsl:template>
  
  <xsl:template match="productIntroName[ancestor::frontMatterTitlePage]">
    <h1 class="productIntroName">
      <xsl:apply-templates/>
    </h1>  
  </xsl:template>

  <xsl:template match="pmTitle[parent::frontMatterTitlePage]">
    <h1 class="pmTitle">
      <xsl:apply-templates/>
    </h1>
  </xsl:template>

  <xsl:template match="shortPmTitle[parent::frontMatterTitlePage]">
    <h2>
      <xsl:apply-templates/>
    </h2>
  </xsl:template>

  <xsl:template match="pmCode[parent::frontMatterTitlePage]">
    <div class="pmCode">
      <h3><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmCode', .)"/></h3>
    </div>    
  </xsl:template>

  <xsl:template match="issueInfo[parent::frontMatterTitlePage]">
    <div class="issueInfo">
      <h3>Issue No. <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueInfo', .)"/></h3>
    </div>
  </xsl:template>
  
  <xsl:template match="issueDate[parent::frontMatterTitlePage]">
    <div class="issueDate">
      <h3><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', .)"/></h3>
    </div>
  </xsl:template>

  <xsl:template match="productAndModel[parent::frontMatterTitlePage]">
    <div class="productAndModel">
      <div class="productName">
        <h3><xsl:apply-templates/></h3>
      </div>
      <xsl:for-each select="productModel">
        <div class="productModel">
          <span>Model: <xsl:apply-templates select="modelName"/></span>
          <xsl:text> </xsl:text>
          <span class="natoStockNumber">NSN<xsl:apply-templates select="natoStockNumber"/>,</span>
          <xsl:text> </xsl:text>
          <span class="identNumber">
            Manufacture Code: <xsl:apply-templates select="identNumber/manufacturerCode"/><xsl:text> </xsl:text><xsl:apply-templates select="identNumber/partAndSerialNumber"/>-<xsl:apply-templates select="endItemCode"/>
          </span>
          
        </div>
      </xsl:for-each>
    </div>
  </xsl:template>

  <xsl:template match="productIllustration">
    <div class="productIllustration">
      <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="enterpriseSpec">
    <div class="enterpriseSpec">
      <div class="enterpriseName">
        <xsl:attribute name="id">
          <xsl:value-of select="enterpriseIdent/@manufacturerCodeValue"/>
        </xsl:attribute>
        <xsl:apply-templates/>
      </div>
      <xsl:apply-templates select="businessUnit"/>
      
    </div>
  </xsl:template>

  <xsl:template match="businessUnit">
    <div class="businessUnit">
      <xsl:call-template name="cgmark"/>
      <xsl:call-template name="id"/>
      <xsl:call-template name="sc"/>
      <div class="businessUnitName">
        <xsl:text> </xsl:text>
        <xsl:apply-templates select="businessUnitName"/>
      </div>
      <div class="businessUnitAddress">
        <xsl:text> </xsl:text>
        <xsl:apply-templates select="businessUnitAddress"/>
      </div>
      <div class="contactPerson">
        <xsl:for-each select="contactPerson">
          <xsl:text> </xsl:text>
          <xsl:apply-templates select="lastName"/>
          <xsl:if test="middleName">
            <xsl:text> </xsl:text>
            <xsl:apply-templates select="middleName"/>
          </xsl:if>
          <xsl:if test="firstName">
            <xsl:text> </xsl:text>
            <xsl:apply-templates select="firstName"/>
          </xsl:if>
          <xsl:if test="jobTitle">
            <xsl:text>, </xsl:text>
            <xsl:apply-templates select="jobTitle"/>
            <xsl:text>,</xsl:text>
          </xsl:if>
          <xsl:if test="contactPersonAddress">
            <xsl:text>, </xsl:text>
            <xsl:apply-templates select="contactPersonAddress"/>
          </xsl:if>
          <xsl:text>.</xsl:text>
        </xsl:for-each>
      </div>

    </div>
  </xsl:template>

  <xsl:template match="enterpriseLogo">
    <div class="enterpriseLogo">
      <xsl:for-each select="symbol">
        <xsl:apply-templates/>
      </xsl:for-each>
    </div>
  </xsl:template>


  

</xsl:transform>