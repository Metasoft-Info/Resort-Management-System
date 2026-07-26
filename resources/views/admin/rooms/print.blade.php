<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Room List - Tufan Resort</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12pt;
            padding: 15mm;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 12px;
            color: #555;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px 10px;
            text-align: left;
            font-size: 11pt;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        @media print {
            body { padding: 10mm; }
            .no-print { display: none; }
        }
        .print-btn {
            position: fixed;
            top: 15px;
            right: 15px;
            background: #059669;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        .print-btn:hover { background: #047857; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>

    <div class="header">
        <h1>Tufan Convention & Resort</h1>
        <p>It's Institution of Tufan Company Limited</p>
        <p>Kamalnagar, Satkhira Sadar | Phone: 01958216727</p>
    </div>

    <div class="meta">
        <span><strong>Report:</strong> Room List</span>
        <span><strong>Total Rooms:</strong> {{ $rooms->count() }}</span>
        <span><strong>Date:</strong> {{ now()->format('d-m-Y') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 60px;">SL</th>
                <th>Room No</th>
                <th>Room Name</th>
                <th>Type</th>
                <th class="text-right">Price/Night</th>
                <th class="text-center">Capacity</th>
                <th class="text-center">Beds</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rooms as $index => $room)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $room->room_number }}</strong></td>
                <td>{{ $room->name }}</td>
                <td>{{ $room->roomType->name ?? ucfirst($room->type) }}</td>
                <td class="text-right">BDT {{ number_format($room->price_per_night, 0) }}</td>
                <td class="text-center">{{ $room->max_guests }}</td>
                <td class="text-center">{{ $room->number_of_beds }}</td>
                <td class="text-center">{{ ucfirst($room->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No rooms found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Developed by Mir Javed Jeetu | 01811480222</p>
        <p>Printed on: {{ now()->format('d-m-Y h:i A') }}</p>
    </div>
</body>
</html>
