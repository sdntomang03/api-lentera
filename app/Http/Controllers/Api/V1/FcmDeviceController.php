<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FcmDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FcmDeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'deviceId' => ['required', 'uuid'],
            'token' => ['required', 'string', 'max:4096'],
            'platform' => ['required', 'in:android,ios'],
            'username' => ['sometimes', 'string', 'max:255', Rule::in([$request->user()->username])],
        ]);

        FcmDevice::updateOrCreate(
            ['device_id' => $data['deviceId']],
            [
                'user_id' => $request->user()->id,
                'username' => $request->user()->username,
                'token' => $data['token'],
                'platform' => $data['platform'],
            ],
        );

        return response()->json([
            'message' => 'Perangkat berhasil didaftarkan untuk notifikasi.',
            'deviceId' => $data['deviceId'],
            'username' => $request->user()->username,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'deviceId' => ['required', 'uuid'],
        ]);

        FcmDevice::query()
            ->where('user_id', $request->user()->id)
            ->where('device_id', $data['deviceId'])
            ->delete();

        return response()->json([
            'message' => 'Perangkat berhasil dilepas dari notifikasi.',
        ]);
    }
}
