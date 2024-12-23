<h1>Job Request Notification</h1>
<p>Hello,</p>
<p>A new job request has been created with the following details:</p>
<ul>
    <li><strong>Type of Job:</strong> {{ $jobRequest->type_of_job }}</li>
    <li><strong>Job Order Number:</strong> {{ $jobRequest->job_order_number }}</li>
    <li><strong>End User:</strong> {{ $jobRequest->end_user }}</li>
    <li><strong>Date Needed:</strong> {{ $jobRequest->date_needed }}</li>
    <li><strong>Problem Description:</strong> {{ $jobRequest->problem_description }}</li>
</ul>
<p>Thank you.</p>
