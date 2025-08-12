<?php

require_once 'UserManager.php';

echo "<h2>Gestão de Usuários</h2>";

$userManager = new UserManager();

$result1 = $userManager->createUser("João Silva", "joao@test.com", "Senha123");
echo "<p>Criar usuário: {$result1}</p>";

$result2 = $userManager->updateUser(0, "João Santos", "joao.santos@test.com", "NovaSenha456");
echo "<p>Atualizar usuário: {$result2}</p>";

$result3 = $userManager->resetPassword(0, "SuperSenha789");
echo "<p>Resetar senha: {$result3}</p>";

echo "<h3>Logs:</h3>";
$logs = $userManager->getLogs();
echo "<ul>";
foreach ($logs as $log) {
    echo "<li>{$log}</li>";
}