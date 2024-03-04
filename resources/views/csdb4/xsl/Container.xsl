<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:output method="html" media-type="text/html" omit-xml-declaration="yes" />
  <xsl:include href="./csdb/Content.xsl" />
  <xsl:include href="./csdb/DmAddress.xsl" />
  <xsl:include href="./csdb/DmAddressItems.xsl" />
  <xsl:include href="./csdb/DmIdent.xsl" />
  <xsl:include href="./csdb/DmRef.xsl" />
  <xsl:include href="./csdb/DmStatus.xsl" />
  <xsl:include href="./csdb/ExternalPub.xsl" />
  <xsl:include href="./csdb/Figure.xsl" />
  <xsl:include href="./csdb/Footnote.xsl" />
  <xsl:include href="./csdb/IdentAndStatusSection.xsl" />
  <xsl:include href="./csdb/InternalRef.xsl" />
  <xsl:include href="./csdb/LevelledPara.xsl" />
  <xsl:include href="./csdb/Para.xsl" />
  <xsl:include href="./csdb/PmRef.xsl" />
  <xsl:include href="./csdb/Security.xsl" />
  <xsl:include href="./csdb/Symbol.xsl" />
  <xsl:include href="./csdb/Table.xsl" />
  <xsl:include href="./csdb/Title.xsl" />
  
  <xsl:include href="./csdb/dmodule/Caption.xsl" />
  <xsl:include href="./csdb/dmodule/DataRestrictions.xsl" />
  <xsl:include href="./csdb/dmodule/FrontMatter.xsl" />
  <xsl:include href="./csdb/dmodule/FrontMatterTitlePage.xsl" />
  <xsl:include href="./csdb/dmodule/FrontMatterList.xsl" />
  <xsl:include href="./csdb/dmodule/Description.xsl" />
  <xsl:include href="./csdb/dmodule/Crew.xsl" />

  <xsl:include href="./group/textElemGroup.xsl" />
  <xsl:include href="./group/reducedParaElemGroup.xsl" />
  <xsl:include href="./group/listElemGroup.xsl" />
  <xsl:include href="./group/warningcautionnote.xsl" />
  
  <xsl:include href="./helper/applic.xsl" />
  <xsl:include href="./helper/controlAuthority.xsl" />
  <xsl:include href="./helper/cgmark.xsl" />
  <xsl:include href="./helper/id.xsl" />
  <xsl:include href="./helper/irtt.xsl" />
  <xsl:include href="./helper/position.xsl" />
  <xsl:include href="./helper/sc.xsl" />

  <xsl:param name="configuration"/>
  <xsl:param name="object_code"/>
  <xsl:param name="icnPath" select="'/images/'"/> <!-- defaultnya nanti ditentukan oleh controller -->

  <xsl:template match="dmodule | pm | dml">
    <xsl:if test="$configuration = 'ForIdentStatusVue'">
      <xsl:apply-templates select="identAndStatusSection"/>
    </xsl:if>
    <xsl:if test="$configuration = 'ContentPreview'">
      <xsl:apply-templates select="content"/>
    </xsl:if>
  </xsl:template>

</xsl:transform>