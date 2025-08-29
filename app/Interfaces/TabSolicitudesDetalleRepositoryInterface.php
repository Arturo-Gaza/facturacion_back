<?php

namespace App\Interfaces;

interface TabSolicitudesDetalleRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function getByIDSolicitud($id);
    public function store(array $data);
    public function update(array $data, $id);
     public function deleteByDetalle( $id);
     public function deleteBySolicitud( $id);
}
