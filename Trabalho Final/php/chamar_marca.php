<?php
require 'banco.php';

// Recebe os dados do input JSON
$input = json_decode(file_get_contents('php://input'), true);
$marcaInput = $input['marca'];

// URL da API para buscar produtos por marca
$apiUrl = "https://makeup-api.herokuapp.com/api/v1/products.json?brand=" . urlencode($marcaInput);

// Faz a requisição para a API
$response = file_get_contents($apiUrl);

// Verifica se a resposta foi bem-sucedida
if ($response === FALSE) {
    die("Erro ao acessar a API.");
}

// Decodifica os dados retornados pela API
$data = json_decode($response, true);

if (is_array($data) && count($data) > 0) {
    foreach ($data as $product) {
        // Obtém os campos do produto
        $marca = $product['brand'] ?? 'Desconhecido';
        $name = $product['name'] ?? 'Sem Nome';
        $preco = $product['price'] ?? '0.00';
        $categoria = $product['category'] ?? 'Sem Categoria';

        // Insere os dados no banco de dados
        $sql = "INSERT INTO produtos (nome, marca, preco, categoria) 
                VALUES (:nome, :marca, :preco, :categoria)";
        
        $stmt = $con->prepare($sql);
        $stmt->execute([
            ':marca' => $brand,
            ':nome' => $name,
            ':preco' => $preco,
            ':categoria' => $categoria
        ]);

        echo "Produto inserido: $nome<br>";
    }
} else {
    echo "Nenhum produto encontrado para a marca: $marcaInput<br>";
}
?>