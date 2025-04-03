<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('TransactionController->store received data: ' . json_encode($request->all()));
        $transaction_type = empty($request['transaction_type']) ? 'transaction' : $request['transaction_type'];

        $trans_data = [
            'transaction_type' => $transaction_type,
            'user_id' => $request['user_id'],
            'order_id' => $request['order_id'],
            'order_item_id' => $request['order_item_id'] ?? null,
            'type' => strtolower($request['type']),
            'txn_id' => $request['txn_id'],
            'amount' => $request['amount'],
            'fee' => $request['fee'] ?? 0,
            'status' => $request['status'],
            'message' => $request['message'],
            'transaction_date' => now(),
        ];

        // Створюємо транзакцію і зберігаємо результат
        $transaction = Transaction::create($trans_data);

        // Логуємо створену транзакцію
        \Log::info('Transaction created: ' . json_encode($transaction->toArray()));

        // Перевіряємо, чи збережено fee коректно
        $savedTransaction = Transaction::where('txn_id', $trans_data['txn_id'])->first();
        \Log::info('Transaction fetched from DB: ' . json_encode($savedTransaction->toArray()));

        return response()->json([
            'error' => false,
            'message' => 'Transaction stored successfully',
            'data' => $transaction
        ]);
    }

    public function update_transaction($txn_id, $data)
    {
        $transaction = Transaction::where('txn_id', $txn_id)->first();
        if ($transaction) {
            $transaction->update($data);
            \Log::info('Transaction updated: ' . json_encode($transaction->toArray()));
            return true;
        }
        \Log::warning('Transaction not found for txn_id: ' . $txn_id);
        return false;
    }
}
