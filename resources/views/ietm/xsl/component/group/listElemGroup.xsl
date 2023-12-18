<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

  <xsl:output method="xml"/>
  
  <!-- <xsl:include href="title.xsl"/> -->
  <!-- <xsl:include href="listItem.xsl"/>  -->

  <xsl:template match="sequentialList">
    <br/>
    <xsl:if test="title">
      <span><br/><b><xsl:value-of select="title"/></b></span>
    </xsl:if>
    <ol>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates />
    </ol>         
  </xsl:template> 

  <xsl:template match="randomList">
    <br/>
    <xsl:if test="title">
      <span><br/><b><xsl:value-of select="title"/></b></span>
    </xsl:if>
    <ul>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </ul>         
  </xsl:template> 
  
  <xsl:template match="listItem">
    <li>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </li>
  </xsl:template>

  <xsl:template match="note">
    <div class="flex justify-content-center">
      <div class="note">
        <div class="heading"><span>NOTE</span></div>
        <xsl:apply-templates/>
      </div>
    </div>
  </xsl:template>

  <xsl:template match="definitionList">
    <br/>
    <dl>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates select="definitionListItem"/>
    </dl>
  </xsl:template>

  <xsl:template match="definitionListItem">
    <!-- di beri page-break-inside biar printing tidak melewati footer-->
    <div>
      <!-- text-align:left karena jika justify, akan merusan #ln;. Lagian seharusnya listitem tidak panjang kalimatnya -->
      <dt style="text-align:left">
        <b><xsl:apply-templates select="listItemTerm"/></b>
        <br/>
      </dt>
      <dd style="text-align:left">
        <xsl:apply-templates select="listItemDefinition"/>
        <br/>
      </dd>
    </div>
  </xsl:template>

  <!-- <xsl:template match="title">
    <b>foo<xsl:apply-templates/></b>
  </xsl:template> -->


</xsl:stylesheet>