<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Renewable Energy Details</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .header h2 { color: #28a745; margin: 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table th, .info-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .info-table th { background-color: #f8f9fa; width: 30%; }
        .footer { text-align: center; font-size: 10px; color: #777; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
        .section-title { background: #e9ecef; padding: 5px 10px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Renewable Energy Entry Details</h2>
        <p>Generated on: {{ date('d-M-Y H:i A') }}</p>
    </div>

    <div class="section-title">General Information</div>
    <table class="info-table">
        <tr>
            <th>Entry Type</th>
            <td>{{ ucfirst($entry->type) }}</td>
        </tr>
        <tr>
            <th>Agent Name</th>
            <td>{{ $entry->creator->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Visit Date</th>
            <td>{{ $entry->visit_date ? date('d-m-Y', strtotime($entry->visit_date)) : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Beneficiary Name</th>
            <td>{{ $entry->beneficiary_name }}</td>
        </tr>
        <tr>
            <th>Mobile No</th>
            <td>{{ $entry->mobile_no }}</td>
        </tr>
        <tr>
            <th>Beneficiary Number</th>
            <td>{{ $entry->beneficiary_number }}</td>
        </tr>
        <tr>
            <th>Village</th>
            <td>{{ $entry->village }}</td>
        </tr>
        <tr>
            <th>Post Office</th>
            <td>{{ $entry->po_name }}</td>
        </tr>
    </table>

    <div class="section-title">Location Details</div>
    <table class="info-table">
        <tr>
            <th>Division</th>
            <td>{{ $entry->division->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>District</th>
            <td>{{ $entry->district->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Upazila</th>
            <td>{{ $entry->upazila->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Union</th>
            <td>{{ $entry->union->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Livestock Details</th>
            <td>{{ $entry->livestock_details ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Plant Details</div>
    <table class="info-table">
        <tr>
            <th>Plant Size</th>
            <td>{{ $entry->plant_size }} {{ $entry->plant_size_unit }}</td>
        </tr>
        <tr>
            <th>Start Date</th>
            <td>{{ $entry->plant_start_date ? date('d-m-Y', strtotime($entry->plant_start_date)) : 'N/A' }}</td>
        </tr>
        <tr>
            <th>End Date</th>
            <td>{{ $entry->plant_end_date ? date('d-m-Y', strtotime($entry->plant_end_date)) : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Remarks</th>
            <td>{{ $entry->remarks ?? 'N/A' }}</td>
        </tr>
    </table>
</body>
</html>
