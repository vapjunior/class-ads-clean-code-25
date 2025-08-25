<?php

require_once 'ComplexReportGenerator.php';



$repositorio = new DadosRepositorio();
$gerador = new GeradorDeRelatorio($repositorio);

// Gerar o relatório
$relatorioCompleto = $gerador->gerarRelatorioComplexo();

// Exibir o relatório formatado
echo "<h1>Relatório de Vendas</h1>";
echo "<p>Relatório complexo gerado com " . count($relatorioCompleto['regions']) . " regiões</p>";
echo "<p>Total geral de vendas: R$ {$relatorioCompleto['summary']['totalSales']}</p>";
echo "<p>Lucro total: R$ {$relatorioCompleto['summary']['totalProfit']}</p>";
echo "<p>Margem de lucro total: " . number_format($relatorioCompleto['summary']['profitMargin'], 2) . "%</p>";




?>