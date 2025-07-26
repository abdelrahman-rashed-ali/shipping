<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ContactMessage, ProductRequest, CompanyRequest, FullProductRequest};

class FormApiController extends Controller
{
    public function contact(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'message' => 'required',
        ]);

        ContactMessage::create($data);

        return response()->json(['message' => 'Contact message sent successfully.'], 201);
    }

    public function product(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'product' => 'nullable|string',
            'message' => 'required',
        ]);

        ProductRequest::create($data);

        return response()->json(['message' => 'Product request submitted.'], 201);
    }

    public function company(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'company' => 'required|string',
            'ship_to' => 'required|string',
        ]);

        CompanyRequest::create($data);

        return response()->json(['message' => 'Company request submitted.'], 201);
    }

    public function fullProduct(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'company_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'product_type' => 'nullable|string',
            'product_shape' => 'required|string',
            'packaging_type' => 'required|string',
            'pacage_weight' => 'required|string',
            'quantity' => 'required|string',
            'ship_to' => 'required|string',
            'shipping_method' => 'required|string',
            'additional_message' => 'required|string',
        ]);

        \App\Models\FullProductRequest::create($data);

        return response()->json(['message' => 'Full product request sent successfully.'], 201);
    }


}
