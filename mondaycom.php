<?php
$token = 'eyJhbGciOiJIUzI1NiJ9.eyJ0aWQiOjI1NTM3Mjg2OSwiYWFpIjoxMSwidWlkIjozOTg1NjY0NSwiaWFkIjoiMjAyMy0wNS0wOVQxMzo1NzowNy4yNDhaIiwicGVyIjoibWU6d3JpdGUiLCJhY3RpZCI6MTQ4OTE1MDcsInJnbiI6InVzZTEifQ.UsOAVUrb-wbWtQV8hpGJXj1KrbSaL0gm7wyjrafVryg';
$apiUrl = 'https://api.monday.com/v2';
$headers = ['Content-Type: application/json', 'Authorization: ' . $token];

$query = 'mutation ($myItemName: String!, $columnVals: JSON!) { create_item (board_id:4440677252, item_name:$myItemName, column_values:$columnVals) { id } }';


$vars = ['myItemName' => '76677777',
'columnVals' => json_encode([
    'text' => "Cabin-54, Sector-74", // Location
    'text_1' => "Cabin-54, Sector-74", //Venue
    'text1' => "32.6", //Price
    'text_11' => "POUND", //Currency
    // 'text8' => json_encode(67777),
    'status1' => json_encode(1),
    'status2' => json_encode(1),
    'date' =>  ['date' => '2023-08-27',]
   
])];


$data = @file_get_contents($apiUrl, false, stream_context_create([
 'http' => [
 'method' => 'POST',
 'header' => $headers,
 'content' => json_encode(['query' => $query, 'variables' => $vars]),
 ]
]));
$responseContent = json_decode($data, true);

echo json_encode($responseContent);
?>