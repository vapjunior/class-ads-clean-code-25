<?php

declare(strict_types=1);

namespace App\Dominio\Servicos;

use App\Dominio\Entidades\Tarefa;
use App\Dominio\Repositorios\TarefaRepositorio;
use App\Dominio\Repositorios\UsuarioRepositorio;
use InvalidArgumentException;

class TarefaServico
{
    private TarefaRepositorio $tarefaRepositorio;
    private UsuarioRepositorio $usuarioRepositorio;

    public function __construct(TarefaRepositorio $tarefaRepositorio, UsuarioRepositorio $usuarioRepositorio)
    {
        $this->tarefaRepositorio = $tarefaRepositorio;
        $this->usuarioRepositorio = $usuarioRepositorio;
    }
    
    public function adicionarTarefa(Tarefa $tarefa): void
    {
        $this->validarTarefa($tarefa);
        $this->tarefaRepositorio->adicionar($tarefa);
    }
    
    public function atualizarTarefa(int $id, Tarefa $dadosTarefa): void
    {
        $tarefaExistente = $this->tarefaRepositorio->buscarPorId($id);
        if (!$tarefaExistente) {
            throw new InvalidArgumentException("Tarefa não encontrada.");
        }
    
        $this->validarTarefa($dadosTarefa);
    
        $this->tarefaRepositorio->atualizar($id, $dadosTarefa);
    }
    
    public function concluirTarefa(int $id): void
    {
        $tarefa = $this->tarefaRepositorio->buscarPorId($id);
        if (!$tarefa) {
            throw new InvalidArgumentException("Tarefa não encontrada.");
        }
    
        if ($tarefa->getStatus() === 'concluida') {
            throw new InvalidArgumentException("Tarefa já concluída.");
        }
    
        $this->tarefaRepositorio->atualizarStatus($id, 'concluida');
    }
    
    public function deletarTarefa(int $id): void
    {
        if (!$this->tarefaRepositorio->buscarPorId($id)) {
            throw new InvalidArgumentException("Tarefa não encontrada.");
        }
    
        $this->tarefaRepositorio->deletar($id);
    }
    
    /**
     * @return array<TarefaDTO>
     */
    public function listarTarefas(?int $usuarioId = null, ?string $status = null, ?string $busca = null): array
    {
        return $this->tarefaRepositorio->buscarTodos($usuarioId, $status, $busca);
    }

    public function gerarRelatorio(): array
    {
        $totalTarefas = $this->tarefaRepositorio->contarTodas();
        $pendentes = $this->tarefaRepositorio->contarPorStatus('pendente');
        $concluidas = $this->tarefaRepositorio->contarPorStatus('concluida');
        $totalUsuarios = $this->usuarioRepositorio->contarTodos();

        return [
            'total_tarefas' => $totalTarefas,
            'pendentes' => $pendentes,
            'concluidas' => $concluidas,
            'usuarios' => $totalUsuarios,
        ];
    }

    private function validarTarefa(Tarefa $tarefa): void
    {
        $titulo = trim($tarefa->getTitulo());
        $descricao = trim($tarefa->getDescricao());
        $usuarioId = $tarefa->getUsuarioId();
        $data = trim($tarefa->getData());

        if (strlen($titulo) < 3) {
            throw new InvalidArgumentException("Título muito curto.");
        }
        if (strlen($titulo) > 100) {
            throw new InvalidArgumentException("Título muito longo.");
        }
        if (empty($titulo)) {
            throw new InvalidArgumentException("Título obrigatório.");
        }
        if (is_numeric($titulo)) {
            throw new InvalidArgumentException("Título inválido.");
        }

        if (strlen($descricao) < 5) {
            throw new InvalidArgumentException("Descrição muito curta.");
        }
        if (strlen($descricao) > 500) {
            throw new InvalidArgumentException("Descrição muito longa.");
        }
        if (empty($descricao)) {
            throw new InvalidArgumentException("Descrição obrigatória.");
        }

        if (!is_numeric($usuarioId)) {
            throw new InvalidArgumentException("Usuário inválido.");
        }
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException("ID de usuário inválido.");
        }
        
        if (!$this->usuarioRepositorio->buscarPorId($usuarioId)) {
            throw new InvalidArgumentException("Usuário não existe.");
        }

        if (empty($data)) {
            throw new InvalidArgumentException("Data obrigatória.");
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            throw new InvalidArgumentException("Data inválida.");
        }
        if (strtotime($data) < strtotime(date('Y-m-d'))) {
            throw new InvalidArgumentException("Data não pode ser no passado.");
        }
    }
}