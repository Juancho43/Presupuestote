<?php
namespace App\Repository\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Js;
use Symfony\Component\HttpFoundation\JsonResponse;

interface IRepository
{
    public function all(): Collection;
    public function find(int $id);
    public function create(FormRequest $data);
    public function update(int $id, FormRequest $data) ;
    public function delete(int $id): bool |JsonResponse;
}
?>
