<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Transaction;
use Carbon\Carbon;

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
    // public function createPaymentIntent(Request $request)
    // {
    //     $user = \App\Models\User::find($request->userID);
    //     if ($user->solde < $request->amount) {
    //         return response()->json(['error' => 'Insufficient Balance!'], 400);
    //     }
    //     $stripeSecret = env('STRIPE_SECRET');
    //     if (!$stripeSecret) {
    //         return response()->json(['error' => 'Secret Key not found in .env'], 500);
    //     }
        
    //     \Stripe\Stripe::setApiKey($stripeSecret);

    //     try {
    //         $intent = PaymentIntent::create([
    //             'amount' => (int)($request->amount * 100),
    //             'currency' => 'usd',
    //             'payment_method' => 'pm_card_visa',
    //             'confirm' => true,
    //             'automatic_payment_methods' => [
    //                 'enabled' => true,
    //                 'allow_redirects' => 'never'
    //             ],
    //             'metadata' => [
    //                 'user_id' => $request->userID,
    //                 'ben_id' => $request->benId
    //             ]
    //         ]);

    //         Transaction::create([
    //             'userID'    => $request->userID,
    //             'benId'     => $request->benId,
    //             'type'      => 'stripe_deposit',
    //             'amount'    => $request->amount,
    //             'status'    => 'pending',
    //             'reference' => $intent->id,
    //             'date'      => Carbon::now(),
    //         ]);

    //         return response()->json([
    //             'client_secret' => $intent->client_secret,
    //             'payment_intent_id' => $intent->id
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // public function confirmPayment(Request $request)
    // {
    //     $stripeSecret = env('STRIPE_SECRET');
    //     \Stripe\Stripe::setApiKey($stripeSecret);

    //     try {
    //         $intentId = $request->payment_intent_id;
    //         $intent = \Stripe\PaymentIntent::retrieve($intentId);

    //         if ($intent->status !== 'succeeded') {
    //             $intent->confirm();
    //         }

    //         $intent = \Stripe\PaymentIntent::retrieve($intentId);

    //         if ($intent->status == 'succeeded') {
    //             $transaction = Transaction::where('reference', $intentId)->first();
                
    //             if ($transaction && $transaction->status !== 'completed') {
    //                 $transaction->update(['status' => 'completed']);
                    
    //                 $user = \App\Models\User::where('userID', $transaction->userID)->first();
    //                 if ($user) {
    //                     $user->increment('solde', $transaction->amount);
    //                 }
    //             }

    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Payment Successful',
    //                 'balance' => $user->solde ?? 0
    //             ]);
    //         }

    //         return response()->json(['status' => $intent->status]);

    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // public function confirmPayment(Request $request)
    // {
    //     $stripeSecret = env('STRIPE_SECRET');
    //     \Stripe\Stripe::setApiKey($stripeSecret);

    //     try {
    //         $intentId = $request->payment_intent_id;
    //         $intent = \Stripe\PaymentIntent::retrieve($intentId);

    //         if ($intent->status == 'succeeded') {
    //             $transaction = Transaction::where('reference', $intentId)->first();
                
    //             if ($transaction && $transaction->status !== 'completed') {
    //                 $transaction->update(['status' => 'completed']);
                    
    //                 $user = \App\Models\User::find($transaction->userID);
                    
    //                 if ($user) {
    //                     // Logic: Jodi type hoy 'deposit', tobe balance barbe. 
    //                     // Jodi type hoy 'payment' (beneficiary-ke), tobe balance kombe.
                        
    //                     if ($transaction->type === 'stripe_deposit') {
    //                         $user->increment('solde', $transaction->amount);
    //                     } 
    //                     else if ($transaction->type === 'payment_to_beneficiary') {
    //                         // User-er balance komanor logic
    //                         $user->decrement('solde', $transaction->amount);
    //                     }
    //                 }
    //             }

    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Transaction Completed',
    //                 'updated_balance' => $user->solde ?? 0
    //             ]);
    //         }
            
    //         return response()->json(['status' => $intent->status]);

    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

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
                
                // 1. Sender (Je taka pathachhe) - Tar balance kombe
                $sender = \App\Models\User::find($transaction->userID);
                if ($sender) {
                    $sender->decrement('solde', $transaction->amount);
                }

                // 2. Receiver/Beneficiary (Jake taka deya hocche) - Tar balance barbe
                // Transaction table-er 'benId' ke amra User ID hishebe dhore nichhi
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
}