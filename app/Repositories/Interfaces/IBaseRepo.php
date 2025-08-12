<?php

namespace App\Repositories\Interfaces;

interface IBaseRepo
{
    public function all();
    public function find($id);
    public function findByCond(array $conditions);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
