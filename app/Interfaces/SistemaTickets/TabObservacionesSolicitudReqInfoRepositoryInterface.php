<?php

namespace App\Interfaces\SistemaTickets;

interface TabObservacionesSolicitudReqInfoRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function getByIdSolicitud($id);
    public function store(array $data);
    public function update(array $data, $id);
}
