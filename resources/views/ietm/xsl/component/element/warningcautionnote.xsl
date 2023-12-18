<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">

  <xsl:output method="xml" omit-xml-declaration="yes"/>
  <!-- <xsl:param name="absolute_path_csdbInput"></xsl:param> -->
  
  <xsl:template match="warning">
    <xsl:variable name="warning_logo">
    <xsl:text>/route/get_transform_csdb/?filename=</xsl:text>
      <xsl:value-of select="symbol/@infoEntityIdent"/>
    </xsl:variable>
    <table style="text-align:center;width:100%">
      <xsl:call-template name="cgmark"/>
      <tr>
        <td>
          <img src="{$warning_logo}" width="20mm"/>
        </td>
      </tr>
      <tr>
        <td style="width:15%">&#160;</td>
        <td style="width:70%;text-align:left">
          <xsl:apply-templates select="warningAndCautionPara"/>
        </td>
        <td style="width:15%">&#160;</td>
      </tr>
    </table>
    <!-- <br/><br/> -->
  </xsl:template>
  
  <xsl:template match="caution">
    <xsl:variable name="caution_logo">
    <xsl:text>/route/get_transform_csdb/?filename=</xsl:text>
      <xsl:value-of select="symbol/@infoEntityIdent"/>
    </xsl:variable>
    <table style="text-align:center;width:100%">
      <xsl:call-template name="cgmark"/>
      <tr>
        <td>
          <img src="{$caution_logo}"/>
        </td>
      </tr>
      <tr>
        <td style="width:15%">&#160;</td>
        <td style="width:70%;text-align:left">
          <xsl:apply-templates select="warningAndCautionPara"/>
        </td>
        <td style="width:15%">&#160;</td>
      </tr>
    </table>
  </xsl:template>
  
  <xsl:template match="note">
    <xsl:variable name="note_logo">
      <xsl:text>/route/get_transform_csdb/?filename=</xsl:text>
      <xsl:value-of select="symbol/@infoEntityIdent"/>
    </xsl:variable>
    <xsl:variable name="border">
      <xsl:if test="@noteType = 'warning'">
        <xsl:text>border-bottom:2px solid red</xsl:text>
      </xsl:if>
      <xsl:if test="@noteType = 'caution'">
        <xsl:text>border-bottom:2px solid orange</xsl:text>
      </xsl:if>
      <xsl:if test="@noteType = 'note'">
        <xsl:text>border-bottom:2px solid grey</xsl:text>
      </xsl:if>
    </xsl:variable>
    <div class="note">
      <xsl:call-template name="cgmark"/>
      <table cellpadding="10mm">
        <xsl:if test="symbol/@infoEntityIdent">
        <tr>
          <td>
            <xsl:if test="child::symbol/@infoEntityIdent">
              <img src="{$note_logo}" class="mt-3 symbol">
                <xsl:if test="symbol/@reproductionWidth">
                  <xsl:attribute name="width"><xsl:value-of select="symbol/@reproductionWidth"/></xsl:attribute>
                </xsl:if>
                <xsl:if test="symbol/@reproductionHeight">
                  <xsl:attribute name="height"><xsl:value-of select="symbol/@reproductionHeight"/></xsl:attribute>
                </xsl:if>
              </img>       
            </xsl:if>
          </td>
        </tr>
        </xsl:if>
        <tr>
          <td>
            <xsl:apply-templates select="notePara"/>
          </td>
        </tr>
      </table>
    </div>
    <br/>
  </xsl:template>

  <xsl:template match="warningAndCautionPara">
    <p>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </p>
  </xsl:template>

  <xsl:template match="notePara">
    <xsl:choose>
      <xsl:when test="parent::note/parent::crewDrillStep">
        <div>
          <xsl:call-template name="cgmark"/>
          <xsl:apply-templates/>
        </div>      
      </xsl:when>
      <xsl:otherwise>
        <p>
          <xsl:call-template name="cgmark"/>
          <xsl:apply-templates/>
        </p>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="attentionRandomList">
    <ul>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </ul>
  </xsl:template>

  <xsl:template match="attentionSequentialList">
    <xsl:if test="title">
      <b><xsl:apply-templates select="title/text()"/></b>
    </xsl:if>
    <ol>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </ol>
  </xsl:template>

  <xsl:template match="attentionRandomListItem">
    <li>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </li>
  </xsl:template>

  <xsl:template match="attentionListItemPara">
    <span>
      <xsl:call-template name="cgmark"/>
      <xsl:apply-templates/>
    </span>
    <xsl:if test="following-sibling::attentionListItemPara">
      <br/>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>