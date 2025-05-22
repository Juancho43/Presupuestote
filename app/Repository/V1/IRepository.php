<?php
// app/Repository/V1/IRepository.php
namespace App\Repository\V1;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface IRepository
{
    public function all(): Collection;
    public function find(int $id): Model;
    public function create($data): Model;
    public function update($data): Model;
    public function delete(int $id): bool;
}
