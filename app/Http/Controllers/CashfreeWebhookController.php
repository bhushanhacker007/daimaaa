<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Services\CashfreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashfreeWebhookController extends Controller
{
    public function handlePayout(Request $request): \Illuminate\Http\JsonResponse
    {
        $payload = $request->all();

        Log::info('Cashfree payout webhook received', ['payload' => $payload]);

        $signature = $request->header('x-cashfree-signature', '');
        $timestamp = $request->header('x-cashfree-timestamp', '');

        if (config('cashfree.webhook_secret')) {
            $service = app(CashfreeService::class);
            if (!$service->verifyWebhookSignature($signature, $request->getContent(), $timestamp)) {
                Log::warning('Cashfree webhook signature invalid');
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        $event = $payload['event'] ?? $payload['type'] ?? null;
        $data = $payload['data'] ?? $payload;

        $transferId = $data['transfer']['transfer_id']
            ?? $data['transfer_id']
            ?? $data['transferId']
            ?? null;

        if (!$transferId) {
            Log::warning('Cashfree webhook: No transfer_id found', ['payload' => $payload]);
            return response()->json(['status' => 'ignored']);
        }

        $payout = Payout::where('reference', $transferId)->first();

        if (!$payout) {
            Log::warning('Cashfree webhook: No payout found for transfer', ['transfer_id' => $transferId]);
            return response()->json(['status' => 'not_found']);
        }

        $status = strtolower(
            $data['transfer']['status']
            ?? $data['status']
            ?? ''
        );

        $utr = $data['transfer']['status_details']['utr']
            ?? $data['utr']
            ?? null;

        $newStatus = match (true) {
            in_array($status, ['success', 'completed']) => 'processed',
            in_array($status, ['failed', 'reversed', 'rejected']) => 'failed',
            default => $payout->status,
        };

        $payout->update([
            'status' => $newStatus,
            'processed_at' => $newStatus === 'processed' ? now() : $payout->processed_at,
            'notes' => trim(($payout->notes ?? '') . ' | Webhook: ' . $status . ($utr ? ' UTR:' . $utr : '')),
        ]);

        Log::info('Cashfree webhook processed', [
            'payout_id' => $payout->id,
            'transfer_id' => $transferId,
            'new_status' => $newStatus,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
