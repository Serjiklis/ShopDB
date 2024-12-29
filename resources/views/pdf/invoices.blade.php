<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Накладные</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
        }

        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .invoice-date {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
@foreach ($invoices as $invoiceNumber => $supplies)
    <div class="invoice">
        <div class="invoice-title">Накладная № {{ $invoiceNumber }}</div>
        <div class="invoice-date">Дата: {{ $supplies->first()->date }}</div>
        <table class="table">
            <thead>
            <tr>
                <th>№</th>
                <th>Артикул</th>
                <th>Наименование товара</th>
                <th>Количество</th>
                <th>Цена за единицу</th>
                <th>Сумма</th>
            </tr>
            </thead>
            <tbody>
            @php $totalSum = 0; @endphp
            @foreach ($supplies as $index => $supply)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $supply->article }}</td>
                    <td>{{ $supply->product->name ?? 'Неизвестно' }}</td>
                    <td>{{ $supply->quantity }}</td>
                    <td>{{ number_format($supply->price, 2, ',', ' ') }}</td>
                    <td>{{ number_format($supply->quantity * $supply->price, 2, ',', ' ') }}</td>
                </tr>
                @php $totalSum += $supply->quantity * $supply->price; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="5">Итого</td>
                <td>{{ number_format($totalSum, 2, ',', ' ') }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="page-break"></div>
@endforeach
</body>
</html>
