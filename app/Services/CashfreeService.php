<?php

namespace App\Services;

use App\Models\DaimaaProfile;
use App\Models\Payout;
use Cashfree\CashfreeVrs;
use Cashfree\Cashfree as CashfreePayout;
use Cashfree\Model\OfflineAadhaarSendOtpRequestSchema;
use Cashfree\Model\OfflineAadhaarVerifyOtpRequestSchema;
use Cashfree\Model\PanRequestSchema;
use Cashfree\Model\CreateRequestRequestSchema;
use Cashfree\Model\CreateBeneficiaryRequest;
use Cashfree\Model\CreateBeneficiaryRequestBeneficiaryInstrumentDetails;
use Cashfree\Model\CreateBeneficiaryRequestBeneficiaryContactDetails;
use Cashfree\Model\CreateTransferRequest;
use Illuminate\Support\Facades\Log;

class CashfreeService
{
    protected CashfreeVrs $vrs;
    protected CashfreePayout $payout;

    public function __construct()
    {
        $env = config('cashfree.environment') === 'production'
            ? CashfreeVrs::$PRODUCTION
            : CashfreeVrs::$SANDBOX;

        // Verification Suite
        CashfreeVrs::$XClientId = config('cashfree.verification.client_id');
        CashfreeVrs::$XClientSecret = config('cashfree.verification.client_secret');
        CashfreeVrs::$XEnvironment = $env;
        $this->vrs = new CashfreeVrs();

        // Payouts
        CashfreePayout::$XClientId = config('cashfree.payout.client_id');
        CashfreePayout::$XClientSecret = config('cashfree.payout.client_secret');
        CashfreePayout::$XEnvironment = $env;
        $this->payout = new CashfreePayout();
    }

    // ──────────────────────────────────────────────────────────
    // AADHAAR VERIFICATION (Offline OTP-based)
    // ──────────────────────────────────────────────────────────

