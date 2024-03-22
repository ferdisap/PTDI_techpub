<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml"/>

  <xsl:param name="icnPath" select="'/images/'"/> <!-- defaultnya nanti ditentukan oleh controller -->

  <xsl:include href="./dmodule.xsl"/>
  <xsl:include href="./pageSequence/body.xsl"/>
  <xsl:include href="./pageSequence/header.xsl"/>
  <xsl:include href="./pageSequence/footer.xsl"/>

  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>
</xsl:transform>