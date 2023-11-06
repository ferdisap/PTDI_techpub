<?php 
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
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
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
// require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Ferdi A');
$pdf->SetTitle('TCPDF Example 00111');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = <<<EOD
<h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
<i>This is the first example of TCPDF library.</i>
<p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
<p>Please check the source code documentation and other examples for further information.</p>
<p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt voluptatum aspernatur repellendus quidem vero labore praesentium esse, delectus doloremque quis, consequuntur unde explicabo aliquam facilis, mollitia est iusto animi quisquam? Aliquam fuga minima non eos quisquam. Alias explicabo cupiditate vitae sed iusto perferendis omnis placeat suscipit eius praesentium dignissimos commodi quasi, natus porro accusantium expedita sequi quo laboriosam odio cum aliquid numquam! Tempore, voluptatibus. Odio et repudiandae eos asperiores? Rem, asperiores at, eveniet illo suscipit nesciunt eos, nisi ut debitis omnis aperiam similique voluptatem soluta nostrum velit nihil modi earum impedit repellat sed. Ab exercitationem dolorum molestiae impedit in, ducimus iusto error laboriosam commodi eos! Delectus laudantium dignissimos culpa ullam ut possimus reprehenderit veritatis inventore corporis iusto consectetur voluptate sed impedit illum at eius necessitatibus, maxime itaque reiciendis animi porro earum aliquam a. Quam saepe sint repudiandae eos magnam doloremque debitis, consequatur adipisci consequuntur fugit velit, reprehenderit aperiam fuga minus beatae temporibus aut maxime modi, quo iste similique delectus. Blanditiis, qui, facere fugiat magnam sed consequuntur ipsa quod tempora temporibus at similique cum iusto, provident rem porro officia? Laborum asperiores repellat quam pariatur vel hic inventore temporibus in officia? Alias sit quidem fuga tempora deserunt est, recusandae et animi assumenda aut expedita, eveniet corporis numquam? Eius sunt dolores aliquam perferendis fugit repellat, porro iste rem minima et id veritatis aspernatur nam quos eos neque maxime, provident illo atque. Inventore, necessitatibus magni. Dolores voluptas ratione id quibusdam voluptatibus cum adipisci labore eum reprehenderit maxime sapiente expedita quis esse beatae ex earum quos veniam reiciendis omnis maiores, explicabo delectus iure numquam laboriosam? Veritatis dolorem tempora, eaque cumque suscipit saepe, laudantium animi tempore nesciunt sunt quae excepturi inventore magnam, rerum aliquid incidunt. Minima perferendis, dolores a quidem aliquam beatae corrupti eligendi eveniet consequuntur voluptas soluta dicta explicabo porro ratione totam! Recusandae, deleniti pariatur! Ut non ex, hic delectus aliquam vero illo quos voluptatum? Vitae voluptatibus eaque consectetur modi officiis nesciunt molestiae suscipit temporibus atque ducimus repellendus asperiores hic quis quae, odit veniam ipsa consequuntur laboriosam sequi. Fuga ratione et voluptates, adipisci neque ad minima! Corporis, dolorum? Eaque quod suscipit exercitationem dolorum sunt adipisci omnis nesciunt deserunt cumque illo, ducimus autem velit nihil iure eligendi inventore iste? Dolorem adipisci molestias quae sit error quo tempore expedita distinctio earum? Perspiciatis praesentium nisi tempora tenetur ab soluta ea saepe? Repellendus dicta doloribus voluptas neque maiores vel a provident consequuntur sapiente omnis unde dignissimos, illo eius deserunt temporibus sint id fuga quo excepturi error aspernatur eos tempore. Debitis corporis assumenda ab laudantium similique qui cumque placeat? Magni iure expedita, nihil, exercitationem fugiat enim neque aperiam molestiae amet quibusdam eius nobis tempora ab commodi in harum dolor ipsam veniam? Velit, reiciendis fuga neque dolor accusantium a quidem et non, nihil sunt eaque laudantium minus hic aperiam aliquid dolore cum, deleniti adipisci at facilis temporibus delectus quas. Quo dolor perspiciatis enim necessitatibus maxime voluptas hic provident porro repellendus reprehenderit quasi excepturi minus, adipisci natus dignissimos odit? Cupiditate eius magnam autem dignissimos at? Laudantium tempore doloremque libero, deleniti eius, distinctio illo rem quasi delectus, perspiciatis officiis hic unde eum quisquam minus assumenda fugiat dolores explicabo vero at veritatis voluptatibus porro! Molestiae doloremque dolores tempora eos beatae, earum reprehenderit omnis maxime fugit sit, soluta fuga, dolorum corporis. Aut molestias veniam deleniti sed quaerat facere voluptatem, illum nihil dicta voluptates provident tempora veritatis rem suscipit perferendis nemo sequi dolor. Eum, eos cumque a magnam, asperiores maxime illo necessitatibus rem aperiam quasi aliquam incidunt debitis aliquid sunt ratione repellendus. Quis minus molestias ipsa harum iste, accusamus, quas asperiores minima soluta, non aut vel labore distinctio quam quod deleniti sapiente cumque amet aspernatur? Perspiciatis fugiat inventore modi eos rem eligendi ea optio est sint vel voluptates, mollitia quas quae! Harum reiciendis nobis itaque cupiditate error repudiandae molestias exercitationem. Quia exercitationem dignissimos natus in velit temporibus quaerat ad repellendus est consectetur, nam fuga possimus libero ipsam placeat iusto sunt veniam. Perferendis eveniet ratione laborum reprehenderit neque consectetur dolore eum fuga, inventore, id iste animi impedit rem modi odio sunt maiores officia totam commodi rerum ex, explicabo perspiciatis soluta sint? Laudantium, nesciunt illo maiores dolore minima rem labore asperiores illum harum, quo eveniet officiis. Ducimus maiores voluptates, sit id eum beatae alias laboriosam autem, aspernatur vitae similique possimus libero perferendis velit ratione expedita eligendi, unde tenetur obcaecati suscipit ipsam! Tempore minima maxime ut quam animi fugit aliquam repellat cumque, iure ex quo commodi a, explicabo doloribus assumenda unde error soluta? Esse officia vitae ea recusandae labore? Facilis earum possimus ratione culpa, minus dolor incidunt consequuntur perspiciatis accusantium soluta neque provident vel voluptatem mollitia laborum similique veritatis quas natus maiores, ex quisquam non est. Tempora officia labore voluptates saepe! Placeat facilis minus ipsum reprehenderit cum, quaerat culpa dolorem officiis corrupti, odit tempora numquam. Rerum excepturi commodi reiciendis ratione praesentium nihil repellendus perspiciatis, doloribus, neque tempore expedita suscipit a hic rem ipsa explicabo placeat accusantium sunt aperiam quos? Est accusamus rem laudantium culpa odit sequi eius optio nemo deleniti excepturi dolores vero, aliquid voluptatum corrupti quia reprehenderit nihil? Fugit placeat ipsum nihil optio pariatur veniam dolor facilis voluptatibus voluptatum, libero modi tempore necessitatibus maiores consectetur blanditiis totam similique quaerat velit quo tempora accusamus! Numquam quae iusto possimus, ducimus doloribus nostrum voluptates ab deleniti explicabo, amet accusamus error cumque magnam non qui repudiandae quos laborum reiciendis, facere minima aperiam distinctio praesentium! Molestiae repellat, quisquam laboriosam consequatur quas pariatur est magni magnam distinctio mollitia veritatis dicta consequuntur odio aut eveniet sed illum iure saepe, sint, sequi voluptatem. Dolor ratione veritatis ullam. Assumenda repellendus cumque impedit, sapiente tempore minima! Aliquam libero modi repellendus consectetur repudiandae doloribus placeat laborum laboriosam assumenda nemo eveniet corporis facilis nesciunt repellat consequuntur nobis, ratione culpa ex numquam ipsum quidem voluptas maiores quos reiciendis? Voluptatem repellendus id mollitia sapiente magni reiciendis, numquam, quod sunt odit voluptatibus culpa iste rem tempore blanditiis similique voluptatum ad! Vitae sint dicta sit. Mollitia porro quisquam aut repellendus repellat, illo dolore ullam. Illo laborum ex ut iste, nemo architecto, quidem cumque laudantium ea quam, minus vitae perspiciatis animi sit quo pariatur soluta.</p>
EOD;

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+