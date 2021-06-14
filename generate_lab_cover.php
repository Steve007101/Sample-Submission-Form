<!-- 
    This is used with upload_submission.php to generate a lab cover letter
-->
<?php
// need PHPWord to edit the word file template
// files loaded with console command:
//  composer require phpoffice/phpword
require_once('vendor/autoload.php'); //need variables from here

// for setting different font styles
use PhpOffice\PhpWord\Element\TextRun;

// need DomPDF to save docx as a pdf
// files loaded with console command:
//  composer require dompdf/dompdf
\PhpOffice\PhpWord\Settings::setPdfRendererPath('vendor/dompdf/dompdf');
\PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

function generateLabCoverLetter($date, $sample_num, $lab_full_comp, 
$lab_oxide, $lab_precious, $lab_moisture, $lab_as, $lab_ba, $lab_cd, $lab_cr, $lab_pb, $lab_hg, $lab_se, $lab_ag, $lab_notes,
$distributor_name, $distributor_phone, $distributor_email, $lab_cover_letter_dir, $template_path, $sample_recipient)  {
    echo "generateLabCoverLetter Starting...".$date. "...Sample number: ". $sample_num."...";

    // make directory for lab cover letters if it doesn't exist
    if (!file_exists($lab_cover_letter_dir)) {
        if (!mkdir($lab_cover_letter_dir, 0777)) {
        echo "\rFailed to create Lab Cover Letter directory...";
        exit();
        } else {
        echo "\rLab Cover Letter directory created...";
        }
    } else {
        echo "\rLab Cover Letter directory exists...";
    } 

    // open the template file for editing
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template_path);

    // not necessary since template is in Calibri already
    // define font style values for all text
    // $templateProcessor->setDefaultFontName('Calibri');

    $text1_arr = [];
    $text2_arr = [];
    $text3_arr = [];
    $text4_arr = [];
    
    // set all the text, this is in an array entry per line
    // because the newline format has to be custom set for PHPWord
    // with a function
    array_push ($text1_arr, $date);
    array_push ($text1_arr, "");
    if ($sample_recipient == "St Louis Testing (Lab)") {
        array_push ($text1_arr, "St. Louis Testing Laboratories, Inc.");
        array_push ($text1_arr, "2810 Clark Avenue");
        array_push ($text1_arr, "Saint Louis, MO 63103");
    } else if ($sample_recipient == "UMSL Labs") {
        array_push ($text1_arr, "University of Missouri – St. Louis");
        array_push ($text1_arr, "1 University Blvd.");
        array_push ($text1_arr, "Department of Chemistry 309 SLB");
        array_push ($text1_arr, "St. Louis, MO 63121");
    }
    array_push ($text1_arr, "");
    array_push ($text1_arr, "Enclosed, please find Interco Sample ".$sample_num.".");
    array_push ($text1_arr, "");
    if ($lab_notes) {
        // need to split this text into from EOL's into the array too
        $textlines = explode(PHP_EOL, $lab_notes);
        foreach($textlines as $line) {
            array_push ($text1_arr, $line);
        }
        array_push ($text1_arr, "");
    }
    if ($lab_full_comp || $lab_oxide || $lab_precious || $lab_moisture 
        || $lab_as || $lab_ba || $lab_cd || $lab_cr 
        || $lab_pb || $lab_hg || $lab_se || $lab_ag) {
        array_push ($text1_arr, "Please run the following lab test(s):");
        array_push ($text1_arr, "");
    }
    if ($lab_full_comp) {
        array_push ($text2_arr, "             Full Compositional Analysis");
        array_push ($text2_arr, "");
        array_push ($text2_arr, "             Page 1: Full Metal Compositional Analysis plus Organics: Carbon (C), Chlorine (Cl), Fluorine (F),");
        array_push ($text2_arr, "             etc.");
        array_push ($text2_arr, "");
    }
    if ($lab_oxide) {
        array_push ($text2_arr, "             Page 2: Oxide Breakdown (if present)");
        array_push ($text2_arr, "");
    }
    if ($lab_precious) {
        array_push ($text2_arr, "             Analysis to include any/all Precious Metals present.");
        array_push ($text2_arr, "");
    }
    if ($lab_moisture) {
        array_push ($text2_arr, "             Please check for moisture content.");
        array_push ($text2_arr, "");
    }
    if ($lab_as || $lab_ba || $lab_cd || $lab_cr || $lab_pb || $lab_hg || $lab_se || $lab_ag) {
        array_push ($text2_arr, "             TCLP Testing -- Please perform Toxicity Characteristic Leaching Procedure test for");
        // need to build list for next line
        $text_tclp = "             ";
        $tclp_count = 0;
        if ($lab_as) {
            $tclp_count++;
            $text_tclp .= "Arsenic (As)";
        }
        if ($lab_ba) {
            if ($tclp_count !=0) {
                $text_tclp .=", ";
            }
            $tclp_count++;
            $text_tclp .= "Barium (Ba)";
        }
        if ($lab_cd) {
            if ($tclp_count !=0) {
                $text_tclp .=", ";
            }
            $tclp_count++;
            $text_tclp .= "Cadmium (Cd)";
        }
        if ($lab_cr) {
            if ($tclp_count !=0) {
                $text_tclp .=", ";
            }
            $tclp_count++;
            $text_tclp .= "Chromium (Cr)";
        }
        if ($lab_pb) {
            if ($tclp_count !=0) {
                $text_tclp .=", ";
            }
            $tclp_count++;
            $text_tclp .= "Lead (Pb)";
        }
        if ($lab_hg) {
            if ($tclp_count !=0) {
                $text_tclp .=", ";
            }
            $tclp_count++;
            $text_tclp .= "Mercury (Hg)";
        }
        if ($lab_se) {
            if ($tclp_count !=0) {
                $text_tclp .=", ";
            }
            $tclp_count++;
            $text_tclp .= "Selenium (Se)";
        }
        $text_tclp_2 = "";
        $special_case = 0;
        if ($lab_ag) {
            if ($tclp_count !=0) {
                $text_tclp .=", ";
            }
            // special case, will need to put silver on newline
            if ($tclp_count == 7) {
                $special_case++;
            }
            $tclp_count++;
            $text_tclp_2 .= "Silver (Ag)";
        }
        // now output the list
        if ($special_case) {
            array_push ($text2_arr, $text_tclp);
            array_push ($text2_arr, "             ".$text_tclp_2);
        } else {
            array_push ($text2_arr, $text_tclp.$text_tclp_2);
        }
        array_push ($text2_arr, "");
        
    }
    array_push ($text3_arr, "Please email reports to labreports@intercotradingco.com as soon as they are available.");
    array_push ($text3_arr, "");
    array_push ($text4_arr, "Should you have any questions, please contact the ".$distributor_name." at ".$distributor_phone);
    array_push ($text4_arr, $distributor_email);
    array_push ($text4_arr, "");
    array_push ($text4_arr, "Thank you,");
    array_push ($text4_arr, "");
    array_push ($text4_arr, "Interco – A Metaltronics Recycler");


    // array to hold each final section
    $content_arr = [];

    // add newlines and formatting for each section
    // since addTextBreak() is the most compatible way to do this and each section
    // has its own formatting array, this is the way I decided to do it
    array_push($content_arr, new TextRun());
    foreach($text1_arr as $line) {
        $content_arr[0]->addText($line, array('italic' => false, 'bold' => false, 'size' => '12'));
        $content_arr[0]->addTextBreak();
    }

    array_push($content_arr, new TextRun());
    foreach($text2_arr as $line) {
        $content_arr[1]->addText($line, array('italic' => true, 'bold' => false, 'size' => '12'));
        $content_arr[1]->addTextBreak();
    }

    array_push($content_arr, new TextRun());
    foreach($text3_arr as $line) {
        $content_arr[2]->addText($line, array('italic' => false, 'bold' => true, 'size' => '12'));
        $content_arr[2]->addTextBreak();
    }

    array_push($content_arr, new TextRun());
    foreach($text4_arr as $line) {
        $content_arr[3]->addText($line, array('italic' => false, 'bold' => false, 'size' => '12'));
        $content_arr[3]->addTextBreak();
    }


