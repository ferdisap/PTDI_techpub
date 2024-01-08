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
              <th>Object Path</th>
              <th>Object Use</th>
              <th>Object Value</th>
              <th>Allowable Flag</th>
              <th>BR Decision Ref</th>
            </tr>
          </thead>
          <tbody>
            <xsl:for-each select="//structureObjectRuleGroup/structureObjectRule">
              <tr>
                <td><code><xsl:value-of select="objectPath"/></code></td>
                <td><xsl:value-of select="objectUse"/></td>
                <td>
                  <xsl:for-each select="objectValue">
                    <xsl:if test="@valueAllowed">
                      <code><xsl:value-of select="@valueAllowed"/></code> - &#160;
                    </xsl:if>
                    <xsl:apply-templates/>
                    <br/>
                  </xsl:for-each>
                </td>
                <td><xsl:value-of select="objectPath/@allowedObjectFlag"/></td>
                <td>
                  <xsl:for-each select="brDecisionRef">
                    <xsl:value-of select="@brDecisionIdentNumber"/>;&#160;
                  </xsl:for-each>
                </td>
              </tr>
            </xsl:for-each>
          </tbody>
        </table>
      </div>
    </div>
  </xsl:template>
</xsl:stylesheet>