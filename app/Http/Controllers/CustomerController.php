<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('pic_name', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%")
                             ->orWhere('address', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'pic_name'  => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'address'   => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : ($request->isJson() ? ($request->input('is_active', true)) : false);

        $customer = Customer::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Pelanggan berhasil disimpan!',
                'customer' => $customer,
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Pelanggan baru berhasil ditambahkan!');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'pic_name'  => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'address'   => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil dihapus!');
    }

    public function searchApi(Request $request)
    {
        $q = $request->input('q', '');
        $customers = Customer::where('is_active', true)
            ->when($q, function ($query, $q) {
                return $query->where('name', 'like', "%{$q}%")
                             ->orWhere('pic_name', 'like', "%{$q}%")
                             ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'pic_name', 'phone']);

        return response()->json($customers);
    }
}