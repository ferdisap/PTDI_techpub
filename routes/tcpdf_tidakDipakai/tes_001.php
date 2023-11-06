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
        // $this->Translate(0,0);
        $html = <<<EOD
        <table style="width:175;font-size:12">
          <tr>
            <td style="width:5mm"></td>
            <td align="left" style="width:75mm">
              <img src="./ietp_n219/assets/header_logo_afm.jpeg" width="65mm"/>
            </td>
            <td align="right" style="width:90mm">
              <br/>
              <div style="line-height:1.5">SECTION 4 <br/> NORMAL PROCEDURES </div>
            </td>
          </tr>
        </table>
        EOD;
        $this->writeHTML($html,true,false,true,false,'C');
      } else {
        // <table style="width:100%;font-size:12"><tr><td align="left"><img src="./ietp_n219/assets/header_logo_afm.jpeg" width="65mm"/></td><td align="right"><br/><div style="line-height:1.5">SECTION 4 <br/> NORMAL PROCEDURES </div></td></tr></table>
        // $this->Translate(10,0);
        $html = <<<EOD
        <table style="width:165mm;font-size:12"><tr><td style="width:5mm"></td><td align="left" style="width:90mm"><br/><div style="line-height:1.5">SECTION 4<br/> NORMAL PROCEDURES</div></td><td align="right" style="width:75mm"><img src="./ietp_n219/assets/header_logo_afm.jpeg" width="65mm"/></td></tr></table>
        EOD;
        $this->writeHTML($html,true,false,true,false,'C');
      }
    }

    // Page footer
    public function Footers() {
      if(($this->getPage() % 2) == 0){
        // $this->Translate(10,0);
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 11);
        // Page number
        // $this->Cell(100, 10, 'Original', 0, 0, 'L', 0, '', 0, false, 'C', 'M');
        // $this->Cell(75, 10, 'D661ND1001', 0, 1, 'R', 0, '', 0, false, 'C', 'M');
        // $this->Cell(100, 10, 'DGCA Approved: DD/MM/YYY', 0, 0, 'L', 0, '', 0, false, 'C', 'M');
        // $this->Cell(75, 10, 'Page 4-'.$this->getPage(), 0, 1, 'R', 0, '', 0, false, 'C', 'M');
        $footer = <<<EOD
        <table style="width:175mm, font-size:12">
          <tr>
            <td style="width:5mm"></td>
            <td align="left" style="width:75mm">Original</td>
            <td align="right" style="width:90mm">D661ND1001</td>
          </tr>
          <tr>
            <td style="width:5mm"></td>
            <td align="left" style="width:90mm">DGCA Approved: dd/mm/yyyy</td>
            <td align="right" style="width:75mm">Page 4-{$this->getPage()}</td>
          </tr>
        </table>
        EOD;
        $this->writeHTML($footer,true,false,true,false,'C');
      } else {
        // $this->Translate(25,0);
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 11);
        // Page number
        // $this->Cell(75, 10, 'D661ND1001', 0, 0, 'L', 0, '', 0, false, 'C', 'M');
        // $this->Cell(100, 10, 'Original', 0, 1, 'R', 0, '', 0, false, 'C', 'M');
        // $this->Cell(75, 10, 'Page 4-'.$this->getAliasNumPage(), 0, 0, 'L', 0, '', 0, false, 'C', 'M');
        // $this->Cell(100, 10, 'DGCA Approved: DD/MM/YYY', 0, 1, 'R', 0, '', 0, false, 'C', 'M');
        $footer = <<<EOD
        <table style="width:175mm, font-size:12">
          <tr>
            <td style="width:5mm"></td>
            <td align="left" style="width:75mm">D661ND1001</td>
            <td align="right" style="width:90mm">Original</td>
          </tr>
          <tr>
            <td style="width:5mm"></td>
            <td align="left" style="width:75mm">Page 4-{$this->getPage()}</td>
            <td align="right" style="width:90mm">DGCA Approved: dd/mm/yyyy</td>
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

      // // custom event and odd page margin
      // if(($this->getPage() % 2) == 0){
      //   // $this->SetMargins(10, 30,25, true); //genap
      //   // $this->Translate(10,0);
      // } else {
      //   // $this->SetMargins(25, 30, 10, true); //ganjil
      //   // $this->Translate(25,0);
      // }
      
      // terminate previous page
      $this->endPage();

      if(($this->getPage() % 2) == 0){
        // $this->setMargins(10,30);// genap
        // $this->setX(0);
        // $this->SetMargins(10, 30, 25, true); // genap
      } else {
        // $this->setMargins(25,30);// genap
        // $this->setX(20);
        // $this->SetMargins(25, 30, 10, true); // genap
      }


      if (!isset($this->original_lMargin) OR $keepmargins) {
        $this->original_lMargin = $this->lMargin;
      }
      if (!isset($this->original_rMargin) OR $keepmargins) {
        $this->original_rMargin = $this->rMargin;
      }

      // start new page
      $this->startPage($orientation, $format, $tocpage);
      // dump($this->getPage(), 'foo');
      
      if(($this->getPage() % 2) == 0){
        // $this->Translate(0,0); //genap
        // $this->SetMargins(15, 30, 0, true); // genap
      } else {
        // $this->Translate(10,0); //ganjil        
        // $this->SetMargins(10, 30, 5, false); // genap
      }
    }
}
// create new PDF document
$pdf = new MYPDF('P','mm','A4',true,'UTF-8',false);
// $pdf->setAbsX(20);
$pdf->setHeaderMargin(5);
// $pdf->SetMargins(15, 30, 0, true); // genap
$pdf->SetMargins(5, 30, 0, true);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();

$html1 = <<<EOD
<div style="text-align:justify">TCPDF Example 003

Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
<div style="border:1px solid yellow; padding-left:100mm;padding-right:100mm">
  <h1>Par 1</h1>
    
  Lorem <span style="font-weight:bold">foo</span> ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque, provident blanditiis et similique atque maiores doloremque quo cupiditate maxime, harum unde. Tempora possimus, veniam adipisci nesciunt libero ad harum quidem dolore veritatis corporis tempore atque quo hic est aliquam odio commodi ducimus molestiae eius exercitationem deserunt. Consequuntur, sint repudiandae modi deleniti dolores optio molestiae hic nostrum adipisci est illum nobis consectetur facere laboriosam pariatur incidunt voluptates similique aspernatur magnam obcaecati doloribus vel veniam! Similique nesciunt incidunt asperiores sint, velit recusandae blanditiis voluptate dicta inventore laboriosam praesentium unde molestiae ipsam sed totam soluta adipisci accusantium ab consequatur quod saepe dolorem ratione. Quaerat, accusantium vel perferendis iure harum perspiciatis sed nostrum eligendi numquam atque ipsam libero. Incidunt sapiente, laborum debitis aliquid ipsa soluta, doloremque cumque id ut itaque minus voluptatibus quidem molestiae et porro repellat velit ea, dolore quisquam a qui? Repudiandae nostrum est quo, placeat porro fuga! Cumque hic et esse illum ratione ipsa quasi fugit quidem, quae voluptatum neque sequi asperiores consequatur modi itaque, inventore repellendus dignissimos dolore saepe officia! Delectus ex alias voluptatem ea vel quisquam itaque officia aut nisi, aliquam hic repellat sapiente dignissimos laudantium quae suscipit exercitationem dolorum tenetur iure modi a nesciunt laboriosam eos. Fugit incidunt qui deserunt tempore quos eos ea debitis nemo quia, quas itaque molestias doloremque. Dolorem, hic. Facere quisquam, numquam similique libero ab a qui eaque suscipit necessitatibus? Consequatur commodi eligendi laboriosam assumenda dignissimos numquam molestiae hic iusto odit, fuga, modi eaque harum. Impedit, facere rerum. At commodi recusandae sit asperiores incidunt ea laboriosam beatae ipsam error sint, consectetur a et sed blanditiis vel odit modi. Porro, beatae facilis debitis iusto ea ex. Enim hic, voluptatum alias suscipit architecto numquam, voluptate quidem esse, porro quaerat itaque necessitatibus quisquam officia dolores adipisci velit beatae soluta totam voluptas? Enim, ea eligendi laborum officia vel consequatur molestias porro a saepe similique! Facere laudantium minus ullam illum doloremque accusantium optio vero, facilis praesentium tempora numquam tenetur dolorem eos alias expedita quo accusamus doloribus dolor molestias ab iusto dicta aperiam? Perferendis pariatur praesentium nisi facere molestias nostrum eius quos est ipsa, tenetur assumenda sit rerum saepe corrupti. Explicabo cum aliquid excepturi modi veniam impedit atque magni neque. Quisquam, tempore fugit! Dolores dolore et, illum totam error placeat architecto rerum eius 
    
