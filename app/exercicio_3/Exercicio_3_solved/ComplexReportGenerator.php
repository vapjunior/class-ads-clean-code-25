<?php

class Venda
{
    public int $customerId;
    public int $productId;
    public int $categoryId;
    public int $regionId;
    public float $amount;
    public string $date;
    public float $cost;

    public function __construct(array $data, float $cost)
    {
        $this->customerId = $data['customerId'];
        $this->productId = $data['productId'];
        $this->categoryId = $data['categoryId'];
        $this->regionId = $data['regionId'];
        $this->amount = $data['amount'];
        $this->date = $data['date'];
        $this->cost = $cost;
    }

    public function getProfit(): float
    {
        return $this->amount - $this->cost;
    }
}

class Produto
{
    public int $id;
    public string $name;
    public float $cost;

    public function __construct(int $id, string $name, float $cost)
    {
        $this->id = $id;
        $this->name = $name;
        $this->cost = $cost;
    }
}

class Cliente
{
    public int $id;
    public string $name;
    public string $tier;

    public function __construct(int $id, string $name, string $tier)
    {
        $this->id = $id;
        $this->name = $name;
        $this->tier = $tier;
    }
}

class DadosRepositorio
{
    private array $vendas;
    private array $clientes;
    private array $produtos;

    public function __construct()
    {
        $this->clientes = [
            1 => new Cliente(1, 'João Silva', 'premium'),
            2 => new Cliente(2, 'Maria Santos', 'standard'),
            3 => new Cliente(3, 'Pedro Costa', 'premium')
        ];
        $this->produtos = [
            1 => new Produto(1, 'Produto A', 50),
            2 => new Produto(2, 'Produto B', 80),
            3 => new Produto(3, 'Produto C', 120)
        ];
        $salesData = [
            ['customerId' => 1, 'productId' => 1, 'categoryId' => 1, 'regionId' => 1, 'amount' => 100, 'date' => '2024-01-01'],
            ['customerId' => 1, 'productId' => 2, 'categoryId' => 2, 'regionId' => 1, 'amount' => 200, 'date' => '2024-01-02'],
            ['customerId' => 2, 'productId' => 1, 'categoryId' => 1, 'regionId' => 2, 'amount' => 150, 'date' => '2024-01-03'],
            ['customerId' => 2, 'productId' => 3, 'categoryId' => 3, 'regionId' => 2, 'amount' => 300, 'date' => '2024-01-04'],
            ['customerId' => 3, 'productId' => 2, 'categoryId' => 2, 'regionId' => 1, 'amount' => 250, 'date' => '2024-01-05'],
            ['customerId' => 3, 'productId' => 3, 'categoryId' => 3, 'regionId' => 3, 'amount' => 400, 'date' => '2024-01-06'],
        ];
        
        $this->vendas = array_map(function($sale) {
            $productCost = $this->produtos[$sale['productId']]->cost;
            return new Venda($sale, $productCost);
        }, $salesData);
    }
    
    public function getVendas(): array { return $this->vendas; }
    public function getClientes(): array { return $this->clientes; }
    public function getProdutos(): array { return $this->produtos; }
}

class ProcessadorDeRelatorio
{
    private array $vendas;

    public function __construct(array $vendas)
    {
        $this->vendas = $vendas;
    }

    public function agruparPor(string $chave, callable $callback): array
    {
        $agrupado = [];
        foreach ($this->vendas as $item) {
            $chaveGrupo = $callback($item);
            if (!isset($agrupado[$chaveGrupo])) {
                $agrupado[$chaveGrupo] = [];
            }
            $agrupado[$chaveGrupo][] = $item;
        }
        return $agrupado;
    }

    public function calcularMetricas(array $itens): array
    {
        if (empty($itens)) {
            return ['totalSales' => 0, 'totalProfit' => 0, 'orderCount' => 0];
        }
        $totalSales = array_sum(array_map(fn(Venda $v) => $v->amount, $itens));
        $totalProfit = array_sum(array_map(fn(Venda $v) => $v->getProfit(), $itens));
        $orderCount = count($itens);
        return [
            'totalSales' => $totalSales,
            'totalProfit' => $totalProfit,
            'orderCount' => $orderCount,
            'averageOrderValue' => $totalSales / $orderCount,
            'profitMargin' => ($totalProfit / $totalSales) * 100
        ];
    }
}

class GeradorDeRelatorio
{
    private DadosRepositorio $repositorio;

    public function __construct(DadosRepositorio $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    public function gerarRelatorioComplexo(): array
    {
        $vendas = $this->repositorio->getVendas();
        $processador = new ProcessadorDeRelatorio($vendas);
        
        $report = [];
        $vendasPorRegiao = $processador->agruparPor('regionId', fn(Venda $v) => $v->regionId);
        
        foreach ($vendasPorRegiao as $regionId => $vendasDaRegiao) {
            $processadorRegiao = new ProcessadorDeRelatorio($vendasDaRegiao);
            $metricasRegiao = $processadorRegiao->calcularMetricas($vendasDaRegiao);
            
            $vendasPorCategoria = $processadorRegiao->agruparPor('categoryId', fn(Venda $v) => $v->categoryId);
            $categoriasReport = [];
            foreach ($vendasPorCategoria as $categoryId => $vendasDaCategoria) {
                $processadorCategoria = new ProcessadorDeRelatorio($vendasDaCategoria);
                $metricasCategoria = $processadorCategoria->calcularMetricas($vendasDaCategoria);
                $categoriasReport[$categoryId] = $metricasCategoria;
            }
            
            $report['regions'][$regionId] = array_merge($metricasRegiao, ['categories' => $categoriasReport]);
        }
        
        $report['summary'] = $processador->calcularMetricas($vendas);
        
        return $report;
    }
}


$repositorio = new DadosRepositorio();
$gerador = new GeradorDeRelatorio($repositorio);
$relatorio = $gerador->gerarRelatorioComplexo();

echo '<pre>';
print_r($relatorio);
echo '</pre>';