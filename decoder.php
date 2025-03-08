<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$filePath = __DIR__ . '/input2.hand';
if (!file_exists($filePath)) {
    die("Error: The file input.hand does not exist at " . $filePath);
}
// Read the file and ensure it's in UTF-8
$code = file_get_contents($filePath);
$code = mb_convert_encoding($code, 'UTF-8', 'auto');

function interpretHPL($code) {
    $memory = array_fill(0, 30000, 0); // Initialize the memory
    $pointer = 0;
    $output = '';
    $loopStack = [];
    $codeLength = mb_strlen($code);
    // echo "Execution start:\n";
    for ($i = 0; $i < $codeLength; $i++) {
        $char = mb_substr($code, $i, 1); // Extract the emoji correctly
      // echo "Instruction: {$char} at position $i | Pointer: $pointer | Value: {$memory[$pointer]}\n";
        switch ($char) {
            case '👉': $pointer++; break;
            case '👈': $pointer = max(0, $pointer - 1); break;
            case '👆': $memory[$pointer] = ($memory[$pointer] + 1) % 256; break;
            case '👇': $memory[$pointer] = ($memory[$pointer] - 1 + 256) % 256; break;
            case '🤜':
                if ($memory[$pointer] == 0) {
                    $depth = 1;
                    while ($depth > 0) {
                        $i++;
                        if ($i >= $codeLength) {
                            throw new Exception("Error: Missing closing 🤜 at position $i");
                        }
                        $nextChar = mb_substr($code, $i, 1);
                        if ($nextChar == '🤜') $depth++;
                        if ($nextChar == '🤛') $depth--;
                    }
                } else {
                    array_push($loopStack, $i);
                }
                break;
            case '🤛':
                if (!empty($loopStack)) {
                    $jumpTo = array_pop($loopStack);
                    if ($memory[$pointer] != 0) {
                        $i = $jumpTo - 1;
                    }
                } else {
                    throw new Exception("Error: Missing opening 🤜 for 🤛 at position $i");
                }
                break;
            case '👊':
                //  echo "Output: " . chr($memory[$pointer]) . " (ASCII: {$memory[$pointer]})\n";
                $output .= chr($memory[$pointer]);
                break;
        }
    //    echo "Memory[0-10]: " . implode(" ", array_slice($memory, 0, 10)) . "\n";
    }
    return $output;
}
$result = interpretHPL($code);
echo "Final output of the input.hand file => " . $result;
?>
