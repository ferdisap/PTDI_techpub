<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:fox="http://xmlgraphics.apache.org/fop/extensions">

  <!-- 
    pt01 = component maintenance
    pt02 = ipd
    pt03 = service bulletin,
    sementara baru pt03 dulu
  -->
  <xsl:template name="pageMasterByPt">
    <xsl:call-template name="masterName"/>
    <xsl:choose>
      <xsl:when test="$masterName = 'pt03'">
        <fo:layout-master-set>
          <fo:simple-page-master master-name="{$masterName}" page-height="29.7cm" page-width="21cm" margin-top="1cm" margin-bottom="1cm" margin-right="2cm">
            <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.0cm"/>
            <fo:region-before region-name="header" extent="1.5cm"/> 
            <fo:region-after region-name="footer" extent="2.0cm"/>
          </fo:simple-page-master>
        </fo:layout-master-set>
      </xsl:when>
      <xsl:otherwise>
        <fo:layout-master-set>
          <fo:simple-page-master master-name="default-A4" page-height="29.7cm" page-width="21cm" margin-top="1cm" margin-bottom="1cm" margin-right="2cm">
            <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.0cm"/>
            <fo:region-before region-name="header" extent="1.5cm"/> 
            <fo:region-after region-name="footer" extent="2.0cm"/>
          </fo:simple-page-master>
        </fo:layout-master-set>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>


</xsl:transform>