</div>
  
<div style="border:1px solid blue">
  <h1>Par 1</h1>
  <p>
  Lorem ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque, provident blanditiis et similique atque maiores doloremque quo cupiditate maxime, harum unde. Tempora possimus, veniam adipisci nesciunt libero ad harum quidem dolore veritatis corporis tempore atque quo hic est aliquam odio commodi ducimus molestiae eius exercitationem deserunt. Consequuntur, sint repudiandae modi deleniti dolores optio molestiae hic nostrum adipisci est illum nobis consectetur facere laboriosam pariatur incidunt voluptates similique aspernatur magnam obcaecati doloribus vel veniam! Similique nesciunt incidunt asperiores sint, velit recusandae blanditiis voluptate dicta inventore laboriosam praesentium unde molestiae ipsam sed totam soluta adipisci accusantium ab consequatur quod saepe dolorem ratione. Quaerat, accusantium vel perferendis iure harum perspiciatis sed nostrum eligendi numquam atque ipsam libero. Incidunt sapiente, laborum debitis aliquid ipsa soluta, doloremque cumque id ut itaque minus voluptatibus quidem molestiae et porro repellat velit ea, dolore quisquam a qui? Repudiandae nostrum est quo, placeat porro fuga! Cumque hic et esse illum ratione ipsa quasi fugit quidem, quae voluptatum neque sequi asperiores consequatur modi itaque, inventore repellendus dignissimos dolore saepe officia! Delectus ex alias voluptatem ea vel quisquam itaque officia aut nisi, aliquam hic repellat sapiente dignissimos laudantium quae suscipit exercitationem dolorum tenetur iure modi a nesciunt laboriosam eos. Fugit incidunt qui deserunt tempore quos eos ea debitis nemo quia, quas itaque molestias doloremque. Dolorem, hic. Facere quisquam, numquam similique libero ab a qui eaque suscipit necessitatibus? Consequatur commodi eligendi laboriosam assumenda dignissimos numquam molestiae hic iusto odit, fuga, modi eaque harum. Impedit, facere rerum. At commodi recusandae sit asperiores incidunt ea laboriosam beatae ipsam error sint, consectetur a et sed blanditiis vel odit modi. Porro, beatae facilis debitis iusto ea ex. Enim hic, voluptatum alias suscipit architecto numquam, voluptate quidem esse, porro quaerat itaque necessitatibus quisquam officia dolores adipisci velit beatae soluta totam voluptas? Enim, ea eligendi laborum officia vel consequatur molestias porro a saepe similique! Facere laudantium minus ullam illum doloremque accusantium optio vero, facilis praesentium tempora numquam tenetur dolorem eos alias expedita quo accusamus doloribus dolor molestias ab iusto dicta aperiam? Perferendis pariatur praesentium nisi facere molestias nostrum eius quos est ipsa, tenetur assumenda sit rerum saepe corrupti. Explicabo cum aliquid excepturi modi veniam impedit atque magni neque. Quisquam, tempore fugit! Dolores dolore et, illum totam error placeat architecto rerum eius earum provident, vel vitae. Veniam maiores, totam laboriosam, recusandae veritatis exercitationem libero 
  </p>
</div>

<table align="center" style="width:100%">
<tr><td style="border:1px solid red;text-align:center;" align="center"><img src="./images/N219.png" width="100mm"/></td></tr>
</table>

<div style="text-align:center;border:1px solid green">
  <img src="./images/N219.png" width="100mm"/>
