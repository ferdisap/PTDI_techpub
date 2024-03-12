<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind"
  xmlns:v-on="https://vuejs.org/on">

  <xsl:output method="html" media-type="text/html" omit-xml-declaration="yes" />

  <xsl:include href="./dmodule/FrontMatter.xsl" />
  <xsl:param name="object_code"/>

  <xsl:template match="content[ancestor::dmodule]">
    <div class="csdbobjectcontent">
      <div>
        <xsl:attribute name="class">
          <xsl:text>sc-</xsl:text>
          <xsl:value-of select="//identAndStatusSection/descendant::security/@securityClassification"/>
        </xsl:attribute>
      </div>      
      <div class="header">
        <div class="logo">
          <xsl:apply-templates select="//identAndStatusSection/descendant::logo/symbol"/>
        </div>
        <div class="sc">
          <xsl:apply-templates select="//identAndStatusSection/descendant::security"/>
          <!-- <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBObject::getSC', 'text')"/> -->
        </div>
        <div class="object-code">
          <xsl:value-of select="$object_code"/>
        </div>
      </div>
      <div class="body">
        <xsl:apply-templates />
      </div>
    </div>
  </xsl:template>

  <xsl:template match="referencedApplicGroup | referencedApplicGroupRef | warningAndCautions | warningAndCautionsRef"></xsl:template>

</xsl:transform>