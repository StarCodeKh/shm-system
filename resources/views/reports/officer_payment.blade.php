<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Officer ReportController</title>
    <style>
        @font-face {
            font-family: 'Khmer OS Battambang Regular';
            src: url("{{ public_path('fonts/Khmer OS Battambang Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        body { font-family: 'Khmer OS Battambang Regular'; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        .total { font-weight: bold; color: green; }
        .col-sm-3{
            float: left;
            width: 33.33%;
        }
    </style>
</head>
<body>
    <center>
        <h1>
            <img width="100px" src="{{$data['logo']}}" style="float: left; margin-right: 40px;"/>
            <small style="float: left; font-size: 20px; text-align: left;">
                <b>ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ <br/>
                សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្រ្តីរាជការ</b>
            </small>
        </h1>
    </center>
    <h2 style="text-align: center;"><strong>របាយការណ៍បង់ប្រាក់រួចរបស់សមាជិក</strong></h2>
    <div><strong>កាលបរិច្ឆេទបោះពុម្ភ:</strong> {{date("Y-m-d")}}</div>
    <table>
        <thead>
            <tr>
                <th>{{trans("common.No")}}</th>
                @foreach($data["dataFields"] as $dataField)
                    <th>{{trans("common.".$dataField)}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <?php $i=1;?>
            @foreach($data["officerPayments"] as $officerPayment)
                <tr>
                    <td>{{$i}}</td>
                    @foreach($data["dataFields"] as $dataField)
                        <td>{{$officerPayment->$dataField}}</td>
                    @endforeach
                </tr>
                <?php $i++;?>
            @endforeach
        </tbody>
    </table>
</body>
</html>