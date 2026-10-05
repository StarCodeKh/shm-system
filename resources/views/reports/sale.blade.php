<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Test ReportController</title>
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
    {{ print_r($data["data"]) }}
    <h1>
        <img width="100px" src="{{ $data['logo']}}" style="float: left;"/>
         របាយការណ៍បើកផ្តល់ប្រាក់ឧបត្ថម្ភសមាជិក ងាយរងគ្រោងដោយទឹកជំនន់
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
                <th>សួស្តីឆ្នាំថ្មីProduct</th>
                <th>Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            
            <tr>
                <td><strong>Total</strong></td>
                <td class="total"><strong>233</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>