    /**
     * Step 1: Send OTP to Aadhaar-linked mobile number.
     * Returns ['ref_id' => string] on success.
     */
    public function aadhaarSendOtp(string $aadhaarNumber): array
    {
        try {
            $request = new OfflineAadhaarSendOtpRequestSchema([
                'aadhaar_number' => $aadhaarNumber,
            ]);

            $response = $this->vrs->VrsOfflineAadhaarSendOtp($request);
            $data = $response[0] ?? null;

            return [
                'success' => true,
                'ref_id' => $data?->getRefId() ?? $data['ref_id'] ?? null,
                'message' => 'OTP sent to Aadhaar-linked mobile',
            ];
        } catch (\Throwable $e) {
            Log::error('Cashfree Aadhaar OTP send failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $this->parseError($e)];
        }
    }

    /**
     * Step 2: Verify the OTP entered by the user.
     * Returns name, address etc on success.
     */
    public function aadhaarVerifyOtp(string $refId, string $otp): array
    {
        try {
            $request = new OfflineAadhaarVerifyOtpRequestSchema([
                'ref_id' => $refId,
                'otp' => $otp,
            ]);

            $response = $this->vrs->VrsOfflineAadhaarVerifyOtp($request);
            $data = $response[0] ?? null;

            return [
                'success' => true,
                'name' => $data?->getName() ?? $data['name'] ?? null,
                'address' => $data?->getAddress() ?? $data['address'] ?? null,
                'gender' => $data?->getGender() ?? $data['gender'] ?? null,
                'dob' => $data?->getDob() ?? $data['dob'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Cashfree Aadhaar OTP verify failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $this->parseError($e)];
        }
    }

    // ──────────────────────────────────────────────────────────
    // PAN VERIFICATION
    // ──────────────────────────────────────────────────────────

    public function verifyPan(string $panNumber): array
    {
        try {
            $request = new PanRequestSchema([
                'pan' => strtoupper($panNumber),
            ]);

            $response = $this->vrs->VrsPanVerification($request);
            $data = $response[0] ?? null;

            return [
                'success' => true,
                'name' => $data?->getRegisteredName() ?? $data['registered_name'] ?? null,
                'pan' => $data?->getPan() ?? $panNumber,
                'valid' => $data?->getValid() ?? $data['valid'] ?? false,
            ];
        } catch (\Throwable $e) {
            Log::error('Cashfree PAN verify failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $this->parseError($e)];
        }
    }

    // ──────────────────────────────────────────────────────────
    // BANK ACCOUNT VERIFICATION (Reverse Penny Drop)
    // ──────────────────────────────────────────────────────────

    public function verifyBankAccount(string $accountNumber, string $ifsc): array
    {
        try {
            $request = new CreateRequestRequestSchema([
                'bank_account' => $accountNumber,
                'ifsc' => strtoupper($ifsc),
            ]);

            $response = $this->vrs->VrsReversePennyDropCreateRequest($request);
            $data = $response[0] ?? null;

            return [
                'success' => true,
                'ref_id' => $data?->getRefId() ?? $data['ref_id'] ?? null,
                'account_holder' => $data?->getAccountHolderName() ?? $data['account_holder_name'] ?? null,
                'status' => $data?->getAccountStatus() ?? $data['account_status'] ?? null,
                'message' => 'Bank verification initiated',
            ];
        } catch (\Throwable $e) {
            Log::error('Cashfree bank verify failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $this->parseError($e)];
        }
    }

    // ──────────────────────────────────────────────────────────
    // PAYOUTS -- Beneficiary Management
    // ──────────────────────────────────────────────────────────

    public function addBeneficiary(DaimaaProfile $profile): array
    {
        try {
            $beneId = 'DAIMAA_' . $profile->user_id;

            $instrument = new CreateBeneficiaryRequestBeneficiaryInstrumentDetails([
                'bank_account_number' => $profile->bank_account_number,
                'bank_ifsc' => $profile->bank_ifsc,
            ]);

            $contact = new CreateBeneficiaryRequestBeneficiaryContactDetails([
                'beneficiary_email' => $profile->user?->email,
                'beneficiary_phone' => $profile->user?->phone,
            ]);

            $request = new CreateBeneficiaryRequest([
                'beneficiary_id' => $beneId,
                'beneficiary_name' => $profile->bank_account_holder ?: $profile->user?->name,
                'beneficiary_instrument_details' => $instrument,
                'beneficiary_contact_details' => $contact,
            ]);

            $this->payout->PayoutCreateBeneficiary('2024-01-01', null, $request);

            $profile->update(['cashfree_beneficiary_id' => $beneId]);

            return ['success' => true, 'beneficiary_id' => $beneId];
        } catch (\Throwable $e) {
            // Beneficiary may already exist
            if (str_contains($e->getMessage(), 'already exists') || str_contains($e->getMessage(), 'duplicate')) {
                $beneId = 'DAIMAA_' . $profile->user_id;
                $profile->update(['cashfree_beneficiary_id' => $beneId]);
                return ['success' => true, 'beneficiary_id' => $beneId, 'note' => 'already_exists'];
            }
            Log::error('Cashfree add beneficiary failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $this->parseError($e)];
        }
    }

    // ──────────────────────────────────────────────────────────
    // PAYOUTS -- Transfer
    // ──────────────────────────────────────────────────────────

    public function initiateTransfer(Payout $payout): array
    {
        try {
            $profile = $payout->daimaa?->daimaaProfile;
            if (!$profile) {
                return ['success' => false, 'message' => 'Daimaa profile not found'];
            }

            if (!$profile->cashfree_beneficiary_id) {
                $beneResult = $this->addBeneficiary($profile);
                if (!$beneResult['success']) return $beneResult;
            }

            $transferId = 'PAY_' . $payout->id . '_' . strtoupper(substr(md5(now()), 0, 6));

            $request = new CreateTransferRequest([
                'transfer_id' => $transferId,
                'transfer_amount' => (float) $payout->amount,
                'transfer_currency' => 'INR',
                'transfer_mode' => 'banktransfer',
                'beneficiary_details' => [
                    'beneficiary_id' => $profile->cashfree_beneficiary_id,
                ],
            ]);

            $response = $this->payout->PayoutInitiateTransfer('2024-01-01', null, $request);
            $data = $response[0] ?? null;

            $cfTransferId = $data?->getCfTransferId() ?? $data['cf_transfer_id'] ?? null;

            $payout->update([
                'reference' => $transferId,
                'notes' => 'CF Transfer: ' . ($cfTransferId ?: $transferId),
                'status' => 'processing',
            ]);

            return [
                'success' => true,
                'transfer_id' => $transferId,
                'cf_transfer_id' => $cfTransferId,
            ];
        } catch (\Throwable $e) {
            Log::error('Cashfree payout transfer failed', [
                'payout_id' => $payout->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => $this->parseError($e)];
        }
    }

    public function getTransferStatus(string $transferId): array
    {
        try {
            $response = $this->payout->PayoutFetchTransfer('2024-01-01', null, null, $transferId);
            $data = $response[0] ?? null;

            return [
                'success' => true,
                'status' => $data?->getStatus() ?? $data['status'] ?? null,
                'utr' => $data?->getStatusDetails()?->getUtr() ?? $data['status_details']['utr'] ?? null,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $this->parseError($e)];
        }
    }

    // ──────────────────────────────────────────────────────────
    // WEBHOOK VERIFICATION
    // ──────────────────────────────────────────────────────────

    public function verifyWebhookSignature(string $signature, string $rawBody, string $timestamp): bool
    {
        try {
            $this->payout->PayoutVerifyWebhookSignature($signature, $rawBody, $timestamp);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Cashfree webhook signature verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function parseError(\Throwable $e): string
    {
        $msg = $e->getMessage();
        if ($e instanceof \Cashfree\ApiException) {
            $body = $e->getResponseBody();
            if (is_string($body)) {
                $decoded = json_decode($body, true);
                $msg = $decoded['message'] ?? $decoded['error'] ?? $msg;
            }
        }
        return $msg;
    }
}
