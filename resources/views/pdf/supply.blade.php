<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
<h2>Invoice: {{ $record->invoice_number }}</h2>
<p>Date: {{ $record->date }}</p>
<p>Customer: {{ $record->customer_name }}</p>

<table>
    <thead>
    <tr>
        <th>Article</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @if ($supplies->isEmpty())
        <tr>
            <td colspan="5">No supplies available.</td>
        </tr>
    @else
        @foreach ($supplies as $supply)
            <tr>
                <td>{{ $supply->article }}</td>
                <td>{{ $supply->product->name ?? 'Unknown' }}</td>
                <td>{{ $supply->quantity }}</td>
                <td>{{ number_format($supply->price, 2) }}</td>
                <td>{{ number_format($supply->quantity * $supply->price, 2) }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>

</table>
</body>
</html>
