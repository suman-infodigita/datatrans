<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Services\WalleeService;
use Wallee\Sdk\Service\TokenService;

class WalleeWebhookController extends Controller
{
    public function handle(Request $request, WalleeService $wallee)
    {
        $data = $request->json()->all();

        if ($data['listenerEntityTechnicalName'] === 'TOKEN') {
            $tokenId = $data['entityId'];
            $tokenService = new TokenService($wallee->apiClient);
            $token = $tokenService->read($wallee->spaceId, $tokenId);
            $wallee->storeToken($token->getId(), $token->getState());
        }

        return response('OK', 200);
    }
}
