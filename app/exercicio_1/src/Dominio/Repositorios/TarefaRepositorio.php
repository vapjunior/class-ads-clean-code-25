<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios;

use App\Dominio\Entidades\Tarefa;

interface TarefaRepositorio
{
    public function adicionar(Tarefa $tarefa): void;
    public function atualizar(int $id, Tarefa $tarefa): void;
    public function atualizarStatus(int $id, string $status): void;
    public function deletar(int $id): void;
    
    /**
     * @return Tarefa|null
     */
    public function buscarPorId(int $id): ?Tarefa;

    /**
     * @return array<Tarefa>
     */
    public function buscarTodos(?int $usuarioId = null, ?string $status = null, ?string $busca = null): array;

    public function contarTodas(): int;
    public function contarPorStatus(string $status): int;
}