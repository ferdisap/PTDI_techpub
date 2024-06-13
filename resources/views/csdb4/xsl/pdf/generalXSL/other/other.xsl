<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">

<xsl:template name="get_logo">
  <xsl:variable name="infoEntityPath">
    <xsl:text>url('</xsl:text>
    <!-- <xsl:text>file:///D:/Temporary/tesimage.png</xsl:text> -->
    <!-- <xsl:text>file:\\\D:\Temporary\tesimage.png</xsl:text> -->
    <!-- <xsl:text>file:\\\D:\Temporary/tesimage.png</xsl:text> -->
    <xsl:value-of select="unparsed-entity-uri(//dmStatus/logo/symbol/@infoEntityIdent)"/>
    <xsl:text>')</xsl:text>
  </xsl:variable>
  
  <fo:block>
    <fo:external-graphic src="{$infoEntityPath}" content-width="scale-to-fit" width="2.5cm"/>
  </fo:block>
</xsl:template>

</xsl:stylesheet>