<?php
session_start();

class Task {
    public int $id;
    public string $title;
    public string $description;
    public int $ownerId;
    public string $status; 
    public string $dueDate;

    public function __construct(int $id, string $title, string $description, int $ownerId, string $dueDate, string $status = 'pendente') {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->dueDate = $dueDate;
        $this->status = $status;
    }
}

class User {
    public int $id;
    public string $name;
    public string $email;
    public string $password;

    public function __construct(int $id, string $name, string $email, string $password) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}

class TaskService {
    private array $tasks = [];
    private array $users = [];
    private int $nextTaskId = 1;
    private int $nextUserId = 1;

    public function __construct() {
    //seed
        $this->users = [
            1 => new User(1, 'João Silva', 'joao@email.com', 'senha123'),
            2 => new User(2, 'Maria Santos', 'maria@email.com', 'abc456')
        ];
        $this->nextUserId = 3;

        $this->tasks = [
            1 => new Task(1, 'Estudar PHP', 'Revisar conceitos básicos', 1, '2024-01-15', 'pendente'),
            2 => new Task(2, 'Fazer compras', 'Ir ao supermercado', 1, '2024-01-14', 'concluida'),
            3 => new Task(3, 'Exercitar-se', 'Academia às 18h', 2, '2024-01-16', 'pendente'),
        ];
        $this->nextTaskId = 4;
    }

    public function addUser(string $name, string $email, string $password): string {
        if (strlen($name) < 2) return "Nome muito curto";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Email inválido";
        foreach ($this->users as $user) {
            if ($user->email === $email) return "Email já existe";
        }
        $user = new User($this->nextUserId++, $name, $email, $password);
        $this->users[$user->id] = $user;
        return "success";
    }

    public function login(string $email, string $password): ?User {
        foreach ($this->users as $user) {
            if ($user->email === $email && $user->password === $password) {
                $_SESSION['uid'] = $user->id;
                $_SESSION['un'] = $user->name;
                return $user;
            }
        }
        return null;
    }

    public function createTask(string $title, string $description, int $ownerId, string $dueDate): string {
        if (!isset($this->users[$ownerId])) return "Usuário não existe";
        if (strlen($title) < 3) return "Título muito curto";
        if (strlen($description) < 5) return "Descrição muito curta";
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) return "Data inválida";
        if (strtotime($dueDate) < strtotime(date('Y-m-d'))) return "Data não pode ser no passado";

        $task = new Task($this->nextTaskId++, $title, $description, $ownerId, $dueDate);
        $this->tasks[$task->id] = $task;
        return "success";
    }

    public function listTasks(?int $ownerId = null, ?string $status = null, ?string $search = null): array {
        $result = $this->tasks;
        if ($ownerId !== null) {
            $result = array_filter($result, fn($t) => $t->ownerId === $ownerId);
        }
        if ($status !== null) {
            $result = array_filter($result, fn($t) => $t->status === $status);
        }
        if ($search !== null) {
            $search = strtolower(trim($search));
            $result = array_filter($result, fn($t) =>
                str_contains(strtolower($t->title), $search) || str_contains(strtolower($t->description), $search)
            );
        }
        return array_values($result);
    }

    public function completeTask(int $id): string {
        if (!isset($this->tasks[$id])) return "Tarefa não encontrada";
        if ($this->tasks[$id]->status === 'concluida') return "Tarefa já concluída";
        $this->tasks[$id]->status = 'concluida';
        return "success";
    }

    public function deleteTask(int $id): string {
        if (!isset($this->tasks[$id])) return "Tarefa não encontrada";
        unset($this->tasks[$id]);
        return "success";
    }

    public function getReport(): array {
        $total = count($this->tasks);
        $pendentes = count(array_filter($this->tasks, fn($t) => $t->status === 'pendente'));
        $concluidas = count(array_filter($this->tasks, fn($t) => $t->status === 'concluida'));
        $usuarios = count($this->users);
        return [
            'total_tarefas' => $total,
            'pendentes' => $pendentes,
            'concluidas' => $concluidas,
            'usuarios' => $usuarios
        ];
    }
}

// USO 

$service = new TaskService();

echo "<h1>Sistema</h1>";

$resUser = $service->addUser('Pedro Oliveira', 'pedro@teste.com', 'senha789');
echo $resUser === 'success' ? "<p>Usuário criado!</p>" : "<p>Erro: $resUser</p>";

$user = $service->login('joao@email.com', 'senha123');
if ($user) {
    echo "<p>Login: {$user->name}</p>";
}

$resTask = $service->createTask('Aprender programação', 'Estudar conceitos de POO e boas práticas', 1, '2024-02-01');
echo $resTask === 'success' ? "<p>Tarefa criada!</p>" : "<p>Erro: $resTask</p>";

$tasks = $service->listTasks();
echo "<h2>Tarefas:</h2>";
foreach ($tasks as $task) {
    $status = $task->status === 'pendente' ? 'aguardando' : 'concluída';
    echo "<p>$status {$task->title} - {$task->description} (Data: {$task->dueDate})</p>";
}

$service->completeTask(1);
echo "<p>Tarefa 1 concluída!</p>";

$report = $service->getReport();
echo "<h2>Relatório:</h2>";
echo "<p>Total: {$report['total_tarefas']} | Pendentes: {$report['pendentes']} | Concluídas: {$report['concluidas']}</p>";
