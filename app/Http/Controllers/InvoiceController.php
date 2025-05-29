<?php

// app/Http/Controllers/InvoiceController.php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk transaction
use Carbon\Carbon; // Untuk tanggal

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Tambahkan Request $request
{
    $query = Invoice::with('details')->orderBy('submit_date', 'desc');

    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('invoice_number', 'LIKE', "%{$searchTerm}%")
              ->orWhere('customer_name', 'LIKE', "%{$searchTerm}%");
        });
    }

    $invoices = $query->paginate(10)->appends($request->only('search')); // appends agar pagination tetap membawa parameter search

    return view('invoices.index', compact('invoices'));

    $invoices = $query->paginate(10)->appends($request->only('search')); // appends agar pagination tetap membawa parameter search

    return view('invoices.index', compact('invoices'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Format waktu saat ini: YYMM, misalnya 2505
        $yearMonth = Carbon::now()->format('ym');

        // Cari invoice terakhir di bulan ini yang sesuai prefix
        $latestInvoice = Invoice::where('invoice_number', 'LIKE', "atha-INV-{$yearMonth}-%")
                                ->orderBy('invoice_number', 'desc')->first();

        $nextNumber = 1; // Default nomor awal
        if ($latestInvoice) {
            // Ambil 4 digit terakhir dari invoice_number sebagai angka urutan
            $lastNumStr = substr($latestInvoice->invoice_number, -4);
            $nextNumber = intval($lastNumStr) + 1; // Tambah satu untuk invoice baru
        }

        // Buat invoice number baru: "atha-INV-2505-0001"
        $invoiceNumber = "atha-INV-{$yearMonth}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Kirim ke view create form
        return view('invoices.create', compact('invoiceNumber'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number',
            'customer_name' => 'required|string|max:100',
            'delivery_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.coil_number' => 'required|string|max:50',
            'details.*.width' => 'required|numeric|min:0',
            'details.*.length' => 'required|numeric|min:0',
            'details.*.thickness' => 'required|numeric|min:0',
            'details.*.weight' => 'required|numeric|min:0',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'customer_name' => $request->customer_name,
                'delivery_date' => $request->delivery_date,
                'submit_date' => Carbon::now(), // Atau biarkan default dari skema jika ada
            ]);

            foreach ($request->details as $detailData) {
                $invoice->details()->create($detailData);
            }

            DB::commit();
            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error creating invoice: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create invoice. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('details'); // Pastikan details di-load
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('details'); // Pastikan details di-load
        // Kita tidak perlu generate nomor invoice baru di sini karena ini edit
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            // invoice_number tidak boleh diubah saat edit, atau jika boleh, pastikan unik kecuali dirinya sendiri
            // 'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $invoice->id,
            'customer_name' => 'required|string|max:100',
            'delivery_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.coil_number' => 'required|string|max:50',
            'details.*.width' => 'required|numeric|min:0',
            'details.*.length' => 'required|numeric|min:0',
            'details.*.thickness' => 'required|numeric|min:0',
            'details.*.weight' => 'required|numeric|min:0',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update([
                'customer_name' => $request->customer_name,
                'delivery_date' => $request->delivery_date,
                // 'invoice_number' => $request->invoice_number, // Jika nomor invoice boleh diubah
            ]);

            // Strategi update detail: Hapus yang lama, buat yang baru
            // Ini lebih sederhana daripada melacak ID detail yang ada, dihapus, atau baru
            $invoice->details()->delete(); // Hapus semua detail lama

            foreach ($request->details as $detailData) {
                $invoice->details()->create($detailData); // Buat detail baru
            }

            DB::commit();
            return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update invoice. Please try again. Error: ' . $e->getMessage());
        }
    }
    public function destroy(Invoice $invoice)
{
    try {
        $invoice->delete(); // Ini juga akan menghapus detail terkait karena onDelete('cascade')
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    } catch (\Exception $e) {
        return redirect()->route('invoices.index')->with('error', 'Failed to delete invoice. Error: ' . $e->getMessage());
    }
}

}