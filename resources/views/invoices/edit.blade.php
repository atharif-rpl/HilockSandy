@extends('layouts.app')

@section('title', 'Edit Invoice - ' . $invoice->invoice_number)

@section('content')
<h2>EDIT INVOICE: {{ $invoice->invoice_number }}</h2>
<hr>
<form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
    @csrf
    @method('PUT') {{-- Method spoofing untuk UPDATE --}}

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="invoice_number_display" class="form-label">Invoice No</label>
            <input type="text" class="form-control" id="invoice_number_display" value="{{ $invoice->invoice_number }}" readonly>
            {{-- Kita tidak mengirim invoice_number untuk diubah di sini, atau jika iya, gunakan input tersembunyi atau input biasa --}}
            {{-- <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}"> --}}
        </div>
        <div class="col-md-4">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $invoice->customer_name) }}" placeholder="Input nama customer" required>
            @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label for="delivery_date" class="form-label">Delivery Date</label>
            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', $invoice->delivery_date->format('Y-m-d')) }}" placeholder="Input delivery date" required>
             @error('delivery_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <h4>Detail Invoice</h4>
    <table class="table table-bordered" id="invoiceDetailsTable">
        <thead>
            <tr>
                <th>Coil Number</th>
                <th>Width (mm)</th>
                <th>Length (mm)</th>
                <th>Thickness (mm)</th>
                <th>Weight (kg)</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            {{-- Baris detail akan diisi berdasarkan data yang ada dan bisa ditambah/dihapus --}}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold">Total Price</td>
                <td id="totalPriceDisplay" class="fw-bold">0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <button type="button" class="btn btn-primary mb-3" id="addLineBtn">Add Line</button>
    <small class="form-text text-muted d-block mb-3">Untuk menambahkan atau mengubah baris pada detail invoice.</small>

    <div class="d-flex justify-content-between">
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-success">Update Invoice</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addLineBtn = document.getElementById('addLineBtn');
        const detailsTableBody = document.querySelector('#invoiceDetailsTable tbody');
        const totalPriceDisplay = document.getElementById('totalPriceDisplay');
        let detailIndex = 0; // Untuk penomoran array input baru

        function addDetailRow(detail = null) {
            const currentIndex = detailIndex; // Gunakan detailIndex saat ini untuk baris ini
            detailIndex++; // Increment untuk baris berikutnya

            const newRow = detailsTableBody.insertRow();
            newRow.innerHTML = `
                <td><input type="text" name="details[${currentIndex}][coil_number]" class="form-control form-control-sm" value="${detail ? detail.coil_number : ''}" required></td>
                <td><input type="number" name="details[${currentIndex}][width]" class="form-control form-control-sm detail-calc" step="0.01" min="0" value="${detail ? detail.width : ''}" required></td>
                <td><input type="number" name="details[${currentIndex}][length]" class="form-control form-control-sm detail-calc" step="0.01" min="0" value="${detail ? detail.length : ''}" required></td>
                <td><input type="number" name="details[${currentIndex}][thickness]" class="form-control form-control-sm detail-calc" step="0.01" min="0" value="${detail ? detail.thickness : ''}" required></td>
                <td><input type="number" name="details[${currentIndex}][weight]" class="form-control form-control-sm detail-calc" step="0.01" min="0" value="${detail ? detail.weight : ''}" required></td>
                <td><input type="number" name="details[${currentIndex}][price]" class="form-control form-control-sm detail-price" step="0.01" min="0" value="${detail ? detail.price : ''}" required></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-line-btn">Remove</button></td>
            `;
            attachEventListenersToRow(newRow);
        }

        function attachEventListenersToRow(row) {
            row.querySelector('.remove-line-btn').addEventListener('click', function() {
                row.remove();
                updateTotalPrice();
            });
            row.querySelectorAll('.detail-price').forEach(input => {
                input.addEventListener('input', updateTotalPrice);
            });
        }

        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll('#invoiceDetailsTable tbody .detail-price').forEach(input => {
                const price = parseFloat(input.value) || 0;
                total += price;
            });
            totalPriceDisplay.textContent = total.toFixed(2);
        }

        addLineBtn.addEventListener('click', () => addDetailRow());

        // Isi tabel dengan data detail yang ada dari $invoice
        const existingDetails = @json(old('details', $invoice->details));
        if (existingDetails && existingDetails.length > 0) {
            existingDetails.forEach(detail => {
                addDetailRow(detail);
            });
        } else if (detailsTableBody.rows.length === 0) { // Jika tidak ada old data dan tidak ada data dari $invoice
             addDetailRow(); // Tambah satu baris kosong
        }
        updateTotalPrice(); // Hitung total harga awal
    });
</script>
@endpush