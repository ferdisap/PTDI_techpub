<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:include href="./frontMatter.xsl"/>
<xsl:include href="./pmEntry.xsl"/>


<xsl:template match="content">
  <xsl:apply-templates/>
</xsl:template>

</xsl:transform>