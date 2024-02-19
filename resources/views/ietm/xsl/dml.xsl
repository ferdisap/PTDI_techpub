<?xml version="1.0" encoding="UTF-8"?>

<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:v-bind="https://vuejs.org/bind" xmlns:v-on="https://vuejs.org/on">

<xsl:output method="html" media-type="text/html" omit-xml-declaration="yes"/>

<xsl:param name="filename"/>

<xsl:template match="dml">
  <!-- <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <title>Data Management List</title>
    </head>
    <body>
    </body>
  </html> -->
  <xsl:apply-templates select="identAndStatusSection"/>
  <xsl:apply-templates select="dmlContent"/>
</xsl:template>

<xsl:template match="identAndStatusSection">
  <div class="identAndStatusSection">
    <h1>IDENTIFICATION AND STATUS SECTION</h1>
    <div>Filename: <xsl:value-of select="$filename"/></div>

    <table>
      <!-- dmlAddress -->
      <tr>
        <td><b>DML Code:</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmlCode', //dmlCode)"/></td>
      </tr>
      <tr>
        <td><b>Issue Number:</b></td>
        <td><xsl:value-of select="//dmlIdent/issueInfo/@issueNumber"/></td>
      </tr>
      <tr>
        <td><b>InWork Number:</b></td>
        <td><xsl:value-of select="//dmlIdent/issueInfo/@inWork"/></td>
      </tr>
      <tr>
        <td><b>Issue Date:</b></td>
        <td><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_issueDate', //issueDate)"/></td>
      </tr>
    
      <!-- dmlStatus -->
      <tr>
        <td><b>Security Classification:</b></td>
        <td>
          <input name="ident-securityClassification" value="{dmlStatus/security/@securityClassification}"/>
        </td>
      </tr>
      <tr>
        <td><b>Brex DM Ref:</b></td>
        <td>
          <input name="ident-brexDmRef" value="{php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', //dmlStatus/descendant::brexDmRef/dmRef/dmRefIdent)}"/>
        </td>
      </tr>
      <tr>
        <td><b>Remarks:</b>
        </td>
        <td>
          <textarea name="ident-remarks">
            <xsl:for-each select="//dmlStatus/remarks/simplePara">
              <!-- <xsl:value-of select="string(.)"/> -->
              <xsl:value-of select="php:function('nl2br',string(.))"/>
            </xsl:for-each>
          </textarea>
        </td>
      </tr>
    </table>
  </div>

</xsl:template>

<xsl:template match="dmlContent">
  <hr/>
  <h1>DML CONTENT</h1>

  <table class="dmlContent">
    <tr>
      <!-- <Sort v-bind:function="sort.bind(this)"/> -->
      <th> Ident Code <Sort emitname="dmlEntry_sorted"/> </th>
      <th> DML <Sort emitname="dmlEntry_sorted"/> | Issue Type <Sort emitname="dmlEntry_sorted"/> </th>
      <th> Security <Sort emitname="dmlEntry_sorted"/> </th>
      <th> Resposible Company <br/> (name <Sort emitname="dmlEntry_sorted"/> | code <Sort emitname="dmlEntry_sorted"/>)</th>
      <th> Answer <Sort emitname="dmlEntry_sorted"/> </th>
      <th> Remarks <Sort emitname="dmlEntry_sorted"/> </th>
    </tr>
    <DmlEntryForm>
    <xsl:for-each select="dmlEntry">
      <tr class="dmlEntry"><td class="dmlEntry-ident">
          <textarea name="entryIdent[]" class="w-full">
            <xsl:apply-templates select="dmRef | pmRef | infoEntityRef | commentRef | dmlRef"/>
          </textarea>
          <div class="text-red-600 text-sm error">
            <xsl:attribute name="v-html">store.error('entryIdent.<xsl:value-of select="position()-1"/>')</xsl:attribute>
          </div>
        </td>
        <td>
          <input class="dmlEntry-dmlEntryType w-2/5" name="dmlEntryType[]" value="{@dmlEntryType}"/> | <input class="dmlEntry-issueType w-2/5" name="issueType[]" value="{@issueType}"/>
          <div class="text-red-600 text-sm error">
            <xsl:attribute name="v-html">store.error('dmlEntryType.<xsl:value-of select="position()-1"/>', 'issueType.<xsl:value-of select="position()-1"/>')</xsl:attribute>
          </div>
        </td>
        <td>
          <input class="dmlEntry-securityClassification w-full" name="securityClassification[]" value="{security/@securityClassification}"/>
          <div class="text-red-600 text-sm error">
            <xsl:attribute name="v-html">store.error('securityClassification.<xsl:value-of select="position()-1"/>')</xsl:attribute>
          </div>
        </td>
        <td class="responsibleCompany">
          <input class="dmlEntry-enterpriseName w-2/5" name="enterpriseName[]" value="{responsiblePartnerCompany/enterpriseName}"/> | <input class="dmlEntry-enterpriseCode w-2/5" name="enterpriseCode[]" value="{responsiblePartnerCompany/enterpriseCode}"/>
          <div class="text-red-600 text-sm error">
            <xsl:attribute name="v-html">store.error('enterpriseName.<xsl:value-of select="position()-1"/>', 'enterpriseCode.<xsl:value-of select="position()-1"/>')</xsl:attribute>
          </div>
        </td>
        <td>-</td>
        <td>
          <textarea name="remarks[]" class="w-full">
            <xsl:apply-templates select="remarks/simplePara"/>
          </textarea>
          <div class="text-red-600 text-sm error">
            <xsl:attribute name="v-html">store.error('remarks.<xsl:value-of select="position()-1"/>')</xsl:attribute>
          </div>
        </td>
      </tr>
    </xsl:for-each>  
    </DmlEntryForm>
  </table>
  <div class="text-red-600 text-sm error">
    <xsl:attribute name="v-html">store.error('entryIdent')</xsl:attribute>
  </div>
  <div class="add-remove_button_container">
    <!-- <xsl:variable name="number"><xsl:number/></xsl:variable> -->
    <!-- <td><button class="material-icons" type="button" onclick="add_dmlEntry_row()">add</button></td>
    <td><button class="material-icons" type="button" onclick="delete_dmlEntry_row()">delete</button></td> -->
    <button class="material-icons" type="button" v-on:click="emitter.emit('add_dmlEntry')">add</button>
    <button class="material-icons" type="button" v-on:click="emitter.emit('remove_dmlEntry')">delete</button>
  </div>
