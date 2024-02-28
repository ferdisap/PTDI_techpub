<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:param name="filename" />

  <xsl:template match="dmStatus">
    <div class="dmStatus">
      <p>Below the status document: <br />
        <ul>
          <li>Security: <xsl:value-of select="security/@securityClassification" /></li>
          <li>Responsible Company: <xsl:value-of select="responsiblePartnerCompany/enterpriseName" />-<xsl:value-of select="responsiblePartnerCompany/@enterpriseCode" /></li>
          <li>Originator Company: <xsl:value-of select="originator/enterpriseName" />-<xsl:value-of
              select="originator/@enterpriseCode" /></li>
          <li>Applicability Document: <a href="#">
              <xsl:value-of
                select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', applicCrossRefTableRef/descendant::dmRefIdent, null, 'DMC-', '')" />
            </a>
          </li>
          <li>Applicability for: 
            <!-- <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve', /, '', 'getApplicability', 'true')" /> -->
            FOOBAR
          </li>
          <li>BREX Document: <a href="#">
              <xsl:value-of
                select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', brexDmRef/descendant::dmRefIdent, null, 'DMC-', '')" />
            </a>
          </li>
          <li>
            Quality Assurance: <xsl:call-template name="getQualityAssurance" select="/"/>
          </li>
          <li>
            Remarks: <xsl:call-template name="getRemarks" select="/"/>
          </li>
        </ul>
      </p>
    </div>
  </xsl:template>

  <xsl:template name="getQualityAssurance">
    <xsl:for-each select="qualityAssurance">
      <span>
        <xsl:value-of select="@applicRefId" />
      </span>
      <xsl:text>|</xsl:text>
      <span>
        <xsl:value-of select="name(child::*)" />
      </span>
      <xsl:text>|</xsl:text>
      <span>
        <xsl:value-of select="child::*/@verificationType" />
      </span>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="getRemarks">
    <xsl:variable name="remarks">
      <xsl:for-each select="remarks/simplePara">
          <xsl:value-of select="string(.)"/>
          <xsl:text>\r\n</xsl:text>
        </xsl:for-each>
      </xsl:variable>
      <xsl:value-of select="php:function('trim', $remarks, '\n\r')"/>    
  </xsl:template>

</xsl:transform>