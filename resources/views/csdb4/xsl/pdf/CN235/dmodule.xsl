<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="dmodule">
    <fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format" font-family="Tahoma">
      <fo:layout-master-set>
        <fo:simple-page-master master-name="default-A4" page-height="29.7cm" page-width="21cm" margin-top="1cm" margin-bottom="1cm" margin-left="2cm" margin-right="2cm">
          <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="1.5cm"/>
          <fo:region-before region-name="header"/>
          <fo:region-after region-name="footer" extent="1.5cm"/>
        </fo:simple-page-master>
      </fo:layout-master-set>
      <fo:page-sequence master-reference="default-A4">
        <fo:static-content flow-name="header">
          <xsl:call-template name="header"/>
        </fo:static-content>
        <fo:static-content flow-name="footer">
          <xsl:call-template name="footer"/>
        </fo:static-content>
        <fo:flow flow-name="body">
          <xsl:call-template name="body"/>
        </fo:flow>
      </fo:page-sequence>
    </fo:root>
  </xsl:template>
</xsl:transform>