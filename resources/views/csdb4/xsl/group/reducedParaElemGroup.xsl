<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
   
  <!-- <xsl:include href="part/caption.xsl"/> -->
  <xsl:output method="xml"/>

  <xsl:template match="reducedPara">
    <p class="reducedPara">
      <xsl:apply-templates/>
    </p>
  </xsl:template>

  <xsl:template match="reducedRandomList">
    <ul>
      <xsl:call-template name="cgmark"/>
      <xsl:for-each select="reducedRandomListItem">
        <li>
          <xsl:apply-templates/>
        </li>
      </xsl:for-each>
    </ul>         
  </xsl:template> 



</xsl:stylesheet>