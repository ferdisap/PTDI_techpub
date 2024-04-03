<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:fox="http://xmlgraphics.apache.org/fop/extensions"
  xmlns:php="http://php.net/xsl">

  <xsl:output method="xml" />

  <!-- <xsl:param name="config_path">'../../../Config.xml</xsl:param> -->
  <xsl:param name="filename"/>

  <xsl:include href="./dmodule.xsl" />
  <xsl:include href="./pm.xsl" />
  <xsl:include href="./master/region.xsl"/>
  <xsl:include href="./Style.xsl"/>
  <xsl:include href="./csdb/para.xsl"/>
  <xsl:include href="./csdb/title.xsl"/>
  <xsl:include href="./csdb/table.xsl"/>
  <xsl:include href="./csdb/media.xsl"/>
  <xsl:include href="./csdb/group/listElemGroup.xsl"/>
  <xsl:include href="./csdb/group/warningcautionnote.xsl"/>
  <xsl:include href="../helper/position.xsl"/>
  <xsl:include href="../helper/security.xsl"/>
  <xsl:include href="../helper/id.xsl"/>
  <xsl:include href="../helper/authority.xsl"/>
  <xsl:include href="../helper/enterprise.xsl"/>

  <xsl:variable name="masterName">
    <xsl:choose>
      <xsl:when test="/pm/@pt">
        <xsl:value-of select="string(/pm/@pt)" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>default-A4</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <xsl:variable name="orientation" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@orientation)"/>
  <xsl:variable name="width" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@width)"/>
  <xsl:variable name="height" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@height)"/>
  <xsl:variable name="mt" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@margin-top)"/>
  <xsl:variable name="mb" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@margin-bottom)"/>
  <xsl:variable name="ml" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@margin-left)"/>
  <xsl:variable name="mr" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@margin-right)"/>
  <xsl:variable name="rb" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@region-before)"/>
  <xsl:variable name="ra" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@region-after)"/>
  <xsl:variable name="stIndent" select="string(document('../../Config.xml')/config/output/layout[@master-name = $masterName]/@start-indent)"/>
  <xsl:variable name="titleNumberWidth">
    <xsl:choose>
      <xsl:when test="boolean($stIndent) or $stIndent != ''">
        <xsl:text>0</xsl:text>
      </xsl:when>
      <xsl:otherwise>1.5cm</xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <xsl:variable name="blockIndent">0cm</xsl:variable>
  
  <xsl:template match="/">
    <fo:root font-family="Arial">

      <xsl:call-template name="setPageMaster">
        <xsl:with-param name="masterName" select="$masterName" />
      </xsl:call-template>

      <xsl:call-template name="setBookmark">
        <xsl:with-param name="masterName" select="$masterName" />
      </xsl:call-template>

      <xsl:call-template name="setPageSequence">
        <xsl:with-param name="masterName" select="$masterName" />
      </xsl:call-template>
    </fo:root>
  </xsl:template>

  <xsl:template name="setPageMaster">
    <xsl:param name="masterName" />
    <xsl:choose>
      <xsl:when test="$masterName != 'default-A4'">
        <xsl:call-template name="pageMasterByPt">
          <xsl:with-param name="pt" select="$masterName" />
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <fo:layout-master-set>
          <!-- kalau tidak ditulis extent, bisa muncul warning di terminal, fo:region-before on
          page 1 exceed the available area in the block-progression direction by 42519
          millipoints. (See position 1:677) -->
          <!-- <fo:simple-page-master master-name="default-A4" page-height="29.7cm" page-width="21cm" -->
          <!-- <fo:simple-page-master master-name="tes" page-height="29.7cm" page-width="21cm" -->
            <!-- margin-top="1cm" margin-bottom="1cm" margin-left="2cm" margin-right="2cm"> -->
            <!-- <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.0cm" /> -->
            <!-- <fo:region-before region-name="header" extent="1.5cm" /> -->
            <!-- <fo:region-after region-name="footer" extent="2.0cm" /> -->
          <!-- </fo:simple-page-master> -->

          <fo:simple-page-master master-name="odd" page-height="{$height}" page-width="{$width}" margin-top="1cm" margin-bottom="1cm" margin-left="3cm" margin-right="1.5cm">
            <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.5cm"/>
            <fo:region-before region-name="header-odd" extent="1.5cm" />
            <fo:region-after region-name="footer-odd" extent="2.0cm" />
          </fo:simple-page-master>
          <fo:simple-page-master master-name="even" page-height="{$height}" page-width="{$width}" margin-top="1cm" margin-bottom="1cm" margin-left="1.5cm" margin-right="3cm">
            <fo:region-body region-name="body" margin-top="1.5cm" margin-bottom="2.5cm"/>
            <fo:region-before region-name="header-even" extent="1.5cm" />
            <fo:region-after region-name="footer-even" extent="2.0cm" />
          </fo:simple-page-master>
          <fo:simple-page-master master-name="left-blank" page-height="{$height}" page-width="{$width}" margin-top="1cm" margin-bottom="1cm" margin-left="1.5cm" margin-right="3cm">            
            <fo:region-body region-name="left_blank" margin-top="1.5cm" margin-bottom="2.0cm"/>
            <fo:region-before region-name="header-left_blank" extent="1.5cm" />
            <fo:region-after region-name="footer-left_blank" extent="2.0cm" />
          </fo:simple-page-master>

          <fo:page-sequence-master master-name="default-A4">
            <fo:repeatable-page-master-alternatives>
              <!-- untuk page between first and last -->
              <fo:conditional-page-master-reference master-reference="odd" page-position="rest" odd-or-even="odd"/>
              <fo:conditional-page-master-reference master-reference="even" page-position="rest" odd-or-even= "even"/>

              <!-- for the first page -->
              <fo:conditional-page-master-reference master-reference="odd" page-position="first" odd-or-even="odd"/>

              <!-- 
                for the end page 1. last, even, and blank akan mencetak intentionally left blank
                kalau 2. last, even, and not blank tidak akan mencetak intentionally left blank
               -->
              <fo:conditional-page-master-reference master-reference="left-blank" page-position="last" odd-or-even="even" blank-or-not-blank="blank"/>
              <fo:conditional-page-master-reference master-reference="even" page-position="last" odd-or-even="even"/>

            </fo:repeatable-page-master-alternatives>
          </fo:page-sequence-master>          
        </fo:layout-master-set>
        
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="setPageSequence">
    <xsl:param name="masterName"/>
    <xsl:choose>
      <xsl:when test="name(/*) = 'pm'">
        <xsl:apply-templates select="pm">
          <xsl:with-param name="masterReference" select="$masterName"/>
        </xsl:apply-templates>
      </xsl:when>
      <xsl:when test="name(/*) = 'dmodule'">
        <xsl:apply-templates select="dmodule">
          <xsl:with-param name="masterReference" select="$masterName"/>
        </xsl:apply-templates>
      </xsl:when>
      <xsl:otherwise>
        <fo:page-sequence master-reference="default-A4">
          <fo:flow flow-name="body">
            <fo:block>Nothing to displayed of &#60;<xsl:value-of select="name(/*)"/>&#62;</fo:block>
          </fo:flow>
        </fo:page-sequence>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="setBookmark">
    <xsl:param name="masterName"/>

    <!-- <fo:bookmark-tree>
      <fo:bookmark internal-destination="block-001">
        <fo:bookmark-title>Any text</fo:bookmark-title>
      </fo:bookmark>
      <fo:bookmark internal-destination="block-002">
        <fo:bookmark-title>Any text</fo:bookmark-title>
      </fo:bookmark>
    </fo:bookmark-tree> -->
  </xsl:template>

  <xsl:template match="__cgmark">
    <fo:change-bar-begin change-bar-class="{generate-id(.)}" change-bar-style="solid" change-bar-width="0.5pt" change-bar-offset="0.5cm"/>
      <xsl:apply-templates/>
    <fo:change-bar-end change-bar-class="{generate-id(.)}"/>
  </xsl:template>

  <!-- <xsl:template match="@changeMark[.='1']"> -->
    <!-- <xsl:value-of select="php:function('dd',string(parent::*), string(../.))"/> -->
    <!-- <xsl:value-of select="php:function('dd',name(parent::*))"/> -->
    <!-- <fo:change-bar-begin change-bar-class="{generate-id(.)}" change-bar-style="solid" change-bar-width="0.5pt" change-bar-offset="0.5cm"/> -->
      <!-- <xsl:apply-templates select="parent::*/."/> -->
      <!-- <xsl:apply-templates select="../."/> -->
    <!-- <fo:change-bar-end change-bar-class="{generate-id(.)}"/> -->
  <!-- </xsl:template> -->
  

</xsl:transform>