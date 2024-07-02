<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:php="http://php.net/xsl" xmlns:fo="http://www.w3.org/1999/XSL/Format">

  <!-- 
    Outstanding:
    1. belum memfungsikan @maintPlanningType, the textual information used to classify the maintenance planning data
    2. belum memfungsikan maintAllocation|toolList|remarkList
   -->

  <!-- 
    Outstanding elemen threshold:
    1. attribute @thresholdType wajib diisi untuk membedakan mana yang threshold dan interval
   -->
   <xsl:template match="maintPlanning">
    <xsl:apply-templates select="commonInfo"/>
    <xsl:apply-templates select="preliminaryRqmts"/>
    <!-- <xsl:apply-templates select="inspectionDefinition|taskDefinition|taskDefinitionAlts|timeLimitInfo"/> -->
    <xsl:choose>
      <xsl:when test="taskDefinition">
        <xsl:call-template name="add_taskDefinition"/>
      </xsl:when>
    </xsl:choose>
  </xsl:template>

   <xsl:template name="add_taskDefinition">
    <fo:block border="1px solid green">
      <fo:table width="100%" border="1pt solid black">
        <fo:table-column column-number="1" column-width="7%"/>
        <fo:table-column column-number="2" column-width="10%"/>
        <fo:table-column column-number="3" column-width="7%"/>
        <fo:table-column column-number="4" column-width="7%"/>
        <fo:table-column column-number="5" column-width="10%"/>
        <fo:table-column column-number="6" column-width="10%"/>
        <fo:table-column column-number="7" column-width="49%"/>
        
        <fo:table-header border="1pt solid black">
          <fo:table-row>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center" number-rows-spanned="2"><fo:block>Task No.</fo:block></fo:table-cell>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center" number-rows-spanned="2"><fo:block>Method</fo:block></fo:table-cell>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center" column-number="3" number-columns-spanned="2" border-bottom="1pt solid black">
              <fo:block>Inspection</fo:block>
            </fo:table-cell>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center" number-rows-spanned="2"><fo:block>Zone</fo:block></fo:table-cell>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center" number-rows-spanned="2"><fo:block>Access</fo:block></fo:table-cell>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center" number-rows-spanned="2"><fo:block>Description</fo:block></fo:table-cell>
          </fo:table-row>
          <fo:table-row>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center"><fo:block>Thres</fo:block></fo:table-cell>
            <fo:table-cell border-right="1pt solid black" display-align="center" text-align="center"><fo:block>Int</fo:block></fo:table-cell>
          </fo:table-row>
        </fo:table-header>
        <fo:table-body border="1pt solid black">
          <xsl:apply-templates select="taskDefinition"/>
        </fo:table-body>
      </fo:table>
    </fo:block>
   </xsl:template>

   <xsl:template match="taskDefinition">
    <fo:table-row text-align="center">
      <fo:table-cell padding="2pt" border-right="1pt solid black">
        <fo:block>
          <xsl:value-of select="@taskIdent"/>
        </fo:block>
      </fo:table-cell>
      <fo:table-cell padding="2pt" border-right="1pt solid black">
        <fo:block>
          <xsl:variable name="taskCode" select="string(@taskCode)"/>
          <xsl:value-of select="$ConfigXML/config/maintenance/task[string(@code) = $taskCode]"/>
        </fo:block>
      </fo:table-cell>
      <fo:table-cell padding="2pt" border-right="1pt solid black">
        <fo:block>
          <xsl:variable name="thresUom" select="string(limit/threshold[@thresholdType = 'threshold']/@thresholdUnitOfMeasure)"/>
          <xsl:apply-templates select="limit/threshold[@thresholdType = 'threshold']"/>
          <xsl:value-of select="$ConfigXML/config/maintenance/threshold[string(@uom) = $thresUom]"/>
        </fo:block>
      </fo:table-cell>
      <fo:table-cell padding="2pt" border-right="1pt solid black">
        <fo:block>
          <xsl:variable name="thresUom" select="string(limit/threshold[@thresholdType = 'interval']/@thresholdUnitOfMeasure)"/>
          <xsl:apply-templates select="limit/threshold[@thresholdType = 'interval']"/>
          <xsl:value-of select="$ConfigXML/config/maintenance/threshold[string(@uom) = $thresUom]"/>
        </fo:block>
      </fo:table-cell>
      <fo:table-cell padding="2pt" border-right="1pt solid black">
        <fo:block>
          <xsl:apply-templates select="preliminaryRqmts/productionMaintData/workAreaLocationGroup/zoneRef"/>
        </fo:block>
      </fo:table-cell>
      <fo:table-cell padding="2pt" border-right="1pt solid black">
        <fo:block>
          <xsl:apply-templates select="preliminaryRqmts/productionMaintData/workAreaLocationGroup/accessPointRef"/>
        </fo:block>
      </fo:table-cell>
      <fo:table-cell padding="2pt" text-align="left">
        <xsl:apply-templates select="task"/>
      </fo:table-cell>
    </fo:table-row>
   </xsl:template>

   <xsl:template match="task">
    <xsl:apply-templates select="taskMarker"/>
    <xsl:apply-templates select="taskTitle"/>
    <xsl:apply-templates select="taskDescr"/>
   </xsl:template>

   <xsl:template match="taskMarker">
    <fo:block text-decoration="underline"><xsl:value-of select="@markerType"/></fo:block>
   </xsl:template>

   <xsl:template match="taskDescr">
    <fo:block>
      <xsl:apply-templates/>
    </fo:block>
   </xsl:template>

   <xsl:template match="taskTitle">
    <fo:block font-weight="bold">
      <xsl:apply-templates select="."/>
    </fo:block>
   </xsl:template>

</xsl:stylesheet>