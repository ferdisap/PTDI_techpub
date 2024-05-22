<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

    <xsl:template match="circuitBreakerRepository">
      <fo:block font-size="14pt" font-weight="bold" margin-bottom="6pt" margin-top="6pt" text-align="center">
        Common Information Repository - Circuit Breaker
      </fo:block>
      <fo:block>
        <xsl:call-template name="style-para"/>
        Following text are circuit breaker installed in aircraft:
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

      <xsl:if test="count(circuitBreakerAlts/child::circuitBreaker) = 1">
        <fo:table-row>
          <xsl:call-template name="add_id"/>
          <fo:table-cell padding-left="2pt" padding-right="2pt">
            <xsl:apply-templates select="circuitBreakerIdent"/>
          </fo:table-cell>
          <fo:table-cell padding-left="2pt" padding-right="2pt">
            <fo:block text-align="left">
              <xsl:apply-templates select="name"/>
              <xsl:if test="shortName">
                <xsl:text>(</xsl:text><xsl:apply-templates select="shortName"/><xsl:text>)</xsl:text>
              </xsl:if>
            </fo:block>
          </fo:table-cell>
          <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
            <fo:block>-</fo:block>
            <!-- <xsl:apply-templates select="circuitBreakerAlts/circuitBreaker/functionalItemRefGroup"/> -->
          </fo:table-cell>
          <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
            <fo:block>-</fo:block>
            <!-- <xsl:apply-templates select="circuitBreakerRefGroup"/> -->
          </fo:table-cell>
          <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
            <fo:block>
              <xsl:value-of select="circuitBreakerAlts/circuitBreaker/amperage"/>
            </fo:block>
          </fo:table-cell>
          <fo:table-cell padding-left="2pt" padding-right="2pt" text-align="center">
            <fo:block>
              <xsl:apply-templates select="circuitBreakerAlts/circuitBreaker/location/quantity"/>
            </fo:block>
          </fo:table-cell>
        </fo:table-row>
      </xsl:if>

      <xsl:if test="count(circuitBreakerAlts/child::circuitBreaker) > 1">
        <!-- isi di sini jika <circuitBreaker lebih dari satu karena element name atau shortname atau FIN, dll akan menggantikan yang parentnya -->
      </xsl:if>
    </xsl:template>

    <xsl:template match="circuitBreakerIdent">
      <fo:block>
        <xsl:apply-templates select="@circuitBreakerNumber"/>
      </fo:block>
    </xsl:template>

    <xsl:template match="functionalItemRefGroup[parent::circuitBreaker]">
      <fo:block>-</fo:block>
    </xsl:template>

    <xsl:template match="circuitBreakerRefGroup[parent::circuitBreakerSpec]">
      <fo:block>-</fo:block>
    </xsl:template>

    <xsl:template match="functionalPhysicalAreaRef[parent::circuitBreakerSpec]">
      <fo:block>-</fo:block>
    </xsl:template>

    <xsl:template name="circuitBreakerRefGroup">
      <fo:block>-</fo:block>
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