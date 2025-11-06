<?php
session_start();
require_once __DIR__ . '/../src/functions.php';

$key = $_POST['key'] ?? '';
$text = $_POST['text'] ?? '';
$mode = $_POST['mode'] ?? 'encrypt';

// reset log
$processLog = [];

try {
    if (trim($key) === '') throw new Exception('Key tidak boleh kosong');
    if (trim($text) === '') throw new Exception('Text tidak boleh kosong');

    $matrix = build_playfair_matrix($key);
    $matrixDisplay = matrix_to_string($matrix);
    $processLog[] = "Membangun matrix dari key: " . strtoupper($key);
    $processLog[] = $matrixDisplay;

    if ($mode === 'encrypt') {
        $prepared = prepare_plaintext($text);
        $processLog[] = "Prepared digraphs: $prepared";
        $GLOBALS['processLog'] = &$processLog;
        $output = playfair_encrypt($prepared, $matrix);
    } else {
        $prepared = prepare_ciphertext($text);
        $processLog[] = "Prepared digraphs (decrypt): $prepared";
        $GLOBALS['processLog'] = &$processLog;
        $output = playfair_decrypt($prepared, $matrix);
    }

    $_SESSION['processLog'] = implode("\n", $processLog);
    $_SESSION['result'] = [
        'matrix_display' => $matrixDisplay,
        'output' => $output
    ];

    // ✅ Jika request datang dari Streamlit (JSON)
    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'key' => $key,
            'text' => $text,
            'mode' => $mode,
            'matrix' => $matrixDisplay,
            'result' => $output,
            'log' => $processLog
        ]);
        exit;
    }

} catch (Exception $e) {
    $_SESSION['processLog'] = implode("\n", $processLog);
    $_SESSION['result'] = [
        'matrix_display' => '',
        'output' => 'ERROR: ' . $e->getMessage()
    ];

    // ✅ Jika Streamlit yang memanggil dan terjadi error
    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'log' => $processLog
        ]);
        exit;
    }
}

// ✅ Jika dari browser (normal PHP web)
header('Location: index.php');
exit;
?>
