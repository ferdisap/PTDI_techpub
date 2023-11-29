<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:template match="frontMatter">
  <hr/>
  <h1>FRONT MATTER</h1>
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="frontMatterTitlePage">
  <div class="frontMatterTitlePage">
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

</xsl:transform>