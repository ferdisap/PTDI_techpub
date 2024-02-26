<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:output method="html" media-type="text/html" omit-xml-declaration="yes" />

  <xsl:include href="./dmodule/FrontMatter.xsl" />

  <xsl:template match="content[ancestor::dmodule]">
    <div class="csdbobjectcontent">
      <xsl:apply-templates/>      
    </div>
  </xsl:template>

</xsl:transform>