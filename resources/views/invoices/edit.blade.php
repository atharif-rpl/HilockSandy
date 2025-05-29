@extends('layouts.app')

@section('title', 'Edit Invoice - ' . $invoice->invoice_number)

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="fas fa-edit me-2"></i>EDIT INVOICE
                    </h2>
                    <p class="text-muted mb-0">{{ $invoice->invoice_number }}</p>
                </div>
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <!-- Status Badge -->
                    @php
                        $statuses = ['paid', 'pending', 'overdue', 'cancelled'];
                        $status = $invoice->status ?? $statuses[array_rand($statuses)];

                        $statusClasses = [
                            'paid' => 'bg-success',
                            'pending' => 'bg-warning',
                            'overdue' => 'bg-danger',
                            'cancelled' => 'bg-secondary'
                        ];

                        $statusClass = $statusClasses[$status] ?? 'bg-secondary';
                        $statusIcons = [
                            'paid' => 'fa-check-circle',
                            'pending' => 'fa-clock',
                            'overdue' => 'fa-exclamation-circle',
                            'cancelled' => 'fa-ban'
                        ];
                        $statusIcon = $statusIcons[$status] ?? 'fa-info-circle';
                    @endphp
                    <span class="badge {{ $statusClass }} fs-6 d-flex align-items-center px-3 py-2">
                        <i class="fas {{ $statusIcon }} me-1"></i> {{ ucfirst($status) }}
                    </span>

                    <!-- Quick Actions -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-tools me-1"></i> Quick Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="quickActionsDropdown">
                            <li><a class="dropdown-item" href="#" id="previewBtn"><i class="fas fa-eye me-2"></i>Preview Changes</a></li>
                            <li><a class="dropdown-item" href="#" id="resetFormBtn"><i class="fas fa-undo me-2"></i>Reset Form</a></li>
                            <li><a class="dropdown-item" href="#" id="duplicateInvoiceBtn"><i class="fas fa-copy me-2"></i>Duplicate Invoice</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('invoices.show', $invoice) }}"><i class="fas fa-file-alt me-2"></i>View Original</a></li>
                        </ul>
                    </div>

                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <div class="progress flex-grow-1 me-3" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" id="formProgress"></div>
                </div>
                <span class="text-muted small" id="progressText">0% Complete</span>
            </div>
        </div>
    </div>

    <!-- Change Tracking Alert -->
    <div class="alert alert-info border-0 shadow-sm mb-4" id="changeTracker" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <span>You have unsaved changes. Don't forget to save your work!</span>
            <button type="button" class="btn-close ms-auto" aria-label="Close" id="dismissChangeAlert"></button>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')

        <!-- Basic Information Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Basic Information
                    <span class="badge bg-secondary ms-2" id="basicInfoStatus">Unchanged</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="invoice_number_display" class="form-label fw-medium">
                            Invoice Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-hashtag text-muted"></i>
                            </span>
                            <input type="text" class="form-control" id="invoice_number_display"
                                   value="{{ $invoice->invoice_number }}" readonly>
                        </div>
                        <small class="form-text text-muted">Invoice number cannot be changed</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="customer_name" class="form-label fw-medium">
                            Customer Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                   id="customer_name" name="customer_name"
                                   value="{{ old('customer_name', $invoice->customer_name) }}"
                                   placeholder="Enter customer name" required autocomplete="off"
                                   data-original="{{ $invoice->customer_name }}">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    id="customerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="customerDropdown" id="customerSuggestions">
                                <!-- Customer suggestions will be populated here -->
                            </ul>
                        </div>
                        @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="delivery_date" class="form-label fw-medium">
                            Delivery Date <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-calendar-alt text-muted"></i>
                            </span>
                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror"
                                   id="delivery_date" name="delivery_date"
                                   value="{{ old('delivery_date', $invoice->delivery_date->format('Y-m-d')) }}"
                                   required data-original="{{ $invoice->delivery_date->format('Y-m-d') }}">
                            <button type="button" class="btn btn-outline-secondary" id="setTodayBtn" title="Set to today">
                                <i class="fas fa-clock"></i>
                            </button>
                        </div>
                        @error('delivery_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Additional Fields -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="notes" class="form-label fw-medium">Notes (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-sticky-note text-muted"></i>
                            </span>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                      placeholder="Add any additional notes..."
                                      data-original="{{ $invoice->notes ?? '' }}">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="currency" class="form-label fw-medium">Currency</label>
                        <select class="form-select" id="currency" name="currency" data-original="{{ $invoice->currency ?? 'IDR' }}">
                            <option value="IDR" {{ (old('currency', $invoice->currency ?? 'IDR') == 'IDR') ? 'selected' : '' }}>IDR (Indonesian Rupiah)</option>
                            <option value="USD" {{ (old('currency', $invoice->currency ?? 'IDR') == 'USD') ? 'selected' : '' }}>USD (US Dollar)</option>
                            <option value="EUR" {{ (old('currency', $invoice->currency ?? 'IDR') == 'EUR') ? 'selected' : '' }}>EUR (Euro)</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="tax_rate" class="form-label fw-medium">Tax Rate (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate"
                                   value="{{ old('tax_rate', $invoice->tax_rate ?? 11) }}"
                                   min="0" max="100" step="0.01"
                                   data-original="{{ $invoice->tax_rate ?? 11 }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Details Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-list text-primary me-2"></i>
                    Invoice Details
                    <span class="badge bg-secondary ms-2" id="detailsStatus">Unchanged</span>
                </h5>
                <div class="d-flex gap-2">
                    <!-- Bulk Actions -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog me-1"></i> Bulk Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                            <li><a class="dropdown-item" href="#" id="selectAllItemsBtn">
                                <i class="fas fa-check-square me-2"></i>Select All
                            </a></li>
                            <li><a class="dropdown-item" href="#" id="clearAllBtn">
                                <i class="fas fa-trash me-2"></i>Clear All
                            </a></li>
                            <li><a class="dropdown-item" href="#" id="duplicateSelectedBtn">
                                <i class="fas fa-copy me-2"></i>Duplicate Selected
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="restoreOriginalBtn">
                                <i class="fas fa-undo me-2"></i>Restore Original
                            </a></li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-sm btn-primary" id="addLineBtn">
                        <i class="fas fa-plus me-1"></i> Add Line
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="invoiceDetailsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th>Coil Number</th>
                                <th>Width (mm)</th>
                                <th>Length (mm)</th>
                                <th>Thickness (mm)</th>
                                <th>Weight (kg)</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Baris detail akan ditambahkan oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                <!-- Summary Section -->
                <div class="border-top p-3 bg-light">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex gap-2 align-items-center">
                                <span class="text-muted">Total Items:</span>
                                <span class="fw-bold" id="totalItemsCount">0</span>
                                <span class="text-muted ms-3">Total Weight:</span>
                                <span class="fw-bold" id="totalWeightDisplay">0.00 kg</span>
                                <span class="text-muted ms-3">Changes:</span>
                                <span class="fw-bold text-warning" id="changesCount">0</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Subtotal:</span>
                                    <span id="subtotalDisplay">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Tax (<span id="taxRateDisplay">11</span>%):</span>
                                    <span id="taxAmountDisplay">0.00</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold text-primary fs-5" id="totalPriceDisplay">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Card -->
        <div class="card shadow-sm border-0 mb-4" id="comparisonCard" style="display: none;">
            <div class="card-header bg-warning bg-opacity-10">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-balance-scale text-warning me-2"></i>
                    Changes Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Original Total</h6>
                        <div class="fs-4 fw-bold">{{ number_format($invoice->total_amount, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">New Total</h6>
                        <div class="fs-4 fw-bold text-primary" id="newTotalDisplay">{{ number_format($invoice->total_amount, 2) }}</div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <span class="text-muted">Difference: </span>
                    <span class="fw-bold" id="differenceDisplay">0.00</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Review all changes carefully before updating the invoice.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="button" class="btn btn-outline-primary" id="previewChangesBtn">
                            <i class="fas fa-eye me-1"></i> Preview Changes
                        </button>
                        <button type="submit" class="btn btn-success" id="updateBtn">
                            <i class="fas fa-save me-1"></i> Update Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be generated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printPreviewBtn">
                    <i class="fas fa-print me-1"></i> Print Preview
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addLineBtn = document.getElementById('addLineBtn');
        const detailsTableBody = document.querySelector('#invoiceDetailsTable tbody');
        const totalPriceDisplay = document.getElementById('totalPriceDisplay');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const taxAmountDisplay = document.getElementById('taxAmountDisplay');
        const taxRateDisplay = document.getElementById('taxRateDisplay');
        const totalItemsCount = document.getElementById('totalItemsCount');
        const totalWeightDisplay = document.getElementById('totalWeightDisplay');
        const formProgress = document.getElementById('formProgress');
        const progressText = document.getElementById('progressText');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const taxRateInput = document.getElementById('tax_rate');
        const changeTracker = document.getElementById('changeTracker');
        const comparisonCard = document.getElementById('comparisonCard');
        const newTotalDisplay = document.getElementById('newTotalDisplay');
        const differenceDisplay = document.getElementById('differenceDisplay');
        const changesCount = document.getElementById('changesCount');

        let detailIndex = 0;
        let hasChanges = false;
        let changeCounter = 0;

        // Original data for comparison
        const originalData = {
            customer_name: '{{ $invoice->customer_name }}',
            delivery_date: '{{ $invoice->delivery_date->format('Y-m-d') }}',
            notes: '{{ $invoice->notes ?? '' }}',
            currency: '{{ $invoice->currency ?? 'IDR' }}',
            tax_rate: {{ $invoice->tax_rate ?? 11 }},
            total_amount: {{ $invoice->total_amount }},
            details: @json($invoice->details)
        };

        // Sample customer data for autocomplete
        const customers = [
            'PT Maju Bersama',
            'CV Teknologi Nusantara',
            'PT Sejahtera Abadi',
            'PT Karya Mandiri',
            'CV Bintang Terang',
            'PT Global Solutions',
            'CV Mandiri Jaya'
        ];

        // Initialize customer autocomplete
        function initializeCustomerAutocomplete() {
            const customerInput = document.getElementById('customer_name');
            const customerSuggestions = document.getElementById('customerSuggestions');

            customerInput.addEventListener('input', function() {
                const value = this.value.toLowerCase();
                customerSuggestions.innerHTML = '';

                if (value.length > 0) {
                    const filtered = customers.filter(customer =>
                        customer.toLowerCase().includes(value)
                    );

                    filtered.forEach(customer => {
                        const li = document.createElement('li');
                        li.innerHTML = `<a class="dropdown-item" href="#">${customer}</a>`;
                        li.addEventListener('click', function(e) {
                            e.preventDefault();
                            customerInput.value = customer;
                            customerSuggestions.innerHTML = '';
                            trackChanges();
                        });
                        customerSuggestions.appendChild(li);
                    });
                }
            });
        }

        // Add detail row function
        function addDetailRow(detail = null) {
            const currentIndex = detailIndex;
            detailIndex++;

            const newRow = detailsTableBody.insertRow();
            newRow.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="form-check-input row-checkbox">
                </td>
                <td>
                    <input type="text" name="details[${currentIndex}][coil_number]"
                           class="form-control form-control-sm" value="${detail ? detail.coil_number : ''}"
                           placeholder="Enter coil number" required>
                </td>
                <td>
                    <input type="number" name="details[${currentIndex}][width]"
                           class="form-control form-control-sm detail-calc" value="${detail ? detail.width : ''}"
                           step="0.01" min="0" placeholder="Width" required>
                </td>
                <td>
                    <input type="number" name="details[${currentIndex}][length]"
                           class="form-control form-control-sm detail-calc" value="${detail ? detail.length : ''}"
                           step="0.01" min="0" placeholder="Length" required>
                </td>
                <td>
                    <input type="number" name="details[${currentIndex}][thickness]"
                           class="form-control form-control-sm detail-calc" value="${detail ? detail.thickness : ''}"
                           step="0.01" min="0" placeholder="Thickness" required>
                </td>
                <td>
                    <input type="number" name="details[${currentIndex}][weight]"
                           class="form-control form-control-sm detail-calc" value="${detail ? detail.weight : ''}"
                           step="0.01" min="0" placeholder="Weight" required>
                </td>
                <td>
                    <input type="number" name="details[${currentIndex}][price]"
                           class="form-control form-control-sm detail-price" value="${detail ? detail.price : ''}"
                           step="0.01" min="0" placeholder="Unit price" required>
                </td>
                <td class="text-end">
                    <span class="fw-bold line-total">0.00</span>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm duplicate-line-btn" title="Duplicate">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-line-btn" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            attachEventListenersToRow(newRow);
            updateCalculations();
            trackChanges();
            return newRow;
        }

        // Attach event listeners to row
        function attachEventListenersToRow(row) {
            // Remove button
            row.querySelector('.remove-line-btn').addEventListener('click', function() {
                row.remove();
                updateCalculations();
                trackChanges();
            });

            // Duplicate button
            row.querySelector('.duplicate-line-btn').addEventListener('click', function() {
                const inputs = row.querySelectorAll('input[type="text"], input[type="number"]');
                const data = {};
                inputs.forEach(input => {
                    const name = input.name.match(/\[([^\]]+)\]$/);
                    if (name) {
                        data[name[1]] = input.value;
                    }
                });
                addDetailRow(data);
            });

            // Price and calculation inputs
            row.querySelectorAll('.detail-price, .detail-calc').forEach(input => {
                input.addEventListener('input', function() {
                    updateLineTotal(row);
                    updateCalculations();
                    trackChanges();
                });
            });

            // Row checkbox
            row.querySelector('.row-checkbox').addEventListener('change', updateSelectAllState);
        }

        // Update line total
        function updateLineTotal(row) {
            const weightInput = row.querySelector('input[name*="[weight]"]');
            const priceInput = row.querySelector('input[name*="[price]"]');
            const totalSpan = row.querySelector('.line-total');

            const weight = parseFloat(weightInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = weight * price;

            totalSpan.textContent = total.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Update all calculations
        function updateCalculations() {
            let subtotal = 0;
            let totalWeight = 0;
            let itemCount = 0;

            document.querySelectorAll('#invoiceDetailsTable tbody tr').forEach(row => {
                const weightInput = row.querySelector('input[name*="[weight]"]');
                const priceInput = row.querySelector('input[name*="[price]"]');

                if (weightInput && priceInput) {
                    const weight = parseFloat(weightInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;

                    subtotal += weight * price;
                    totalWeight += weight;
                    itemCount++;
                }
            });

            const taxRate = parseFloat(taxRateInput.value) || 0;
            const taxAmount = subtotal * (taxRate / 100);
            const total = subtotal + taxAmount;

            // Update displays
            subtotalDisplay.textContent = subtotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            taxRateDisplay.textContent = taxRate.toFixed(1);
            taxAmountDisplay.textContent = taxAmount.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            totalPriceDisplay.textContent = total.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            newTotalDisplay.textContent = total.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // Calculate difference
            const difference = total - originalData.total_amount;
            differenceDisplay.textContent = difference.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            differenceDisplay.className = difference > 0 ? 'fw-bold text-success' :
                                        difference < 0 ? 'fw-bold text-danger' : 'fw-bold';

            totalItemsCount.textContent = itemCount;
            totalWeightDisplay.textContent = totalWeight.toFixed(2) + ' kg';

            updateFormProgress();
        }

        // Track changes
        function trackChanges() {
            const currentData = {
                customer_name: document.getElementById('customer_name').value,
                delivery_date: document.getElementById('delivery_date').value,
                notes: document.getElementById('notes').value,
                currency: document.getElementById('currency').value,
                tax_rate: parseFloat(document.getElementById('tax_rate').value) || 0
            };

            let changes = 0;

            // Check basic info changes
            Object.keys(currentData).forEach(key => {
                if (currentData[key] != originalData[key]) {
                    changes++;
                }
            });

            // Check details changes (simplified)
            const currentRows = document.querySelectorAll('#invoiceDetailsTable tbody tr').length;
            const originalRows = originalData.details.length;

            if (currentRows !== originalRows) {
                changes++;
            }

            hasChanges = changes > 0;
            changeCounter = changes;
            changesCount.textContent = changes;

            // Update status badges
            document.getElementById('basicInfoStatus').textContent = changes > 0 ? 'Modified' : 'Unchanged';
            document.getElementById('basicInfoStatus').className = changes > 0 ? 'badge bg-warning' : 'badge bg-secondary';

            document.getElementById('detailsStatus').textContent = currentRows !== originalRows ? 'Modified' : 'Unchanged';
            document.getElementById('detailsStatus').className = currentRows !== originalRows ? 'badge bg-warning' : 'badge bg-secondary';

            // Show/hide change tracker
            if (hasChanges) {
                changeTracker.style.display = 'block';
                comparisonCard.style.display = 'block';
            } else {
                changeTracker.style.display = 'none';
                comparisonCard.style.display = 'none';
            }
        }

        // Update form progress
        function updateFormProgress() {
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            let filledFields = 0;

            requiredFields.forEach(field => {
                if (field.value.trim() !== '') {
                    filledFields++;
                }
            });

            const progress = (filledFields / requiredFields.length) * 100;
            formProgress.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '% Complete';
        }

        // Select all functionality
        function updateSelectAllState() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');

            if (checkboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedBoxes.length === checkboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else if (checkedBoxes.length > 0) {
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            }
        }

        // Event listeners
        addLineBtn.addEventListener('click', () => addDetailRow());

        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Tax rate change
        taxRateInput.addEventListener('input', function() {
            updateCalculations();
            trackChanges();
        });

        // Set today button
        document.getElementById('setTodayBtn').addEventListener('click', function() {
            document.getElementById('delivery_date').value = new Date().toISOString().split('T')[0];
            trackChanges();
        });

        // Track changes on form inputs
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', trackChanges);
            field.addEventListener('change', trackChanges);
        });

        // Clear all button
        document.getElementById('clearAllBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to clear all invoice details?')) {
                detailsTableBody.innerHTML = '';
                updateCalculations();
                trackChanges();
            }
        });

        // Restore original button
        document.getElementById('restoreOriginalBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to restore the original invoice data?')) {
                // Restore basic info
                document.getElementById('customer_name').value = originalData.customer_name;
                document.getElementById('delivery_date').value = originalData.delivery_date;
                document.getElementById('notes').value = originalData.notes;
                document.getElementById('currency').value = originalData.currency;
                document.getElementById('tax_rate').value = originalData.tax_rate;

                // Restore details
                detailsTableBody.innerHTML = '';
                detailIndex = 0;
                originalData.details.forEach(detail => {
                    addDetailRow(detail);
                });

                trackChanges();
            }
        });

        // Reset form button
        document.getElementById('resetFormBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to reset the form to original values?')) {
                location.reload();
            }
        });

        // Duplicate invoice button
        document.getElementById('duplicateInvoiceBtn').addEventListener('click', function() {
            alert('Duplicate functionality would create a new invoice with current data (This is a frontend-only demo)');
        });

        // Preview functionality
        document.getElementById('previewBtn').addEventListener('click', function() {
            generatePreview();
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        });

        document.getElementById('previewChangesBtn').addEventListener('click', function() {
            generatePreview();
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        });

        // Generate preview
        function generatePreview() {
            const customerName = document.getElementById('customer_name').value;
            const deliveryDate = document.getElementById('delivery_date').value;
            const notes = document.getElementById('notes').value;

            let itemsHtml = '';
            let itemNumber = 1;

            document.querySelectorAll('#invoiceDetailsTable tbody tr').forEach(row => {
                const inputs = row.querySelectorAll('input');
                if (inputs.length > 0) {
                    const coilNumber = inputs[1].value;
                    const width = inputs[2].value;
                    const length = inputs[3].value;
                    const thickness = inputs[4].value;
                    const weight = inputs[5].value;
                    const price = inputs[6].value;
                    const total = (parseFloat(weight) || 0) * (parseFloat(price) || 0);

                    itemsHtml += `
                        <tr>
                            <td>${itemNumber++}</td>
                            <td>${coilNumber}</td>
                            <td>${width}</td>
                            <td>${length}</td>
                            <td>${thickness}</td>
                            <td>${weight}</td>
                            <td>${parseFloat(price).toLocaleString('id-ID')}</td>
                            <td class="text-end">${total.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                }
            });

            const previewHtml = `
                <div class="invoice-preview">
                    <div class="text-center mb-4">
                        <h3>INVOICE (UPDATED)</h3>
                        <p class="text-muted">{{ $invoice->invoice_number }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Bill To:</h6>
                            <p>${customerName}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Delivery Date:</strong> ${new Date(deliveryDate).toLocaleDateString('id-ID')}</p>
                            <p><strong>Invoice Date:</strong> {{ $invoice->submit_date->format('d F Y') }}</p>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Coil Number</th>
                                    <th>Width (mm)</th>
                                    <th>Length (mm)</th>
                                    <th>Thickness (mm)</th>
                                    <th>Weight (kg)</th>
                                    <th>Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            ${notes ? `<p><strong>Notes:</strong><br>${notes}</p>` : ''}
                        </div>
                        <div class="col-md-6">
                            <div class="text-end">
                                <p>Subtotal: ${subtotalDisplay.textContent}</p>
                                <p>Tax (${taxRateDisplay.textContent}%): ${taxAmountDisplay.textContent}</p>
                                <h5>Total: ${totalPriceDisplay.textContent}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('previewContent').innerHTML = previewHtml;
        }

        // Print preview
        document.getElementById('printPreviewBtn').addEventListener('click', function() {
            const printContent = document.getElementById('previewContent').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Invoice Preview - {{ $invoice->invoice_number }}</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            @media print {
                                .btn { display: none; }
                            }
                        </style>
                    </head>
                    <body class="p-4">
                        ${printContent}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        });

        // Dismiss change alert
        document.getElementById('dismissChangeAlert').addEventListener('click', function() {
            changeTracker.style.display = 'none';
        });

        // Initialize
        initializeCustomerAutocomplete();

        // Load existing details
        const existingDetails = @json(old('details', $invoice->details));
        if (existingDetails && existingDetails.length > 0) {
            existingDetails.forEach(detail => {
                addDetailRow(detail);
            });
        } else if (detailsTableBody.rows.length === 0) {
            addDetailRow();
        }

        // Initial calculations
        updateCalculations();
        updateFormProgress();

        // Warn before leaving if there are unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });
</script>

<style>
    .invoice-preview {
        font-family: Arial, sans-serif;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .table th {
        font-weight: 600;
        white-space: nowrap;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }

    .progress {
        background-color: #e9ecef;
    }

    .alert {
        border: none;
        border-radius: 0.5rem;
    }

    @media (max-width: 767.98px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            border-radius: 0.25rem !important;
            margin-bottom: 2px;
        }
    }
</style>
@endpush