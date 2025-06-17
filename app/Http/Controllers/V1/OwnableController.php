<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\V1\IOwnableCollection;
use App\Services\V1\OwnableService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class OwnableController extends Controller
{
    private OwnableService $service;

    public function __construct()
    {
        $this->service = OwnableService::getInstance();
    }


    use ApiResponseTrait;
    public function search($entity, $search = ''): JsonResponse
    {
        try {
            $result = $this->service->search($entity, $search);
            return $this->successResponse(
                new IOwnableCollection($result),
                "Search results retrieved successfully",
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                "Error retrieving search results: " . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
