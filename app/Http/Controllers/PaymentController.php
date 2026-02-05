<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'metadata' => [
                    'userId' => $request->userId, 
                    'integration_check' => 'accept_a_payment'
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // public function handleWebhook(Request $request) 
    // {
    //     $payload = $request->getContent();
    //     $sig_header = $request->header('Stripe-Signature');
    //     $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

    //     try {
    //         $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Invalid signature'], 400);
    //     }

    //     if ($event->type == 'payment_intent.succeeded') {
    //         $paymentIntent = $event->data->object;
    //         $userId = $paymentIntent->metadata->userId;
    //         $amount = $paymentIntent->amount / 100;

    //         DB::transaction(function () use ($userId, $amount, $paymentIntent) {

    //             DB::table('user')->where('userID', $userId)->decrement('solde', $amount);

    //             DB::table('transaction')->insert([
    //                 'userID' => $userId,
    //                 'amount' => $amount,
    //                 'status' => 'completed',
    //                 'date'   => now()
    //             ]);

    //             $card = $paymentIntent->payment_method_details->card ?? null;
    //             if ($card) {
    //                 DB::table('compte')->updateOrInsert(
    //                     ['numero' => $paymentIntent->id], 
    //                     [
    //                         'userId'    => $userId,
    //                         'lastdigit' => $card->last4,
    //                         'type'      => $card->brand,
    //                         'monnaie'   => strtoupper($paymentIntent->currency),
    //                         'date'      => now(),
    //                     ]
    //                 );
    //             }
    //         });
    //     }

    //     return response()->json(['status' => 'success']);
    // }

    public function handleWebhook(Request $request) 
    {
        // $sig_header = $request->header('Stripe-Signature');
        // $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        // $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

        $event = json_decode($request->getContent());

        if ($event->type == 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $userId = $paymentIntent->metadata->userId;
            $amount = $paymentIntent->amount;

            DB::transaction(function () use ($userId, $amount, $paymentIntent) {
                DB::table('user')->where('userID', $userId)->decrement('solde', $amount);

                DB::table('transaction')->insert([
                    'userID' => $userId,
                    'amount' => $amount,
                    'status' => 'completed',
                    'date'   => now()
                ]);

                $card = $paymentIntent->payment_method_details->card ?? null;
                if ($card) {
                    DB::table('compte')->updateOrInsert(
                        ['numero' => $paymentIntent->id], 
                        [
                            'userId'    => $userId,
                            'lastdigit' => $card->last4,
                            'type'      => $card->brand,
                            'monnaie'   => strtoupper($paymentIntent->currency),
                            'date'      => now(),
                        ]
                    );
                }
            });
        }
        return response()->json(['status' => 'success']);
    }


    public function getTransactionSummary(Request $request)
    {
        try {
            $beneficiary = DB::table('user')
                ->where('userID', $request->beneficiaryId)
                ->first(['nprenom']); 

            $originalAmount = (float)$request->amount;
            $discount = 0;
            $couponCode = $request->couponCode;

            // if ($couponCode) { // Uncomment if you want to fetch coupons from the database
            //     $coupon = DB::table('coupons')
            //         ->where('code', $couponCode)
            //         ->where('status', 'active')
            //         ->first();

            //     if ($coupon) {
            //         if ($coupon->type === 'percent') {
            //             $discount = ($originalAmount * $coupon->value) / 100;
            //         } else {
            //             $discount = (float)$coupon->value;
            //         }
            //     }
            // }

            $coupons = [
                'SAVE10'    => ['type' => 'fixed', 'value' => 10],
                'SAVE20'    => ['type' => 'fixed', 'value' => 20],
                'SAVE50'    => ['type' => 'fixed', 'value' => 50],
                'WELCOME10' => ['type' => 'percent', 'value' => 10],
            ];

            if (isset($coupons[$couponCode])) {
                $coupon = $coupons[$couponCode];
                if ($coupon['type'] === 'percent') {
                    $discount = ($originalAmount * $coupon['value']) / 100;
                } else {
                    $discount = (float)$coupon['value'];
                }
            }

            $totalAmount = $originalAmount - $discount;

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_amount' => $originalAmount,
                    'discount_amount'    => $discount,
                    'total_amount'       => $totalAmount,
                    'details' => [
                        'type'           => $request->transactionType ?? 'Depot Banquaire',
                        'beneficiary'    => $beneficiary ? $beneficiary->nprenom : 'Unknown',
                        'payment_method' => $request->paymentMethod ?? 'N/A',
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
