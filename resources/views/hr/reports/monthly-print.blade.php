<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الحضور الشهري — {{ $month->translatedFormat('F Y') }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 24px; color: #111; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background: #f3f4f6; }
        .summary { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; min-width: 120px; }
        .card strong { display: block; font-size: 18px; margin-top: 4px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="margin-bottom:16px;padding:8px 16px;">طباعة</button>
    <h1>تقرير الحضور الشهري</h1>
    <p class="meta">{{ $month->translatedFormat('F Y') }} — من {{ $start->format('Y/m/d') }} إلى {{ $end->format('Y/m/d') }}</p>

    <div class="summary">
        <div class="card">الموظفون<strong>{{ $summary['employees_count'] }}</strong></div>
        <div class="card">حضور<strong>{{ $summary['total_present'] }}</strong></div>
        <div class="card">غياب<strong>{{ $summary['total_absent'] }}</strong></div>
        <div class="card">تأخير<strong>{{ $summary['total_late'] }}</strong></div>
        <div class="card">إجازات<strong>{{ $summary['total_leave_days'] }}</strong></div>
        <div class="card">أذونات<strong>{{ $summary['total_permits'] }}</strong></div>
        <div class="card">معدل الحضور<strong>{{ $summary['avg_attendance_rate'] }}%</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>الموظف</th>
                <th>القسم</th>
                <th>متوقع</th>
                <th>حضور</th>
                <th>تأخير</th>
                <th>غياب</th>
                <th>إجازات</th>
                <th>أذونات</th>
                <th>ساعات</th>
                <th>معدل</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td>{{ $row['employee']->first_name }} {{ $row['employee']->last_name }}</td>
                <td>{{ $row['employee']->department?->name ?? '—' }}</td>
                <td>{{ $row['expected_days'] }}</td>
                <td>{{ $row['present_days'] }}</td>
                <td>{{ $row['late_days'] }}</td>
                <td>{{ $row['absent_days'] }}</td>
                <td>{{ $row['leave_days'] }}</td>
                <td>{{ $row['permit_count'] }}</td>
                <td>{{ $row['total_hours'] }}</td>
                <td>{{ $row['attendance_rate'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
