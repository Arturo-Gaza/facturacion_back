<?php

namespace App\Repositories\SistemaFacturacion;

use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use App\Models\Solicitud;
use Illuminate\Http\Request;
class SolicitudRepository implements SolicitudRepositoryInterface
{
    public function getAll()
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->get();
    }

    public function getByID($id): ?Solicitud
    {
        return Solicitud::with(['usuario', 'empleado', 'estadoSolicitud'])->find($id);
    }

    public function store(Request $request): Solicitud
    {
        $solicitud = new Solicitud();
        $solicitud->usuario_id = $request->usuario_id;
        $solicitud->estado_id = 1; // Estado por defecto

        // Guardar imagen
        if ($request->hasFile('imagen')) {
            $rutaImagen = $solicitud->guardarImagen($request->file('imagen'));
            $solicitud->imagen_url = $rutaImagen;
        }

        $solicitud->save();
        
        return $solicitud;
    }

    public function update(array $data, $id): ?Solicitud
    {
        $solicitud = Solicitud::find($id);
        if ($solicitud) {
            $solicitud->update($data);
        }
        return $solicitud;
    }

        public function getByUsuario(int $usuario_id)
    {
           return Solicitud::where('usuario_id', $usuario_id)
                    ->with(['usuario', 'empleado', 'estadoSolicitud'])
                    ->get();
    }

}