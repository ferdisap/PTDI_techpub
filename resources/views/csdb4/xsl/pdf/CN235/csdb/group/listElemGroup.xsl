<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">
  
  <!-- <xsl:template match="sequentialList">
    <xsl:if test="title">
      <fo:block><xsl:value-of select="title"/></fo:block>
    </xsl:if>
    <ol class="sequentialList">
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates />
    </ol>         
  </xsl:template>  -->

  <xsl:template match="randomList">
    <xsl:if test="title">
      <fo:block><xsl:value-of select="title"/></fo:block>
    </xsl:if>
    <fo:list-block>
      <fo:list-item>
        <fo:list-item-label>&#x02022;</fo:list-item-label>
        <fo:list-item-body>
          <fo:block>AAA</fo:block>
        </fo:list-item-body>
      </fo:list-item>
    </fo:list-block>
    <!-- <ul class="randomList">
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </ul>          -->
  </xsl:template> 
  
  <!-- <xsl:template match="listItem">
    <li class="listItem">
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </li>
  </xsl:template>
  
  <xsl:template match="definitionList">
    <br/>
    <dl class="definitionList">
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates select="definitionListItem"/>
    </dl>
  </xsl:template>

  <xsl:template match="definitionListItem">
    <div class="definitionListItem">
      <dt>
        <b><xsl:apply-templates select="listItemTerm"/></b>
        <br/>
      </dt>
      <dd>
        <xsl:apply-templates select="listItemDefinition"/>
        <br/>
      </dd>
    </div>
  </xsl:template> -->



</xsl:stylesheet>