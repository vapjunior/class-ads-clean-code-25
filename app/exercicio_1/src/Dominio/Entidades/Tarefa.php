<?php

declare(strict_types=1);

namespace App\Dominio\Entidades;

class Tarefa
{
    private ?int $id;
    private string $titulo;
    private string $descricao;
    private int $usuarioId;
    private string $status;
    private string $data;

    public function __construct(
        ?int $id,
        string $titulo,
        string $descricao,
        int $usuarioId,
        string $status,
        string $data
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->usuarioId = $usuarioId;
        $this->status = $status;
        $this->data = $data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getData(): string
    {
        return $this->data;
    }

    
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}