/*     // format each section
    $content1 = new TextRun();
    $content1->addText($text1, array('italic' => false, 'bold' => false, 'size' => '12'));
    $content2 = new TextRun();
    $content2->addText($text2, array('italic' => true, 'bold' => false, 'size' => '12'));
    $content3 = new TextRun();
    $content3->addText($text3, array('italic' => false, 'bold' => true, 'size' => '12'));
    $content4 = new TextRun();
    $content4->addText($text4, array('italic' => false, 'bold' => false, 'size' => '12')); */

    // put the sections into the template in the data1-4 spots
    $templateProcessor->setComplexValue('data1', $content_arr[0]);
    $templateProcessor->setComplexValue('data2', $content_arr[1]);
    $templateProcessor->setComplexValue('data3', $content_arr[2]);
    $templateProcessor->setComplexValue('data4', $content_arr[3]);

    $basename = $sample_num;

    // create output filename
    $docxName = $basename.".docx";
    // in case of multiple files with same name, add a number
    $nameOffset = 1;
    while(file_exists($lab_cover_letter_dir."/".$docxName))
    {
      $docxName= $nameOffset.$docxName;
      $docxNameOffset++;
    } 
    // save docx file
    $templateProcessor->saveAs($lab_cover_letter_dir."/".$docxName);
    if (file_exists($lab_cover_letter_dir."/".$docxName)) {
        echo "Lab cover letter ".$lab_cover_letter_dir."/".$docxName." successfully created";
    }
    else {
        echo "Lab cover letter ".$lab_cover_letter_dir."/".$docxName." failed to be created";
    }
    // create and save pdf
    // currently looks like crap, very bad formatting
/*     $pdfName = $basename.".pdf";
    $document = $phpWord->loadTemplate($lab_cover_letter_dir."/".$docxName);
	$document->saveAs($temDoc);
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($temDoc);
	$xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord,'PDF');
	$xmlWriter->save($lab_cover_letter_dir."/".$pdfName);  // Save to PDF
    unlink($temDoc);
    if (file_exists($lab_cover_letter_dir."/".$pdfName)) {
        echo "Lab cover letter ".$lab_cover_letter_dir."/".$pdfName." successfully created";
    }
    else {
        echo "Lab cover letter ".$lab_cover_letter_dir."/".$pdfName." failed to be created";
    } */
    return $docxName;
}

?>
