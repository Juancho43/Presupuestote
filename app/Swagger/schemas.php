<?php


/**
 * @OA\Schema(
 *     schema="BudgetItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="made_date", type="string", format="date"),
 *     @OA\Property(property="dead_line", type="string", format="date", nullable=true),
 *     @OA\Property(property="total_price", type="number", format="float", nullable=true),
 *     @OA\Property(property="profit", type="number", format="float", nullable=true),
 *     @OA\Property(property="client_id", type="integer")
 * )
 */

/**
 * @OA\Schema(
 *     schema="BudgetCollection",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/BudgetItem")
 *     ),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="status", type="integer")
 * )
 */
