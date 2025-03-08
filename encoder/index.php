<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function textToHPL($text) {
    $hplCode = '';
    $prevAscii = 0;

    foreach (str_split($text) as $char) {
        $ascii = ord($char);
        $diff = $ascii - $prevAscii;

        if ($diff > 0) {
            $hplCode .= str_repeat('👆', $diff);
        } elseif ($diff < 0) {
            $hplCode .= str_repeat('👇', abs($diff));
        }

        $hplCode .= '👊'; // Output character
        $prevAscii = $ascii;
    }

    return $hplCode;
}

$inputText = "Hello!"; // Change this to any text you want
$hplCode = textToHPL($inputText);
$filePath = __DIR__ . '/input.hand';

file_put_contents($filePath, $hplCode);

echo "File 'input.hand' created with HPL code for: '$inputText'\n";
?>
