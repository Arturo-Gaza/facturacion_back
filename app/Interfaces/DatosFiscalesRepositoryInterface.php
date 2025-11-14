<?php

namespace App\Interfaces;

use App\Models\DatosFiscal;
use Illuminate\Http\Request;

interface DatosFiscalesRepositoryInterface
{
    public function getAll();
     public function extraerDatosCFDI(Request $data);
    public function getByID($id): ?DatosFiscal;
     public function getByUsr($id);
    public function store(array $data): DatosFiscal;
    public function storeConDomicilio(array $data,array $direccion );
     public function storeCompleto(array $data,array $direccion,array $regimenesData );
     public function updateCompleto(array $data, array $direccion, array $regimenes, $idDatosFiscales );
    public function update(array $data, $id): ?DatosFiscal;
     public function eliminarReceptor($id);
      public function validarCantidadRFC( $id_user);
      
}