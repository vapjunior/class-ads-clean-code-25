<?php

declare(strict_types=1);

namespace App\Infra\Repositorios;

use App\Dominio\Entidades\Usuario;
use App\Dominio\Repositorios\UsuarioRepositorio;

class InMemoryUsuarioRepositorio implements UsuarioRepositorio
{
    private array $usuarios;
    private int $proximoId = 3;

    public function __construct()
    {
        $this->usuarios = [
            1 => new Usuario(1, 'João Silva', 'joao@email.com', 'senha123'),
            2 => new Usuario(2, 'Maria Santos', 'maria@email.com', 'abc456'),
        ];
    }

    public function adicionar(Usuario $usuario): void
    {
        $usuario->setId($this->proximoId++);
        $this->usuarios[$usuario->getId()] = $usuario;
    }

    public function buscarPorId(int $id): ?Usuario
    {
        return $this->usuarios[$id] ?? null;
    }

    public function buscarPorEmail(string $email): ?Usuario
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getEmail() === $email) {
                return $usuario;
            }
        }
        return null;
    }

    public function contarTodos(): int
    {
        return count($this->usuarios);
    }
}