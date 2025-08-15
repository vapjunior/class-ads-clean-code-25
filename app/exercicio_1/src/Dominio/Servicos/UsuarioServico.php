<?php

declare(strict_types=1);

namespace App\Dominio\Servicos;

use App\Dominio\Entidades\Usuario;
use App\Dominio\Repositorios\UsuarioRepositorio;
use InvalidArgumentException;

class UsuarioServico
{
    private UsuarioRepositorio $usuarioRepositorio;

    public function __construct(UsuarioRepositorio $usuarioRepositorio)
    {
        $this->usuarioRepositorio = $usuarioRepositorio;
    }

    public function adicionarUsuario(Usuario $usuario): void
    {
        $this->validarUsuario($usuario);

        if ($this->usuarioRepositorio->buscarPorEmail($usuario->getEmail())) {
            throw new InvalidArgumentException("Email já existe.");
        }

        $this->usuarioRepositorio->adicionar($usuario);
    }

    public function login(string $email, string $senha): ?Usuario
    {
        $usuario = $this->usuarioRepositorio->buscarPorEmail($email);

        if (!$usuario || $usuario->getSenha() !== $senha) {
            return null;
        }

        return $usuario;
    }
    
    private function validarUsuario(Usuario $usuario): void
    {
        $nome = trim($usuario->getNome());
        $email = trim($usuario->getEmail());
        $senha = trim($usuario->getSenha() ?? '');

        if (strlen($nome) < 2) {
            throw new InvalidArgumentException("Nome muito curto.");
        }
        if (strlen($nome) > 100) {
            throw new InvalidArgumentException("Nome muito longo.");
        }
        if (empty($nome)) {
            throw new InvalidArgumentException("Nome obrigatório.");
        }
        if (is_numeric($nome)) {
            throw new InvalidArgumentException("Nome inválido.");
        }
        if (strpos($nome, ' ') === false) {
            throw new InvalidArgumentException("Nome deve ter sobrenome.");
        }

        if (strlen($email) < 5) {
            throw new InvalidArgumentException("Email muito curto.");
        }
        if (strlen($email) > 200) {
            throw new InvalidArgumentException("Email muito longo.");
        }
        if (empty($email)) {
            throw new InvalidArgumentException("Email obrigatório.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email inválido.");
        }

        if (strlen($senha) < 6) {
            throw new InvalidArgumentException("Senha muito curta.");
        }
        if (strlen($senha) > 50) {
            throw new InvalidArgumentException("Senha muito longa.");
        }
        if (empty($senha)) {
            throw new InvalidArgumentException("Senha obrigatória.");
        }
        if (!preg_match('/[0-9]/', $senha)) {
            throw new InvalidArgumentException("Senha deve ter número.");
        }
        if (!preg_match('/[a-zA-Z]/', $senha)) {
            throw new InvalidArgumentException("Senha deve ter letra.");
        }
    }
}