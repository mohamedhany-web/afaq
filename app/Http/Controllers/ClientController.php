<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::query()
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('company', 'like', '%' . request('search') . '%')
                    ->orWhere('email', 'like', '%' . request('search') . '%');
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->latest()
            ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = Client::normalizeType($request->client_type);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone' => 'nullable|string|max:20',
            'id_number' => $type === 'freelance' ? 'required|string|max:50' : 'nullable|string|max:50',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'industry' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'client_type' => 'nullable|in:' . implode(',', Client::typeKeys()),
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ], [
            'id_number.required' => 'رقم البطاقة إلزامي لعملاء فري لانس.',
        ]);

        $validator->after(function ($v) {
            $phone = $v->getData()['phone'] ?? null;
            if (!$phone) {
                return;
            }
            $duplicate = Client::findByNormalizedPhone($phone);
            if ($duplicate) {
                $v->errors()->add('phone', 'رقم الهاتف مسجّل مسبقاً للعميل: ' . $duplicate->name);
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Client::create(array_merge($request->all(), [
            'created_by' => auth()->id(),
            'client_type' => Client::normalizeType($request->client_type),
        ]));

        return redirect()->route('clients.index')
            ->with('success', 'تم إنشاء العميل بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // تحميل العلاقات مع التحقق من وجودها
        $client->load(['projects', 'sales', 'serviceReports.uploader']);
        
        // التحقق من أن العلاقة تعمل بشكل صحيح
        if (!$client->relationLoaded('projects')) {
            $client->load('projects');
        }
        
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $type = Client::normalizeType($request->client_type);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'id_number' => $type === 'freelance' ? 'required|string|max:50' : 'nullable|string|max:50',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'industry' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'client_type' => 'nullable|in:' . implode(',', Client::typeKeys()),
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ], [
            'id_number.required' => 'رقم البطاقة إلزامي لعملاء فري لانس.',
        ]);

        $validator->after(function ($v) use ($client) {
            $phone = $v->getData()['phone'] ?? null;
            if (!$phone) {
                return;
            }
            $duplicate = Client::findByNormalizedPhone($phone, $client->id);
            if ($duplicate) {
                $v->errors()->add('phone', 'رقم الهاتف مسجّل مسبقاً للعميل: ' . $duplicate->name);
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $client->update(array_merge($request->all(), [
            'client_type' => Client::normalizeType($request->client_type),
        ]));

        return redirect()->route('clients.index')
            ->with('success', 'تم تحديث العميل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Check if client has projects or sales
        if ($client->projects()->count() > 0 || $client->sales()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف العميل لأنه مرتبط بمشاريع أو مبيعات');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }
}
