<?php

$stmt = $pdo -> prepare("UPDATE products SET quantity = quantity + :quantity WHERE id = :products_id");
foreach($listStock as $stock){    
    $stmt->execute([':quantity' => $stock['quantity'], ':products_id' => $stock['products_id']]);
}

$stmt = $pdo -> prepare("UPDATE inventory_item SET quantity = quantity + :quantity WHERE product_id = :products_id AND inventory_id = :inventory_id");
foreach($listStock as $stock){
    $stmt->execute([':quantity' => $stock['quantity'], ':products_id' => $stock['products_id'], ':inventory_id' => $stock['inventory_id']]);
}
?>