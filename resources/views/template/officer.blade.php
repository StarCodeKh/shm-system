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
    <h1>
        <img width="100px" src="{{ $data['logo']}}" style="float: left;"/>
         របាយការណ៍សមាជិក
    </h1>
    <p><strong>Month:</strong> Date</p>
    <div class="row">
        <div class="col-sm-3" style="float: left; widht: 32.99%">
            <h3>​ចំនួនមន្ត្រី</h3>
            827
        </div>
        <div class="col-sm-3" style="float: left; widht: 32.99%">
            <h3>​ចំនួនមន្ត្រី</h3>
            827
        </div>
        <div class="col-sm-3" style="float: left; widht: 32.99%">
            <h3>​ចំនួនមន្ត្រី</h3>
            827
        </div>
        <div class="col-sm-3" style="float: left; widht: 32.99%">
            <h3>​ចំនួនមន្ត្រី</h3>
            827
        </div>
    </div>
    <table>
        <thead>
            <tr>
                @foreach($data["dataFields"] as $dataField)
                    <th>{{$dataField}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data["officers"] as $officer)
                <tr>
                    @foreach($data["dataFields"] as $dataField)
                        <td>{{$officer->$dataField}}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>