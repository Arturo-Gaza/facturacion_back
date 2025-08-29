<?php

namespace App\Interfaces\SistemaTickets;

interface TabCotizacionesSolicitudRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function getByIdDetalle($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete( $id);
}