</div>

Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio quis illum deserunt. Fugit enim sunt illum laudantium, delectus voluptatibus expedita sit excepturi aliquam ducimus, placeat molestiae. Voluptates, accusantium? Architecto animi rerum temporibus nobis commodi officiis aut quibusdam nisi suscipit at. Totam expedita aspernatur error quas voluptatibus nihil a mollitia architecto repellat sed, quae velit itaque, porro labore ratione nam ipsum officia. Modi consectetur, hic quae iste quis, dolorum ipsum asperiores facere officiis doloribus maxime vel est nam reprehenderit magnam tempore, repellat quam nesciunt ullam obcaecati nulla? Quisquam reprehenderit sit vel eveniet asperiores nemo molestias ipsam suscipit expedita dolore assumenda est hic neque repellat repellendus rerum eligendi illo, odio error alias facere recusandae beatae qui. Dolore officiis rerum in maiores, libero, delectus quos totam inventore eveniet magni similique numquam iure non quas ab commodi dolor voluptatum quis blanditiis laudantium! Vel a nesciunt quae error? Dignissimos repudiandae accusantium aut consectetur similique, tenetur doloribus exercitationem rerum reiciendis quibusdam quam repellat dolorum obcaecati, et magni est suscipit ipsa ex saepe eveniet dolor optio qui debitis! Suscipit ipsum enim soluta voluptatem aut neque consequuntur ipsa dolorem in voluptatum officia obcaecati facere blanditiis debitis nisi reprehenderit adipisci, pariatur voluptates, doloremque optio! Ab reiciendis laudantium blanditiis. Vel cum, vitae blanditiis, voluptate quae reprehenderit illo recusandae eum odio odit soluta laboriosam dolore harum. Dicta rerum velit vel ipsa labore facere molestiae. Aut quaerat ipsam mollitia ipsa at quos molestias hic! Aspernatur minus maiores blanditiis iste sequi reiciendis veritatis unde ex, hic vitae, doloribus tenetur sint corporis tempore ea. Consequuntur rem ex tempore explicabo eligendi praesentium laudantium similique quam vero non deserunt optio, eos voluptatum vitae molestias, ducimus facilis necessitatibus qui. Itaque tempore at eveniet explicabo eaque illo laboriosam, neque quas, nesciunt sint, porro corrupti voluptatem in dolorum? Similique voluptatum harum ad natus libero. Modi inventore dolor cum! Quo cum laboriosam eaque soluta unde cupiditate quidem quisquam, repellat nostrum quis esse id non aperiam veniam. Quas perferendis cum laudantium, voluptas distinctio similique minima blanditiis, veritatis dolores voluptatibus in facilis corrupti nobis, natus saepe! Eaque labore rerum ipsa omnis temporibus harum ipsum ipsam a. Fugit accusamus voluptas ipsum cumque aperiam aut laudantium nulla voluptatem voluptates aliquam laboriosam unde, similique magnam quas ratione! Dolorem quia corrupti mollitia nemo ea veritatis explicabo, ad deleniti unde numquam, dolore repudiandae voluptas officiis laboriosam animi? Fuga impedit iste ea veritatis velit, molestias officiis, ratione, modi adipisci sunt et illum deserunt! Saepe explicabo autem id aperiam dignissimos blanditiis, eaque eligendi cupiditate accusamus quo minima repellendus, expedita culpa hic fuga et quisquam, molestiae enim? Similique culpa officiis voluptates laboriosam repellat. Optio natus laborum cumque eos officiis porro eum magni soluta aspernatur. Perferendis fugiat earum consequuntur eligendi voluptatibus incidunt aspernatur delectus molestias necessitatibus. Incidunt ut, tempore ipsa veritatis ratione provident impedit autem officia vitae earum. Autem deserunt ut delectus debitis deleniti maiores voluptates ipsum repellat ea, facere quae ex fugiat beatae repudiandae temporibus sequi voluptas vero, aliquam voluptate repellendus, omnis non! Rerum nobis, est odit et repellendus, mollitia quam aliquid saepe dolore fugit nam laudantium ipsam. Quaerat laborum fuga ducimus ipsa, minima doloribus labore vitae delectus maiores sunt in provident quos repudiandae officia cupiditate dignissimos, dolor suscipit harum culpa! Ipsa delectus nihil excepturi saepe magni voluptates aliquam, fuga obcaecati hic repudiandae deleniti eos facere dignissimos illum maxime qui perspiciatis doloremque. Officia mollitia aliquid ducimus omnis! Quam itaque consequatur earum velit at. Nemo alias explicabo nihil maiores quidem quis quia nam laudantium beatae dicta, natus deleniti harum odio repellendus. Fuga libero provident accusamus commodi facere vitae suscipit atque nisi nemo totam vel necessitatibus illum, consectetur, ipsa ab. Quae numquam laboriosam id nobis cupiditate, repudiandae natus facere esse ducimus sit sequi, quam aut, dolorum earum voluptates blanditiis deserunt repellat exercitationem quisquam! Quos sunt cumque odio necessitatibus dolores asperiores facilis atque doloribus quis placeat id, consectetur numquam quo ut corrupti error sint suscipit obcaecati, ullam illo et repudiandae fuga vel est. Exercitationem dicta mollitia tempora maiores dolores vel, saepe perferendis eum reprehenderit suscipit porro placeat aut non aliquid numquam repellendus nam impedit ullam quos et odio sapiente similique ab? Obcaecati saepe dolorum, soluta officia minus laborum sit blanditiis consequuntur atque, iure qui delectus totam. Fugiat possimus architecto, ad aperiam vel exercitationem placeat provident aut commodi itaque assumenda sapiente expedita magnam excepturi totam quae necessitatibus sint laboriosam obcaecati quo voluptatem. Eveniet reprehenderit, quasi omnis earum aliquam ab suscipit aliquid maxime animi. Eaque esse quisquam fugiat, in nemo beatae, exercitationem qui debitis ea ipsum optio aspernatur quaerat rerum quo dolorum quos? Cupiditate consequatur officiis pariatur vero, eaque enim aut dicta corrupti illo obcaecati. Quisquam, voluptas doloribus quas nobis fuga nisi, recusandae quasi totam ipsa ducimus magni consectetur voluptates tempore dolores! Aspernatur ex omnis libero at soluta, asperiores mollitia adipisci ratione voluptatum nam amet cumque. Officiis fugit, commodi optio deleniti maiores libero eius unde eaque asperiores iure similique quo eligendi perspiciatis deserunt dolore quasi?
</div>

<table align="center" style="width:100%">
<tr><td style="border:1px solid red;text-align:center;" align="center"><img src="./images/N219.png" width="100mm"/></td></tr>
</table>

<div style="text-align:center;border:1px solid green">
  <img src="./images/N219.png" width="100mm"/>
</div>
EOD;

// $pdf->setCellPaddings(10);
// $pdf->writeHTMLCell('','','','',$html1,1, 1);

