<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRF {{ $jobRequest['status'] === 'C' ? 'Rejected' : 'Approved' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .col {
            flex: 1;
            padding: 5px;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }
        .logo {
            height: 50px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 1.2em;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f4f4f4;
        }
        .bg-primary {
            background-color: #007bff;
            color: white;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .signature {
            height: 50px;
            vertical-align: top;
        }
        .border-bottom {
            border-bottom: 1px solid black;
        }
        .note {
            font-size: 0.9em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="https://ticket.appsafexpress.com/logo.php" alt="Left Logo">
            <div class="title">MATERIALS REQUISITION FORM</div>
            <div class="font-weight-bold font-italic">{{ $jobRequest['mrf_order_number'] }}</div>
        </div>

        <table class="table">
            <tr>
                <td><strong>SITE:</strong> {{ $jobRequest['site_name'] }}</td>
                <td><strong>DATE REQUEST:</strong> {{ $jobRequest['date_requested'] }}</td>
            </tr>
            <tr>
                <td><strong>REQUISITIONER:</strong> {{ $jobRequest['createdby']  }}</td>
                <td><strong>DATE NEEDED:</strong> {{ $jobRequest['date_needed'] }}</td>
            </tr>
        </table>

        <table class="table text-center">
            <thead class="bg-primary">
                <tr>
                    <th>ITEM NO.</th>
                    <th>PARTICULARS</th>
                    <th>DESCRIPTION</th>
                    <th>QUANTITY</th>
                    <th>UNIT</th>
                    <th>UNIT PRICE</th>
                    <th>PRINCIPAL AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($jobRequest['materials']))
                @foreach($jobRequest['materials'] as $material)
                    <tr>
                        <td>{{ $material['particular'] }}</td>
                        <td>{{ $material['description'] }}</td>
                        <td>{{ $material['quantity'] }}</td>
                        <td>{{ $material['uom'] }}</td>
                        <td>{{ number_format($material['price'], 2) }}</td>
                        <td>{{ number_format($material['total_amount'], 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align: center;">No materials listed</td>
                </tr>
            @endif
            </tbody>
        </table>

        <p class="font-weight-bold">PURPOSE: {{ $jobRequest['purpose'] }}</p>

        <table class="table">
            <tr>
                <td colspan="2">PREPARED BY: {{ $jobRequest['createdby']  }}</td>
            </tr>
            <tr>
                <td colspan="2" class="note">Note: Procurement cut-off time of receiving requests: MWF @8AM-2PM</td>
            </tr>
            <tr>
                <td colspan="2" class="note">*7 Days lead for consumables (Repeat Order)</td>
            </tr>
            <tr>
                <td colspan="2" class="note">*10 Days lead time for (New Request) beyond TOR for Local Vendor</td>
            </tr>
            <tr>
                <td colspan="2" class="note">*30 Days lead time for (New Request) beyond TOR for Foreign Vendor</td>
            </tr>
            <tr>
                <td class="signature">
                    <span class="border-bottom">Prepared By: {{ $jobRequest['createdby']  }}</span><br>
                    {{ $jobRequest['createdbyPosition']  }}
                </td>
                <td class="signature">
                    <span class="border-bottom">Noted By: {{ $jobRequest['departmenthead']  }}</span><br>
                    {{ $jobRequest['departmentheadPosition']  }}
                </td>
            </tr>
        </table>
    </div>



        <strong>Status:</strong> {{ $jobRequest['status'] === 'C' ? 'Rejected' : 'Approved' }}
    </div>
</body>
</html>
