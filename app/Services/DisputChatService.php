<?php

namespace App\Services;

use App\Models\Disput;
use App\Models\DisputMessage;
use App\Models\ReturnRequest;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\DisputAdminNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AfterShipApiController;

class DisputChatService
{
    public function getMessages($disputId, $userType = 'user')
    {
        \Log::debug('DisputChatService getMessages', [
            'disputId' => $disputId,
            'userType' => $userType,
            'auth_user_id' => Auth::id(),
        ]);

        $disput = Disput::where('id', $disputId)->with('seller')->firstOrFail();

        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller' && $disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'admin' && Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        $messages = $disput->messages()->with('sender')->orderBy('created_at', 'asc')->get()->map(function ($message) use ($disput, $userType) {
            $senderName = $message->sender ? $message->sender->username : 'Unknown';
            $senderType = $message->sender && $message->sender->id === Auth::id() ? $userType : ($message->sender && $message->sender->id === $disput->seller_id ? 'seller' : ($message->sender && $message->sender->role_id === 1 ? 'admin' : 'user'));

            return [
                'id' => $message->id,
                'disput_id' => $message->disput_id,
                'sender_id' => $message->sender_id,
                'sender_type' => $senderType,
                'sender_name' => $senderName,
                'message' => $message->message,
                'refund_amount' => $message->refund_amount,
                'application_type' => $message->application_type,
                'refund_method' => $message->refund_method,
                'evidence_path' => $message->evidence_path ?? [],
                'proposal_status' => $message->proposal_status,
                'created_at' => $message->created_at->toIso8601String(),
            ];
        });

        return $messages;
    }

    public function sendMessage($disputId, $message, $userType = 'user')
    {
        $disput = Disput::where('id', $disputId)->firstOrFail();

        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            \Log::info('Disput access check', [
                'userType' => $userType,
                'user_id' => Auth::id(),
                'disput_user_id' => $disput->user_id,
                'disput_seller_id' => $disput->seller_id,
            ]);
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller' && $disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'admin' && Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        $disput->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $message,
        ]);

        return ['message' => 'Message sent successfully'];
    }

    public function acceptProposal($disputId, $messageId, $userType)
    {
        $disput = Disput::where('id', $disputId)->firstOrFail();
        $message = DisputMessage::where('id', $messageId)->where('disput_id', $disputId)->firstOrFail();

        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller' && $disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($message->proposal_status !== 'open') {
            throw new \Exception('This proposal is no longer open.');
        }

        DisputMessage::create([
            'disput_id' => $disputId,
            'sender_id' => Auth::id(),
            'message' => 'Proposal accepted',
            'proposal_status' => 'accepted',
        ]);

        $message->update(['proposal_status' => 'accepted']);

        $returnRequest = ReturnRequest::find($disput->return_request_id);
        $returnRequest->update([
            'refund_amount' => $message->refund_amount,
            'application_type' => $message->application_type,
            'refund_method' => $message->refund_method,
        ]);

        $disput->update(['status' => 'accepted']);

        return ['message' => 'Proposal accepted successfully'];
    }

    public function submitContrproposal($disputId, $messageId, $data, $files, $userType)
    {
        $disput = Disput::where('id', $disputId)->firstOrFail();
        $message = DisputMessage::where('id', $messageId)->where('disput_id', $disputId)->firstOrFail();

        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller' && $disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($message->proposal_status !== 'open') {
            throw new \Exception('This proposal is no longer open.');
        }

        $evidencePaths = [];
        if (!empty($files)) {
            foreach ($files as $file) {
                $path = $file->store('return_evidences', 'public');
                $evidencePaths[] = $path;
            }
        }

        DisputMessage::create([
            'disput_id' => $disputId,
            'sender_id' => Auth::id(),
            'message' => $data['message'],
            'refund_amount' => $data['refund_amount'],
            'application_type' => $data['application_type'],
            'refund_method' => $data['refund_method'],
            'evidence_path' => $evidencePaths,
            'proposal_status' => 'open',
        ]);

        $message->update(['proposal_status' => 'counter']);

        return ['message' => 'Counterproposal submitted successfully'];
    }

    public function callAdmin($disputId, $messageId, $userType)
    {
        $disput = Disput::where('id', $disputId)->firstOrFail();
        $message = DisputMessage::where('id', $messageId)->where('disput_id', $disputId)->firstOrFail();

        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller' && $disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($message->proposal_status !== 'open') {
            throw new \Exception('This proposal is no longer open.');
        }

        $disput->update([
            'status' => 'pending_admin',
            'admin_requested_at' => now(),
            'admin_requester_id' => Auth::id(),
        ]);

        DisputMessage::create([
            'disput_id' => $disputId,
            'sender_id' => Auth::id(),
            'message' => 'Admin intervention requested',
            'proposal_status' => 'admin_call',
        ]);

        $message->update(['proposal_status' => 'admin_call']);

        $admins = User::where('role_id', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new DisputAdminNotification($disput));
        }

        return ['message' => 'Admin intervention requested successfully'];
    }

    public function submitTracking($disputId, $trackingNumber, $courierService, $userType)
    {
        $disput = Disput::where('id', $disputId)->with('returnRequest')->firstOrFail();
        $returnRequest = $disput->returnRequest;

        if (
            $returnRequest->application_type !== 'return_and_refund' ||
            ($returnRequest->status != 2 && $returnRequest->status != 3)
        ) {
            throw new \Exception('Tracking submission is not allowed for this return request.');
        }

        if ($userType === 'user' && $disput->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'seller' && $disput->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        } elseif ($userType === 'admin' && Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        try {
            return DB::transaction(function () use ($disput, $returnRequest, $trackingNumber, $courierService) {
                $afterShipController = app(AfterShipApiController::class);
                $response = $afterShipController->createTracking(new \Illuminate\Http\Request([
                    'order_id' => $returnRequest->order_id,
                    'parcel_id' => null,
                    'courier_agency' => $courierService,
                    'tracking_id' => $trackingNumber,
                ]));

                $responseData = json_decode($response->getContent(), true);
                $afterShipSuccess = $response->getStatusCode() === 201 && isset($responseData['tracking']['id']);

                $orderTracking = \App\Models\OrderTracking::updateOrCreate(
                    [
                        'order_id' => $returnRequest->order_id,
                        'tracking_number' => $trackingNumber,
                        'courier_agency' => $courierService,
                    ],
                    [
                        'order_item_id' => $returnRequest->order_item_id,
                        'carrier_id' => $courierService,
                        'tracking_id' => $trackingNumber,
                        'parcel_id' => null,
                        'status' => 'pending',
                        'date' => now(),
                        'sender_id' => Auth::id(),
                        'aftership_tracking_id' => $afterShipSuccess ? $responseData['tracking']['id'] : null,
                        'aftership_data' => $afterShipSuccess ? json_encode($responseData['tracking']) : null,
                        'url' => $afterShipSuccess ? ($responseData['tracking']['tracking_url'] ?? null) : null,
                    ]
                );

                $returnRequest->update([
                    'order_tracking_id' => $orderTracking->id,
                    'status' => 3, // return_pickedup
                ]);

                \App\Models\DisputMessage::create([
                    'disput_id' => $disput->id,
                    'sender_id' => Auth::id(),
                    'message' => 'Tracking information submitted: ' . $trackingNumber . ' (' . $courierService . ')' . ($afterShipSuccess ? '' : ' (Failed to register with AfterShip)'),
                    'proposal_status' => 'tracking_submitted',
                ]);

                return ['message' => 'Tracking information submitted successfully'];
            });
        } catch (\Exception $e) {
            \Log::error('DisputChatService: Error submitting tracking', [
                'disputId' => $disputId,
                'tracking_number' => $trackingNumber,
                'courier_service' => $courierService,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