$html2 = <<<EOD
<table style="width:100%" align="left">
  <tr>
    <td style="width:5mm; border:1px solid green"></td>
    <td style="width:165mm; border:1px solid green">
      <div style="border:1px solid yellow; padding-left:100mm;padding-right:100mm">
        <h1>Par 1</h1>
        Lorem <span style="font-weight:bold">foo</span> ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus
      </div>
    </td>
  </tr>
  <tr>
    <td style="width:5mm; border:1px solid blue"></td>
    <td style="width:165mm; border:1px solid blue" align="justify"><h1>Par 1.1 foo</h1>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consectetur facere velit eum quo veniam! Expedita assumenda voluptate tenetur accusamus adipisci, cupiditate ullam eaque doloribus est nemo eos omnis eius pariatur itaque quisquam sed minima debitis aliquam tempore voluptatum accusantium. Cum delectus nihil, excepturi voluptate omnis deserunt amet aspernatur impedit nostrum molestiae sunt nesciunt rerum odit eligendi architecto ipsam quam dolor sint, beatae quod officia enim ex? Reprehenderit sint dolorum sed dolore modi necessitatibus, quibusdam deleniti minima temporibus pariatur vitae aut minus voluptate sapiente ab corporis consequatur vero delectus harum. Ex fugiat alias voluptates molestiae quasi ipsa dolorum nobis repudiandae, aliquid, delectus ad libero sed placeat. Fuga, repellat! Fugit necessitatibus voluptatem, architecto natus ab voluptatum dolor nam eum. Reprehenderit dolor repudiandae ea suscipit vitae enim quasi voluptas beatae mollitia ab, sapiente deserunt, ipsum, magnam aspernatur saepe alias voluptatem necessitatibus! Quam impedit, aliquid architecto repellat ipsum facilis enim recusandae eaque cupiditate pariatur tenetur dolorum odio vitae? Voluptas, ipsam quisquam. Accusamus odit omnis ut vitae quaerat optio quasi aperiam, non facere magni distinctio ipsum deleniti veritatis placeat. Accusantium vel cupiditate tempora, ea, odio totam ipsam odit doloribus, saepe veritatis quo. Exercitationem, perspiciatis expedita? Eligendi similique, accusantium repellendus sed repellat fugiat aliquid, iusto sit accusamus quaerat suscipit id perferendis, quasi minima magni autem necessitatibus dicta laborum? Magnam ratione vero veniam quidem nesciunt itaque eveniet reiciendis maiores provident id quia obcaecati aliquid atque aut reprehenderit accusamus illo quo cupiditate fuga, odio, libero repudiandae. Ipsa odio aliquid quia veniam, nesciunt eos dolore? Culpa maxime mollitia totam modi commodi deleniti quam odio voluptates magni neque, rem reiciendis sed consectetur doloribus possimus ducimus nesciunt ex est, perferendis, magnam animi. Labore temporibus asperiores nam impedit aut, excepturi reiciendis architecto quas voluptates fuga iure voluptatum hic. Quo totam nulla molestias laborum quaerat aperiam dignissimos aliquam. Delectus esse, voluptatibus reiciendis rerum doloremque nulla fugiat error et voluptatum provident odit ex corrupti, quae doloribus magni sequi reprehenderit, assumenda explicabo nam cupiditate tenetur non. Dolor error laudantium iure eius enim ipsa quis vel! Pariatur, a iste. Voluptate sequi iste, praesentium tempora autem, voluptatem repellendus deserunt qui pariatur labore corrupti sapiente consequuntur odit ullam veniam! Natus cupiditate, odit eveniet ducimus quae obcaecati! Quod sit, quasi temporibus ad pariatur suscipit sint perspiciatis at unde similique a numquam labore assumenda velit, dolorem molestias dolores ipsam odit quam! Sed, commodi quo. Nemo, sapiente reprehenderit! Non exercitationem esse voluptas vero, earum nobis illum hic veniam sunt et labore similique est nemo temporibus explicabo nesciunt atque quisquam enim? In molestias vitae ratione illo sit officiis quaerat, eos, eum corporis nulla accusamus rem ipsum accusantium excepturi voluptatem odit, suscipit temporibus? Quas repellat perspiciatis consequatur exercitationem nihil quibusdam quaerat veritatis? Id libero est placeat iure beatae ab architecto tempore vero, dolore minus assumenda ipsa aliquid! Eaque reprehenderit iure quibusdam doloremque excepturi, inventore illum ratione ipsam ex? Adipisci ullam sed, eveniet porro similique sint suscipit ex accusamus placeat voluptates ea sit odio beatae aspernatur quod cum earum vero impedit perspiciatis facilis voluptatibus inventore? Vitae architecto assumenda explicabo. Qui, veniam illo! Veniam, rerum.</td>
  </tr>
  <tr>
    <td style="width:5mm; border:1px solid red"></td>
    <td style="width:165mm; border:1px solid red">
      <div style="border:1px solid yellow; padding-left:100mm;padding-right:100mm">
        <h1>Par 1.1</h1>
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Officiis a similique neque repellat totam ducimus qui quasi nisi consequuntur quos, alias eum dignissimos doloremque numquam expedita officia sapiente adipisci excepturi voluptatem impedit minus. Porro cum, sunt nam unde voluptate, aspernatur nihil perspiciatis hic itaque dolore fuga voluptas repudiandae officia quisquam? Neque a eligendi ut veritatis sit ea sapiente soluta praesentium unde eius nulla odit officiis id tempore nemo error fuga earum ipsum esse exercitationem, perspiciatis at nesciunt natus. Laboriosam saepe nobis qui animi, aut officia quis eum corporis ab ex fugiat voluptate cum? Placeat quas fuga reprehenderit repudiandae eligendi ipsum assumenda, neque reiciendis est natus ipsa, sequi molestias, tempora nemo repellendus! Veritatis dignissimos nesciunt rerum. Sequi eligendi, sed excepturi, similique doloribus nulla obcaecati, ipsa praesentium maxime cumque eius sapiente optio mollitia earum beatae quis. Debitis sequi molestiae natus dolorem! Libero, inventore doloremque ullam cupiditate quo doloribus, mollitia adipisci perspiciatis sequi quis dicta incidunt rerum harum aperiam beatae! Odit fuga ex a, distinctio voluptas suscipit voluptates hic minus voluptate id accusamus rerum iure? Nobis optio deleniti illum, suscipit maxime sunt esse, et dolores sit autem quod molestiae. Eum suscipit illum earum nisi. Doloremque eum rem libero porro iste architecto corrupti esse modi in tempora. Aliquid earum fugit voluptatem unde? Accusamus quam incidunt fugiat tempore doloribus optio veniam, eum quas sit hic mollitia, accusantium et nemo tenetur. Officiis esse sit libero animi vero porro atque. Repellat architecto, totam sed, adipisci mollitia dolorum minus eum pariatur odio excepturi, beatae ex. Facere non repellendus vitae officia officiis, laboriosam natus reiciendis aperiam, placeat cumque atque animi quae quaerat ullam! Ipsam expedita sunt repellendus molestias dolores, optio id illum iusto sequi quis aspernatur corrupti suscipit libero autem consequatur quas qui, voluptates et accusantium esse. Ad, totam! Quaerat molestiae quas sunt recusandae tenetur amet debitis aperiam! Illo id quae sit ea ipsam, dolore, quod porro, deserunt corporis deleniti dolorum a? Eveniet hic excepturi consectetur molestias. Magni, asperiores! Quidem, eius! Ex illum officia enim sint vitae minus facilis qui? Sed quae odit temporibus neque dignissimos sapiente quisquam natus, animi laboriosam laudantium minima, libero qui blanditiis. Nisi amet laudantium iusto id vel soluta numquam quo, cumque reprehenderit sequi, eius, omnis ad natus cum aperiam nostrum? Atque, architecto, temporibus sed labore velit delectus modi earum provident dolore sequi nobis! Reprehenderit aliquam illo blanditiis minima natus, commodi quae perspiciatis, officiis quo quis hic explicabo nesciunt, ducimus aperiam cum ipsum repellendus. Facilis dolores voluptatem blanditiis unde, pariatur vero nisi, provident temporibus possimus assumenda sint quisquam, vel harum quae? Molestias at et vitae culpa molestiae, tempore dicta magnam doloremque quasi totam ipsa ratione numquam exercitationem neque ipsum doloribus alias sit veniam! Provident laborum illo pariatur aut. Odit ipsam repellendus distinctio optio, vitae ea accusantium rem quae neque veniam nihil magnam obcaecati quod soluta minima autem quos voluptatibus dolore enim voluptates eum, eveniet laudantium. Dolores qui animi voluptate fugit ipsum molestias quae, odit eos veritatis fuga iusto corporis doloremque harum officiis excepturi necessitatibus enim, totam ullam vitae. Ducimus quod incidunt, fuga omnis dolorum, ipsum asperiores saepe maiores laboriosam minima amet, unde illo nam. Fugiat voluptatem animi aliquid voluptatibus sequi nisi, accusamus facilis, consequuntur facere praesentium sed hic! Labore saepe enim obcaecati eos cupiditate velit voluptatem illo. Quibusdam, quisquam fuga voluptatibus doloribus sit deleniti corporis neque consequatur aliquid obcaecati? Fugit, architecto quaerat! Maiores quos, perferendis architecto possimus enim excepturi quae atque molestiae dolorum, quia ducimus laborum dolore ullam ab corporis est fuga animi repellat id facilis aliquam ut veritatis neque! Consequuntur pariatur nisi nostrum similique accusantium maiores voluptates laudantium facere delectus tenetur. Esse aliquid alias repellendus quibusdam repellat minima, vel modi vero impedit obcaecati sint? Quas debitis, quis illo delectus exercitationem dignissimos quasi sapiente! Est, esse ipsum, mollitia obcaecati voluptate possimus repudiandae temporibus inventore incidunt numquam at molestias eveniet in asperiores error veniam necessitatibus blanditiis velit laudantium hic suscipit ducimus nam. Enim, facilis! Itaque dolore sequi possimus et! Nostrum vel, commodi quidem sunt perferendis totam animi, nulla officiis ullam, tenetur nihil! Animi ullam, voluptatem rem nulla ipsa aspernatur blanditiis ab minus expedita et similique temporibus quas necessitatibus omnis sint laudantium dolorem enim quo voluptate. Autem, excepturi cupiditate at temporibus illum ab quidem aspernatur reiciendis in delectus reprehenderit laboriosam, aut dolores? A esse nisi mollitia harum eius soluta fugiat hic voluptatibus ex adipisci suscipit sint placeat, nihil quis quo corporis doloribus? Accusamus omnis exercitationem adipisci libero molestiae, veritatis laudantium tenetur sint, cum facere nulla, ab fugiat perferendis dolor aperiam corrupti nihil consectetur eligendi quas. Nesciunt dolores et, recusandae aliquam veritatis at in ab accusamus repudiandae architecto, quasi aliquid voluptates est perferendis voluptatibus odit minus laudantium a inventore unde placeat ut velit? Perferendis, laudantium suscipit laboriosam fuga dicta tempora provident vitae sit assumenda accusamus dignissimos, recusandae incidunt ullam doloremque nihil culpa voluptas consequuntur porro aspernatur placeat expedita. Illum harum in sint id inventore atque, libero ducimus consequatur accusamus debitis neque quasi, mollitia recusandae delectus architecto vitae vero? Dolorem optio necessitatibus ullam, numquam aliquid ut doloribus a praesentium eos exercitationem voluptates nostrum debitis alias porro maiores accusantium natus quisquam repudiandae nulla, minus quia. Quia explicabo sunt numquam, asperiores repellat aut quod maxime natus qui quae nisi ea beatae quis soluta cupiditate minus autem facilis mollitia. Magnam numquam distinctio blanditiis tempora ipsam vel molestias labore? Ipsam, inventore? Cumque, voluptates, error voluptatem minima amet ad, atque ipsum minus veritatis eius aut tempora assumenda? Ipsam quae aut provident labore quidem quos facilis libero nihil quod. Itaque hic tenetur alias debitis fuga sunt nisi voluptas sapiente atque minima porro consequuntur sed temporibus ad autem recusandae minus odio a perspiciatis molestias, excepturi, libero fugiat praesentium deleniti. Ducimus unde eius libero tempora, quod totam error omnis modi vel eligendi illum, at itaque. Quis ex quos voluptas aliquam nisi dicta esse debitis, placeat suscipit. Eius esse et quod sequi consequuntur a magni perferendis! Quos reiciendis assumenda soluta rerum cupiditate deserunt voluptate maiores vel magnam eos! Quos perspiciatis tenetur repellendus. Necessitatibus voluptates at quisquam delectus fugiat saepe, exercitationem nihil facere velit ipsa minus! Unde nemo est, hic, quibusdam ad incidunt, alias dolorum dolores nesciunt earum totam.
      </div>
    </td>
  </tr>
