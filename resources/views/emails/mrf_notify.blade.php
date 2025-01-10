<!DOCTYPE html>
<html lang="en">
<style>
    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .badge-warning {
        color: #856404;
        background-color: #fff3cd;
    }

    .badge-success {
        color: #155724;
        background-color: #d4edda;
    }

    .badge-danger {
        color: #721c24;
        background-color: #f8d7da;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Order Request</title>

</head>

<body>
    <div id=":n4" class="ii gt"
        jslog="20277; u014N:xr6bB; 1:WyIjdGhyZWFkLWY6MTgxOTE5MTcxMTE0MzY2ODYyNiJd; 4:WyIjbXNnLWY6MTgxOTE5MTcxMTE0MzY2ODYyNiIsbnVsbCxudWxsLG51bGwsMSwwLFsxLDAsMF0sOTMsNjI0LG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCwxLG51bGwsWzNdLFszXSxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCwwXQ..">
        <div id=":n5" class="a3s aiL ">
            <div class="adM">
            </div><u></u>
            <div
                style="margin:0;font-family:'Rubik','Segoe UI',Tahoma,Geneva,Verdana,sans-serif!important;text-align:center">
                <div
                    style="max-width:600px;margin:10px auto;border:1px solid #c3cdc9;border-radius:8px;background-color:white">
                    <div style="height:200px;width:100%;margin-top:60px;border-bottom:1px solid #c3cdc9">
                        <img width="175" height="70" src="https://ticket.appsafexpress.com/logo.php"
                            class="CToWUd" data-bit="iit">
                        <h2 style="font-weight:500;color:#2d4f43">Material Request Form</h2>

                    </div>
                    <div style="padding:48px;margin:auto;text-align:left">

                        {{-- Show this if the status is 'A' (Approved) or 'C' (Rejected) --}}
                        <?php if ($emailRequest->status === 'A' || $emailRequest->status === 'C') : ?>
                        <p>
                            <strong>Hi {{ $emailRequest->created }}!</strong>
                        </p>
                        <?php endif; ?>

                        {{-- Show this if the status is 'P' (Pending) --}}
                        <?php if ($emailRequest->status === 'P') : ?>
                        <p>
                            <strong>Hi {{ $emailRequest->departmenthead }}!</strong>
                        </p>
                        <p>{{ $emailRequest->created }} has requested your approval for the following:</p>
                        <?php endif; ?>

                        <table
                            style="margin-top:10px;margin-bottom:30px;width:100%;border-radius:4px;border:1px solid #c3cdc9;border-collapse:separate;border-spacing:0px">
                            <tbody>

                                <tr>
                                    <td style="border-bottom:1px solid #c3cdc9;padding:10px">
                                        <div
                                            style="text-transform:uppercase;font-size:12px;color:#2d4f43;font-weight:bold">
                                            MRF Number</div>
                                        <div>
                                            {{ $emailRequest->mrf_order_number }}
                                        </div>
                                    </td>
                                </tr>
                             
                                <tr>
                                    <td style="border-bottom:1px solid #c3cdc9;padding:10px">
                                        <div
                                            style="text-transform:uppercase;font-size:12px;color:#2d4f43;font-weight:bold">
                                            Purpose</div>
                                        <div>
                                            {{ $emailRequest->purpose }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom:1px solid #c3cdc9;padding:10px">
                                        <div
                                            style="text-transform:uppercase;font-size:12px;color:#2d4f43;font-weight:bold">
                                            Date Needed</div>
                                        <div>
                                            {{ $emailRequest->date_needed }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom:1px solid #c3cdc9;padding:10px">
                                        <div
                                            style="text-transform:uppercase;font-size:12px;color:#2d4f43;font-weight:bold">
                                            STATUS</div>
                                        <div>
                                            <?php
                                            $badgeClass = '';
                                            $statusText = '';

                                            if ($emailRequest->status === 'P') {
                                                $badgeClass = 'badge-warning';
                                                $statusText = 'Pending';
                                            } elseif ($emailRequest->status === 'A') {
                                                $badgeClass = 'badge-success';
                                                $statusText = 'Approved';
                                            } elseif ($emailRequest->status === 'C') {
                                                $badgeClass = 'badge-danger';
                                                $statusText = 'Rejected';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span>
                                        </div>

                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        <?php if ($emailRequest->status === 'P') : ?>
                        <tbody>
                            <tr>
                                <td>
                                    <div
                                        style="background-color:#17ad49;border:none;border-radius:4px;width:120px;height:32px;color:#ffffff;font-size:16px;font-style:normal;font-weight:500;font-family:&quot;Roboto&quot;,&quot;Arial&quot;;line-height:32px;display:inline-block;float:left;margin:3px;text-align:center">
                                        <a href="https://slimonitoring.appsafexpress.com/job-order-request-list"
                                            style="color:#fff;text-decoration:none" target="_blank">Approve</a>
                                    </div>
                                    <div
                                        style="background-color:#da2f38;border:none;border-radius:4px;width:120px;height:32px;color:#ffffff;font-size:16px;font-style:normal;font-weight:500;font-family:&quot;Roboto&quot;,&quot;Arial&quot;;line-height:32px;display:inline-block;float:left;margin:3px;text-align:center">
                                        <a href="https://slimonitoring.appsafexpress.com/job-order-request-list"
                                            style="color:#fff;text-decoration:none" target="_blank">Reject</a>
                                    </div>

                                </td>
                            </tr>
                        </tbody>
                        <?php endif; ?>
                    </div>
                </div>



            </div>
        </div>
    </div>
</body>

</html>
