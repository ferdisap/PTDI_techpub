<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:fo="http://www.w3.org/1999/XSL/Format"
  xmlns:php="http://php.net/xsl">
  
  <xsl:template name="header">
    <xsl:param name="masterName"/>
    <xsl:param name="oddOrEven"/>
    <xsl:param name="pagePosition"/>
    <xsl:param name="entry"/>
    <xsl:choose>
      <xsl:when test="$masterName = 'default-A4'">
        <xsl:choose>
          <xsl:when test="$oddOrEven = 'odd'">
            <xsl:call-template name="header-odd-default-A4">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="$oddOrEven = 'even'">
            <xsl:call-template name="header-even-default-A4">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
        </xsl:choose>
      </xsl:when>
      <xsl:when test="$masterName = 'default-A4L'">
        <xsl:choose>
          <xsl:when test="$oddOrEven = 'odd'">
            <xsl:call-template name="header-odd-default-A4L">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="$oddOrEven = 'even'">
            <xsl:call-template name="header-even-default-A4L">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
        </xsl:choose>
      </xsl:when>
      <xsl:when test="$masterName = 'default-pm'">
        <xsl:choose>
          <xsl:when test="$oddOrEven = 'odd'">
            <xsl:call-template name="header-odd-default-A4">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="$oddOrEven = 'even'">
            <xsl:call-template name="header-even-default-A4">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
        </xsl:choose>
      </xsl:when>
      <!-- ini nanti diubah, mungkin header-footer nya tidak relevan lagi pakai yang default A4 kalau pakai kertas A5-->
      <xsl:when test="$masterName = 'poh'">
        <xsl:choose>
          <xsl:when test="$oddOrEven = 'odd'">
            <xsl:call-template name="header-odd-default-A4">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="$oddOrEven = 'even'">
            <xsl:call-template name="header-even-default-A4">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
        </xsl:choose>
      </xsl:when>
      <xsl:when test="$masterName = 'maintPlanning'">
        <xsl:choose>
          <xsl:when test="$oddOrEven = 'odd'">
            <xsl:call-template name="header-odd-default-A4L">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="$oddOrEven = 'even'">
            <xsl:call-template name="header-even-default-A4L">
              <xsl:with-param name="masterName" select="$masterName"/>
              <xsl:with-param name="entry" select="$entry"/>
            </xsl:call-template>
          </xsl:when>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <fo:block>&#160;</fo:block>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="footer">
    <xsl:param name="id"/>
    <xsl:param name="masterName"/>
    <xsl:param name="oddOrEven"/>
    <xsl:param name="entry"/>
    <xsl:choose>
      <xsl:when test="$masterName = 'default-A4'">
        <xsl:if test="$oddOrEven = 'odd'">
          <xsl:call-template name="footer-odd-default-A4">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
        <xsl:if test="$oddOrEven = 'even'">
          <xsl:call-template name="footer-even-default-A4">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
      </xsl:when>
      <xsl:when test="$masterName = 'default-A4L'">
        <xsl:if test="$oddOrEven = 'odd'">
          <xsl:call-template name="footer-odd-default-A4L">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
        <xsl:if test="$oddOrEven = 'even'">
          <xsl:call-template name="footer-even-default-A4L">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
      </xsl:when>
      <xsl:when test="$masterName = 'default-pm'">
        <xsl:if test="$oddOrEven = 'odd'">
          <xsl:call-template name="footer-odd-default-A4">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
        <xsl:if test="$oddOrEven = 'even'">
          <xsl:call-template name="footer-even-default-A4">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
      </xsl:when>
      <!-- ini nanti diubah, mungkin header-footer nya tidak relevan lagi pakai yang default A4 kalau pakai kertas A5 -->
      <xsl:when test="$masterName = 'poh'">
        <xsl:if test="$oddOrEven = 'odd'">
          <xsl:call-template name="footer-odd-default-A4">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
        <xsl:if test="$oddOrEven = 'even'">
          <xsl:call-template name="footer-even-default-A4">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
      </xsl:when>
      <xsl:when test="$masterName = 'maintPlanning'">
        <xsl:if test="$oddOrEven = 'odd'">
          <xsl:call-template name="footer-odd-default-A4L">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
        <xsl:if test="$oddOrEven = 'even'">
          <xsl:call-template name="footer-even-default-A4L">
            <xsl:with-param name="masterName" select="$masterName"/>
            <xsl:with-param name="id" select="$id"/>
            <xsl:with-param name="entry" select="$entry"/>
          </xsl:call-template>
        </xsl:if>
      </xsl:when>
      <xsl:otherwise>
        <fo:block>&#160;</fo:block>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!-- depreciated, sebelumnya dipanggil di dmodule.xsl. sekarang langsung panggil apply-templates select content -->
  <xsl:template name="body">
    <!-- <fo:block>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Explicabo illo nam eaque odit iure velit? Placeat delectus nemo enim qui inventore unde maiores temporibus iure doloremque sed? Voluptates quam tempore architecto nemo magnam amet ullam dignissimos placeat praesentium ipsa eum, dolore non exercitationem, maiores veniam eligendi iusto vitae voluptate aspernatur!</fo:block>
    <fo:block>
      <fo:basic-link internal-destination="fnt-001">tes link</fo:basic-link>
    </fo:block> -->
    <xsl:apply-templates select="content"/>
    <!-- <fo:block/> -->
    <!-- <fo:block>aaaa</fo:block> -->
    <!-- <fo:block-container break-after="page" id="block-001">
      <fo:block>
        <fo:change-bar-begin change-bar-class="tesid" change-bar-style="solid" change-bar-width="0.5pt" change-bar-offset="0.5cm"/>
        Lorem ipsum dolor sit, amet consectetur
        <fo:footnote>
          <fo:inline>1</fo:inline>
          <fo:footnote-body>
            <fo:block>Lorem ipsum dolor sit amet consectetur adipisicing elit. Error asperiores perferendis doloremque accusamus eius in labore delectus sapiente! Dolores, sit!</fo:block>
          </fo:footnote-body>
        </fo:footnote>
        adipisicing elit. Dolorem, quod? Delectus, sequi vel similique omnis doloribus fugiat minus iusto neque corrupti voluptas numquam molestias laudantium in repellat ipsum aliquid, quisquam dolorum facere debitis non reprehenderit rerum possimus alias fugit! Dolor ducimus rerum iusto animi reprehenderit fugiat, voluptas impedit, ipsam placeat excepturi dolorum dolore nemo expedita? Suscipit molestiae quisquam est tempora, voluptate vel a odio mollitia dolor blanditiis inventore quas! Explicabo eum, in expedita tenetur vitae nostrum a culpa dicta commodi consequuntur quidem unde neque quae numquam quo fugit rerum similique aut odio quod quisquam id! Distinctio iste molestias soluta qui.
        <fo:change-bar-end change-bar-class="tesid"/>
      </fo:block>
      <fo:block text-align="justify">Lorem ipsum dolor sit amet consectetur adipisicing elit. Error ex amet perspiciatis ipsa sit voluptatem culpa distinctio nostrum sapiente dolore dolorum quisquam, officia est porro veritatis totam, exercitationem sint possimus commodi quaerat. Laborum repellendus commodi delectus tenetur dicta minima, blanditiis expedita inventore incidunt consectetur architecto rerum libero vero eveniet in dignissimos id itaque laboriosam reiciendis mollitia nemo sit minus eius iusto! Explicabo, eius quod iure reiciendis, provident facere nulla fuga, quibusdam natus illo consequatur distinctio? Odio, amet cumque! Corrupti beatae perspiciatis odio, autem nam aut repellendus tempore a, ipsa, quas reiciendis quidem distinctio voluptas velit doloribus optio. Eaque architecto praesentium alias ad, voluptatibus mollitia recusandae atque ducimus quod explicabo obcaecati animi consequuntur adipisci optio! Ipsum, repellendus eum maxime tempora voluptate aspernatur ea corrupti autem velit. Asperiores veritatis nemo dolorem nisi voluptatum iusto provident dolore. Reiciendis qui consequuntur et commodi dolorem culpa quaerat ab itaque aut esse sed mollitia molestias doloremque accusamus, optio vitae exercitationem quod aspernatur? Consequatur esse soluta fuga. Consequuntur voluptate quasi incidunt, facere temporibus asperiores blanditiis rem. Suscipit, illo nobis quas exercitationem quam sapiente labore quibusdam eum rerum eos commodi, porro, cumque reprehenderit vitae asperiores pariatur. Facilis suscipit, voluptatibus odio molestias alias enim eaque repellendus. Sunt libero dolorum doloribus ipsa maxime fuga omnis quo tempore commodi sit? Nostrum nisi facilis aliquid ab unde error, accusantium sed dolor perferendis deserunt, a molestias cupiditate beatae ducimus corrupti odit eligendi veniam exercitationem id enim dolores dolorem provident. Perspiciatis neque iste cupiditate in odit ex vitae, cum necessitatibus unde mollitia enim quaerat quos harum culpa explicabo eligendi dolorum non id, iusto accusamus odio. Rem quod nobis necessitatibus voluptates non reiciendis vel odio! Repudiandae tempora, quasi atque totam iste dolor animi assumenda aperiam vitae recusandae enim error provident possimus porro pariatur! Incidunt, at et. Illum ipsa optio officia illo hic facere accusamus culpa corrupti fugiat. Officia totam laudantium veritatis iure, quasi fugiat molestias eum corrupti possimus reiciendis vel eius, ratione accusantium. Quidem delectus ducimus iste, consequatur repellat beatae quis porro quasi nostrum doloribus maiores dicta laudantium error eveniet optio ea minus obcaecati odit reprehenderit corrupti repellendus voluptatum voluptate? Omnis atque provident quidem earum, eos asperiores quod vitae ab! Possimus itaque temporibus voluptate, deserunt at tempora perferendis animi. Facilis amet maxime neque eum! At ducimus perferendis, iusto corrupti assumenda optio sint aliquam. Autem aperiam facilis adipisci similique modi tempora, eos illo eius accusantium cupiditate beatae facere eveniet officiis ipsa vero molestias inventore nemo maiores quis reiciendis a aut consectetur. Quam dolorem rerum totam nostrum porro. Pariatur quam ab alias velit, commodi iste ex neque enim dolore quas fuga blanditiis odio, optio modi natus aliquid, animi nulla. Excepturi, explicabo. Eligendi labore corporis architecto commodi assumenda ex dicta. Maxime numquam nisi aut commodi, labore doloribus veritatis aliquid tempora fugit ut animi eum hic odit tempore quo libero ad at non laboriosam deleniti voluptatum placeat. Quos sint at deleniti quod, reprehenderit repellat ipsum est exercitationem? Blanditiis ducimus saepe eum unde, nobis odit reiciendis quae nemo deserunt dolorum voluptate ipsum nesciunt, eius enim nisi molestiae totam dolore delectus recusandae magnam laudantium. Deserunt nam nostrum rerum quaerat quidem ea tempore architecto error, corrupti quis cum, officia perferendis libero reprehenderit facere laudantium exercitationem iste quibusdam maiores veritatis. Earum quam, mollitia a sunt quod aliquid eaque harum dolore sequi obcaecati facilis dignissimos deleniti. Molestias neque, reprehenderit dolore commodi fugiat quidem odio voluptates tempora maxime adipisci, repellat autem similique saepe mollitia molestiae? Recusandae laborum enim dolore, velit fugiat perferendis vitae ipsam voluptatum at molestiae dolorem quam nostrum odit porro, ea quisquam eligendi veritatis ex adipisci tempora soluta molestias aspernatur aperiam quo. Facere, aspernatur repellendus, quam tenetur recusandae nemo sed illo veniam autem, ipsam consequatur? Pariatur nulla error odit, accusantium ut itaque consequuntur corporis quas odio aliquid exercitationem a aperiam asperiores excepturi cum assumenda dignissimos atque voluptatum enim. Quas saepe vel obcaecati earum ea corporis totam atque. Quibusdam officiis dolore aliquam blanditiis, soluta doloremque sed corporis ut distinctio repudiandae velit, voluptatum dignissimos et! Perferendis tenetur amet consequuntur ea deleniti alias cumque recusandae? Architecto ad quos incidunt sequi inventore aliquam distinctio necessitatibus consequuntur cum! Libero natus labore neque? Iure dignissimos illo quo expedita ea sint sed tenetur neque eligendi perspiciatis. Ratione iure sapiente, beatae quibusdam itaque iste rem architecto blanditiis soluta incidunt? Molestiae deleniti asperiores fugiat tempore delectus ratione, commodi et, similique perferendis quos iure ab accusamus deserunt voluptas sapiente consequuntur nostrum cupiditate autem iste? Rerum asperiores facere eos nihil maiores amet tenetur ab praesentium sapiente, porro consequuntur? Accusantium blanditiis ipsum aspernatur, quo ipsa expedita nobis voluptas veniam deserunt quidem est culpa sequi odit neque rerum nulla sed ducimus, laboriosam consequatur ex, quos perferendis quae delectus? Doloribus quod excepturi doloremque minima tempore! Sed vel neque tempora est reiciendis corporis nobis dolore aliquam quas quidem inventore, maxime ipsa, ut ab dolores ullam! Sed eveniet blanditiis fuga maiores quis nemo. Explicabo temporibus magni, similique consectetur quasi optio hic veniam. Praesentium facilis corporis itaque quia illo perferendis saepe quas exercitationem reprehenderit possimus a, asperiores, dolores dicta eum temporibus nostrum fugiat quos. Quo rem facere saepe aperiam officiis unde. Sit iusto, rem vitae recusandae ipsum assumenda sint, laboriosam tempora dignissimos at quis. Pariatur iure corporis adipisci dolore quos minima quod eius. Reprehenderit, aliquam? Vel molestiae delectus tempora sit eum quia ea, debitis sapiente accusamus hic eveniet autem non, architecto quibusdam sequi molestias quam consequatur eaque numquam quis cum nemo esse laboriosam quas. Sunt, fugiat distinctio. Velit quaerat in aliquam dolore incidunt! Ut facilis saepe repudiandae doloremque quia accusamus. Omnis eaque sit natus necessitatibus, soluta repellendus id dolor corrupti fuga. Vel eveniet in necessitatibus provident! Minima ipsum eaque totam sed tempora hic repellendus cumque. Dolorem dolor obcaecati amet beatae nisi expedita nulla praesentium, laborum ratione necessitatibus accusamus, maxime fugiat soluta sequi ex ipsam perferendis explicabo aperiam cum officia facere voluptates maiores? Provident, odit expedita. Tempora provident et ipsam eaque exercitationem, beatae voluptatibus fugiat blanditiis obcaecati rerum. Sapiente voluptatum explicabo rerum ratione quia expedita necessitatibus laborum nostrum ipsam nemo cupiditate magnam nam voluptas perspiciatis voluptate in totam natus vel nihil quam, iure eius vero, architecto error! Facere qui architecto sunt.</fo:block>
    </fo:block-container>
    <fo:block id="block-002">Hello World TES TES2!</fo:block>
    <fo:block>Hello World TES TES3!</fo:block> -->
  </xsl:template>

  <xsl:template name="headerleftBlank_xx">
    <xsl:param name="masterName"/>
    <xsl:param name="entry"/>
    <xsl:call-template name="header">
      <xsl:with-param name="masterName" select="$masterName"/>
      <xsl:with-param name="oddOrEven">even</xsl:with-param>
      <xsl:with-param name="entry" select="$entry"/>
    </xsl:call-template>
    <fo:block-container position="fixed">
      <fo:block>Intentionally Left Blank</fo:block>
    </fo:block-container>
  </xsl:template>

  <xsl:template name="headerleftBlank">
    <xsl:param name="masterName"/>
    <xsl:param name="entry"/>
    <xsl:call-template name="header">
      <xsl:with-param name="masterName" select="$masterName"/>
      <xsl:with-param name="oddOrEven">even</xsl:with-param>
      <xsl:with-param name="entry" select="$entry"/>
    </xsl:call-template>
    <fo:block-container position="fixed">
      <xsl:choose>
        <xsl:when test="$masterName = 'default-pm' or $masterName = 'default-A4'">
          <xsl:attribute name="top">14cm</xsl:attribute>
          <xsl:attribute name="left">7cm</xsl:attribute>
        </xsl:when>
        <xsl:when test="$masterName = 'poh'">
          <xsl:attribute name="top">10cm</xsl:attribute>
          <xsl:attribute name="left">4cm</xsl:attribute>
        </xsl:when>
        <xsl:when test="$masterName = 'maintPlanning'">
          <xsl:attribute name="top">9cm</xsl:attribute>
          <xsl:attribute name="left">12cm</xsl:attribute>
        </xsl:when>
        <xsl:otherwise>
          <!-- TBD: jika mastername berbeda-beda, sesuai kertas dan orientasi kertas masing-masing -->
        </xsl:otherwise>
      </xsl:choose>
      <fo:block>Intentionally left blank</fo:block>
    </fo:block-container>
  </xsl:template>

  <!-- dipanggil di fo:page-sequence, cek dmodule.xsl atau pm.xsl -->
  <xsl:template name="getRegion">
      <xsl:param name="masterReference"/>
      <xsl:param name="id"/>
      <xsl:param name="entry"/>

      <xsl:variable name="regionName_for_body"><xsl:call-template name="get_layout_regionName_for_body"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- ini akan di panggil di page-sequence, see pm.xsl or dmodule.xsl -->
      <xsl:variable name="regionName_for_bodyLeftBlank"><xsl:call-template name="get_layout_regionName_for_bodyLeftBlank"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- sejauh ini, ini belum digunakan karena tulisan 'intentionally left blank' akan ditulis di static-content header, see region.xsl -->
      <xsl:variable name="regionName_for_headerOdd"><xsl:call-template name="get_layout_regionName_for_headerOdd"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- ini dipanggil di static-content, see region.xsl -->
      <xsl:variable name="regionName_for_footerOdd"><xsl:call-template name="get_layout_regionName_for_footerOdd"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- ini dipanggil di static-content, see region.xsl -->
      <xsl:variable name="regionName_for_headerEven"><xsl:call-template name="get_layout_regionName_for_headerEven"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- ini dipanggil di static-content, see region.xsl -->
      <xsl:variable name="regionName_for_footerEven"><xsl:call-template name="get_layout_regionName_for_footerEven"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- ini dipanggil di static-content, see region.xsl -->
      <xsl:variable name="regionName_for_headerLeftBlank"><xsl:call-template name="get_layout_regionName_for_headerLeftBlank"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- ini dipanggil di static-content, see region.xsl -->
      <xsl:variable name="regionName_for_footerLeftBlank"><xsl:call-template name="get_layout_regionName_for_footerLeftBlank"><xsl:with-param name="masterName" select="$masterReference"/></xsl:call-template></xsl:variable> <!-- ini dipanggil di static-content, see region.xsl -->

      <fo:static-content flow-name="{$regionName_for_headerOdd}">
        <xsl:call-template name="header">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'odd'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="{$regionName_for_headerEven}">
        <xsl:call-template name="header">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="{$regionName_for_footerOdd}">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'odd'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="{$regionName_for_footerEven}">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="{$regionName_for_headerLeftBlank}">
        <xsl:call-template name="headerleftBlank">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="{$regionName_for_footerLeftBlank}">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="xsl-footnote-separator">
        <fo:block>---------------</fo:block>
      </fo:static-content>
  </xsl:template>

  <xsl:template name="getRegion_xx">
      <xsl:param name="masterReference"/>
      <xsl:param name="id"/>
      <xsl:param name="entry"/>
      <fo:static-content flow-name="header-odd">
        <xsl:call-template name="header">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'odd'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="header-even">
        <xsl:call-template name="header">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="footer-odd">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'odd'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="footer-even">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="header-left_blank">
        <xsl:call-template name="headerleftBlank">
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="footer-left_blank">
        <xsl:call-template name="footer">
          <xsl:with-param name="id" select="$id"/>
          <xsl:with-param name="masterName" select="$masterReference"/>
          <xsl:with-param name="oddOrEven" select="'even'"/>
          <xsl:with-param name="entry" select="$entry"/>
        </xsl:call-template>
      </fo:static-content>
      <fo:static-content flow-name="xsl-footnote-separator">
        <fo:block>---------------</fo:block>
      </fo:static-content>
  </xsl:template>


</xsl:transform>