</table>
EOD;
$pdf->writeHTML($html2,true,false,true,false,'C');


// $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set auto page breaks
// set image scale factor
// add a page

// set document information
// $pdf->SetCreator(PDF_CREATOR);
// $pdf->SetAuthor('Nicola Asuni');
// $pdf->SetTitle('TCPDF Example 003');
// $pdf->SetSubject('TCPDF Tutorial');
// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// $pdf->SetMargins(25, 30, 10, true); // ganjil
// $pdf->SetMargins(10, 30, 25, true); // genap
// $pdf->SetMargins(0, 30, 0, true); // genap
// $pdf->TranslateX(25);

// set header and footer fonts
// $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
// $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));



// ---------------------------------------------------------

// set font
// $pdf->SetFont('times', 'BI', 12);


// set some text to print
$html = <<<EOD
<div style="text-align:justify">TCPDF Example 003

Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
<div style="border:1px solid yellow; padding-left:100mm;padding-right:100mm">
  <h1>Par 1</h1>
    
  Lorem <span style="font-weight:bold">foo</span> ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque, provident blanditiis et similique atque maiores doloremque quo cupiditate maxime, harum unde. Tempora possimus, veniam adipisci nesciunt libero ad harum quidem dolore veritatis corporis tempore atque quo hic est aliquam odio commodi ducimus molestiae eius exercitationem deserunt. Consequuntur, sint repudiandae modi deleniti dolores optio molestiae hic nostrum adipisci est illum nobis consectetur facere laboriosam pariatur incidunt voluptates similique aspernatur magnam obcaecati doloribus vel veniam! Similique nesciunt incidunt asperiores sint, velit recusandae blanditiis voluptate dicta inventore laboriosam praesentium unde molestiae ipsam sed totam soluta adipisci accusantium ab consequatur quod saepe dolorem ratione. Quaerat, accusantium vel perferendis iure harum perspiciatis sed nostrum eligendi numquam atque ipsam libero. Incidunt sapiente, laborum debitis aliquid ipsa soluta, doloremque cumque id ut itaque minus voluptatibus quidem molestiae et porro repellat velit ea, dolore quisquam a qui? Repudiandae nostrum est quo, placeat porro fuga! Cumque hic et esse illum ratione ipsa quasi fugit quidem, quae voluptatum neque sequi asperiores consequatur modi itaque, inventore repellendus dignissimos dolore saepe officia! Delectus ex alias voluptatem ea vel quisquam itaque officia aut nisi, aliquam hic repellat sapiente dignissimos laudantium quae suscipit exercitationem dolorum tenetur iure modi a nesciunt laboriosam eos. Fugit incidunt qui deserunt tempore quos eos ea debitis nemo quia, quas itaque molestias doloremque. Dolorem, hic. Facere quisquam, numquam similique libero ab a qui eaque suscipit necessitatibus? Consequatur commodi eligendi laboriosam assumenda dignissimos numquam molestiae hic iusto odit, fuga, modi eaque harum. Impedit, facere rerum. At commodi recusandae sit asperiores incidunt ea laboriosam beatae ipsam error sint, consectetur a et sed blanditiis vel odit modi. Porro, beatae facilis debitis iusto ea ex. Enim hic, voluptatum alias suscipit architecto numquam, voluptate quidem esse, porro quaerat itaque necessitatibus quisquam officia dolores adipisci velit beatae soluta totam voluptas? Enim, ea eligendi laborum officia vel consequatur molestias porro a saepe similique! Facere laudantium minus ullam illum doloremque accusantium optio vero, facilis praesentium tempora numquam tenetur dolorem eos alias expedita quo accusamus doloribus dolor molestias ab iusto dicta aperiam? Perferendis pariatur praesentium nisi facere molestias nostrum eius quos est ipsa, tenetur assumenda sit rerum saepe corrupti. Explicabo cum aliquid excepturi modi veniam impedit atque magni neque. Quisquam, tempore fugit! Dolores dolore et, illum totam error placeat architecto rerum eius 
    
</div>
  
