<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/">

<xsl:output method="html" media-type="text/html" omit-xml-declaration="yes"/>

<xsl:param name="filename"/>

<xsl:template match="dml">
  <!-- <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <title>Data Management List</title>
    </head>
    <body>
    </body>
  </html> -->
  <xsl:apply-templates select="identAndStatusSection"/>
  <xsl:apply-templates select="dmlContent"/>
</xsl:template>

<xsl:template match="identAndStatusSection">
  <div class="identAndStatusSection">
    <h1>IDENTIFICATION AND STATUS SECTION</h1>
    <div>Filename: <xsl:value-of select="$filename"/></div>

    <table>
      <!-- dmlAddress -->
      <tr>
        <td><b>DML Code:</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmlCode', //dmlCode)"/></td>
      </tr>
      <tr>
        <td><b>Issue Number:</b></td>
        <td><xsl:value-of select="//dmlIdent/issueInfo/@issueNumber"/></td>
      </tr>
      <tr>
        <td><b>InWork Number:</b></td>
        <td><xsl:value-of select="//dmlIdent/issueInfo/@inWork"/></td>
      </tr>
      <tr>
        <td><b>Issue Date:</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', //issueDate)"/></td>
      </tr>
    
      <!-- dmlStatus -->
      <tr>
        <td><b>Security Classification:</b></td>
        <td><xsl:value-of select="dmlStatus/security/@securityClassification"/></td>
      </tr>
      <tr>
        <td><b>Brex DM Ref:</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', //dmlStatus/descendant::brexDmRef/dmRef/dmRefIdent)"/></td>
      </tr>
      <tr>
        <td><b>Remarks</b>
        </td>
        <td>
          <xsl:for-each select="//dmlStatus/remarks/simplePara">
            <span><xsl:value-of select="."/></span> <br/>
          </xsl:for-each>
        </td>
      </tr>
    </table>
  </div>

</xsl:template>

<xsl:template match="dmlContent">
  <hr/>
  <h1>DML CONTENT</h1>

  <table>
    <tr>
      <th> Ident Code <Sort v-bind:function="sort.bind(this)"/> </th>
      <th> Issue Type <Sort v-bind:function="sort.bind(this)"/> </th>
      <th> Security <Sort v-bind:function="sort.bind(this)"/> </th>
      <th> Resposible Company <Sort v-bind:function="sort.bind(this)"/> </th>
      <th> Answer <Sort v-bind:function="sort.bind(this)"/> </th>
      <th> Remarks <Sort v-bind:function="sort.bind(this)"/> </th>
    </tr>
    <xsl:for-each select="dmlEntry">
      <tr>
        <td>
          <xsl:apply-templates select="dmRef | pmRef | infoEntityRef | commentRef | dmlRef"/>
        </td>
        <td>
          <xsl:value-of select="@issueType"/>
        </td>
        <td>
          <xsl:value-of select="security/@securityClassification"/>
        </td>
        <td>
          <xsl:value-of select="responsiblePartnerCompany/enterpriseName"/>
          (<xsl:value-of select="responsiblePartnerCompany/@enterpriseCode"/>)
        </td>
        <td>-</td>
        <td>-</td>
      </tr>
    </xsl:for-each>  
  </table>
</xsl:template>

<xsl:template match="dmRef">
  <span><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', dmRefIdent, null, 'DMC-', '')"/></span>
</xsl:template>

<xsl:template match="pmRef">
  <span><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmIdent', pmRefIdent, null, 'PMC-', '')"/></span>
</xsl:template>

<xsl:template match="infoEntityRef">
  <span>
    <xsl:value-of select="@infoEntityRefIdent"/>
  </span>
</xsl:template>

<xsl:template match="commentRef">
  <span>Belum ada fungsi untuk resolve commentRefIdent</span>
</xsl:template>

<xsl:template match="dmlRef">
  <span>Belum ada fungsi untuk resolve dmlRefIdent</span>
</xsl:template>


</xsl:transform>