<?php

namespace App\DTOs\V1;

use App\States\WorkState\WorkState;
use Illuminate\Support\Facades\Date;

readonly class WorkDTO
{
    public function __construct(
        public int|null $id = null,
        public int|null $order = null,
        public string|null $name = null,
        public string|null $notes = null,
        public int|null $estimated_time = null,
        public Date|null $dead_line = null,
        public float|null $cost = null,
        public WorkState|null $state = null,
        public BudgetDTO|null $budget = null,
    ) {
    }
}