<div style="border:1px solid blue">
  <h1>Par 1</h1>
  <p>
  Lorem ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque, provident blanditiis et similique atque maiores doloremque quo cupiditate maxime, harum unde. Tempora possimus, veniam adipisci nesciunt libero ad harum quidem dolore veritatis corporis tempore atque quo hic est aliquam odio commodi ducimus molestiae eius exercitationem deserunt. Consequuntur, sint repudiandae modi deleniti dolores optio molestiae hic nostrum adipisci est illum nobis consectetur facere laboriosam pariatur incidunt voluptates similique aspernatur magnam obcaecati doloribus vel veniam! Similique nesciunt incidunt asperiores sint, velit recusandae blanditiis voluptate dicta inventore laboriosam praesentium unde molestiae ipsam sed totam soluta adipisci accusantium ab consequatur quod saepe dolorem ratione. Quaerat, accusantium vel perferendis iure harum perspiciatis sed nostrum eligendi numquam atque ipsam libero. Incidunt sapiente, laborum debitis aliquid ipsa soluta, doloremque cumque id ut itaque minus voluptatibus quidem molestiae et porro repellat velit ea, dolore quisquam a qui? Repudiandae nostrum est quo, placeat porro fuga! Cumque hic et esse illum ratione ipsa quasi fugit quidem, quae voluptatum neque sequi asperiores consequatur modi itaque, inventore repellendus dignissimos dolore saepe officia! Delectus ex alias voluptatem ea vel quisquam itaque officia aut nisi, aliquam hic repellat sapiente dignissimos laudantium quae suscipit exercitationem dolorum tenetur iure modi a nesciunt laboriosam eos. Fugit incidunt qui deserunt tempore quos eos ea debitis nemo quia, quas itaque molestias doloremque. Dolorem, hic. Facere quisquam, numquam similique libero ab a qui eaque suscipit necessitatibus? Consequatur commodi eligendi laboriosam assumenda dignissimos numquam molestiae hic iusto odit, fuga, modi eaque harum. Impedit, facere rerum. At commodi recusandae sit asperiores incidunt ea laboriosam beatae ipsam error sint, consectetur a et sed blanditiis vel odit modi. Porro, beatae facilis debitis iusto ea ex. Enim hic, voluptatum alias suscipit architecto numquam, voluptate quidem esse, porro quaerat itaque necessitatibus quisquam officia dolores adipisci velit beatae soluta totam voluptas? Enim, ea eligendi laborum officia vel consequatur molestias porro a saepe similique! Facere laudantium minus ullam illum doloremque accusantium optio vero, facilis praesentium tempora numquam tenetur dolorem eos alias expedita quo accusamus doloribus dolor molestias ab iusto dicta aperiam? Perferendis pariatur praesentium nisi facere molestias nostrum eius quos est ipsa, tenetur assumenda sit rerum saepe corrupti. Explicabo cum aliquid excepturi modi veniam impedit atque magni neque. Quisquam, tempore fugit! Dolores dolore et, illum totam error placeat architecto rerum eius earum provident, vel vitae. Veniam maiores, totam laboriosam, recusandae veritatis exercitationem libero 
  </p>
</div>

<table align="center" style="width:100%">
<tr><td style="border:1px solid red;text-align:center;" align="center"><img src="./images/N219.png" width="100mm"/></td></tr>
</table>

<div style="text-align:center;border:1px solid green">
  <img src="./images/N219.png" width="100mm"/>
</div>

Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio quis illum deserunt. Fugit enim sunt illum laudantium, delectus voluptatibus expedita sit excepturi aliquam ducimus, placeat molestiae. Voluptates, accusantium? Architecto animi rerum temporibus nobis commodi officiis aut quibusdam nisi suscipit at. Totam expedita aspernatur error quas voluptatibus nihil a mollitia architecto repellat sed, quae velit itaque, porro labore ratione nam ipsum officia. Modi consectetur, hic quae iste quis, dolorum ipsum asperiores facere officiis doloribus maxime vel est nam reprehenderit magnam tempore, repellat quam nesciunt ullam obcaecati nulla? Quisquam reprehenderit sit vel eveniet asperiores nemo molestias ipsam suscipit expedita dolore assumenda est hic neque repellat repellendus rerum eligendi illo, odio error alias facere recusandae beatae qui. Dolore officiis rerum in maiores, libero, delectus quos totam inventore eveniet magni similique numquam iure non quas ab commodi dolor voluptatum quis blanditiis laudantium! Vel a nesciunt quae error? Dignissimos repudiandae accusantium aut consectetur similique, tenetur doloribus exercitationem rerum reiciendis quibusdam quam repellat dolorum obcaecati, et magni est suscipit ipsa ex saepe eveniet dolor optio qui debitis! Suscipit ipsum enim soluta voluptatem aut neque consequuntur ipsa dolorem in voluptatum officia obcaecati facere blanditiis debitis nisi reprehenderit adipisci, pariatur voluptates, doloremque optio! Ab reiciendis laudantium blanditiis. Vel cum, vitae blanditiis, voluptate quae reprehenderit illo recusandae eum odio odit soluta laboriosam dolore harum. Dicta rerum velit vel ipsa labore facere molestiae. Aut quaerat ipsam mollitia ipsa at quos molestias hic! Aspernatur minus maiores blanditiis iste sequi reiciendis veritatis unde ex, hic vitae, doloribus tenetur sint corporis tempore ea. Consequuntur rem ex tempore explicabo eligendi praesentium laudantium similique quam vero non deserunt optio, eos voluptatum vitae molestias, ducimus facilis necessitatibus qui. Itaque tempore at eveniet explicabo eaque illo laboriosam, neque quas, nesciunt sint, porro corrupti voluptatem in dolorum? Similique voluptatum harum ad natus libero. Modi inventore dolor cum! Quo cum laboriosam eaque soluta unde cupiditate quidem quisquam, repellat nostrum quis esse id non aperiam veniam. Quas perferendis cum laudantium, voluptas distinctio similique minima blanditiis, veritatis dolores voluptatibus in facilis corrupti nobis, natus saepe! Eaque labore rerum ipsa omnis temporibus harum ipsum ipsam a. Fugit accusamus voluptas ipsum cumque aperiam aut laudantium nulla voluptatem voluptates aliquam laboriosam unde, similique magnam quas ratione! Dolorem quia corrupti mollitia nemo ea veritatis explicabo, ad deleniti unde numquam, dolore repudiandae voluptas officiis laboriosam animi? Fuga impedit iste ea veritatis velit, molestias officiis, ratione, modi adipisci sunt et illum deserunt! Saepe explicabo autem id aperiam dignissimos blanditiis, eaque eligendi cupiditate accusamus quo minima repellendus, expedita culpa hic fuga et quisquam, molestiae enim? Similique culpa officiis voluptates laboriosam repellat. Optio natus laborum cumque eos officiis porro eum magni soluta aspernatur. Perferendis fugiat earum consequuntur eligendi voluptatibus incidunt aspernatur delectus molestias necessitatibus. Incidunt ut, tempore ipsa veritatis ratione provident impedit autem officia vitae earum. Autem deserunt ut delectus debitis deleniti maiores voluptates ipsum repellat ea, facere quae ex fugiat beatae repudiandae temporibus sequi voluptas vero, aliquam voluptate repellendus, omnis non! Rerum nobis, est odit et repellendus, mollitia quam aliquid saepe dolore fugit nam laudantium ipsam. Quaerat laborum fuga ducimus ipsa, minima doloribus labore vitae delectus maiores sunt in provident quos repudiandae officia cupiditate dignissimos, dolor suscipit harum culpa! Ipsa delectus nihil excepturi saepe magni voluptates aliquam, fuga obcaecati hic repudiandae deleniti eos facere dignissimos illum maxime qui perspiciatis doloremque. Officia mollitia aliquid ducimus omnis! Quam itaque consequatur earum velit at. Nemo alias explicabo nihil maiores quidem quis quia nam laudantium beatae dicta, natus deleniti harum odio repellendus. Fuga libero provident accusamus commodi facere vitae suscipit atque nisi nemo totam vel necessitatibus illum, consectetur, ipsa ab. Quae numquam laboriosam id nobis cupiditate, repudiandae natus facere esse ducimus sit sequi, quam aut, dolorum earum voluptates blanditiis deserunt repellat exercitationem quisquam! Quos sunt cumque odio necessitatibus dolores asperiores facilis atque doloribus quis placeat id, consectetur numquam quo ut corrupti error sint suscipit obcaecati, ullam illo et repudiandae fuga vel est. Exercitationem dicta mollitia tempora maiores dolores vel, saepe perferendis eum reprehenderit suscipit porro placeat aut non aliquid numquam repellendus nam impedit ullam quos et odio sapiente similique ab? Obcaecati saepe dolorum, soluta officia minus laborum sit blanditiis consequuntur atque, iure qui delectus totam. Fugiat possimus architecto, ad aperiam vel exercitationem placeat provident aut commodi itaque assumenda sapiente expedita magnam excepturi totam quae necessitatibus sint laboriosam obcaecati quo voluptatem. Eveniet reprehenderit, quasi omnis earum aliquam ab suscipit aliquid maxime animi. Eaque esse quisquam fugiat, in nemo beatae, exercitationem qui debitis ea ipsum optio aspernatur quaerat rerum quo dolorum quos? Cupiditate consequatur officiis pariatur vero, eaque enim aut dicta corrupti illo obcaecati. Quisquam, voluptas doloribus quas nobis fuga nisi, recusandae quasi totam ipsa ducimus magni consectetur voluptates tempore dolores! Aspernatur ex omnis libero at soluta, asperiores mollitia adipisci ratione voluptatum nam amet cumque. Officiis fugit, commodi optio deleniti maiores libero eius unde eaque asperiores iure similique quo eligendi perspiciatis deserunt dolore quasi?
</div>

