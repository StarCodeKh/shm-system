<?php

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment;Filename=document_name.xls');
echo '<html>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<body style="font-family: Khmer MEF1">';
echo '<div class="consolidate-payroll-container rotate-screen" style="margin: 0px auto;"><center><span class="font-mef2" style="color: rgb(13, 114, 162); font-size: 10pt;">
        បញ្ជីរាយនាម <br>
        មន្រ្តីរាជការទទួលប្រាក់ឧបត្ថម្ភមុខងារក្រុមការងារកែទម្រង់ការគ្រប់គ្រងហិរញ្ញវត្ថុសាធារណៈ';
if ($incentiveInfo['data']['general']['count_month'] > 1) {
    echo '<span>
                    សម្រាប់ខែ '.$incentiveInfo['data']['general']['khmer_from_month'].' ដល់ '.$incentiveInfo['data']['general']['khmer_to_month'].'
                </span>';
} else {
    echo '<span>
                សម្រាប់ខែ'.$incentiveInfo['data']['general']['khmer_month'].'
            </span>';
}
echo 'ឆ្នាំ'.$incentiveInfo['data']['general']['khmer_year'].'
         <br></span> <div class="clearfix"></div></center> <div id="content">
         <table border="1" class="table">
            <thead>
                <tr style="background-color: rgb(236, 226, 65);">
                    <th colspan="2" class="border-dash" style="width: 50px;">ល.រ</th> 
                    <th class="border-dash">គោត្តនាម និង នាម</th> 
                    <th class="border-dash">តួនាទី</th> 
                    <th class="border-dash">ឈ្មោះធនាគា</th> 
                    <th class="border-dash">លេខគណនី</th> 
                    <th class="border-dash">សន្ទស្សន៍</th> 
                    <th class="border-dash">ប្រាក់ឧបត្ថម្ភ</th> 
                    <th class="border-dash">ប្រាក់បង្វែរ</th> 
                    <th class="border-dash" style="background-color: rgb(234, 198, 204);">ប្រាក់រំលឹក</th> 
                    <th class="border-dash">ប្រាក់ឧបត្ថម្ភជាប់ពន្ធ</th> 
                    <th class="border-dash">ពន្ធត្រូវបង់</th> 
                    <th class="border-dash">ប្រាក់ត្រូវបើក</th> 
                    <th class="border-dash">ផ្សេងៗ</th>
                </tr> 
                <tr style="background: rgb(172, 158, 39); color: rgb(255, 255, 255);">
                    <td style="font-size: 7pt; padding: 0px;"><i><center>1</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>2</center></i></td>
                    <td style="font-size: 7pt; padding: 0px;"><i><center>3</center></i></td>
                    <td style="font-size: 7pt; padding: 0px;"><i><center>4</center></i></td>
                    <td style="font-size: 7pt; padding: 0px;"><i><center>5</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>6</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>7</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>8</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>9</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px; background-color: rgb(234, 198, 204);"><i><center>10</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>11=8+9+10</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>12=គណនាពន្ធតាមថ្នាក់</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>13=11-12</center></i></td> 
                    <td style="font-size: 7pt; padding: 0px;"><i><center>14</center></i></td>
                </tr>
            </thead>
            <tbody data="[object Object]" style="width: 100%;">';
foreach ($incentiveInfo['data']['incentiveByEntities'] as $incentiveByEntity) {
    echo '<tr style="background: rgb(189, 215, 238);">
                            <td colspan="5">
                                <b>'.$incentiveByEntity['code'].'-'.$incentiveByEntity['entity'].'</b>
                            </td>
                            <td></td>
                            <td><center>សរុប '.$incentiveByEntity['total_officer'].'រូប</center></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr> ';

    foreach ($incentiveByEntity['officers'] as $officer) {
        echo '<tr data="[object Object]">
                                <td><center></center></td>
                                <td><center>'.$officer['no'].'</center></td>
                                <td>'.$officer['first_name'].' '.$officer['last_name'].'</td>
                                <td>'.$officer['duty'].'</td>
                                <td>'.$officer['bank'].'</td>
                                <td>&#8203;'.$officer['bank_account_no'].'</td>
                                <td>'.$officer['index'].'</td>
                                <td>'.$officer['incentive'].'</td>
                                <td>'.$officer['return_amount'].'</td>
                                <td>'.$officer['reminder_amount'].'</td>
                                <td>'.$officer['total_incentive_with_reminder'].'</td>
                                <td>'.$officer['tax'].'</td>
                                <td>'.$officer['incentive_after_tax'].'</td>
                                <td></td>
                            </tr>';
    }
}
echo '</table>
         </div>';
echo '</body>';
echo '</html>';
