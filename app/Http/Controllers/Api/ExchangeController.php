<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    private CurrencyExchangeService $currencyExchangeService;

    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function rates(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'regex:/^[A-Z]+(,[A-Z]+)*$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $neededCurrencies = $request->query('currency') !== null ? explode(',', $request->query('currency')) : null;
        $data             = $this->currencyExchangeService->getRates($neededCurrencies);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $data,
        ]);
    }

    public function convertCurrency(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'currency_from' => ['required', 'string', 'regex:/^[A-Z]+$/'],
            'currency_to'   => ['required', 'string', 'regex:/^[A-Z]+$/'],
            'value'         => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $currencyFrom = $request->input('currency_from');
        $currencyTo   = $request->input('currency_to');
        $value        = $request->input('value');

        try {
            $data = $this->currencyExchangeService->convertCurrency($currencyFrom, $currencyTo, $value);
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data'   => $data,
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
}
