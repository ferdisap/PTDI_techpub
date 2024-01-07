<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <!-- <xsl:include href="./detail.xsl" /> -->

  <xsl:output method="xml" omit-xml-declaration="yes"/>

  <xsl:template match="dmodule">
    <div style="width:100%">
      <div class="container-lg mt-3" style="display:flex;width:90%;height:65vh;justify-content: center;">
        <table id="brdpList-table">
            <thead>
              <tr>
                <th scope="col">Ident</th>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">Audit</th>
                <th scope="col">Decision</th>
              </tr>
              <tr>
                <th>
                  <div class="input-group">
                    <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="ident" id="" max="5" min="1" value="1"/>
                    <input type="text" class="form-control" filterBy="ident" placeholder="search ident.." v-on_keypress="search()"/>
                  </div>
                </th>
                <th>
                  <div class="input-group">
                    <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="title" id="" max="5" min="1" value="2"/>
                    <input type="text" class="form-control" filterBy="title" placeholder="search title.." v-on_keypress="search()"/>
                  </div>
                </th>
                <th>
                  <div class="input-group">
                    <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="category" id="" max="5" min="1" value="3"/>
                    <input type="text" class="form-control" filterBy="category" placeholder="search category.." v-on_keypress="search()"/>
                  </div>
                </th>
                <th>
                  <div class="input-group">
                    <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="audit" id="" max="5" min="1" value="4"/>
                    <input type="text" class="form-control" filterBy="audit" placeholder="search audit.." v-on_keypress="search()"/>
                  </div>
                </th>
                <th>
                  <div class="input-group">
                    <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="decision" id="" max="5" min="1" value="5"/>
                    <input type="text" class="form-control" filterBy="decision" placeholder="search decision.." v-on_keypress="search()"/>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <xsl:for-each select="//content/brDoc/brLevelledPara">
                <xsl:apply-templates/>
              </xsl:for-each>
            </tbody>       
        </table>
      </div>
      <xsl:apply-templates select="//dmStatus/security"/>
    </div>
  </xsl:template>

  <xsl:template match="brPara">
    <tr class="hover:bg-blue-400">
      <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
      <xsl:attribute name="v-on_click">openDetail('<xsl:value-of select="@brDecisionPointUniqueIdent"/>','<xsl:value-of select="./brDecision/@brDecisionIdentNumber"/>','<xsl:value-of select="@id"/>',this)</xsl:attribute>
      <th scope="row">
          <a>
            <xsl:attribute name="href">javascript:void(0)</xsl:attribute>
            <xsl:value-of select="@brDecisionPointUniqueIdent"/>
          </a>
      </th>
      <td>
        <xsl:value-of select="brDecisionPointContent/title"/>
      </td>
      <td>
        <xsl:for-each select="brRelatedTo/brCategoryGroup/brCategory">
          <xsl:value-of select="@brCategoryNumber"/>,&#160;
        </xsl:for-each>
      </td>
      <td><xsl:apply-templates select="brAudit/brCurrentStatus"/></td>
      <td><xsl:apply-templates select="brDecision"/></td>
    </tr>
    <tr style="display:none">
      <td colspan="5">
      </td>
    </tr>
  </xsl:template>

  <xsl:template match="brDecision">
    <xsl:if test="not(descendant::brDecisionPending)">
      <span><xsl:value-of select="@brDecisionIdentNumber"/></span>
    </xsl:if>
    <xsl:if test="boolean(descendant::brDecisionPending)">
      <span style="color:red">PENDING - </span><br/>
      <span><xsl:value-of select="@brDecisionIdentNumber"/></span>
    </xsl:if>
  </xsl:template>

  <xsl:template match="brCurrentStatus">
    <xsl:choose>
      <xsl:when test="@brStatus = 'brst01'"><span class="fw-bold text-warning">Unverified</span></xsl:when>
      <xsl:when test="@brStatus = 'brst02'"><span class="text-success">First verification</span></xsl:when>
      <xsl:when test="@brStatus = 'brst03'"><span class="text-success">Second verification</span></xsl:when>
      <xsl:when test="@brStatus = 'brst10'"><span class="text-success">Not Auditted</span></xsl:when>
    </xsl:choose>
  </xsl:template>

</xsl:stylesheet>