<table align="center" style="width:100%">
<tr><td style="border:1px solid red;text-align:center;" align="center"><img src="./images/N219.png" width="100mm"/></td></tr>
</table>

<div style="text-align:center;border:1px solid green">
  <img src="./images/N219.png" width="100mm"/>
</div>
EOD;

// $text = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro obcaecati harum, vero perspiciatis nobis tenetur, sapiente optio modi velit, dignissimos ratione enim. Odit ullam fugiat harum quam esse, iusto culpa, ea quia magnam aliquid distinctio! Ab repellat libero eaque voluptatum inventore amet quia ipsa, officia ullam necessitatibus delectus ad tempora labore eos beatae quidem. Temporibus voluptate deserunt voluptatibus quaerat sed quam voluptas minima fuga accusamus sequi magnam similique, quo recusandae enim corrupti nisi veritatis labore eligendi quod exercitationem doloribus fugiat? Debitis itaque et praesentium, tenetur laudantium natus vel vero tempore quam magni, blanditiis nam odio sit qui earum quasi laboriosam at accusantium unde laborum quae voluptates quisquam ratione delectus. Nesciunt voluptatem totam facere maiores sit enim praesentium maxime eum dignissimos a ipsa similique officia vitae fugiat modi quia, laudantium iure minus incidunt quis voluptate aperiam sequi ducimus! Nulla architecto debitis, provident dolor enim amet illo saepe suscipit hic inventore, assumenda nesciunt. Modi obcaecati numquam consequuntur repellendus sint illo, repellat recusandae maxime similique nemo excepturi, iusto labore amet magnam eligendi dolorem suscipit at magni? Eligendi animi incidunt debitis ea reiciendis exercitationem, laborum doloribus iure omnis error, expedita placeat eveniet non quidem quos ipsam voluptatem possimus odio? Fuga beatae aliquid, numquam molestiae modi doloremque aut voluptatum placeat nulla fugit. Rem nostrum repellendus exercitationem neque. Dicta, tempora sint! Ratione cupiditate, blanditiis ad sint, excepturi aperiam dolorum non porro repellat pariatur adipisci? Deserunt dolorum consequuntur cum veritatis, ut repellat vitae modi itaque aliquid at voluptate! Odio fugit neque, earum cumque saepe, voluptatem corporis sint adipisci provident aspernatur molestiae magnam minima a porro eos esse, maiores quisquam perspiciatis fuga. Excepturi placeat minus architecto odit perspiciatis accusantium quasi facilis sit! Cupiditate fugit fuga placeat tempora? Recusandae facere atque ex numquam dolorem quas ea, delectus explicabo quaerat nisi id quisquam nostrum corporis dicta totam ipsam quidem qui.";
// $pdf->setCellPaddings(10,null,0,null);
// $pdf->Write('',$text,'',false,'J',false,0,false,false);

// $par_level_1 = <<<EOD
// <div style="text-align:justify">TCPDF Example 003<br/>

// Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.
//   <h1>Par 1</h1>
//   <p style="border-left:10pt solid red">Lorem <span style="font-weight:bold;">foo</span> ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque,</p>
//   <br/>
//   <br/>
//   <br/>
//   <br/>
//   <p style="padding-left:10pt">provident blanditiis et similique atque maiores doloremque quo cupiditate maxime, harum unde. Tempora possimus, veniam adipisci nesciunt libero ad harum quidem dolore veritatis corporis tempore atque quo hic est aliquam odio commodi ducimus molestiae eius exercitationem deserunt. Consequuntur, sint repudiandae modi deleniti dolores optio molestiae hic nostrum adipisci est illum nobis consectetur facere laboriosam pariatur incidunt voluptates similique aspernatur magnam obcaecati doloribus vel veniam! Similique nesciunt incidunt asperiores sint, velit recusandae blanditiis voluptate dicta inventore laboriosam praesentium unde molestiae ipsam sed totam soluta adipisci accusantium ab consequatur quod saepe dolorem ratione. Quaerat, accusantium vel perferendis iure harum perspiciatis sed nostrum eligendi numquam atque ipsam libero. Incidunt sapiente, laborum debitis aliquid ipsa soluta, doloremque cumque id ut itaque minus voluptatibus quidem molestiae et porro repellat velit ea, dolore quisquam a qui? Repudiandae nostrum est quo, placeat porro fuga! Cumque hic et esse illum ratione ipsa quasi fugit quidem, quae voluptatum neque sequi asperiores consequatur modi itaque, inventore repellendus dignissimos dolore saepe officia! Delectus ex alias voluptatem ea vel quisquam itaque officia aut nisi, aliquam hic repellat sapiente dignissimos laudantium quae suscipit exercitationem dolorum tenetur iure modi a nesciunt laboriosam eos. Fugit incidunt qui deserunt tempore quos eos ea debitis nemo quia, quas itaque molestias doloremque. Dolorem, hic. Facere quisquam, numquam similique libero ab a qui eaque suscipit necessitatibus? Consequatur commodi eligendi laboriosam assumenda dignissimos numquam molestiae hic iusto odit, fuga, modi eaque harum. Impedit, facere rerum. At commodi recusandae sit asperiores incidunt ea laboriosam beatae ipsam error sint, consectetur a et sed blanditiis vel odit modi. Porro, beatae facilis debitis iusto ea ex. Enim hic, voluptatum alias suscipit architecto numquam, voluptate quidem esse, porro quaerat itaque necessitatibus quisquam officia dolores adipisci velit beatae soluta totam voluptas? Enim, ea eligendi laborum officia vel consequatur molestias porro a saepe similique! Facere laudantium minus ullam illum doloremque accusantium optio vero, facilis praesentium tempora numquam tenetur dolorem eos alias expedita quo accusamus doloribus dolor molestias ab iusto dicta aperiam? Perferendis pariatur praesentium nisi facere molestias nostrum eius quos est ipsa, tenetur assumenda sit rerum saepe corrupti. Explicabo cum aliquid excepturi modi veniam impedit atque magni neque. Quisquam, tempore fugit! Dolores dolore et, illum totam error placeat architecto rerum eius</p>   
// </div>
// EOD;
// $node1 = <<<EOD
//   Lorem
// EOD;
// $node2 = <<<EOD
//   ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque
// EOD;
// $pdf->setCellPaddings(10,0,0,0);
// $pdf->writeHTMLCell(0,0,'','','foo',1,0,false,true,'L');
// $pdf->writeHTMLCell(10,0,'','','bar',0,0,false,true,'L');
// $pdf->writeHTMLCell(0,0,'','','baz',0,0,false,true,'L');
// $pdf->writeHTML($node2,false,false,true,true,'L');
// $pdf->writeHTML('bar',false,false,true,true,'L');
// $this->original_lMargin = $this->lMargin;
// dd($pdf->getMargins());
// dd($pdf->getCellPaddings());
// $str = "The string ends in escape: ";
// $str = chr(377); /* add an escape character at the end of $str */
// dd(chr(122), chr(121), chr(90));

