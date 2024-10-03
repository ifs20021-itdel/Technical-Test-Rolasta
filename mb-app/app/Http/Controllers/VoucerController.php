<?php

namespace App\Http\Controllers;

use App\Models\Voucer;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVoucerRequest;
use App\Http\Requests\UpdateVoucerRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;

class VoucerController extends Controller implements HasMiddleware
{
    public static function middleware(){
        return[
            new middleware ('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Voucer::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data =  $request->validate([
            'name' => 'required ',
            'value' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

       $voucer = $request->user()->voucers()->create($data);
        return response([
            'voucer' => $voucer
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Voucer $voucer)
    {
        return ['voucer' => $voucer];
    }

    public function claim(Request $request, Post $post , $id)
    {
        // Validasi input untuk kode voucher
        $post = Post::findOrFail($id);
        $data = $request->validate([
            'voucer_code' => 'required|string', // Memvalidasi input voucer_code
        ]);

        // Ambil voucher dari database berdasarkan voucer_code yang diberikan
        $voucer = Voucer::where('name', $data['voucer_code'])->first();

        // Cek apakah voucher ada
        if (!$voucer) {
            return response([
                'message' => 'Kode voucher tidak valid.'
            ], 400);
        }

        // Cek apakah voucher masih berlaku berdasarkan tanggal mulai dan akhir
        $currentDate = now();
        if ($currentDate < $voucer->start_date || $currentDate > $voucer->end_date) {
            return response([
                'message' => 'Voucher sudah kadaluarsa atau belum berlaku.'
            ], 400);
        }

        // Ambil harga dari model Post
        $price = $post->price;

        // Kurangi harga berdasarkan nilai voucher
        $priceAfterDiscount = max(0, $price - $voucer->value); // Pastikan harga tidak negatif

        return response([
            'message' => 'Voucher berhasil diklaim!',
            'original_price' => $price,
            'discount_value' => $voucer->value,
            'price_after_discount' => $priceAfterDiscount,
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voucer $voucer)
    {
        Gate::authorize('modify', $voucer);
        $data =  $request->validate([
            'name' => 'required ',
            'value' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $voucer->update($data);
        return ['voucer' => $voucer];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucer $voucer)
    {
        Gate::authorize('modify', $voucer);
        $voucer->delete();
        return ['message' => 'deleted'];
    }
}
