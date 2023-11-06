<?php
//============================================================+
// File name   : example_003.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 003 for TCPDF class
//               Custom Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
// require_once('tcpdf_include.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
      if(($this->getPage() % 2) == 0){
        $header = <<<EOD
        <table style="width:100%;font-size:10">
          <tr>
            <td align="left">
              <br/>
              <div style="line-height:1.5">SECTION 4 <br/> NORMAL PROCEDURES </div>
            </td>
            <td align="right">
              <img src="./ietp_n219/assets/header_logo_afm.jpeg" width="65mm"/>
            </td>
          </tr>
        </table>
        EOD;
        $this->writeHTML($header, true);
      } else {
        $header = <<<EOD
        <table style="width:100%;font-size:10">
          <tr>
            <td align="left">
              <img src="./ietp_n219/assets/header_logo_afm.jpeg" width="65mm"/>
            </td>
            <td align="right">
              <br/>
              <div style="line-height:1.5">SECTION 4 <br/> NORMAL PROCEDURES </div>
            </td>
          </tr>
        </table>
        EOD;
        $this->writeHTML($header, true);
      } 
    }
    // Page footer
    public function Footer() {
      if(($this->getPage() % 2) == 0){
        $this->SetY(-15);
        $footer = <<<EOD
        <table style="width:100%;font-size:10">
          <tr>
            <td align="left">D661ND1001</td>
            <td align="right">Original</td>
          </tr>
          <tr>
            <td align="left">Page 4-{$this->getPage()}</td>
            <td align="right">DGCA Approved: dd/mm/yyyy</td>
          </tr>
        </table>
        EOD;
        $this->writeHTML($footer,true,false,true,false,'C');
      } else {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        $footer = <<<EOD
        <table style="width:100%;font-size:10">
          <tr>
            <td align="left">Original</td>
            <td align="right">D661ND1001</td>
          </tr>
          <tr>
            <td align="left">DGCA Approved: dd/mm/yyyy</td>
            <td align="right">Page 4-{$this->getPage()}</td>
          </tr>
        </table>
        EOD;
        $this->writeHTML($footer,true,false,true,false,'C');
      }
    }

    public function AddPage($orientation='', $format='', $keepmargins=false, $tocpage=false) {
      
      if ($this->inxobj) {
        // we are inside an XObject template
        return;
      }
      
      // terminate previous page
      $this->endPage();

      if(($this->getPage() % 2) == 0){
        $this->SetMargins(25, 30, 10, true); // genap
      } else {
        $this->SetMargins(10, 30, 25, true); // ganjil
      }


      if (!isset($this->original_lMargin) OR $keepmargins) {
        $this->original_lMargin = $this->lMargin;
      }
      if (!isset($this->original_rMargin) OR $keepmargins) {
        $this->original_rMargin = $this->rMargin;
      }

      // start new page
      $this->startPage($orientation, $format, $tocpage);
    }
}
// create new PDF document
$pdf = new MYPDF('P','mm','A5',true,'UTF-8',false);
$pdf->setFontSize(9);
// $pdf->setAbsX(20);
$pdf->setHeaderMargin(5);
// $pdf->SetMargins(15, 30, 0, true); // genap
// $pdf->SetMargins(5, 30, 0, true);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->AddPage();

