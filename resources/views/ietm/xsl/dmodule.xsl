<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<!-- <xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" exclude-result-prefixes="php xsi"> -->

  <xsl:output method="html" media-type="text/html"/>

  <xsl:include href="./component/content.xsl"/>

  <xsl:param name="repoName"/>
  <xsl:param name="objectpath"/>
  <xsl:param name="absolute_objectpath"/>

  <xsl:template match="/">
    <div class="dmodule">
      <xsl:apply-templates select="//identAndStatusSection" />
      <xsl:apply-templates select="//content" />
    </div>
  </xsl:template>

  <xsl:template match="identAndStatusSection">
    <div class="identAndStatusSection" v-show="store.showIdentSection">
      <div class="schema">Schema: <xsl:value-of
          select="php:function('Ptdi\Mpub\CSDB::getSchemaUsed', /,'filename')" /></div>

      <h1 class="header">IDENTIFICATION AND STATUS SECTION</h1>

      <table>
        <!-- dmAddress -->
        <tr>
          <td>
            <b>DM Code: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_dmCode', //dmAddress/descendant::dmCode)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Issue Number: </b>
          </td>
          <td>
            <xsl:value-of select="//dmAddress/descendant::issueInfo/@issueNumber" />
          </td>
        </tr>
        <tr>
          <td>
            <b>InWork Number: </b>
          </td>
          <td>
            <xsl:value-of select="//dmAddress/descendant::issueInfo/@inWork" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Issue Date: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', //dmAddress/descendant::issueDate)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Title: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', //dmAddress/descendant::dmTitle)" />
          </td>
        </tr>

        <!-- dmStatus -->
        <tr>
          <td>
            <b>Security Classification: </b>
          </td>
          <td>
            <xsl:value-of select="dmStatus/security/@securityClassification" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Responsible Partner Company: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_responsiblePartnerCompany', dmStatus/responsiblePartnerCompany, 'both')" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Originator: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_responsiblePartnerCompany', dmStatus/originator, 'both')" />
          </td>
        </tr>
        <tr>
          <td>
            <b>ACT: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', dmStatus/applicCrossRefTableRef/descendant::dmRefIdent)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Applicability: </b>
          </td>
          <td>
            <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve', /, '', 'getApplicability', 'true')" />
            <!-- <xsl:value-of select="php:function('App\Models\Csdb::getApplicability')" /> -->
          </td>
        </tr>
        <tr>
          <td>
            <b>Brex DM Ref: </b>
          </td>
          <td>
            <xsl:value-of
              select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', dmStatus/brexDmRef/descendant::dmRefIdent)" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Quality Assurance</b>
          </td>
          <td>
            <xsl:for-each select="dmStatus/qualityAssurance">
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
        <tr>
          <td><b>Remakrs:</b></td>
          <td>
            <xsl:variable name="remarks">
              <xsl:for-each select="//dmStatus/remarks/simplePara">
                <xsl:value-of select="string(.)"/>
                <xsl:text>\r\n</xsl:text>
              </xsl:for-each>
            </xsl:variable>
            <xsl:value-of select="php:function('trim', $remarks, '\n\r')"/>            
          </td>
        </tr>
      </table>

    </div>

  </xsl:template>


</xsl:transform>