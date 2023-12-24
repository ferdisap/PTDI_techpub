<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:template match="frontMatter">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="frontMatterTitlePage">
  <div class="frontMatterTitlePage">
    <h1 class="text-center font-bold text-2xl mb-3"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', //identAndStatusSection/dmAddress/dmAddressItems/dmTitle)"/></h1>
    <table>
      <tr>
        <td><b>Product Intro Name</b></td>
        <td><xsl:value-of select="productIntroName/name"/></td>
      </tr>
      <tr>
        <td><b>Publication Module Title</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmTitle', pmTitle, shortPmTitle)"/></td>
      </tr>
      <tr>
        <td><b>Publication  Module Code</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmCode', pmCode)"/></td>
      </tr>
      <tr>
        <td><b>Issue Info</b></td>
        <td>Issue number: <xsl:value-of select="issueInfo/@issueNumber"/>, Inwork number: <xsl:value-of select="issueInfo/@inWork"/></td>
      </tr>
      <tr>
        <td><b>Issue Date</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', issueDate)"/></td>
      </tr>
      <tr>
        <td colspan="2">
          <xsl:for-each select="productIllustration/graphic">
          <img>
            <xsl:attribute name="src">
              <xsl:value-of select="$objectpath"/>
              <xsl:text>/</xsl:text>
              <xsl:value-of select="$repoName"/>
              <xsl:text>/</xsl:text>
              <xsl:value-of select="@infoEntityIdent"/>
            </xsl:attribute>
            <!-- <xsl:attribute name="src">
              <xsl:text>/api/ietm/</xsl:text>
              <xsl:value-of select="$repoName"/>
              <xsl:text>/</xsl:text>
              <xsl:value-of select="@infoEntityIdent"/>
            </xsl:attribute> -->
          </img>
          <br/>
          </xsl:for-each>
        </td>
      </tr>
      <tr>
        <td><b>Security Classification</b></td>
        <td><xsl:value-of select="security/@securityClassification"/></td>
      </tr>
      <tr>
        <td><b>Product Illustration</b></td>
        <td>
          <xsl:for-each select="productIllustration/graphic">
            <span><xsl:value-of select="@infoEntityIdent"/></span><br/>
          </xsl:for-each>
        </td>
      </tr>
      <tr>
        <td><b>Responsible Partner Company</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_responsiblePartnerCompany', responsiblePartnerCompany, 'both')"/></td>
      </tr>
      <tr>
        <td><b>Front Matter Info</b></td>
        <td>Type: <xsl:value-of select="frontMatterInfo/@frontMatterInfoType"/></td>
      </tr>
      
    </table>
  </div>
</xsl:template>

<xsl:template match="frontMatterList">
   <div>
    Issue Number: <xsl:value-of select="issueInfo/@issueNumber"/><xsl:text> | </xsl:text>
    Inwork Number: <xsl:value-of select="issueInfo/@inWork"/>
  </div>
  <div>
    Issue Date: <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', issueDate)"/>
  </div>
  <xsl:apply-templates select="frontMatterSubList"/>
</xsl:template>

<xsl:template match="frontMatterSubList">
  <div class="frontMatterSubList">
    <h1 style="text-align:center"><xsl:value-of select="title"/></h1>
    <table>
      <tr>
        <th>DM Code</th>
        <th>Issue Date</th>
        <th>Issue Type</th>
        <th>Title</th>
        <th>Applicability</th>
      </tr>
      <xsl:apply-templates select="frontMatterDmEntry"/>
    </table>
  </div>
</xsl:template>

<xsl:template match="frontMatterDmEntry">
  <xsl:variable name="filename" select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', descendant::dmRefIdent)"/>
  <tr>
    <td class="dmCode">
      <router-link>
        <xsl:attribute name="to">
          <xsl:value-of select="$filename"/>
        </xsl:attribute>
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmCode', descendant::dmCode)"/>
      </router-link>
      <!-- <a href="javascript:void(0)">
        <xsl:attribute name="onclick">
          <xsl:variable name="fn">(this.__vueParentComponent.ctx.$parent.link('<xsl:value-of select="$filename"/>'))</xsl:variable>
          <xsl:value-of select="$fn"/>
        </xsl:attribute>
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmCode', descendant::dmCode)"/>
      </a> -->
    </td>
    <td class="issueDate"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve', /, $filename, 'resolve_issueDate')"/></td>
    <td class="issueType"><xsl:value-of select="@issueType"/></td>
    <td class="title"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve', /, $filename, 'resolve_dmTitle')"/></td>
    <td class="applicability"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve', /, $filename, 'getApplicability')"/></td>
  </tr>
</xsl:template>

<xsl:template match="frontMatterPmEntry">
  <div class="frontMatterPmEntry">
    <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmIdent', descendant::pmRefIdent)"/>
  </div>
</xsl:template>

<xsl:template match="frontMatterExternalPubEntry">
  <div class="frontMatterExternalPubEntry">
    <span><xsl:value-of select="externalPubRef/externalPubTitle"/></span><br/>
    <span><xsl:value-of select="externalPubRef/externalPubCode"/></span><br/>
  </div>
</xsl:template>

</xsl:transform>