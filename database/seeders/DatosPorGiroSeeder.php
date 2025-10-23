<?php

namespace Database\Seeders;

use App\Models\CatDatosPorGiro;
use Illuminate\Database\Seeder;
use App\Models\CatGiro;

class DatosPorGiroSeeder extends Seeder
{
    public function run()
    {
        // CASOS SUGERIDOS para varios giros
        $mapeo = [
            'Casetas' => [
                ['nombre'=>'numero_caseta','label'=>'Número de caseta','tipo'=>'string','requerido'=>true],
                ['nombre'=>'sentido','label'=>'Sentido (ej. CDMX - Puebla)','tipo'=>'string','requerido'=>false],
                ['nombre'=>'peaje','label'=>'Peaje (importe)','tipo'=>'numeric','requerido'=>false],
            ],
            'Gasolinería' => [
                ['nombre'=>'numero_isla','label'=>'Número de isla','tipo'=>'string','requerido'=>false],
                ['nombre'=>'marca','label'=>'Marca','tipo'=>'string','requerido'=>false],
                ['nombre'=>'tipo_combustible','label'=>'Tipo de combustible','tipo'=>'string','requerido'=>false],
                ['nombre'=>'precio_por_litro','label'=>'Precio por litro','tipo'=>'numeric','requerido'=>false],
            ],
            'Restaurante' => [
                ['nombre'=>'num_mesa','label'=>'Número de mesa','tipo'=>'string','requerido'=>false],
                ['nombre'=>'folio_factura','label'=>'Folio de factura','tipo'=>'string','requerido'=>false],
                ['nombre'=>'telefono','label'=>'Teléfono','tipo'=>'string','requerido'=>false],
            ],
            'Taxi' => [
                ['nombre'=>'placa','label'=>'Placa','tipo'=>'string','requerido'=>false],
                ['nombre'=>'numero_concesion','label'=>'Número de concesión','tipo'=>'string','requerido'=>false],
            ],
            'Hotel' => [
                ['nombre'=>'numero_habitacion','label'=>'Número de habitación','tipo'=>'string','requerido'=>false],
                ['nombre'=>'noches','label'=>'Noches','tipo'=>'numeric','requerido'=>false],
                ['nombre'=>'estrellas','label'=>'Estrellas del hotel','tipo'=>'numeric','requerido'=>false],
            ],
            'Estacionamiento' => [
                ['nombre'=>'numero_plazas','label'=>'Número de plazas','tipo'=>'numeric','requerido'=>false],
                ['nombre'=>'tarifa_por_hora','label'=>'Tarifa por hora','tipo'=>'numeric','requerido'=>false],
            ],
            'Gas' => [
                ['nombre'=>'tipo','label'=>'Tipo (por ejemplo: gas LP/gn)','tipo'=>'string','requerido'=>false],
                ['nombre'=>'metros_cubicos','label'=>'Metros cúbicos','tipo'=>'numeric','requerido'=>false],
            ],
            'Panadería' => [
                ['nombre'=>'producto_principal','label'=>'Producto principal','tipo'=>'string','requerido'=>false],
            ],
            'Tienda' => [
                ['nombre'=>'categoria','label'=>'Categoría de tienda','tipo'=>'string','requerido'=>false],
            ],
        ];

        foreach ($mapeo as $giroNombre => $campos) {
            $giro = CatGiro::where('nombre', $giroNombre)->first();
            if (!$giro) continue;
            foreach ($campos as $c) {
                CatDatosPorGiro::updateOrCreate(
                    [
                        'id_giro' => $giro->id,
                        'nombre_dato_giro' => $c['nombre']
                    ],
                    [
                        'label' => $c['label'],
                        'tipo' => $c['tipo'],
                        'requerido' => $c['requerido']
                    ]
                );
            }
        }
    }
}
