<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">


<xsl:template match="pmEntry">
  <div class="pmEntry">
    <hr/>
    <h1>PM Entry <span style="font-size:8pt;font-weight:normal"><xsl:value-of select="@pmEntryType"/></span></h1>
  
    <ol>
      <xsl:apply-templates/>
    </ol>
  </div>
  
</xsl:template>

<xsl:template match="dmRef[ancestor::pm]">
  <xsl:variable name="filename">
    <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', dmRefIdent)"/>
  </xsl:variable>
  <li>
    <table>
      <tr>
        <td><b>Data Module </b></td>
        <td>
          <a>
            <xsl:attribute name="href">
              <xsl:text>/route/get_detail_csdb_object?filename=</xsl:text>
              <xsl:value-of select="$filename"/>
            </xsl:attribute>
            <xsl:attribute name="target">
              <xsl:text>_parent</xsl:text>
            </xsl:attribute>
            <xsl:value-of select="$filename"/>
          </a>
        </td>
      </tr>
    </table>
  </li>
</xsl:template>

<xsl:template match="pmRef[ancestor::pm]">
  <xsl:variable name="filename">
    <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmIdent', pmRefIdent)"/>
  </xsl:variable>
  <li>
    <table>
      <tr>
        <td><b>Publication Module</b></td>
        <td>
          <a>
            <xsl:attribute name="href">
              <xsl:text>/route/get_detail_csdb_object?filename=</xsl:text>
              <xsl:value-of select="$filename"/>
            </xsl:attribute>
            <xsl:attribute name="target">
              <xsl:text>_parent</xsl:text>
            </xsl:attribute>
            <xsl:value-of select="$filename"/>
          </a>
        </td>
      </tr>
    </table>
  </li>
</xsl:template>

<xsl:template match="externalPubRef[ancestor::pm]">
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