$html = <<<EOD
<div style="border:1px solid red;">
  <div style="border:1px solid green;">
    <div style="border:1px solid blue;nested:true">
      <h1>level 1</h1>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Error tenetur fuga atque excepturi et quae quisquam explicabo eius labore at cum assumenda totam incidunt, maxime neque repellat quam ab repudiandae autem deserunt rerum repellendus. Omnis libero eius amet sunt aliquam ipsa, odio fugit animi aspernatur officia aut vel. Dolorem tenetur repellat dicta maiores magnam dolore odio, libero quis! Ratione incidunt atque itaque ut voluptatibus dolor omnis id? Optio facilis ea architecto nam voluptates omnis quidem nemo in perspiciatis natus tempore quae ullam placeat cumque inventore et, corrupti, maiores accusamus ex commodi vel neque atque. Fuga exercitationem quaerat deleniti optio alias quas dolores quis qui a, est non ad? Enim reprehenderit distinctio quidem libero ipsam? Alias voluptatem facilis officiis consectetur error dolorem, maiores quos iusto, deleniti repudiandae et laudantium. Odit ut praesentium accusantium rem maiores, est, quam labore accusamus necessitatibus facilis dolore! Placeat, enim voluptas voluptatibus aut esse alias quod ipsam nobis doloremque aspernatur omnis ea? Aperiam eos itaque sunt mollitia. Voluptate modi nulla quo facilis eius, velit quis nostrum assumenda necessitatibus quaerat adipisci, odit, beatae vero? Amet consequuntur tenetur nesciunt perspiciatis omnis ipsa magnam, similique necessitatibus quod inventore rerum optio, sit labore fugit veniam officiis iste eaque recusandae asperiores modi excepturi est. Eaque et provident nemo voluptatibus doloremque, eligendi reiciendis repudiandae velit optio placeat officia esse quibusdam, ut a distinctio! Similique quia itaque deleniti reiciendis aliquid nobis iste, saepe soluta dolore id in sapiente ut eveniet culpa vel placeat distinctio excepturi commodi nulla, omnis error nemo molestias at? Similique delectus corrupti officiis aliquid illum, vero numquam in aut at recusandae quis ex tenetur vel! Ipsum facere asperiores culpa dolorem, laboriosam veniam deserunt? Qui porro laudantium, nam molestiae id dolorum voluptas atque ipsum modi in sint, vitae reiciendis explicabo temporibus mollitia, necessitatibus facilis est officiis iure perferendis exercitationem. Delectus, facere itaque.</p>
    </div>
    <table>
      <tr>
        <td>foo</td>
        <td>bar</td>
      </tr>
    </table>
    <div style="border:1px solid blue;">
      <h1>level 2</h1>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Error tenetur fuga atque excepturi et quae quisquam explicabo eius labore at cum assumenda totam incidunt, maxime neque repellat quam ab repudiandae autem deserunt rerum repellendus. Omnis libero eius amet sunt aliquam ipsa, odio fugit animi aspernatur officia aut vel. Dolorem tenetur repellat dicta maiores magnam dolore odio, libero quis! Ratione incidunt atque itaque ut voluptatibus dolor omnis id? Optio facilis ea architecto nam voluptates omnis quidem nemo in perspiciatis natus tempore quae ullam placeat cumque inventore et, corrupti, maiores accusamus ex commodi vel neque atque. Fuga exercitationem quaerat deleniti optio alias quas dolores quis qui a, est non ad? Enim reprehenderit distinctio quidem libero ipsam? Alias voluptatem facilis officiis consectetur error dolorem, maiores quos iusto, deleniti repudiandae et laudantium. Odit ut praesentium accusantium rem maiores, est, quam labore accusamus necessitatibus facilis dolore! Placeat, enim voluptas voluptatibus aut esse alias quod ipsam nobis doloremque aspernatur omnis ea? Aperiam eos itaque sunt mollitia. Voluptate modi nulla quo facilis eius, velit quis nostrum assumenda necessitatibus quaerat adipisci, odit, beatae vero? Amet consequuntur tenetur nesciunt perspiciatis omnis ipsa magnam, similique necessitatibus quod inventore rerum optio, sit labore fugit veniam officiis iste eaque recusandae asperiores modi excepturi est. Eaque et provident nemo voluptatibus doloremque, eligendi reiciendis repudiandae velit optio placeat officia esse quibusdam, ut a distinctio! Similique quia itaque deleniti reiciendis aliquid nobis iste, saepe soluta dolore id in sapiente ut eveniet culpa vel placeat distinctio excepturi commodi nulla, omnis error nemo molestias at? Similique delectus corrupti officiis aliquid illum, vero numquam in aut at recusandae quis ex tenetur vel! Ipsum facere asperiores culpa dolorem, laboriosam veniam deserunt? Qui porro laudantium, nam molestiae id dolorum voluptas atque ipsum modi in sint, vitae reiciendis explicabo temporibus mollitia, necessitatibus facilis est officiis iure perferendis exercitationem. Delectus, facere itaque.</p>
    </div>
  </div>
<div>
EOD;

