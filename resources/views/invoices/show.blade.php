@extends('layouts.app')

@section('title', 'Invoice Detail - ' . $invoice->invoice_number)

@section('content')
<div class="container py-4">
    <!-- Header Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="fas fa-file-invoice me-2"></i>INVOICE DETAIL
                    </h2>
                    <p class="text-muted mb-0">{{ $invoice->invoice_number }}</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <!-- Status Badge -->
                    @php
                        // Mock status if not available in the model
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

                    <!-- Action Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v me-1"></i> Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown">
                            <li><a class="dropdown-item" href="#" id="printInvoiceBtn"><i class="fas fa-print me-2"></i>Print Invoice</a></li>
                            <li><a class="dropdown-item" href="#" id="downloadPdfBtn"><i class="fas fa-file-pdf me-2 text-danger"></i>Download PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="sendEmailBtn"><i class="fas fa-envelope me-2 text-primary"></i>Send via Email</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @if(Auth::user() && Auth::user()->role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('invoices.edit', $invoice) }}"><i class="fas fa-edit me-2 text-warning"></i>Edit Invoice</a></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash-alt me-2"></i>Delete Invoice
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Invoice Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Invoice Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted small mb-1">Invoice Number</div>
                        <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted small mb-1">Customer</div>
                        <div class="fw-bold">{{ $invoice->customer_name }}</div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted small mb-1">Delivery Date</div>
                        <div class="fw-bold">
                            <i class="fas fa-calendar-alt me-1 text-muted"></i>
                            {{ $invoice->delivery_date->format('d F Y') }}
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted small mb-1">Submit Date</div>
                        <div class="fw-bold">
                            <i class="fas fa-clock me-1 text-muted"></i>
                            {{ $invoice->submit_date->format('d F Y H:i') }}
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted small mb-1">Total Amount</div>
                        <div class="fw-bold fs-4 text-primary">
                            {{ number_format($invoice->total_amount, 2) }}
                        </div>
                    </div>

                    <!-- Additional Information (Mock Data) -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted small mb-1">Payment Status</div>
                        <div class="fw-bold">
                            <span class="badge {{ $statusClass }}">
                                <i class="fas {{ $statusIcon }} me-1"></i> {{ ucfirst($status) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="text-muted small mb-1">Created By</div>
                        <div class="fw-bold">
                            {{ Auth::user() ? Auth::user()->name : 'System User' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items Card -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-list text-primary me-2"></i>
                        Invoice Items
                    </h5>
                    <span class="badge bg-primary">{{ count($invoice->details) }} Items</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Coil Number</th>
                                    <th>Width (mm)</th>
                                    <th>Length (mm)</th>
                                    <th>Thickness (mm)</th>
                                    <th>Weight (kg)</th>
                                    <th class="text-end">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->details as $detail)
                                <tr>
                                    <td class="fw-medium">{{ $detail->coil_number }}</td>
                                    <td>{{ number_format($detail->width, 2) }}</td>
                                    <td>{{ number_format($detail->length, 2) }}</td>
                                    <td>{{ number_format($detail->thickness, 2) }}</td>
                                    <td>{{ number_format($detail->weight, 2) }}</td>
                                    <td class="text-end">{{ number_format($detail->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">
                                        @php
                                            $totalWeight = $invoice->details->sum('weight');
                                        @endphp
                                        {{ number_format($totalWeight, 2) }} kg
                                    </td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row">
        <!-- Item Statistics Card -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Item Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <div class="border rounded p-3 text-center h-100">
                                <div class="text-muted small mb-1">Total Items</div>
                                <div class="fw-bold fs-4">{{ count($invoice->details) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <div class="border rounded p-3 text-center h-100">
                                <div class="text-muted small mb-1">Total Weight</div>
                                <div class="fw-bold fs-4">{{ number_format($totalWeight, 2) }}</div>
                                <div class="text-muted small">kilograms</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center h-100">
                                <div class="text-muted small mb-1">Avg. Thickness</div>
                                <div class="fw-bold fs-4">
                                    @php
                                        $avgThickness = $invoice->details->avg('thickness');
                                    @endphp
                                    {{ number_format($avgThickness, 2) }}
                                </div>
                                <div class="text-muted small">millimeters</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center h-100">
                                <div class="text-muted small mb-1">Avg. Price</div>
                                <div class="fw-bold fs-4">
                                    @php
                                        $avgPrice = $invoice->details->avg('price');
                                    @endphp
                                    {{ number_format($avgPrice, 2) }}
                                </div>
                                <div class="text-muted small">per item</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-qrcode text-primary me-2"></i>
                        Quick Access
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode" class="mb-3"></div>
                    <p class="text-muted small mb-0">Scan this QR code to quickly access this invoice on mobile devices</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes & Activity Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light py-3">
            <ul class="nav nav-tabs card-header-tabs" id="notesActivityTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="true">
                        <i class="fas fa-sticky-note me-1"></i> Notes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">
                        <i class="fas fa-history me-1"></i> Activity Log
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="notesActivityTabContent">
                <div class="tab-pane fade show active" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                    <div class="mb-3">
                        <label for="invoiceNotes" class="form-label">Invoice Notes</label>
                        <textarea class="form-control" id="invoiceNotes" rows="3" placeholder="Add notes about this invoice...">{{ $invoice->notes ?? '' }}</textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="saveNotesBtn">
                            <i class="fas fa-save me-1"></i> Save Notes
                        </button>
                    </div>
                </div>
                <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                    <div class="timeline">
                        <!-- Mock activity data -->
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">{{ $invoice->submit_date->format('M d') }}</div>
                                <div class="timeline-item-marker-indicator bg-primary"></div>
                            </div>
                            <div class="timeline-item-content">
                                <span class="fw-bold">Invoice Created</span>
                                <p class="mb-0 text-muted small">Invoice was created by {{ Auth::user() ? Auth::user()->name : 'System User' }}</p>
                                <p class="text-muted small">{{ $invoice->submit_date->format('H:i') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">{{ $invoice->submit_date->addDays(1)->format('M d') }}</div>
                                <div class="timeline-item-marker-indicator bg-success"></div>
                            </div>
                            <div class="timeline-item-content">
                                <span class="fw-bold">Invoice Sent</span>
                                <p class="mb-0 text-muted small">Invoice was sent to customer</p>
                                <p class="text-muted small">{{ $invoice->submit_date->addDays(1)->format('H:i') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">{{ $invoice->submit_date->addDays(3)->format('M d') }}</div>
                                <div class="timeline-item-marker-indicator bg-warning"></div>
                            </div>
                            <div class="timeline-item-content">
                                <span class="fw-bold">Payment Reminder</span>
                                <p class="mb-0 text-muted small">Payment reminder sent to customer</p>
                                <p class="text-muted small">{{ $invoice->submit_date->addDays(3)->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Invoice List
        </a>
        <div class="d-flex gap-2">
            @if(Auth::user() && Auth::user()->role === 'admin')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit Invoice
                </a>
            @endif
            <button type="button" class="btn btn-primary" id="printInvoiceBtn2">
                <i class="fas fa-print me-1"></i> Print Invoice
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete invoice <strong>{{ $invoice->invoice_number }}</strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Print Template (Hidden) -->
<div id="printTemplate" style="display: none;">
    <div class="invoice-print">
        <div class="text-center mb-4">
            <h2>INVOICE</h2>
            <p>{{ $invoice->invoice_number }}</p>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h5>Bill To:</h5>
                <p>{{ $invoice->customer_name }}</p>
            </div>
            <div class="col-6 text-end">
                <p><strong>Delivery Date:</strong> {{ $invoice->delivery_date->format('d F Y') }}</p>
                <p><strong>Invoice Date:</strong> {{ $invoice->submit_date->format('d F Y') }}</p>
            </div>
        </div>

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

        <div class="row mt-4">
            <div class="col-6">
                <p><strong>Notes:</strong></p>
                <p>{{ $invoice->notes ?? 'No notes available.' }}</p>
            </div>
            <div class="col-6 text-end">
                <p><strong>Thank you for your business!</strong></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // QR Code Generation
        new QRCode(document.getElementById("qrcode"), {
            text: window.location.href,
            width: 128,
            height: 128,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Print Invoice
        function printInvoice() {
            const printContent = document.getElementById('printTemplate').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Invoice - {{ $invoice->invoice_number }}</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            body { padding: 20px; }
                            @media print {
                                @page { margin: 0.5cm; }
                                .no-print { display: none; }
                            }
                        </style>
                    </head>
                    <body>
                        ${printContent}
                        <div class="text-center mt-4 no-print">
                            <button class="btn btn-primary" onclick="window.print()">Print</button>
                            <button class="btn btn-secondary ms-2" onclick="window.close()">Close</button>
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }

        document.getElementById('printInvoiceBtn').addEventListener('click', function(e) {
            e.preventDefault();
            printInvoice();
        });

        document.getElementById('printInvoiceBtn2').addEventListener('click', function(e) {
            e.preventDefault();
            printInvoice();
        });

        // Download PDF (Mock)
        document.getElementById('downloadPdfBtn').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Generating PDF... (This is a frontend-only demo)');
        });

        // Send Email (Mock)
        document.getElementById('sendEmailBtn').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Sending email... (This is a frontend-only demo)');
        });

        // Save Notes (Mock)
        document.getElementById('saveNotesBtn').addEventListener('click', function() {
            const notes = document.getElementById('invoiceNotes').value;

            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Notes saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.getElementById('notes').prepend(alert);

            setTimeout(() => {
                alert.remove();
            }, 3000);
        });
    });
</script>

<style>
    /* Timeline styling */
    .timeline {
        position: relative;
        padding-left: 1rem;
        margin: 0 0 0 1rem;
        border-left: 1px solid #dee2e6;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item-marker {
        position: absolute;
        left: -1.5rem;
        width: 3rem;
    }

    .timeline-item-marker-text {
        width: 100%;
        text-align: center;
        font-size: 0.75rem;
        color: #6c757d;
    }

    .timeline-item-marker-indicator {
        display: block;
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 100%;
        margin: 0.25rem auto 0;
    }

    .timeline-item-content {
        padding-left: 2rem;
        padding-bottom: 0.5rem;
    }

    /* QR Code styling */
    #qrcode {
        display: inline-block;
        padding: 10px;
        background: white;
        border-radius: 4px;
    }

    /* Print styling */
    @media print {
        .invoice-print {
            font-family: Arial, sans-serif;
        }
    }
</style>
@endpush