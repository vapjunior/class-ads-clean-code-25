<?php

declare(strict_types=1);

namespace App\Dominio\Repositorios;

use App\Dominio\Entidades\Usuario;

interface UsuarioRepositorio
{
    public function adicionar(Usuario $usuario): void;
    public function buscarPorId(int $id): ?Usuario;
    public function buscarPorEmail(string $email): ?Usuario;
    public function contarTodos(): int;
}