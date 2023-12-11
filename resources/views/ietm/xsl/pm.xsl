<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php xsi">

  <xsl:output method="html" />

  <xsl:template match="/">
    <div class="pm">
      <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="identAndStatusSection">
    <div class="identAndStatusSection">
      <div>Schema: <xsl:value-of
          select="php:function('Ptdi\Mpub\CSDB::getSchemaUsed', /,'filename')" /></div>

      <h1>IDENTIFICATION AND STATUS SECTION</h1>

      <table>
        <!-- pmAddress -->
        <tr>
          <td>
            <b>DM Code: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_pmCode', //pmAddress/descendant::pmCode)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Issue Number: </b>
          </td>
          <td>
            <xsl:value-of select="//pmAddress/descendant::issueInfo/@issueNumber" />
          </td>
        </tr>
        <tr>
          <td>
            <b>InWork Number: </b>
          </td>
          <td>
            <xsl:value-of select="//pmAddress/descendant::issueInfo/@inWork" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Issue Date: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', //pmAddress/descendant::issueDate)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Title: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_pmTitle', //pmAddress/descendant::pmTitle)" />
          </td>
        </tr>

        <!-- pmStatus -->
        <tr>
          <td>
            <b>Security Classification: </b>
          </td>
          <td>
            <xsl:value-of select="pmStatus/security/@securityClassification" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Responsible Partner Company: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_responsiblePartnerCompany', pmStatus/responsiblePartnerCompany, 'both')" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Originator: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_responsiblePartnerCompany', pmStatus/originator, 'both')" />
          </td>
        </tr>
        <tr>
          <td>
            <b>ACT: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', pmStatus/applicCrossRefTableRef/descendant::dmRefIdent)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Applicability: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve', /, '', 'getApplicability', 'true')" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Brex DM Ref: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', pmStatus/brexDmRef/descendant::dmRefIdent)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Quality Assurance</b>
          </td>
          <td>
            <xsl:for-each select="pmStatus/qualityAssurance">
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
          </td>
        </tr>
      </table>

    </div>

  </xsl:template>


</xsl:transform>