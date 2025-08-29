<?php

namespace App\Interfaces;

interface TabSolicitudesRepositoryInterface
{
    public function getAll($id);
    public function getByID($id);
    public function asignar($data);
    public function reasignar($data);
    public function reporte($datos,$filtros);
    public function cambiarEstatus($data);
    public function store(array $data);
    public function update(array $data, $id);
    public function formatearSolicitud($id);
    public function getCotizaciones($id);
}
