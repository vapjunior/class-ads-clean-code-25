<?php

declare(strict_types=1);

use App\Dominio\Entidades\Usuario;
use App\Dominio\Entidades\Tarefa;
use App\Infra\Repositorios\InMemoryTarefaRepositorio;
use App\Infra\Repositorios\InMemoryUsuarioRepositorio;
use App\Dominio\Servicos\TarefaServico;
use App\Dominio\Servicos\UsuarioServico;

spl_autoload_register(function (string $nomeDaClasse) {
    $pastaBase = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $caminhoDoArquivo = str_replace(['App\\', '\\'], ['', DIRECTORY_SEPARATOR], $nomeDaClasse);
    $arquivo = $pastaBase . $caminhoDoArquivo . '.php';

    if (file_exists($arquivo)) {
        require_once $arquivo;
    }
});

echo "<h1>Sistema</h1>";

$tarefaRepositorio = new InMemoryTarefaRepositorio();
$usuarioRepositorio = new InMemoryUsuarioRepositorio();

$tarefaService = new TarefaServico($tarefaRepositorio, $usuarioRepositorio);
$usuarioService = new UsuarioServico($usuarioRepositorio);

// --- 1. Adicionar novo usuário: Equivalente a $sistema->au() ---
echo "<h2>Adicionar novo usuário:</h2>";
try {
    $novoUsuario = new Usuario(0, 'Pedro Oliveira', 'pedro@teste.com', 'senha789');
    $usuarioService->adicionarUsuario($novoUsuario);
    echo "<p>Usuário criado!</p>";
} catch (InvalidArgumentException $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}

// --- 2. Fazer login (simulado): Equivalente a $sistema->lg() ---
echo "<h2>Login:</h2>";
$emailLogin = 'joao@email.com';
$senhaLogin = 'senha123';
try {
    $userLogado = $usuarioService->login($emailLogin, $senhaLogin);
    if ($userLogado) {
        echo "<p>Login: " . $userLogado->getNome() . "</p>";
    } else {
        echo "<p style='color: red;'>Erro: Credenciais inválidas.</p>";
    }
} catch (InvalidArgumentException $e) {
    echo "<p style='color: red;'>Erro no login: " . $e->getMessage() . "</p>";
}


// --- 3. Adicionar uma nova tarefa: Equivalente a $sistema->a() ---
echo "<h2>Adicionar Tarefa:</h2>";
try {
    $novaTarefa = new Tarefa(
        0,
        'Aprender programação',
        'Estudar conceitos de POO e boas práticas',
        1,
        'pendente',
        '2024-02-01'
    );
    $tarefaService->adicionarTarefa($novaTarefa);
    echo "<p>Tarefa criada!</p>";
} catch (InvalidArgumentException $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}

// --- 4. Listar todas as tarefas: Equivalente a $sistema->l() ---
echo "<h2>Tarefas:</h2>";
try {
    $tarefas = $tarefaService->listarTarefas();
    foreach ($tarefas as $tarefa) {
        $status = $tarefa->getStatus() === 'pendente' ? 'aguardando' : 'concluída';
        echo "<p>$status {$tarefa->getTitulo()} - {$tarefa->getDescricao()} (Data: {$tarefa->getData()})</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro ao listar tarefas: " . $e->getMessage() . "</p>";
}


// --- 5. Concluir a Tarefa 1: Equivalente a $sistema->cs(1) ---
echo "<h2>Concluir Tarefa 1:</h2>";
try {
    $tarefaService->concluirTarefa(1);
    echo "<p>Tarefa 1 concluída!</p>";
} catch (InvalidArgumentException $e) {
    echo "<p style='color: red;'>Erro ao concluir tarefa 1: " . $e->getMessage() . "</p>";
}

echo "<h2>Relatório:</h2>";
try {
    $rel = $tarefaService->gerarRelatorio();
    echo "<p>Total: {$rel['total_tarefas']} | Pendentes: {$rel['pendentes']} | Concluídas: {$rel['concluidas']}</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro ao gerar relatório: " . $e->getMessage() . "</p>";
}
