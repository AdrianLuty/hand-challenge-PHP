<?php
$filePath = __DIR__ . '/input.hand';
if (!file_exists($filePath)) {
    die("Error: The file input.hand does not exist at " . $filePath);
}
$code = file_get_contents($filePath);
$code = mb_convert_encoding($code, 'UTF-8', 'auto');
function interpretHPL($code) {
    $memory = array_fill(0, 30000, 0);
    $pointer = 0;
    $output = '';
    $loopStack = [];
    $codeLength = mb_strlen($code);
    for ($i = 0; $i < $codeLength; $i++) {
        $char = mb_substr($code, $i, 1);
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
                $output .= chr($memory[$pointer]);
                break;
        }
    }
    return $output;
}
$result = interpretHPL($code);
echo "Final output of the input.hand file => " . $result;
?>
