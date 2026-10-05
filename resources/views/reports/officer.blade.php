<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Officer ReportController</title>
    <style>
        @font-face {
            font-family: 'KantumruyPro';
            /* src: url("{{ public_path('fonts/KantumruyPro-Regular.ttf') }}") format("truetype"); */
            src: "./fonts/KantumruyPro-Regular.ttf";
            font-weight: normal;
            font-style: normal;
        }
        html body { font-family: 'KantumruyPro'; }
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
<body style="font-family: 'KantumruyPro';">
    <center>
        <h1>
            <img width="100px" src="{{$data['logo']}}" style="float: left; margin-right: 40px;"/>
            <small style="float: left; font-size: 20px; text-align: left;">
                <b>ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ <br/>
                សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្រ្តីរាជការ</b>
            </small>
        </h1>
    </center>
    <p><strong>កាលបរិច្ឆេទបោះពុម្ភ:</strong> {{date("Y-m-d")}}</p>
    <!-- <div class="row">
        <div class="col-sm-3" style="width: 24%; padding-right: 2px; border:1px solid blue;background-color: #f00;">
            <h3>​ចំនួនមន្ត្រីសរុប</h3>
            {{$data["summary"]["total"]}}
        </div>
        <div class="col-sm-3" style="width: 24%; padding-right: 2px; border:1px solid blue;background-color: #f00;">
            <h3>​ចំនួនប្រុស</h3>
            {{$data["summary"]["male"]}}
        </div>
        <div class="col-sm-3" style="width: 24%; padding-right: 2px; border:1px solid blue;background-color: #f00;">
            <h3>​ចំនួនស្រ្តី</h3>
            {{$data["summary"]["female"]}}
        </div>
    </div> -->
    <h4>​ចំនួនមន្ត្រីសរុប: {{$data["summary"]["total"]}} , ​ចំនួនប្រុស: {{$data["summary"]["male"]}}, ​ចំនួនស្រី: {{$data["summary"]["female"]}}</h4>
    
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
            @foreach($data["officers"] as $officer)
                <tr>
                    <td>{{$i}}</td>
                    @foreach($data["dataFields"] as $dataField)
                        <td>{{$officer->$dataField}}​</td>
                    @endforeach
                </tr>
                <?php $i++;?>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: center;">
        <center>រក្សាសិទ្ធគ្រប់យ៉ាងដោយសមាគមសង្គហមិត្ត បុគ្គលិក មន្ត្រីរាជការ នៃក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ</center>
    </div>
</body>
</html>