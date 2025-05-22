@extends('layouts.app')

@section('title', 'Invoice Detail - ' . $invoice->invoice_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>INVOICE DETAIL: {{ $invoice->invoice_number }}</h2>
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back to History</a>
</div>
<hr>

<div class="row mb-3">
    <div class="col-md-6">
        <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Customer Name:</strong> {{ $invoice->customer_name }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Delivery Date:</strong> {{ $invoice->delivery_date->format('d F Y') }}</p>
        <p><strong>Submit Date:</strong> {{ $invoice->submit_date->format('d F Y H:i') }}</p>
    </div>
</div>

<h4>Invoice Items</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Coil Number</th>
            <th>Width (mm)</th>
            <th>Length (mm)</th>
            <th>Thickness (mm)</th>
            <th>Weight (kg)</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->details as $detail)
        <tr>
            <td>{{ $detail->coil_number }}</td>
            <td>{{ number_format($detail->width, 2) }}</td>
            <td>{{ number_format($detail->length, 2) }}</td>
            <td>{{ number_format($detail->thickness, 2) }}</td>
            <td>{{ number_format($detail->weight, 2) }}</td>
            <td>{{ number_format($detail->price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-end fw-bold">Total Amount:</td>
            <td class="fw-bold">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endsection