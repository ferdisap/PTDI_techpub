<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" omit-xml-declaration="yes"/>

  <xsl:template match="/">
  <div>
    <div class="d-flex justify-content-center flex-column container mt-3">
      <p><xsl:value-of select="//commonInfo"/></p>
      <table>
        <thead>
          <tr>
            <th>System</th>
            <th>SubSystem</th>
            <th>SubSubSystem</th>
            <th>Unit/assembly</th>
          </tr>
        </thead>
        <tbody>
          <xsl:for-each select="//snsSystem">
            <tr>
              <td><xsl:value-of select="snsCode"/> - <xsl:value-of select="snsTitle"/></td>
              <td>
                <xsl:for-each select="snsSubSystem">
                  <xsl:value-of select="snsCode"/> - <xsl:value-of select="snsTitle"/><br/>
                </xsl:for-each>
              </td>
              <td>
                <xsl:for-each select="snsSubSystem/snsSubSubSystem">
                  <xsl:value-of select="snsCode"/> - <xsl:value-of select="snsTitle"/><br/>
                </xsl:for-each>
              </td>
              <td></td>
            </tr>
          </xsl:for-each>

        </tbody>
      </table>
    </div>
  </div>
  </xsl:template>
</xsl:stylesheet>