<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:param name="filename" />

  <xsl:template match="dataRestrictions">
    <div class="dataRestriction">
      <xsl:apply-templates select="restrictionInfo"/>
      <xsl:apply-templates select="restrictionInstructions"/>
    </div>
  </xsl:template>

  <xsl:template match="restrictionInfo">
    <div class="restrictionInfo">
      <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="copyright">
    <div class="copyright">
      <p class="copyrightPara">
        <xsl:apply-templates/>
      </p>
    </div>
  </xsl:template>

  <xsl:template match="policyStatement">
    <p class="policyStatement">
      <xsl:call-template name="cgmark"/>
      <xsl:call-template name="id"/>
      <xsl:call-template name="sc"/>
      <xsl:apply-template/>
    </p>
  </xsl:template>

  <xsl:template match="dataConds">
    <p class="dataConds">
      <xsl:call-template name="cgmark"/>
      <xsl:call-template name="id"/>
      <xsl:call-template name="sc"/>
      <xsl:apply-template/>
    </p>
  </xsl:template>

</xsl:transform>