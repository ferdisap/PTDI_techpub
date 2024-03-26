<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="dmodule">
    <fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format" font-family="Tahoma" xmlns:fox="http://xmlgraphics.apache.org/fop/extensions">
      <fo:layout-master-set>
        <fo:simple-page-master master-name="default-A4" page-height="29.7cm" page-width="21cm" margin-top="1cm" margin-bottom="1cm" margin-left="2cm" margin-right="2cm">
          <!-- kalau tidak ditulis extent, bisa muncul warning di terminal, fo:region-before on page 1 exceed the available area in the block-progression direction by 42519 millipoints. (See position 1:677) -->
          <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.0cm"/>
          <fo:region-before region-name="header" extent="1.5cm"/> 
          <fo:region-after region-name="footer" extent="2.0cm"/>
        </fo:simple-page-master>
      </fo:layout-master-set>
      <!-- 
        Untuk Publication Module:
          1. layout-master-set akan ditaruh diluar file dmodule.xsl atau pm.xsl
          2. setiap 'pt' (pmType) harus memiliki satu (simple-)page-master, untuk mengakomodir setiap pmEntry
          3. setiap referensi pmEntry/dmRef memiliki satu page-sequence sehingga page-number bisa berbeda2 setiap data module
          4. setiap referensi pmEntry/pmRef akan membuat mengulangi proses dari step 2.
          5. setiap referensi pmEntry/pmEntry/..(pmEntry), akan membuat proses yang sama juga, tapi tidak membuat page-master lagi
          6. setiap referensi pmEntry/externalPubRef, akan membuat halaman kosong satu/2 lembar berisi text link ke external pub dokument tersebut 
          7. usahakan referensi pmEntry dikelompokkan sesuai 'pmt' agar kedepannya bisa membuat style sesuai attribute 'pmt' nya
       -->
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
      
      <!-- <fo:page-sequence master-reference="default-A4" initial-page-number="1" id="foo">
        <fo:static-content flow-name="header">
          <xsl:call-template name="header"/>
        </fo:static-content>
        <fo:static-content flow-name="footer">
          <fo:block text-align="center"> Page <fo:page-number/> of <fo:page-number-citation-last ref-id="foo"/></fo:block>
        </fo:static-content>
        <fo:flow flow-name="body">
          <xsl:call-template name="body"/>
        </fo:flow>
      </fo:page-sequence> -->

      <!-- belum bisa digunakan untuk multiple page pdf external/images, ref apache.org FOP extension -->
      <!-- <xsl:variable name="infoEntityPath"> -->
        <!-- <xsl:text>url('</xsl:text> -->
        <!-- <xsl:text>file:///D:/Temporary/tesimage.png</xsl:text> -->
        <!-- <xsl:text>file:\\\D:\Temporary\tesimage.png</xsl:text> -->
        <!-- <xsl:value-of select="unparsed-entity-uri(//dmStatus/logo/symbol/@infoEntityIdent)"/> -->
        <!-- <xsl:text>file:///D:/Temporary/tespdf.pdf</xsl:text> -->
        <!-- <xsl:text>file:\\\D:\Temporary/tespdf.pdf</xsl:text> -->
        <!-- <xsl:text>')</xsl:text> -->
      <!-- </xsl:variable> -->
      <!-- <fox:external-document src="{$infoEntityPath}"/> -->
    </fo:root>
  </xsl:template>
</xsl:transform>