$html2 = <<<EOD
<div>
  <h2>Level 2</h2>
  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aperiam voluptatem delectus qui sequi quas natus, sit id. Vitae labore perspiciatis, quidem illum in ad tempora facilis nemo repellat, eius nostrum! Rerum, molestias, exercitationem veniam beatae, magni sequi voluptates blanditiis deleniti ratione dolor illo vel distinctio. Atque veritatis inventore exercitationem iusto?</p>
  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aperiam voluptatem delectus qui sequi quas natus, sit id. Vitae labore perspiciatis, quidem illum in ad tempora facilis nemo repellat, eius nostrum! Rerum, molestias, exercitationem veniam beatae, magni sequi voluptates blanditiis deleniti ratione dolor illo vel distinctio. Atque veritatis inventore exercitationem iusto?</p>
  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quam non numquam necessitatibus delectus accusamus enim fugiat voluptatem. Ipsa, accusamus commodi numquam, asperiores voluptate libero eaque deserunt aperiam aliquid exercitationem unde suscipit? Reiciendis, odit doloribus. Totam fugit unde at explicabo tenetur. Eligendi sequi obcaecati quod facere dolor dicta ea ullam quaerat! Quam a, laborum neque natus assumenda, quaerat amet at obcaecati iste cum iure, quos molestias? Optio officiis deleniti magni placeat alias assumenda soluta blanditiis exercitationem magnam quae, ducimus dicta amet culpa velit, temporibus, ab quas? Nobis quia, reprehenderit aut accusamus quidem earum nihil voluptate? Veniam ducimus corrupti ratione esse rem odit voluptatem eligendi nesciunt tempore, aliquam iste necessitatibus in molestias quibusdam a exercitationem modi. Voluptate aspernatur explicabo perferendis ullam aliquam eos obcaecati et recusandae aut doloremque fuga debitis hic sed, qui at vel voluptatibus sit excepturi tempora fugiat minima nihil? Sed cumque velit ipsum unde! Praesentium quos aut soluta? Voluptates corporis atque inventore amet illum quaerat explicabo in eum obcaecati, ex aliquid iusto sed alias sit et fugiat ipsum quasi! Asperiores ratione distinctio deserunt totam nesciunt dolorum officia rerum quia odio, ad, minus vitae. Maxime similique quae porro natus rerum expedita corrupti odit itaque molestias enim ad cupiditate quod, soluta tenetur? Illo saepe placeat nihil obcaecati tempora ducimus at eos aliquam fugit. Odio soluta praesentium eum ullam tempora repudiandae iste suscipit sapiente voluptas, dolorum omnis. Inventore consectetur illo labore omnis consequuntur dolorem deleniti, mollitia reiciendis necessitatibus modi nulla minima quaerat delectus vitae nemo deserunt harum ipsum distinctio? Ea, architecto, neque accusamus dolor magnam ipsam beatae iusto suscipit aut accusantium rem dolorum necessitatibus modi sunt assumenda unde! Ratione est distinctio suscipit officiis tempore nulla enim. Nesciunt deserunt repellendus fuga aperiam architecto! Architecto deserunt culpa, cum repellat, unde nulla blanditiis quaerat saepe deleniti accusantium minus libero illo sapiente earum recusandae iure dolor, consectetur delectus hic? In accusantium rem dolorem non possimus corporis impedit voluptates officia eum. Dolore rem officia id sequi assumenda doloremque et quos consequuntur nobis inventore ut reiciendis exercitationem facilis optio, deserunt magni cum excepturi quis ipsum. Asperiores cupiditate enim magni voluptatibus nostrum mollitia. Soluta ad ducimus enim exercitationem itaque ea porro, consequatur consectetur hic sequi doloribus, numquam asperiores optio ratione magni quidem quo sed placeat aut fugiat? Reiciendis, ipsum eaque. Assumenda incidunt quis animi unde quisquam. Nam maiores minima impedit, quis error ipsam qui! Quaerat accusamus maxime facere beatae numquam odit. Laboriosam, nobis reprehenderit. Inventore illum ratione cumque tenetur id doloremque reiciendis laboriosam obcaecati mollitia nisi! Explicabo quo consequatur, illo magni ex non quasi dolorem iure enim tenetur sunt fugit? Aliquam maiores autem culpa provident delectus neque deserunt velit quam explicabo, eos in doloribus nostrum, debitis maxime nihil, natus eligendi ea! Explicabo quidem adipisci mollitia ullam quisquam modi dolores sapiente est corporis aspernatur. Culpa, ut accusantium architecto cupiditate, excepturi rem illo animi itaque fugit ab quibusdam. Eos sint molestiae voluptates provident dolore harum cumque fugit fugiat, vel at obcaecati labore debitis aut itaque aliquam illum. Obcaecati praesentium distinctio repellendus odio quae eligendi architecto, modi consectetur ducimus, eius iste recusandae dignissimos maiores beatae asperiores tempora expedita mollitia culpa voluptate molestias. Officiis corrupti, sed iusto eaque voluptatum qui corporis! Debitis nulla, consequatur illum quidem, necessitatibus id corrupti doloribus numquam accusantium eos suscipit amet unde tenetur! Atque culpa numquam aut laudantium veniam. Aperiam reprehenderit odit dolor, suscipit ipsum facilis maiores doloribus in eveniet, consequuntur laborum magni ut aut, recusandae quam sint debitis non beatae cumque deserunt quaerat sed veritatis rem quas! Illum rem, quos harum earum quibusdam iste maxime? Atque dignissimos cumque voluptatem, perferendis sint deleniti fugit aut nemo. At maxime libero nesciunt id numquam, inventore minus autem. A non sed amet ratione facere quod itaque veritatis natus assumenda dolores quidem facilis vel minima aliquid dolorum, eum necessitatibus nulla quae? Blanditiis, repellat ex, libero quis commodi enim, dolorum eum accusamus quasi est voluptate amet dolore at deleniti? Praesentium enim vero minima assumenda et mollitia ea accusantium sequi cum facere. Quam sed dolorem voluptatem laborum. Doloribus ab minus quas amet fugit impedit, libero culpa, voluptates adipisci officiis eos alias nobis accusamus consequatur blanditiis! Aut quae similique dignissimos error esse culpa. Saepe sint atque magnam ducimus voluptas porro voluptate expedita tempora veritatis, voluptatem praesentium a eligendi eum soluta vero sequi hic ratione amet? Qui sequi praesentium mollitia, alias odio temporibus voluptatum quas cupiditate, cumque rerum quasi necessitatibus natus ipsa consequatur pariatur molestias! Natus minima voluptatem odit dolorem? Nisi nostrum fugiat rerum in laboriosam quia! Expedita eligendi, nulla ut iste quisquam nisi commodi earum neque! Libero sint vitae, eius totam blanditiis exercitationem perferendis veniam voluptas! Labore commodi, eos ex autem sit ducimus vero tenetur! Cum dolorum corporis, autem dolor quaerat magni sapiente laudantium incidunt eligendi, repellat doloremque harum consequatur voluptatem consectetur. Rerum quasi doloribus, praesentium ratione quia adipisci accusamus iusto nihil expedita id nobis beatae molestias pariatur, deleniti dolores consequuntur error! Ducimus accusantium alias quia et ipsa cupiditate ipsam sequi, dolorum facere repudiandae corporis iusto minima, aspernatur enim. Quia corrupti cupiditate neque id consequatur, incidunt ad illum ipsa delectus porro modi ducimus similique iusto vero voluptatem deserunt repellat non aliquid aspernatur tempora! Accusamus nemo et eum ratione nam laboriosam magni mollitia? Soluta cupiditate ratione architecto alias veritatis similique, animi quia ut culpa voluptatem vero itaque neque officiis nisi est quam nobis! Nam recusandae vel eaque dolorum labore eos in quaerat maiores? Quibusdam veniam excepturi rerum, nobis maxime dolor blanditiis pariatur odio. Aliquam, blanditiis vero expedita ratione, modi ut ad sint ipsum ullam fugit explicabo in quos necessitatibus eligendi dicta corrupti ea recusandae saepe obcaecati ab eveniet laudantium beatae? Voluptatem laudantium aperiam accusantium alias distinctio ipsa sapiente inventore architecto iste totam commodi, sint iure voluptate tempora? Alias aspernatur repellendus quam cupiditate, excepturi quo quis voluptate aliquam, provident est consequuntur fugit rerum asperiores facere fugiat nemo nostrum velit enim rem. Quisquam unde, optio harum est hic aut quis error provident earum consectetur ab sint, modi nam, vitae porro dignissimos sed aperiam quas totam. Commodi eum quis voluptas esse reprehenderit aliquid aspernatur quisquam consequatur soluta, suscipit unde dolor nostrum eaque consectetur, obcaecati tenetur possimus deserunt dolores reiciendis neque.</p>
