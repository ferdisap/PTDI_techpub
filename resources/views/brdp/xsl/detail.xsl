<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" omit-xml-declaration="yes"/>

  <xsl:template match="brPara">
      <div class="container w-100">

        <!-- BR Detail Container -->
        <div id="brPara">
          
          <h1>
              <xsl:value-of select="@brDecisionPointUniqueIdent"/>
              <xsl:if test="brDecision/brDecisionPending">&#160;-&#160;<span style="background-color:red">PENDING</span></xsl:if>
          </h1>
          
          <h4>
            <xsl:value-of select="descendant::brDecisionPointContent/title"/>
          </h4>

          <hr/>

          <!-- brDecision Related to -->
          <div class="related_to text-wrap">
            <h4 class="bold my-0">Business rule decision point is related to:</h4><br/>

            <div class="ms-3">
              <!-- place in spesification -->
              <div class=""> 
                <span class="fw-bold">Place in specification:</span>
                <p class="mt-0 mb-3">
                  <span class="fw-bold">
                    <xsl:apply-templates select="brRelatedTo/descendant::dmCode" />
                  </span>
                  &#160; <xsl:value-of select="brRelatedTo/descendant::dmRef/@referredFragment"/>
                </p>
              </div>

              <!-- business rule category -->
              <xsl:comment>business rule category</xsl:comment>
              <div class="mt-0 mb-3``">
                <div onclick="hideShow(this)">
                  <span class="fw-bold">Business rule category: <xsl:value-of select="brRelatedTo/descendant::brCategory/@brCategoryNumber" /></span>
                  &#160; <xsl:value-of select="brRelatedTo/descendant::brCategory/title"/>
                </div>
                <div class="d-none">
                  <p><xsl:value-of select="brRelatedTo/descendant::brCategoryDescription"/></p>
                </div>
              </div>

              <!-- Related S1000D XML Schemas -->
              <div class="mt-0 mb-3``">
                <span class="fw-bold" onclick="hideShow(this)">Business rule applicability schema: </span> &#160; <xsl:value-of select="count((brRelatedTo/s1000dSchemas/@*[.='1']))"/> schema(s) applied.
                <div class="d-none">
                  <xsl:apply-templates select="brRelatedTo/s1000dSchemas"/>
                </div>
              </div>

              <!-- Decision Point Content -->
              <xsl:comment>Decision Point Content</xsl:comment>
              <div class="mt-0 mb-3``">
                
                <span class="fw-bold">Business rule decision point content: </span> &#160;
                      <xsl:apply-templates select="descendant::brDecisionPointContent/title">
                        <xsl:with-param name="heading">none</xsl:with-param>
                      </xsl:apply-templates>

                <xsl:apply-templates select="descendant::brDecisionPointContent/brDecisionPointText">
                  <xsl:with-param name="isShowDmCode" select="'yes'"/>
                </xsl:apply-templates>
              </div>

              <!-- value Allowed -->
              <div class="mt-0 mb-3``">
                <xsl:apply-templates select="descendant::brDecisionPointContent/brDecisionPointValueGroup">
                    <xsl:with-param name="marginBottom" select="'no'"/>
                </xsl:apply-templates>
               </div>

              <!-- Audit -->
              <div class="mt-0 mb-3">
                <span class="fw-bold">Audit: </span> for <xsl:for-each select="brAudit/brDecisionRef"><xsl:value-of select="@brDecisionIdentNumber"/>; &#160;</xsl:for-each>
                <br/>
                Status: <xsl:apply-templates select="brAudit/brCurrentStatus"/>
                <div>
                  <xsl:apply-templates select="brAudit/brAction"/>
                </div>              
              </div>

            </div>

          </div>
        </div>
      </div>
  </xsl:template>

  <xsl:template match="brCurrentStatus">
    <xsl:choose>
      <xsl:when test="@brStatus = 'brst01'"><span class="fw-bold text-warning">Unverified</span></xsl:when>
      <xsl:when test="@brStatus = 'brst02'"><span class="text-success">First verification</span></xsl:when>
      <xsl:when test="@brStatus = 'brst03'"><span class="text-success">Second verification</span></xsl:when>      
      <xsl:when test="@brStatus = 'brst10'"><span class="text-success">Not Auditted</span></xsl:when>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="brDecisionPointValueGroup">
    <xsl:param name="marginBottom"/>
    <div onclick="hideShow(this)">
      <span class="fw-bold">Values allowed: &#160;</span>You may select <span class="fw-bold"><xsl:value-of select="@brDecisionValueSelection"/></span> value.
      <br/>
    </div>
    <div class="d-none">
      <xsl:for-each select="brDecisionPointValue">
        <span class="fw-bold">Value allowed: &#160;</span><xsl:value-of select="@brDecisionValueAllowed"/><br/>
        <xsl:apply-templates>
          <xsl:with-param name="marginBottom" select = "$marginBottom" />
        </xsl:apply-templates><br/>
      </xsl:for-each>
    </div>
  </xsl:template>

  <xsl:template match="s1000dSchemas"> 
    <ul>
      <xsl:for-each select="@*[.=1]">
        <xsl:choose>
          <xsl:when test="name()='appliccrossreftableXsd'">
              <li>Applicable Cross Reference Table</li>
          </xsl:when>
          <xsl:when test="name()='brDocXsd'">
              <li>Business rule data module</li>
          </xsl:when>
          <xsl:when test="name()='brexXsd'">
              <li>Business rule exchange index</li>
          </xsl:when>
          <xsl:when test="name()='checklistXsd'">
              <li>Crew checklist</li>
          </xsl:when>
          <xsl:when test="name()='commentXsd'">
              <li>Comment</li>
          </xsl:when>
          <xsl:when test="name()='comrepXsd'">
              <li>Common information repository</li>
          </xsl:when>
          <xsl:when test="name()='condcrossreftableXsd'">
              <li>Applicability conditions cross reference table</li>
          </xsl:when>
          <xsl:when test="name()='containerXsd'">
              <li>Container</li>
          </xsl:when>
          <xsl:when test="name()='crewXsd'">
              <li>Crew</li>
          </xsl:when>
          <xsl:when test="name()='ddnXsd'">
              <li>Data dispatch note</li>
          </xsl:when>
          <xsl:when test="name()='descriptXsd'">
              <li>Descriptive</li>
          </xsl:when>
          <xsl:when test="name()='dmlXsd'">
              <li>Data management list</li>
          </xsl:when>
          <xsl:when test="name()='faultXsd'">
              <li>Fault</li>
          </xsl:when>
          <xsl:when test="name()='frontmatterXsd'">
              <li>Front matter</li>
          </xsl:when>
          <xsl:when test="name()='icnMetadataXsd'">
              <li>ICN metadata</li>
          </xsl:when>
          <xsl:when test="name()='ipdXsd'">
              <li>IPD data module</li>
          </xsl:when>
          <xsl:when test="name()='learningXsd'">
              <li>Learning data module</li>
          </xsl:when>
          <xsl:when test="name()='pmXsd'">
              <li>Pulication module</li>
          </xsl:when>
          <xsl:when test="name()='prdcrossreftableXsd'">
              <li>Product crossreference table data module</li>
          </xsl:when>
          <xsl:when test="name()='procedXsd'">
              <li>Procedural data module</li>
          </xsl:when>
          <xsl:when test="name()='processXsd'">
              <li>Process data module</li>
          </xsl:when>
          <xsl:when test="name()='sbXsd'">
              <li>Service bulletin data module</li>
          </xsl:when>
          <xsl:when test="name()='schedulXsd'">
              <li>Maintenance planning data module</li>
          </xsl:when>
          <xsl:when test="name()='scocontentXsd'">
              <li>SCO content data module</li>
          </xsl:when>
          <xsl:when test="name()='scormcontentpackageXsd'">
              <li>SCORM</li>
          </xsl:when>
          <xsl:when test="name()='updateXsd'">
              <li>Update file Schema</li>
          </xsl:when>
          <xsl:when test="name()='wrngdataXsd'">
              <li>Wiring data module</li>
          </xsl:when>
          <xsl:when test="name()='wrngfldsXsd'">
              <li>Wiring fields data module</li>
          </xsl:when>
        </xsl:choose>
      </xsl:for-each> 
    </ul>  
  </xsl:template>

  <xsl:template match="brDecisionPointValue">
    <p>Value allowed: <xsl:value-of select="@brDecisionValueAllowed"/></p>
  </xsl:template>

  <xsl:template match="brDecision">
    <div id="brDecision" class="container w-100 my-3 text-wrap">
      <h3>BR Decision</h3>
      <div class="ms-3">
        <xsl:apply-templates select="brDecisionText"/>
        <xsl:for-each select="brDecisionValueGroup/brDecisionValue">
          <div>
            <span class="fw-bold">Value Registered: </span><span><xsl:value-of select="@brDecisionValueRegistered"/></span>
            <xsl:apply-templates/>
          </div>
        </xsl:for-each>
      </div>
    </div>        
  </xsl:template>

</xsl:stylesheet>