</xsl:template>

<xsl:template match="dmRef">
  <!-- <span class="dmRef"><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', dmRefIdent, null, 'DMC-', '')"/></span> -->
  <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmIdent', dmRefIdent, null, 'DMC-', '')"/>
</xsl:template>

<xsl:template match="pmRef">
  <!-- <span><xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmIdent', pmRefIdent, null, 'PMC-', '')"/></span> -->
  <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_pmIdent', pmRefIdent, null, 'PMC-', '')"/>
</xsl:template>

<xsl:template match="infoEntityRef">
  <!-- <span><xsl:value-of select="@infoEntityRefIdent"/></span> -->
  <xsl:value-of select="@infoEntityRefIdent"/>
</xsl:template>

<xsl:template match="commentRef">
  <!-- <span>Belum ada fungsi untuk resolve commentRefIdent</span> -->
</xsl:template>

<xsl:template match="dmlRef">
  <xsl:value-of select="php:function('Ptdi\Mpub\CSDB::resolve_dmlIdent', dmlRefIdent, null, 'DML-', '' )"/>
</xsl:template>


</xsl:transform>


<!-- <tr class="dmlEntry">
  <td class="dmlEntry-ident">
    <textarea name="entryIdent[]" class="w-full">
      <xsl:apply-templates select="dmRef | pmRef | infoEntityRef | commentRef | dmlRef"/>
    </textarea>
  </td>
  <td>
    <input class="dmlEntry-dmlEntryType w-2/5" name="dmlEntryType[]" value="{@dmlEntryType}"/> | <input class="dmlEntry-issueType w-2/5" name="issueType[]" value="{@issueType}"/>
  </td>
  <td>
    <input class="dmlEntry-securityClassification w-full" name="securityClassification[]" value="{security/@securityClassification}"/>
  </td>
  <td class="responsibleCompany">
    <input class="dmlEntry-enterpriseName w-2/5" name="enterpriseName[]" value="{responsiblePartnerCompany/enterpriseName}"/> 
    <input class="dmlEntry-enterpriseCode w-2/5" name="enterpriseCode[]" value="{responsiblePartnerCompany/enterpriseCode}"/>
    <div class="text-red-600 text-sm">
      <xsl:attribute name="v-html">store.error('enterpriseCode.<xsl:value-of select="position()-1"/>')</xsl:attribute>
    </div>
  </td>
  <td>-</td>
  <td>
    <textarea name="remarks[]" class="w-full">
      <xsl:apply-templates select="remarks/simplePara"/>
    </textarea>
  </td>
</tr>
<tr class="add_dmlEntry">
  <xsl:variable name="number"><xsl:number/></xsl:variable>
  <td><button class="material-icons" type="button" onclick="add_dmlEntry_row()">add</button></td>
  <xsl:if test="$number>=2">
    <td><button class="material-icons" type="button" onclick="delete_dmlEntry_row()">delete</button></td>
  </xsl:if>
</tr> -->