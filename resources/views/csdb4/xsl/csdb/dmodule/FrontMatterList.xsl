<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:template match="frontMatterList">
    <div class="frontMatterList">
      
      <h1 class="title">
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', //identAndStatusSection/dmAddress/descendant::dmTitle)"/>
      </h1>
      <h2 class="issueInfo">
        <xsl:text>Issue </xsl:text><xsl:value-of select="//identAndStatusSection/dmAddress/descendant::dmIdent/issueInfo/@issueNumber"/>
      </h2>
      <xsl:apply-templates select="reducedPara"/>
      <xsl:apply-templates select="frontMatterSubList"/>

      <!-- footnote harus disesuaikan lagi dengan yang PDF -->
      <div class="footnote-container">
        <xsl:apply-templates select="footnote"/>
      </div>
    </div>
  </xsl:template>

  <!-- fm02 LEODM -->
  <xsl:template match="frontMatterSubList[../@frontMatterType = 'fm02']">
    <div class="frontMatterSubList">
      <xsl:if test="title">
        <h1 class="title"><xsl:apply-templates select="title"/></h1>
      </xsl:if>
      <xsl:apply-templates select="reducedPara"/>
      <table class="frontMatterEntryList">
        <thead>
          <tr>
            <th class="title">Title</th>
            <th class="code">DMC/PMC</th>
            <th class="issueType"></th>
            <th class="issueDate-issueNumber">Issue date/no.</th>
            <th class="numberOfPages">No. of pages</th>
            <th class="applicability">Applicable to</th>
            <th class="remarks">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <xsl:apply-templates select="frontMatterDmEntry | frontMatterPmEntry | frontMatterExternalPubEntry"/>
        </tbody>
      </table>
    </div>
  </xsl:template>

  <xsl:template name="issueTypeLEODM">
    <xsl:variable name="issueType"><xsl:value-of select="@issueType"/></xsl:variable>
    <xsl:choose>
      <xsl:when test="$issueType = 'new'">N</xsl:when>
      <xsl:when test="$issueType = 'changed'">C</xsl:when>
      <xsl:otherwise><xsl:value-of select="$issueType"/></xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="frontMatterDmEntry">
    <tr class="frontMatterDmEntry">
      <xsl:call-template name="applicRefId"/>
      <td class="title">
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmTitle', descendant::dmRefAddressItems/dmTitle)"/>
      </td>
      <td class="code">
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmCode', descendant::dmRefIdent/dmCode)"/>
      </td>
      <td class="issueType">
        <xsl:call-template name="issueTypeLEODM"/>
      </td>
      <td class="issueDate issueNumber">
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', descendant::issueDate)"/>, 
        <xsl:value-of select="descendant::issueInfo/@issueNumber"/>
      </td>
      <td class="numberOfPages">
        <xsl:apply-templates select="numberOfPages"/>
      </td>
      <td class="applicability">
        <xsl:call-template name="get_applicability"/>
      </td>
      <td class="remarks">
        <xsl:apply-templates select="footnoteRemarks"/>
      </td>
    </tr>
  </xsl:template>

  <xsl:template match="frontMatterPmEntry">
    <tr class="frontMatterDmEntry">
      <td class="title">
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmTitle', descendant::pmRefAddressItems/pmTitle)"/>
      </td>
      <td class="code">
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmCode', descendant::pmRefIdent/pmCode)"/>
        <!-- ini nanti ditambah identExtension if extended publication module code is used -->
      </td>
      <td class="issueType">
        <xsl:call-template name="issueTypeLEODM"/>
      </td>
      <td class="issueDate issueNumber">
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', descendant::issueDate)"/>, 
        <xsl:value-of select="descendant::issueInfo/@issueNumber"/>
      </td>
      <td class="numberOfPages">
        <xsl:apply-templates select="numberOfPages"/>
      </td>
      <td class="applicability">
        <xsl:call-template name="get_applicability"/>
      </td>
      <td class="remarks">
        <xsl:apply-templates select="footnoteRemarks"/>
      </td>
    </tr>
  </xsl:template>

  <xsl:template match="frontMatterExternalPubEntry">
    <tr class="frontMatterExternalPubEntry">
      <td class="title">
        <xsl:apply-templates select="externalPubRef/externalPubRefIdent/externalPubTitle"/>        
      </td>
      <td class="code">
        <xsl:apply-templates select="externalPubRef/externalPubRefIdent/externalPubCode"/>
      </td>
      <td class="issueType">
        <xsl:call-template name="issueTypeLEODM"/>
      </td>
      <td>
        <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_externalPubIssueDate', descendant::externalPubIssueDate)"/>
      </td>
      <td class="numberOfPages">
        <xsl:apply-templates select="numberOfPages"/>
      </td>
      <td class="applicability">
        <xsl:call-template name="get_applicability"/>
      </td>
      <td class="remarks">
        <xsl:apply-templates select="footnoteRemarks"/>
      </td>
    </tr>
  </xsl:template>

  

</xsl:transform>