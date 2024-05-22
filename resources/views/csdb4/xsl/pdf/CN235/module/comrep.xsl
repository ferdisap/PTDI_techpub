<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">

    <xsl:include href="./sub/circuitBreakerRepository.xsl" />

    <xsl:template match="commonRepository">
      <fo:block text-align="justify" start-indent="0">
      <!-- <fo:block text-align="justify"> -->
        <xsl:call-template name="add_id"/>
        <xsl:call-template name="add_controlAuthority"/>
        <xsl:call-template name="add_security"/>
        <xsl:apply-templates/>
      </fo:block>
    </xsl:template>

</xsl:transform>