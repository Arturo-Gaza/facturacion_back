<?php

namespace App\Classes;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApiResponseHelper
{
    public static function rollback($e, $message = 'Ocurrio un error durante el proceso')
    {
        DB::rollBack();
        self::throw($e, $message);
    }
    //hola

    public static function throw($e, $message = 'Ocurrio un error durante el proceso')
    {
        Log::info($e);
        throw new HttpResponseException(response()->json([
            'message' => $message,
            'errors' => [$message],
            'data' => $e,
            'success' => false,
        ], 500));
    }

    public static function sendResponse($result, $message = '', $code = 200, $data2 = null)
    {
        if ($code === 204) {
            return response()->noContent();
        }

        $response = [
            'success' => true,
            'data' => self::transformDates($result),
            'data2' => self::transformDates($data2)
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

private static function transformDates($data)
{
    if ($data instanceof \Illuminate\Support\Collection) {
        return $data->map(function ($item) {
            return self::transformDates($item);
        });
    }

    if ($data instanceof \Illuminate\Database\Eloquent\Model) {
        $dates = $data->getDates();
        $arrayData = $data->toArray(); // Incluye atributos, relaciones, appends

        // Formatea solo los campos de fecha definidos por el modelo
        foreach ($dates as $dateField) {
            if (isset($arrayData[$dateField]) && !empty($arrayData[$dateField])) {
                $arrayData[$dateField] = \Carbon\Carbon::parse($arrayData[$dateField], 'UTC')
                    ->setTimezone('America/Mexico_City')
                    ->translatedFormat('d-M-Y H:i');
            }
        }

        // Procesa relaciones cargadas
        foreach ($data->getRelations() as $relationKey => $relationValue) {
            $arrayData[$relationKey] = self::transformDates($relationValue);
        }

        return $arrayData;
    }

    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = self::transformDates($value);
        }
        return $data;
    }

    return $data;
}


}