</div>
EOD;
$html = preg_replace("/[\r\n]|\s{2,}/",'',$html);
// $pdf->setCellPaddings(10);
// $pdf->writeHTML($html,true,false,false,true,'L');
// $pdf->writeHTMLCell(0,'','','',$html,1,1);

$html2 = preg_replace("/[\r\n]|\s{2,}/",'',$html2);
$pdf->setCellPaddings(5);
// $pdf->setLeftMargin(50);
// $pdf->setTopMargin(30);
// $pdf->writeHTML($html2,true,false,false,true,'L');
// $pdf->writeHTMLCell(0,'','','',$html2,1,1);
$pdf->MultiCell(0,0,$html2,1,'J',false,1,'','',true,0,true,false,0,'T',true);
$pdf->MultiCell(0,0,'foo',1,'J',false,1,'','',true,0,true,false,0,'T',true);
// $pdf->Mult;
// $pdf->Rect(5,20,1,10);

if($pdf->getNumPages() % 2){
  $pdf->AddPage();
  $html = <<<EOD
  <div>
  <br/><br/><br/><br/><br/><br/><br/>
  <br/><br/><br/><br/><br/><br/><br/>
  <br/><br/><br/><br/><br/><br/><br/>
  INTENTIONALLY LEFT BLANK
  </div>
  EOD;
  $pdf->writeHTML($html, true,false,true,false,'C');
}
// dd($pdf->getPage());
// dd($pdf->lastPage());

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('tes_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+