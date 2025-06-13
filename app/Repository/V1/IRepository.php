<?php
// app/Repository/V1/IRepository.php
namespace App\Repository\V1;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

interface IRepository
{
    public function all(int $page = 1): Paginator;
    public function find(int $id): Model;
    public function create($data): Model;
    public function update($data): Model;
    public function delete(int $id): bool;
}
