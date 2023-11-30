<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:param name="absolute_path_csdb_input"/>

<xsl:template match="pmEntry">
  <div class="pmEntry">
    <hr/>
    <h1>PM Entry <span style="font-size:8pt;font-weight:normal"><xsl:value-of select="@pmEntryType"/></span></h1>
  
    <ol>
      <xsl:apply-templates/>
    </ol>
  </div>
  
</xsl:template>

<xsl:template match="dmRef">
  <li>
    <table>
      <tr>
        <td><b>Data Module </b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', dmRefIdent)"/></td>
      </tr>
    </table>
  </li>
</xsl:template>

<xsl:template match="pmRef">
  <li>
    <table>
      <tr>
        <td><b>Publication Module</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmIdent', pmRefIdent)"/></td>
      </tr>
    </table>
  </li>
</xsl:template>

<xsl:template match="externalPubRef">
  <li>
    <table>
      <tr>
        <td><b>External Publication</b></td>
        <td>function are not ready</td>
      </tr>
    </table>
  </li>
</xsl:template>

</xsl:transform>