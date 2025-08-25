<?php

declare(strict_types=1);

namespace App\Infra\Repositorios;

use App\Dominio\Entidades\Tarefa;
use App\Dominio\Repositorios\TarefaRepositorio;

class InMemoryTarefaRepositorio implements TarefaRepositorio
{
    /**
     * @var array<Tarefa>
     */
    private array $tarefas;
    private int $proximoId = 4;

    public function __construct()
    {
        $this->tarefas = [
            1 => new Tarefa(1, 'Estudar PHP', 'Revisar conceitos básicos', 1, 'pendente', '2024-01-15'),
            2 => new Tarefa(2, 'Fazer compras', 'Ir ao supermercado', 1, 'concluida', '2024-01-14'),
            3 => new Tarefa(3, 'Exercitar-se', 'Academia às 18h', 2, 'pendente', '2024-01-16'),
        ];
    }

    public function adicionar(Tarefa $tarefa): void
    {
        $tarefa->setId($this->proximoId++);
        $this->tarefas[$tarefa->getId()] = $tarefa;
    }

    public function atualizar(int $id, Tarefa $tarefa): void
    {
        if (isset($this->tarefas[$id])) {
            $this->tarefas[$id] = $tarefa;
        }
    }

    public function atualizarStatus(int $id, string $status): void
    {
        if (isset($this->tarefas[$id])) {
            $this->tarefas[$id]->setStatus($status);
        }
    }

    public function deletar(int $id): void
    {
        unset($this->tarefas[$id]);
    }

    public function buscarPorId(int $id): ?Tarefa
    {
        return $this->tarefas[$id] ?? null;
    }

    public function buscarTodos(?int $usuarioId = null, ?string $status = null, ?string $busca = null): array
    {
        $resultado = $this->tarefas;

        if ($usuarioId !== null) {
            $resultado = array_filter($resultado, fn(Tarefa $tarefa) => $tarefa->getUsuarioId() === $usuarioId);
        }
        
        if ($status !== null) {
            $resultado = array_filter($resultado, fn(Tarefa $tarefa) => $tarefa->getStatus() === $status);
        }

        if ($busca !== null) {
            $busca = trim($busca);
            $resultado = array_filter($resultado, fn(Tarefa $tarefa) => stripos($tarefa->getTitulo(), $busca) !== false || stripos($tarefa->getDescricao(), $busca) !== false);
        }

        return array_values($resultado);
    }

    public function contarTodas(): int
    {
        return count($this->tarefas);
    }

    public function contarPorStatus(string $status): int
    {
        return count(array_filter($this->tarefas, fn(Tarefa $tarefa) => $tarefa->getStatus() === $status));
    }
}