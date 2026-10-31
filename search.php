<?php
require_once 'config.php';

$query = strtolower(trim($_GET['q'] ?? ''));
if (!$query) {
  echo json_encode([]);
  exit;
}

$results = [];
$seenAircraftModels = [];
$seenEngineModels = [];
$seenPartNames = [];

// Step 1: Match against aircraft_models
$stmt = $pdo->prepare("SELECT DISTINCT model, type_designator, model_types, manufacturer 
                       FROM aircraft_models 
                       WHERE LOWER(model) = ? 
                          OR LOWER(type_designator) = ? 
                          OR LOWER(model_types) = ? 
                          OR LOWER(manufacturer) = ?");
$stmt->execute([$query, $query, $query, $query]);

while ($row = $stmt->fetch()) {
  $match_column = '';
  $value = '';

  if (strtolower($row['model']) === $query) {
    $match_column = 'model';
    $value = $row['model'];
  } elseif (strtolower($row['type_designator']) === $query) {
    $match_column = 'type_designator';
    $value = $row['type_designator'];
  } elseif (strtolower($row['model_types']) === $query) {
    $match_column = 'model_types';
    $value = $row['model_types'];
  } elseif (strtolower($row['manufacturer']) === $query) {
    $match_column = 'manufacturer';
    $value = $row['manufacturer'];
  }

  $value = strtolower($value);
  if (!in_array($value, $seenAircraftModels)) {
    $seenAircraftModels[] = $value;
    $results[] = [
      'id' => null,
      'name' => $value,
      'type' => 'aircraft',
      'match_column' => $match_column
    ];
  }

  if (count($seenAircraftModels) >= 5) break;
}

// Step 2: Basic aircraft fallback
if (count($seenAircraftModels) < 5) {
  $stmt = $pdo->prepare("SELECT aircraft_id AS id, model, manufacturer 
                         FROM aircrafts 
                         WHERE LOWER(model) LIKE ? LIMIT 50");
  $stmt->execute(["%$query%"]);
  while ($row = $stmt->fetch()) {
    $model = strtolower($row['model']);
    if (!in_array($model, $seenAircraftModels)) {
      $seenAircraftModels[] = $model;
      $results[] = [
        'id' => $row['id'],
        'name' => $row['model'],
        'manufacturer' => $row['manufacturer'],
        'type' => 'aircraft',
        'match_column' => 'model'
      ];
    }
    if (count($seenAircraftModels) >= 5) break;
  }
}

// Step 3: Engine search
$stmt = $pdo->prepare("SELECT engine_id AS id, model FROM engines WHERE LOWER(model) LIKE ? LIMIT 50");
$stmt->execute(["%$query%"]);
while ($row = $stmt->fetch()) {
  $model = strtolower($row['model']);
  if (!in_array($model, $seenEngineModels)) {
    $seenEngineModels[] = $model;
    $results[] = [
      'id' => $row['id'],
      'name' => $row['model'],
      'type' => 'engine'
    ];
  }
  if (count($seenEngineModels) >= 5) break;
}

// Step 4: Part search
$stmt = $pdo->prepare("SELECT part_id AS id, part_name, part_number 
                       FROM parts 
                       WHERE LOWER(part_name) LIKE ? OR LOWER(part_number) LIKE ? LIMIT 50");
$stmt->execute(["%$query%", "%$query%"]);
while ($row = $stmt->fetch()) {
  $partName = strtolower($row['part_name']);
  if (!in_array($partName, $seenPartNames)) {
    $seenPartNames[] = $partName;
    $results[] = [
      'id' => $row['id'],
      'name' => $row['part_name'] . " ({$row['part_number']})",
      'type' => 'part'
    ];
  }
  if (count($seenPartNames) >= 5) break;
}

header('Content-Type: application/json');
echo json_encode($results);
?>