// dd(ord('o'));
// $str1 = 'foo ';
$str1 = <<<EOD
Lorem ipsum dolor sit amet <span style="font-weight:bold">consectetur</span> adipisicing elit. 
Porro obcaecati harum, vero perspiciatis nobis tenetur, sapiente optio modi velit, dignissimos ratione enim. Odit 
ullam fugiat harum quam esse, iusto culpa, ea quia magnam aliquid distinctio! Ab repellat libero eaque voluptatum inventore amet quia ipsa, officia ullam necessitatibus delectus ad tempora labore eos beatae quidem. Temporibus voluptate deserunt voluptatibus quaerat sed quam voluptas minima fuga accusamus sequi magnam similique, quo recusandae enim corrupti nisi veritatis labore eligendi quod exercitationem doloribus fugiat? Debitis itaque et praesentium, tenetur laudantium natus vel vero tempore quam magni, blanditiis nam odio sit qui earum quasi laboriosam at accusantium unde laborum quae voluptates quisquam ratione delectus. Nesciunt voluptatem totam facere maiores sit enim praesentium maxime eum dignissimos a ipsa similique officia vitae fugiat modi quia, laudantium iure minus incidunt quis voluptate aperiam sequi ducimus! Nulla architecto debitis, provident dolor enim amet illo saepe suscipit hic inventore, assumenda nesciunt. Modi obcaecati numquam consequuntur repellendus sint illo, repellat recusandae maxime similique nemo excepturi, iusto labore amet magnam eligendi dolorem suscipit at magni? Eligendi animi incidunt debitis ea reiciendis exercitationem, laborum doloribus iure omnis error, expedita placeat eveniet non quidem quos ipsam voluptatem possimus odio? Fuga beatae aliquid, numquam molestiae modi doloremque aut voluptatum placeat nulla fugit. Rem nostrum repellendus exercitationem neque. Dicta, tempora sint! Ratione cupiditate, blanditiis ad sint, excepturi aperiam dolorum non porro repellat pariatur adipisci? Deserunt dolorum consequuntur cum veritatis, ut repellat vitae modi itaque aliquid at voluptate! Odio fugit neque, earum cumque saepe, voluptatem corporis sint adipisci provident aspernatur molestiae magnam minima a porro eos esse, maiores quisquam perspiciatis fuga. Excepturi placeat minus architecto odit perspiciatis accusantium quasi facilis sit! Cupiditate fugit fuga placeat tempora? Recusandae facere atque

EOD;

// $str2 = <<<EOD
// delectus explicabo quaerat nisi id quisquam nostrum corporis dicta totam ipsam quidem qui
// EOD;
// $pdf->setCellPaddings(10,null,0,null);
// // $pdf->writeHTMLCell($charWidth+2,0,'','',$str1,1,0,false,true,'L');
// $pdf->writeHTMLCell('',0,'','',$str1,1,1,false,true,'L');
// $pdf->writeHTMLCell('',0,'','',$str2,1,0,false,true,'L');

// print a block of text using Write()
// $pdf->Write(0, $html, '', 0, 'C', true, 0, false, false, 0);
// $pdf->writeHTMLCell(100,'','', $html, 1, 1,false, true,'C');


// $charWidth = 0;
// foreach (str_split($str1) as $char){
//   $charWidth += $pdf->GetCharWidth(chr(ord($char)));
// }
// $pdf->setCellPaddings(0,null,0,null);
// $pdf->writeHTMLCell('',0,'','','bar',1,0,false,true,'L');
// dd($pdf->getMargins());
// $pdf->setCellPaddings(0,null,0,null);
// $pdf->MultiCell($charWidth,'',$str1,0,'J',false,0);
// $pdf->WriteHTMLCell($charWidth,'','','',$str1,1,0);
// $pdf->setCellPaddings(10,null,0,null);
// $pdf->writeHTML($str1, false);

// $str2 = 'bar ';
// $charWidth = 0;
// foreach (str_split($str2) as $char){
//   $charWidth += $pdf->GetCharWidth(chr(ord($char)));
// }
// $pdf->setCellPaddings(0,null,0,null);
// $pdf->MultiCell($charWidth,'',$str2,0,'J',false,0);

// $str3 = 'baz.';
// $charWidth = 0;
// foreach (str_split($str3) as $char){
//   $charWidth += $pdf->GetCharWidth(chr(ord($char)));
// }
// $pdf->setCellPaddings(0,null,0,null);
// $pdf->MultiCell($charWidth,'',$str3,0,'J',false,0);



// $pdf->writeHTMLCell(0,0,'','','baz',0,0,false,true,'L');
// dd($charWidth);
// dd(chr(102), chr(111), chr(111));
// dd($pdf->GetCharWidth(90));
// $indent = $pdf->getMargins()['left'] + $pdf->getMargins()['padding_left'];
// $pdf->Text($indent,'','foo',0,false,true,1,0,'L',false,'',0);
// $pdf->Text(0,'',$str,0,false,true,1,0,'L',false,'',0);
// $pdf->Text($charWidth,'','bar',0,false,true,0,0,'L',false,'',0);

// $txt = 'ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque';
// $pdf->writeHTML($txt,false,false,true,true,'L');

// <span style="font-weight:bold;">fooas,alsmakmskaskassssssssssssssssssssssssssssssssssssssssssaksasm</span>
// $pdf->setCellPaddings(10,0,0,0);
// $node3 = <<<EOD
//   ipsum dolor sit amet consectetur adipisicing elit. Obcaecati numquam impedit voluptatibus distinctio praesentium omnis laborum, animi voluptatum natus maiores iure voluptas accusamus, deserunt nisi suscipit quisquam, sed cum autem! Consequatur, id numquam alias veniam, at pariatur, atque quibusdam inventore soluta excepturi reiciendis necessitatibus. Perferendis tempore placeat itaque
// EOD;
// $pdf->setCellPaddings(10,0,0,0);
// $pdf->writeHTMLCell(0,0,'','',$node3,'L',0,false,true,'L');


// $pdf->writeHTML($pdf->fixHTMLCode($par_level_1), 1, 1,false,true);
// $pdf->writeHTML($par_level_1, 1, 1,false,true);


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