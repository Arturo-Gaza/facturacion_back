<?php

namespace App\Interfaces;

interface TabArchivoSolicitudesDetalleRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function getByIDSolicitudDeta($id);
    public function store( $data);
    public function delete($id);
    public function update(array $data, $id);
}
