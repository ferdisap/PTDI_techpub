<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:fox="http://xmlgraphics.apache.org/fop/extensions" xmlns:php="http://php.net/xsl">

  <!-- 
    pt01 = component maintenance
    pt02 = ipd
    pt03 = service bulletin,
    sementara baru pt03 dulu
  -->

  <xsl:template name="pageMasterByPt">
    <xsl:param name="masterName"/>
    
    <fo:layout-master-set>
      <!-- jika not(pm/@pt) maka akan pakai default-pm, see Main.xsl -->
      
      <!-- 
        Jadi, semua page-sequence-master dan simple-page-master akan dikumpulkan/ditulis semua di xsl.fo.
        Ini akan membuat file xsl.fo menjadi besar, tapi belum ada solusinya.
      -->

      <!-- masterName: default-pm -->
      <xsl:call-template name="get_simplePageMaster"/>
      <xsl:call-template name="get_pageSequenceMaster">
        <xsl:with-param name="masterName" select="$masterName"/>
      </xsl:call-template>
      <!-- masterName: default-pm01 -->
      <!-- masterName: default-pm02 -->
      <!-- masterName: default-pm03 -->
      <xsl:call-template name="get_simplePageMaster">
        <xsl:with-param name="width" select="'10.5cm'"/>
        <xsl:with-param name="height" select="'14.85cm'"/>
        
        <xsl:with-param name="masterName_for_odd">oddA5</xsl:with-param>
        <xsl:with-param name="marginTop_for_odd">1cm</xsl:with-param>
        <xsl:with-param name="marginBottom_for_odd">1cm</xsl:with-param>
        <xsl:with-param name="marginLeft_for_odd">2cm</xsl:with-param>
        <xsl:with-param name="marginRight_for_odd">1cm</xsl:with-param>
        <xsl:with-param name="marginTop_for_odd_body">1.5cm</xsl:with-param>
        <xsl:with-param name="marginBottom_for_odd_body">1.2cm</xsl:with-param>
        <xsl:with-param name="extent_for_odd_header">1.5cm</xsl:with-param>
        <xsl:with-param name="extent_for_odd_footer">1.5cm</xsl:with-param>

        <xsl:with-param name="masterName_for_even">evenA5</xsl:with-param>
        <xsl:with-param name="marginTop_for_even">1cm</xsl:with-param>
        <xsl:with-param name="marginBottom_for_even">1cm</xsl:with-param>
        <xsl:with-param name="marginLeft_for_even">1cm</xsl:with-param>
        <xsl:with-param name="marginRight_for_even">2cm</xsl:with-param>
        <xsl:with-param name="marginTop_for_even_body">1.5cm</xsl:with-param>
        <xsl:with-param name="marginBottom_for_even_body">1.3cm</xsl:with-param>
        <xsl:with-param name="extent_for_even_header">1.5cm</xsl:with-param>
        <xsl:with-param name="extent_for_even_footer">1.5cm</xsl:with-param>

        <xsl:with-param name="masterName_for_leftBlank">left-blankA5</xsl:with-param>
        <xsl:with-param name="marginTop_for_leftBlank">1cm</xsl:with-param>
        <xsl:with-param name="marginBottom_for_leftBlank">1cm</xsl:with-param>
        <xsl:with-param name="marginLeft_for_leftBlank">1cm</xsl:with-param>
        <xsl:with-param name="marginRight_for_leftBlank">2cm</xsl:with-param>
        <xsl:with-param name="marginTop_for_leftBlank_body">1.5cm</xsl:with-param>
        <xsl:with-param name="marginBottom_for_leftBlank_body">1.5cm</xsl:with-param>
        <xsl:with-param name="extent_for_leftBlank_header">1.5cm</xsl:with-param>
        <xsl:with-param name="extent_for_leftBlank_footer">1.5cm</xsl:with-param>
      </xsl:call-template>
      <xsl:call-template name="get_pageSequenceMaster">
        <xsl:with-param name="masterName" select="'poh'"/>
        <xsl:with-param name="odd_masterReference">oddA5</xsl:with-param>
        <xsl:with-param name="even_masterReference">evenA5</xsl:with-param>
        <xsl:with-param name="leftBlank_masterReference">left-blankA5</xsl:with-param>
      </xsl:call-template>
      <!-- masterName: default-pm04 -->
      <!-- masterName: dan seterusnya -->
      
    </fo:layout-master-set>
  </xsl:template>

  <xsl:template name="get_simplePageMaster">
    <!-- setelan defaultnya sama kayak default-A4.xsl -->
    <xsl:param name="width" select="$width"/>
    <xsl:param name="height" select="$height"/>

    <xsl:param name="masterName_for_odd">oddA4</xsl:param> <!-- ini dipakai untuk simple-page-master@master-name, lihat dibawah -->
    <xsl:param name="marginTop_for_odd">1cm</xsl:param>
    <xsl:param name="marginBottom_for_odd">1cm</xsl:param>
    <xsl:param name="marginLeft_for_odd">3cm</xsl:param>
    <xsl:param name="marginRight_for_odd">1.5cm</xsl:param>
    <xsl:param name="marginTop_for_odd_body">1.5cm</xsl:param>
    <xsl:param name="marginBottom_for_odd_body">1.7cm</xsl:param>
    <xsl:param name="extent_for_odd_header">1.5cm</xsl:param>
    <xsl:param name="extent_for_odd_footer">1.5cm</xsl:param>

    <xsl:param name="masterName_for_even">evenA4</xsl:param> <!-- ini dipakai untuk simple-page-master@master-name, lihat dibawah -->
    <xsl:param name="marginTop_for_even">1cm</xsl:param>
    <xsl:param name="marginBottom_for_even">1cm</xsl:param>
    <xsl:param name="marginLeft_for_even">1.5cm</xsl:param>
    <xsl:param name="marginRight_for_even">3cm</xsl:param>
    <xsl:param name="marginTop_for_even_body">1.5cm</xsl:param>
    <xsl:param name="marginBottom_for_even_body">1.7cm</xsl:param>
    <xsl:param name="extent_for_even_header">1.5cm</xsl:param>
    <xsl:param name="extent_for_even_footer">1.5cm</xsl:param>

    <xsl:param name="masterName_for_leftBlank">left-blankA4</xsl:param> <!-- ini dipakai untuk simple-page-master@master-name, lihat dibawah -->
    <xsl:param name="marginTop_for_leftBlank">1cm</xsl:param>
    <xsl:param name="marginBottom_for_leftBlank">1cm</xsl:param>
    <xsl:param name="marginLeft_for_leftBlank">1.5cm</xsl:param>
    <xsl:param name="marginRight_for_leftBlank">3cm</xsl:param>
    <xsl:param name="marginTop_for_leftBlank_body">1.5cm</xsl:param>
    <xsl:param name="marginBottom_for_leftBlank_body">2cm</xsl:param>
    <xsl:param name="extent_for_leftBlank_header">1.5cm</xsl:param>
    <xsl:param name="extent_for_leftBlank_footer">2cm</xsl:param>

    <!-- 
      jika berbeda pm/@pt, terlebih beda kertas/orientasi/width-height, region.xsl, pm.xsl, dmodule.xsl harus ditambahkan agar flow-name nya sesuai dengan region-name dan master-name
     -->
    <xsl:param name="regionName_for_body">body</xsl:param> <!-- ini akan di panggil di page-sequence, see pm.xsl or dmodule.xsl -->
    <xsl:param name="regionName_for_bodyLeftBlank">left_blank</xsl:param> <!-- sejauh ini, ini belum digunakan karena tulisan 'intentionally left blank' akan ditulis di static-content header, see region.xsl -->
    <xsl:param name="regionName_for_headerOdd">header-odd</xsl:param> <!-- ini dipanggil di static-content, see region.xsl -->
    <xsl:param name="regionName_for_footerOdd">footer-odd</xsl:param> <!-- ini dipanggil di static-content, see region.xsl -->
    <xsl:param name="regionName_for_headerEven">header-even</xsl:param> <!-- ini dipanggil di static-content, see region.xsl -->
    <xsl:param name="regionName_for_footerEven">footer-even</xsl:param> <!-- ini dipanggil di static-content, see region.xsl -->
    <xsl:param name="regionName_for_headerLeftBlank">header-left_blank</xsl:param> <!-- ini dipanggil di static-content, see region.xsl -->
    <xsl:param name="regionName_for_footerLeftBlank">footer-left_blank</xsl:param> <!-- ini dipanggil di static-content, see region.xsl -->

    <!-- nyontek ke default-A4.xsl -->
    <fo:simple-page-master master-name="{$masterName_for_odd}" page-height="{$height}" page-width="{$width}" margin-top="{$marginTop_for_odd}" margin-bottom="{$marginBottom_for_odd}" margin-left="{$marginLeft_for_odd}" margin-right="{$marginRight_for_odd}">
      <fo:region-body region-name="{$regionName_for_body}" margin-top="{$marginTop_for_odd_body}" margin-bottom="{$marginBottom_for_odd_body}"/>
      <fo:region-before region-name="{$regionName_for_headerOdd}" extent="{$extent_for_odd_header}" />
      <fo:region-after region-name="{$regionName_for_footerOdd}" extent="{$extent_for_odd_footer}" />
    </fo:simple-page-master>
    <fo:simple-page-master master-name="{$masterName_for_even}" page-height="{$height}" page-width="{$width}" margin-top="{$marginTop_for_even}" margin-bottom="{$marginBottom_for_even}" margin-left="{$marginLeft_for_even}" margin-right="{$marginRight_for_even}">
      <fo:region-body region-name="{$regionName_for_body}" margin-top="{$marginTop_for_even_body}" margin-bottom="{$marginBottom_for_even_body}"/>
      <fo:region-before region-name="{$regionName_for_headerEven}" extent="{$extent_for_even_header}"/>
      <fo:region-after region-name="{$regionName_for_footerEven}" extent="{$extent_for_even_footer}"/>
    </fo:simple-page-master>
    <fo:simple-page-master master-name="{$masterName_for_leftBlank}" page-height="{$height}" page-width="{$width}" margin-top="{$marginTop_for_leftBlank}" margin-bottom="{$marginBottom_for_leftBlank}" margin-left="{$marginLeft_for_leftBlank}" margin-right="{$marginRight_for_leftBlank}">            
      <fo:region-body region-name="{$regionName_for_bodyLeftBlank}" margin-top="{$marginTop_for_leftBlank_body}" margin-bottom="{$marginBottom_for_leftBlank_body}"/>
      <fo:region-before region-name="{$regionName_for_headerLeftBlank}" extent="{$extent_for_leftBlank_header}" />
      <fo:region-after region-name="{$regionName_for_footerLeftBlank}" extent="{$extent_for_leftBlank_footer}" />
    </fo:simple-page-master>
  </xsl:template>

  <!-- defaultnya pakai intentionally left blank -->
  <xsl:template name="get_pageSequenceMaster">
    <xsl:param name="masterName">default-pm</xsl:param>
    <xsl:param name="odd_masterReference">oddA4</xsl:param>
    <xsl:param name="even_masterReference">evenA4</xsl:param>
    <xsl:param name="leftBlank_masterReference">left-blankA4</xsl:param>
    <!-- nyontek ke default-A4.xsl -->
    <fo:page-sequence-master master-name="{$masterName}">
      <fo:repeatable-page-master-alternatives>
        <!-- untuk page between first and last -->
        <fo:conditional-page-master-reference master-reference="{$odd_masterReference}" page-position="rest" odd-or-even="odd"/>
        <fo:conditional-page-master-reference master-reference="{$even_masterReference}" page-position="rest" odd-or-even= "even"/>

        <!-- for the first page -->
        <fo:conditional-page-master-reference master-reference="{$odd_masterReference}" page-position="first" odd-or-even="odd"/>

        <!-- 
          for the end page 1. last, even, and blank akan mencetak intentionally left blank
          kalau 2. last, even, and not blank tidak akan mencetak intentionally left blank
        -->
        <fo:conditional-page-master-reference master-reference="{$leftBlank_masterReference}" page-position="last" odd-or-even="even" blank-or-not-blank="blank"/>
        <fo:conditional-page-master-reference master-reference="{$even_masterReference}" page-position="last" odd-or-even="even"/>    
      </fo:repeatable-page-master-alternatives>
    </fo:page-sequence-master>
  </xsl:template>
</xsl:transform>
