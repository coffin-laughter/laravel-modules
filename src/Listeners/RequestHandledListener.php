<?php

declare(strict_types=1);

/**
 *  +-------------------------------------------------------------------------------------------
 *  | Coffin [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 *  +-------------------------------------------------------------------------------------------
 *  | This is not a free software, without any authorization is not allowed to use and spread.
 *  +-------------------------------------------------------------------------------------------
 *  | Copyright (c) 2006~2024 All rights reserved.
 *  +-------------------------------------------------------------------------------------------
 *  | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 *  +-------------------------------------------------------------------------------------------
 */

namespace Nwidart\Modules\Listeners;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\JsonResponse;
use Nwidart\Modules\Enums\Code;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RequestHandledListener
{
    /**
     * Handle the event.
     *
     * @param RequestHandled $event
     * @return void
     */
    public function handle(RequestHandled $event): void
    {
        if (isRequestFromAjax()) {
            $response = $event->response;

            if ($response instanceof JsonResponse) {
                $exception = $response->exception;

                if ($response->getStatusCode() == SymfonyResponse::HTTP_OK && !$exception) {
                    $response->setData($this->formatData($response->getData()));
                }
            }
        }
    }

    /**
     * @param mixed $data
     * @return array
     */
    protected function formatData(mixed $data): array
    {
        $responseData = [
            'success' => true,
            'code' => Code::SUCCESS->value(),
            'message' => Code::SUCCESS->message(),
        ];

        if (is_object($data) && property_exists($data, 'per_page')
            && property_exists($data, 'total')
            && property_exists($data, 'current_page')) {
            $responseData['data'] = $data->data;
            $responseData['total'] = $data->total;
            $responseData['limit'] = $data->per_page;
            $responseData['page'] = $data->current_page;

            return $responseData;
        }

        $responseData['data'] = $data;

        return $responseData;
    }
}
