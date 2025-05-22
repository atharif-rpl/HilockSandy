@extends('layouts.app')

@section('title', 'Invoice History')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>INVOICE HISTORY</h2>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">Add new invoice</a>
    </div>
    <p>Halaman ini menampilkan data yang telah diinputkan sebelumnya. Halaman ini yang pertama kali akan ditampilkan.</p>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Invoice Number</th>
                    <th>Customer</th>
                    <th>Delivery Date</th>
                    <th>Submit Date</th>
                    <th>Amount</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $index => $invoice)
                    <tr>
                        <td>{{ $invoices->firstItem() + $index }}</td>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>{{ $invoice->delivery_date->format('d M Y') }}</td>
                        <td>{{ $invoice->submit_date->format('d M Y H:i') }}</td>
                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="action-buttons d-flex gap-1">
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> <span class="d-none d-md-inline">View</span>
                            </a>

                            @auth
                                @if (Auth::user()->role === 'admin')
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> <span class="d-none d-md-inline">Edit</span>
                                    </a>

                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this invoice? This action cannot be undone.');"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash-alt"></i> <span class="d-none d-md-inline">Delete</span>
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $invoices->links() }} {{-- Untuk pagination --}}
@endsection
