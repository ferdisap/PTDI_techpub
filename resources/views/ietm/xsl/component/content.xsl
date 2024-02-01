<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

<xsl:include href="./frontMatter.xsl"/>
<xsl:include href="./pmEntry.xsl"/>

<xsl:include href="./descript.xsl"/>
<xsl:include href="./crew.xsl"/>
<xsl:include href="./applicCrossRefTable.xsl"/>
<xsl:include href="./condCrossRefTable.xsl"/>
<xsl:include href="./productCrossRefTable.xsl"/>

<xsl:include href="./attribute/id.xsl" />
<xsl:include href="./attribute/cgmark.xsl" />
<xsl:include href="./helper/position.xsl"/>
<xsl:include href="./group/textElemGroup.xsl" />
<xsl:include href="./group/listElemGroup.xsl" />
<xsl:include href="./element/levelledPara.xsl"/>
<xsl:include href="./element/warningcautionnote.xsl"/>

<xsl:include href="../../../brex/xsl/contextRules.xsl"/>
<xsl:include href="../../../brex/xsl/nonContextRules.xsl"/>
<xsl:include href="../../../brex/xsl/snsRules.xsl"/>


<xsl:template match="content">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="brex">
  <div>
    <xsl:value-of select="commonInfo"/>
  </div>
  <div>
    <xsl:apply-templates select="snsRules"/>
  </div>
  <div>
    <xsl:apply-templates select="contextRules"/>
  </div>
</xsl:template>

</xsl:transform>