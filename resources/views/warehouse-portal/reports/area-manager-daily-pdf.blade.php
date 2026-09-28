<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Area Manager Daily Report - {{ $monthLabel }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { margin: 0; }
    </style>
</head>
<body>
    @include('warehouse-portal.reports.partials.area-manager-daily-sheet')
</body>
</html>
