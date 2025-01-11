<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRF {{ $jobRequest['status'] === 'C' ? 'Rejected' : 'Approved' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .container {
    width: auto;
    max-width: 100%;
    height: auto;
    margin: 0 auto;
    position: relative;
    padding: 1cm;
    box-sizing: border-box;
    background-color: #fff;
}
@page {
    size: A4;
    margin: 1cm;
}


        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header img {
            width: 150px;
        }

        .header div {
            text-align: right;
        }

        .header div p {
            margin: 0;
        }

        .logo {
            width: 260px;
            /* Adjust size as needed */
            height: auto;
        }

        .title {
            text-align: center;
            margin: 20px 0;
        }

        .details,
        .table,
        .footer {
            width: 100%;
            border-collapse: collapse;
        }

        .details td,
        .table td,
        .footer td {
            border: 1px solid #000;
            padding: 5px;
        }

        .details td {
            width: 50%;
        }

        .table th,
        .table td {
            text-align: center;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            border: 1px solid #000;
        }

        .footer td {
            text-align: left;
        }

        .footer .signature {
            height: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="flex: 1; text-align: left;">
                <img class="logo" src="{{ public_path('assets/dist/img/logo.png') }}" alt="Safexpress Logistics Inc.">
            </div>
            
            <div style="flex: 1; text-align: center;">
                <p>MATERIALS REQUISITION FORM</p>
            </div>
        
            <div style="flex: 1; text-align: right;">
                <p>{{ $jobRequest['mrf_order_number'] }}</p>
            </div>
        </div>
        
        

        <table class="details">
            <tr>
                <td><strong>SITE:</strong> {{ $jobRequest['site_name'] }}</td>
                <td><strong>DATE REQUEST:</strong> {{ $jobRequest['date_requested'] }}</td>
            </tr>
            <tr>
                <td><strong>REQUISITIONER:</strong> {{ $jobRequest['createdby'] }}</td>
                <td><strong>DATE NEEDED:</strong> {{ $jobRequest['date_needed'] }}</td>
            </tr>
        </table>

        <table class="table" style="margin-top:10px;">
            <thead>
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
                @if (!empty($jobRequest['materials']))
                    @foreach ($jobRequest['materials'] as $index => $material)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $material['particulars'] }}</td>
                            <td>{{ $material['description'] }}</td>
                            <td>{{ $material['quantity'] }}</td>
                            <td>{{ $material['uom'] }}</td>
                            <td>{{ number_format($material['unit_price'], 2) }}</td>
                            <td>{{  number_format($material['quantity'] * $material['unit_price'],2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" style="text-align: center;">No materials listed</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <p class="font-weight-bold">PURPOSE: {{ $jobRequest['purpose'] }}</p>

        <table class="footer">
            <tr>
                <td colspan="2">PREPARED BY: {{ $jobRequest['createdby'] }}</td>
            </tr>
            <tr>
                <td colspan="2" class="note">Note: Procurement cut-off time of receiving requests: MWF @8AM-2PM
                </td>
            </tr>
            <tr>
                <td colspan="2" class="note">*7 Days lead for consumables (Repeat Order)</td>
            </tr>
            <tr>
                <td colspan="2" class="note">*10 Days lead time for (New Request) beyond TOR for Local Vendor</td>
            </tr>
            <tr>
                <td colspan="2" class="note">*30 Days lead time for (New Request) beyond TOR for Foreign Vendor
                </td>
            </tr>
            <tr>
                <td class="signature">
                    Prepared By:<br/> <span style="text-decoration: underline;">{{ $jobRequest['createdby'] }}</span><br>
                    {{ $jobRequest['createdbyPosition'] }}
                </td>
                <td class="signature">
                    Noted By:<br/>
                        @if ($jobRequest['status'] === 'C')
                         
                        @else
                        <span style="text-decoration: underline;" >
                         {{ $jobRequest['departmenthead'] }}</span><br>
                    {{ $jobRequest['departmentheadPosition'] }}
                        @endif
                </td>
            </tr>
        </table>
        <br>
         <span>{{ $jobRequest['status'] === 'C' ? 'Rejected' : 'Approved' }} Date:{{ $jobRequest['ApprovedDate'] }}</span>
    </div>

    
    </div>
</body>

</html>
