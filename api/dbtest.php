<?php
require_once __DIR__ . '/api.php';

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Test database connection
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    if (!$result) {
        throw new Exception($conn->error);
    }

    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        $structure = [];
        
        // Get table structure
        $columns = $conn->query("DESCRIBE " . $tableName);
        while ($col = $columns->fetch_assoc()) {
            $structure[] = $col;
        }
        
        // Get row count
        $count = $conn->query("SELECT COUNT(*) as count FROM " . $tableName)->fetch_assoc()['count'];
        
        $tables[$tableName] = [
            'structure' => $structure,
            'row_count' => $count
        ];
    }

    sendResponse([
        'connected' => true,
        'server_info' => $conn->server_info,
        'database_name' => $conn->database,
        'tables' => $tables
    ]);
} catch (Exception $e) {
    sendError('Database test failed: ' . $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}