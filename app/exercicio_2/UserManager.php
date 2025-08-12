<?php

class UserManager
{
    private array $users = [];
    private array $logs = [];

    public function createUser(string $name, string $email, string $password): string
    {
        if (empty($name)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome vazio fornecido";
            return "Nome é obrigatório";
        }
        if (strlen($name) < 2) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome muito curto: " . $name;
            return "Nome deve ter pelo menos 2 caracteres";
        }
        if (strlen($name) > 100) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome muito longo: " . $name;
            return "Nome deve ter no máximo 100 caracteres";
        }
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $name)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome com caracteres inválidos: " . $name;
            return "Nome deve conter apenas letras e espaços";
        }

        if (empty($email)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email vazio fornecido";
            return "Email é obrigatório";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email inválido: " . $email;
            return "Email inválido";
        }
        if (strlen($email) > 255) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email muito longo: " . $email;
            return "Email muito longo";
        }

        $emailExists = false;
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email já existe: " . $email;
            return "Email já está em uso";
        }

        if (empty($password)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha vazia fornecida";
            return "Senha é obrigatória";
        }

        if (strlen($password) < 8) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha muito curta";
            return "Senha deve ter pelo menos 8 caracteres";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha sem maiúscula";
            return "Senha deve ter pelo menos uma letra maiúscula";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha sem número";
            return "Senha deve ter pelo menos um número";
        }

        $this->users[] = ['name' => $name, 'email' => $email, 'password' => $password];
        $this->logs[] = date('Y-m-d H:i:s') . " - Sucesso: Usuário criado: " . $email;
        return "Usuário criado com sucesso";
    }

    public function updateUser(int $id, string $name, string $email, string $password): string
    {
        if (!isset($this->users[$id])) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Usuário não encontrado: ID " . $id;
            return "Usuário não encontrado";
        }

        if (empty($name)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome vazio fornecido";
            return "Nome é obrigatório";
        }

        if (strlen($name) < 2) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome muito curto: " . $name;
            return "Nome deve ter pelo menos 2 caracteres";
        }

        if (strlen($name) > 100) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome muito longo: " . $name;
            return "Nome deve ter no máximo 100 caracteres";
        }

        if (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $name)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Nome com caracteres inválidos: " . $name;
            return "Nome deve conter apenas letras e espaços";
        }

        if (empty($email)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email vazio fornecido";
            return "Email é obrigatório";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email inválido: " . $email;
            return "Email inválido";
        }
        if (strlen($email) > 255) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email muito longo: " . $email;
            return "Email muito longo";
        }

        $emailExists = false;
        foreach ($this->users as $index => $user) {
            if ($user['email'] === $email && $index !== $id) {
                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Email já existe: " . $email;
            return "Email já está em uso";
        }

        if (empty($password)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha vazia fornecida";
            return "Senha é obrigatória";
        }

        if (strlen($password) < 8) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha muito curta";
            return "Senha deve ter pelo menos 8 caracteres";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha sem maiúscula";
            return "Senha deve ter pelo menos uma letra maiúscula";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha sem número";
            return "Senha deve ter pelo menos um número";
        }

        $this->users[$id] = ['name' => $name, 'email' => $email, 'password' => $password];
        $this->logs[] = date('Y-m-d H:i:s') . " - Sucesso: Usuário atualizado: " . $email;
        return "Usuário atualizado com sucesso";
    }

    public function resetPassword(int $id, string $newPassword): string
    {
        if (!isset($this->users[$id])) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Usuário não encontrado: ID " . $id;
            return "Usuário não encontrado";
        }

        if (empty($newPassword)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha vazia fornecida";
            return "Senha é obrigatória";
        }

        if (strlen($newPassword) < 8) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha muito curta";
            return "Senha deve ter pelo menos 8 caracteres";
        }

        if (!preg_match('/[A-Z]/', $newPassword)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha sem maiúscula";
            return "Senha deve ter pelo menos uma letra maiúscula";
        }

        if (!preg_match('/[0-9]/', $newPassword)) {
            $this->logs[] = date('Y-m-d H:i:s') . " - Erro: Senha sem número";
            return "Senha deve ter pelo menos um número";
        }

        $this->users[$id]['password'] = $newPassword;
        $this->logs[] = date('Y-m-d H:i:s') . " - Sucesso: Senha resetada para usuário ID: " . $id;
        return "Senha resetada com sucesso";
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}