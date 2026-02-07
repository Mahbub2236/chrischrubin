<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'userID' => 'required',
            'amount' => 'required|numeric|min:1',
            'benId'  => 'required'
        ]);

        // Step 1: User-er balance check kora
        $user = \App\Models\User::find($request->userID);
        if (!$user || $user->solde < $request->amount) {
            return response()->json(['error' => 'Insufficient balance!'], 400);
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => (int)($request->amount * 100),
                'currency' => 'usd',
                'payment_method' => 'pm_card_visa',
                'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
            ]);

            // Step 2: Transaction record toiri (Type hobe 'payment')
            Transaction::create([
                'userID'    => $request->userID,
                'benId'     => $request->benId,
                'type'      => 'payment_to_beneficiary', // Type change kora holo
                'amount'    => $request->amount,
                'status'    => 'pending',
                'reference' => $intent->id,
                'date'      => \Carbon\Carbon::now(),
            ]);

            return response()->json([
                'payment_intent_id' => $intent->id,
                'status' => 'Intent Created'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function confirmPayment(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $intentId = $request->payment_intent_id;
            $intent = \Stripe\PaymentIntent::retrieve($intentId);

            if ($intent->status !== 'succeeded') {
                $intent->confirm();
            }

            $intent = \Stripe\PaymentIntent::retrieve($intentId);

            if ($intent->status == 'succeeded') {
                $transaction = Transaction::where('reference', $intentId)->first();
                
                if ($transaction && $transaction->status !== 'completed') {
                    $transaction->update(['status' => 'completed']);
                    
                    $sender = \App\Models\User::find($transaction->userID);
                    if ($sender) {
                        $sender->decrement('solde', $transaction->amount);
                    }

                    $receiver = \App\Models\User::find($transaction->benId); 
                    if ($receiver) {
                        $receiver->increment('solde', $transaction->amount);
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment successful!',
                        'sender_new_balance' => $sender->solde ?? null,
                        'receiver_new_balance' => $receiver->solde ?? null
                    ]); 
                }
            }

            return response()->json(['status' => $intent->status]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function processTransaction(Request $request) 
    {
        $amount = $request->amount;
        $discount = 0;
        if ($request->coupon == 'SAVE10') $discount = 10;
        elseif ($request->coupon == 'SAVE20') $discount = 20;
        elseif ($request->coupon == 'SAVE50') $discount = 50;
        elseif ($request->coupon == 'WELCOME10') $discount = ($amount * 10) / 100;

        $finalAmount = $amount - $discount;

        DB::table('transaction')->insert([
            'userId' => $request->userId,
            'benId' => $request->benId,
            'amount' => $finalAmount,
            'type' => $request->type,
            'status' => 'Pending',
            'created_at' => now()
        ]);

        return response()->json(['message' => 'Transaction Successful!']);
    }
}