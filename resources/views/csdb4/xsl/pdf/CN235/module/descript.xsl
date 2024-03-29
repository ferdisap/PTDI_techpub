<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

  <xsl:template match="content[name(child::*) = 'description']">

    <!-- <fo:table>
      <fo:table-body>
        <fo:table-row>
          <fo:table-cell>
            <fo:block>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quia, sit!</fo:block>
          </fo:table-cell>
          <fo:table-cell>
            <fo:block>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quia, sit!</fo:block>
          </fo:table-cell>
        </fo:table-row>
        <fo:table-row>
          <fo:table-cell>
            <fo:block>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quia iure alias id ea illo repellendus praesentium. Consequatur sit officiis sequi minus architecto? Totam perferendis quisquam ab corrupti distinctio minus exercitationem?</fo:block>
          </fo:table-cell>
          <fo:table-cell>
            <fo:block>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quia iure alias id ea illo repellendus praesentium. Consequatur sit officiis sequi minus architecto? Totam perferendis quisquam ab corrupti distinctio minus exercitationem?</fo:block>
          </fo:table-cell>
        </fo:table-row>
      </fo:table-body>
    </fo:table> -->

    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="levelledPara">
    <xsl:param name="level">
      <xsl:text>s</xsl:text>
      <xsl:value-of select="php:function('Ptdi\Mpub\Main\CSDBStatic::checkLevel', ., 1)"/>
    </xsl:param>
    <fo:block text-align="justify">
      <xsl:call-template name="style-levelledPara">
        <xsl:with-param name="level" select="$level"/>
      </xsl:call-template>
      <xsl:apply-templates>
        <xsl:with-param name="level" select="$level"/>
      </xsl:apply-templates>
    </fo:block>
  </xsl:template>

  </xsl:transform>