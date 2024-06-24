<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:fox="http://xmlgraphics.apache.org/fop/extensions" xmlns:php="http://php.net/xsl">

  <!-- 
    pt01 = component maintenance
    pt02 = ipd
    pt03 = service bulletin,
    sementara baru pt03 dulu
  -->

  <xsl:template name="pageMasterByPt">
    <xsl:param name="masterName" select="$masterName"/>
    
    <fo:layout-master-set>
      <!-- jika not(pm/@pt) maka akan pakai default-pm, see Main.xsl -->
      
      <!-- 
        Jadi, semua page-sequence-master dan simple-page-master akan dikumpulkan/ditulis semua di xsl.fo.
        Ini akan membuat file xsl.fo menjadi besar, tapi belum ada solusinya.
      -->
      
      <!-- masterName: default-pm -->
      <xsl:call-template name="get_simplePageMaster">
        <xsl:with-param name="masterName" select="$masterName"/>
      </xsl:call-template>
      <xsl:call-template name="get_pageSequenceMaster">
        <xsl:with-param name="masterName" select="$masterName"/>
      </xsl:call-template>

      <!-- scan pmRef disetiap PMC -->
      <xsl:for-each select="//pmRef">
        <xsl:variable name="pmDoc" select="php:function(
        'Ptdi\Mpub\Main\CSDBStatic::document',
        $csdb_path,
        php:function('Ptdi\Mpub\Main\CSDBStatic::resolve_pmIdent', descendant::pmRefIdent)
        )"/>
        <xsl:variable name="pt" select="string($pmDoc/pm/@pmType)"/>
        <xsl:variable name="masterName" select="string($ConfigXML/config/pmGroup/pt[string(@type) = $pt])"/>
        
        <xsl:call-template name="get_simplePageMaster">
          <xsl:with-param name="masterName" select="$masterName"/>
        </xsl:call-template>
        <xsl:call-template name="get_pageSequenceMaster">
          <xsl:with-param name="masterName" select="$masterName"/>
        </xsl:call-template>
      </xsl:for-each>

      <!-- masterName: default-pm01 -->
      <!-- masterName: default-pm02 -->
      <!-- masterName: default-pm03 -->
      <!-- <xsl:call-template name="get_simplePageMaster">
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
      </xsl:call-template> -->
      <!-- <xsl:call-template name="get_pageSequenceMaster">
        <xsl:with-param name="masterName" select="'poh'"/>
        <xsl:with-param name="odd_masterReference">oddA5</xsl:with-param>
        <xsl:with-param name="even_masterReference">evenA5</xsl:with-param>
        <xsl:with-param name="leftBlank_masterReference">left-blankA5</xsl:with-param>
      </xsl:call-template> -->
      <!-- masterName: default-pm04 -->
      <!-- masterName: dan seterusnya -->
      
    </fo:layout-master-set>
  </xsl:template>

</xsl:transform>
