<?php

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment;Filename=document_name.xls');
echo '<html>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<body style="font-family: Khmer MEF1">';
echo '<div class="container-inner"><center><span class="font-mef2" style="color: rgb(13, 114, 162); font-size: 12pt;">
        បញ្ជីសរុប<br></span> <span class="font-mef2" style="color: rgb(13, 114, 162); font-size: 10pt;">
        មន្ត្រីរាជការទទួលប្រាក់ឧបត្ថម្ភលើកទឹកចិត្ត<br> <span>';
if ($incentiveInfo['data']['general']['count_month'] > 1) {
    echo '<span>
                            ប្រចាំខែ '.$incentiveInfo['data']['general']['khmer_from_month'].' ដល់ '.$incentiveInfo['data']['general']['khmer_to_month'].'
                        </span>';
} else {
    echo '<span>
                        ប្រចាំខែ'.$incentiveInfo['data']['general']['khmer_month'].'
                    </span>';
}
echo '</span> <br></span> <div class="clearfix"></div> <br></center> <div class="content">
            <table border="1" class="table">
                <thead>
                    <tr style="background: rgb(234, 227, 34);">
                        <th style="width: 4%;"><center>ល.រ</center></th> 
                        <th style="width: 46%;"><center>អង្គភាព</center></th> 
                        <th style="width: 12%;"><center>ចំនួនមន្ត្រី</center></th> 
                        <th style="width: 20%;"><center>ប្រាក់ឧមត្ថម្ភត្រូវបើក</center></th> 
                        <th style="width: 18%;"><center>ផ្សេងៗ</center></th>
                    </tr>
                </thead>';

foreach ($incentiveInfo['data']['incentiveByEntities'] as $incentiveByEntity) {
    echo '<tr data="[object Object]" class="line-height-tr">
                            <td><center>'.$incentiveByEntity['no'].'</center></td>
                            <td>
                                <b>'.$incentiveByEntity['entity'].'</b>
                            </td>
                            <td><center>'.$incentiveByEntity['total_officer'].'</center></td>
                            <td>
                                <span class="pull-right"><b>'.$incentiveByEntity['total_incentive_with_reminder'].'</b></span>
                            </td>
                            <td></td>
                    </tr>';
}

echo '<tr style="background-color: rgb(179, 230, 255);">
                    <td colspan="2"><span class="font-mef2"><center>សរុបរួម</center></span></td> <td><center><b style="text-decoration: underline;">'.$incentiveInfo['data']['general']['total_officer'].'</b></center></td> 
                    <td><span class="pull-right font-mef2" style="text-decoration: underline;">'.$incentiveInfo['data']['general']['total_incentive_with_reminder_in_khmer'].'</span></td> 
                    <td></td>
                </tr>
            </table>
        </div> <div class="sign-move-from-top" style="position: relative; font-size: 9pt;"><div class="pull-right" style="position: relative; break-inside: avoid;"><span><center>
                ថ្ងៃ'.$incentiveInfo['data']['general']['khmer_day'].' ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_date'].'<br>
                រាជធានីភ្នំពេញ ថ្ងៃទី'.$incentiveInfo['data']['general']['khmer_current_day'].'
                ខែ'.$incentiveInfo['data']['general']['khmer_current_month'].'
                ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_current_year'].'<br> 
                <span class="font-mef2">រៀបចំដោយ <br>មន្ត្រីជំនាញថវិកា<br></span> 
                <div class="font-mef2" style="padding-top: 33px;">'.$incentiveInfo['data']['general']['prepared_by'].'</div></center></span></div> <div class="clearfix"></div> <div class="pull-left" style="margin-top: -20px; position: relative; break-inside: avoid;"><span><center>
                បានឃើញ និងពិនិត្យត្រឹមត្រូវ<br>
                សូមគោរពជូន ឯកឧត្តមអគ្គលេខាធិការ <br>
                មេត្តាពិនិត្យ និងសម្រេចដ៏ខ្ពង់ខ្ពស់ <br>
                ថ្ងៃ <span style="font-family: arial !important;"><span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                ខែ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_date'].' <br>
                រាជធានីភ្នំពេញ 
                ថ្ងៃ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                ខែ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_current_year'].' <br> <span class="font-mef2">អគ្គលេខាធិការរង</span></center></span></div> <div class="clearfix"></div> <div class="pull-right" style="margin-top: -30px; position: relative; break-inside: avoid;"><span><center>
                បានឃើញ និងសូមគោរពជូនសម្រេចដ៍ខ្ពង់ខ្ពស់<br>
                ឯកឧត្តម្ភអគ្គបណ្ឌិតសភាចារ្យ ឧបនាយករដ្ឋមន្ត្រី<br>
                ថ្ងៃ <span style="font-family: arial !important;"><span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                ខែ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_date'].' <br>
                រាជធានីភ្នំពេញ 
                ថ្ងៃ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                ខែ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_current_year'].' <br> <span class="font-mef2">រដ្ឋលេខាធិការ<br>
                និងជាអគ្គលេខាធិការ នៃអគ្គលេខាធិការដ្ឋាន<br>
                គណៈកម្មាធិការដឹកនាំការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ</span></center></span></div> <div class="clearfix"></div> <div class="pull-left" style="margin-top: -20px; position: relative; break-inside: avoid;"><span><center>
                បានឃើញ និងឯកភាព<br>
                ថ្ងៃ <span style="font-family: arial !important;"><span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span> 
                ខែ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  
                ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_date'].' <br>
                រាជធានីភ្នំពេញ 
                ថ្ងៃ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                ខែ <span style="font-family: arial !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
                ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_current_year'].' <br> <span class="font-mef2">ឧបនាយករដ្ឋមន្ត្រី<br>
                រដ្ឋមន្ត្រីក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ</span></center></span></div></div></div>';
echo '</body>';
echo '</html>';
