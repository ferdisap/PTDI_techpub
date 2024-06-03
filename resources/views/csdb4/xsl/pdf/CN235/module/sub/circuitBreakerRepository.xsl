<?xml version="1.0" encoding="UTF-8"?>

<!-- Outstanding
  1. belum mengakomodir elemen circuitBreakerIdent/@itemOriginator
  2. belum mengakomodir elemen circuitBreakerIdent/@contextIdent
  3. belum mengakomodir elemen circuitBreakerIdent/@manufacturerCodeValue
  4. belum mengakomodir elemen functionalItemRefGroup dan attribute nya
  5. belum mengakomodir elemen circuitBreakerRefGroup dan attribute nya
  6. belum mengakomodir elemen functionalPhysicalAreaRef dan attribute nya
-->

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

    <xsl:template match="circuitBreakerRepository">
      <fo:block font-size="14pt" font-weight="bold" margin-bottom="6pt" margin-top="6pt" text-align="center">
        Common Information Repository - Circuit Breaker
      </fo:block>
      <fo:block>
        <xsl:call-template name="add_id"/>
        <xsl:call-template name="add_controlAuthority"/>
        <xsl:call-template name="add_security"/>
        <fo:table border-top="1pt solid black" border-bottom="1pt solid black" width="100%">
          <fo:table-column column-number="1" column-width="20%"/>
          <fo:table-column column-number="1" column-width="35%"/>
          <fo:table-column column-number="1" column-width="10%"/>
          <fo:table-column column-number="1" column-width="15%"/>
          <fo:table-column column-number="1" column-width="8%"/>
          <fo:table-column column-number="1" column-width="12%"/>
          <fo:table-header>
            <fo:table-row border-bottom="1pt solid black">
              <fo:table-cell padding="4pt"><fo:block>Label</fo:block></fo:table-cell>
              <fo:table-cell padding="4pt"><fo:block>Name/function</fo:block></fo:table-cell>
              <fo:table-cell padding="4pt" text-align="center"><fo:block>FIN</fo:block></fo:table-cell>
              <fo:table-cell padding="4pt" text-align="center"><fo:block>Group</fo:block></fo:table-cell>
              <fo:table-cell padding="4pt" text-align="center"><fo:block>Amp</fo:block></fo:table-cell>
              <fo:table-cell padding="4pt" text-align="center"><fo:block>Panel</fo:block></fo:table-cell>
            </fo:table-row>
          </fo:table-header>
          <fo:table-body>
            <xsl:apply-templates select="circuitBreakerSpec"/>
          </fo:table-body>
        </fo:table>
      </fo:block>
    </xsl:template>

    <xsl:template match="circuitBreakerSpec">
      <xsl:if test="@controlAuthorityRefs">
        <fo:table-row keep-together="always">
          <fo:table-cell number-columns-spanned="3">
            <xsl:call-template name="add_controlAuthority"/>
          </fo:table-cell>
        </fo:table-row>
      </xsl:if>

      <xsl:if test="@securityClassification or @commercialClassification or @caveat">
        <fo:table-row keep-together="always">
          <fo:table-cell number-columns-spanned="3">
            <xsl:call-template name="add_security"/>
          </fo:table-cell>
        </fo:table-row>
      </xsl:if>

      <xsl:apply-templates select="circuitBreakerAlts/circuitBreaker"/>
    </xsl:template>

    <xsl:template match="circuitBreaker">
      <fo:table-row>
        <fo:table-cell padding-left="2pt" padding-right="2pt">
          <xsl:apply-templates select="ancestor::circuitBreakerSpec/circuitBreakerIdent"/>
        </fo:table-cell>
        <fo:table-cell padding-left="2pt" padding-right="2pt">
          <fo:block text-align="left">
              <xsl:call-template name="style-para"/>
              <xsl:apply-templates select="ancestor::circuitBreakerSpec/name"/>
              <xsl:if test="ancestor::circuitBreakerSpec/shortName">
                <xsl:text>(</xsl:text><xsl:apply-templates select="ancestor::circuitBreakerSpec/shortName"/><xsl:text>)</xsl:text>
              </xsl:if>
            </fo:block>
        </fo:table-cell>
        <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
          <fo:block>
            <xsl:call-template name="style-para"/>
            <xsl:text>-</xsl:text>
          </fo:block>
          <!-- <xsl:apply-templates select="ancestor::circuitBreakerSpec/circuitBreakerAlts/circuitBreaker/functionalItemRefGroup"/> -->
        </fo:table-cell>
        <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
          <fo:block>
            <xsl:call-template name="style-para"/>
            <xsl:text>-</xsl:text>
          </fo:block>
          <!-- <xsl:apply-templates select="ancestor::circuitBreakerSpec/circuitBreakerRefGroup"/> -->
        </fo:table-cell>
        <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
          <fo:block>
            <xsl:call-template name="style-para"/>
            <xsl:value-of select="ancestor::circuitBreakerSpec/circuitBreakerAlts/circuitBreaker/amperage"/>
          </fo:block>
        </fo:table-cell>
        <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
          <fo:block>
            <xsl:call-template name="style-para"/>
            <xsl:apply-templates select="ancestor::circuitBreakerSpec/circuitBreakerAlts/circuitBreaker/location/quantity"/>
          </fo:block>
        </fo:table-cell>        
      </fo:table-row>
    </xsl:template>

    <xsl:template match="circuitBreakerIdent">
      <fo:block>
        <xsl:call-template name="style-para"/>
        <xsl:apply-templates select="@circuitBreakerNumber"/>
      </fo:block>
    </xsl:template>

    <xsl:template match="functionalItemRefGroup[parent::circuitBreaker]">
      <fo:block>
        <xsl:call-template name="style-para"/>
        <xsl:text>-</xsl:text>
      </fo:block>
    </xsl:template>

    <xsl:template match="circuitBreakerRefGroup[parent::circuitBreakerSpec]">
      <fo:block>
        <xsl:call-template name="style-para"/>
        <xsl:text>-</xsl:text>
      </fo:block>
    </xsl:template>

    <xsl:template match="functionalPhysicalAreaRef[parent::circuitBreakerSpec]">
      <fo:block>
        <xsl:call-template name="style-para"/>
        <xsl:text>-</xsl:text>
      </fo:block>
    </xsl:template>

    <xsl:template name="circuitBreakerRefGroup">
      <fo:block>
        <xsl:call-template name="style-para"/>
        <xsl:text>-</xsl:text>
      </fo:block>
    </xsl:template>

    <xsl:template name="circuitBreakerAlts">
      <fo:block>
        <xsl:call-template name="add_id"/>
        <xsl:call-template name="add_controlAuthority"/>
        <xsl:call-template name="add_security"/>
        <xsl:apply-templates/>
      </fo:block>
    </xsl:template>

    <xsl:template name="circuitBreaker">
      <fo:block>
        <xsl:call-template name="add_id"/>
        <xsl:call-template name="add_applicability"/>
        <xsl:call-template name="add_controlAuthority"/>
        <xsl:call-template name="add_security"/>
        
        <xsl:apply-templates select="name"/>
        <xsl:if test="shortname">
          <xsl:text>(</xsl:text><xsl:apply-templates select="name"/><xsl:text>)</xsl:text>          
        </xsl:if>
        <xsl:apply-templates select="circuitBreakerClass"/>
        <xsl:apply-templates select="location"/>

      </fo:block>
    </xsl:template>

    <xsl:template match="circuitBreakerClass">

    </xsl:template>

    

</xsl:transform>