<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans): JsonResponse
    {
        $data = $request->all();

        if (! $midtrans->signatureValid($data)) {
            return response()->json(['message' => 'Signature tidak valid'], 403);
        }

        if ($pembayaran = Pembayaran::where('order_id_midtrans', $data['order_id'] ?? null)->first()) {
            $midtrans->terapkanStatus($pembayaran, $data);
        }

        return response()->json(['message' => 'OK']);
    }
}