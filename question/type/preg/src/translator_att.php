<?php

    $COMMENT_COUNT = 2;
    $TAB = '	';
    $SPACE = ' ';
    $EOL = chr(10);
    $TAB1 = '    ';
    $TAB2 = '        ';
    $TAB3 = '                        ';
    $TAB4 = '                      ';
    $FUNCTION_PREFIX = 'function data_for_test_att_basic_';

    $in = fopen('basic.dat.txt', 'r');
    $out = fopen('result.txt', 'w');

    for ($i = 0; $i < $COMMENT_COUNT; $i++) {
        fgets($in);
    }

    $counter = 0;
    while (!feof($in)) {
        $line = fgets($in);
        if (feof($in)) {
            break;
        }
        $line = substr($line, 0, strlen($line) - 1);    // \n issue.

        $modifiers = '';
        $regex = '';
        $string = '';
        $indexes = '';
        $index_first = array();
        $length = array();
        $left = '';
        $next = '';

        $i = 0;

        // get modifiers.
        while ($i < strlen($line) && $line[$i] !== $TAB) {
            $modifiers .= $line[$i];
            $i++;
        }

        // skip tabs.
        while ($i < strlen($line) && $line[$i] === $TAB) {
            $i++;
        }

        // get the regex.
        while ($i < strlen($line) && $line[$i] !== $TAB) {
            $regex .= $line[$i];
            $i++;
        }

        // skip tabs.
        while ($i < strlen($line) && $line[$i] === $TAB) {
            $i++;
        }

        // get the string.
        while ($i < strlen($line) && $line[$i] !== $TAB) {
            $string .= $line[$i];
            $i++;
        }
        if ($string === 'NULL') {
            $string = '';
        }

        // skip tabs.
        while ($i < strlen($line) && $line[$i] === $TAB) {
            $i++;
        }

        // get indexes.
        while ($i < strlen($line)) {
            $indexes .= $line[$i];
            $i++;
        }

        // do not include errors.
        if ($indexes[0] !== '(') {
            continue;
        }

        // get indexes and lengths.
        $index1 = '';
        $index2 = '';
        $number = -1;
        $first = false;
        $second = false;
        for ($j = 0; $j < strlen($indexes); $j++) {
            if ($indexes[$j] === '(') {
                $index1 = '';
                $index2 = '';
                $number++;
                $first = true;
                $second = false;
                continue;
            }
            if ($indexes[$j] === ',') {
                $first = false;
                $second = true;
                continue;
            }
            if ($indexes[$j] === ')') {
                $index_first[] = (int)$index1;
                $length[] = (int)$index2 - (int)$index1;
                continue;
            }
            if ($first) {
                $index1 .= $indexes[$j];
            } else {
                $index2 .= $indexes[$j];
            }
        }
        $index2write = '';
        $length2write = '';
        foreach ($index_first as $key => $value) {
            $index2write .= $key . '=>' . $value . ',';
        }
        $index2write = 'array(' . substr($index2write, 0, strlen($index2write) - 1) . ')';
        foreach ($length as $key => $value) {
            $length2write .= $key . '=>' . $value . ',';
        }
        $length2write = 'array(' . substr($length2write, 0, strlen($length2write) - 1) . ')';

        /*echo 'modifiers: ' . $modifiers . $EOL;
        echo 'regex: ' . $regex . $EOL;
        echo 'string: ' . $string . $EOL;
        echo 'indexes: '; print_r($index_first) . $EOL;
        echo 'lengths: '; print_r($length) . $EOL;
        echo $EOL;*/

        fwrite($out, $TAB1 . $FUNCTION_PREFIX . $counter++ . '() {' . $EOL);
        fwrite($out, $TAB2 . '$test1 = array( \'str\'=>"' . $string . '",' . $EOL);
        fwrite($out, $TAB3 . '\'is_match\'=>true,' . $EOL);
        fwrite($out, $TAB3 . '\'full\'=>true,' . $EOL);
        fwrite($out, $TAB3 . '\'index_first\'=>' . $index2write . ',' . $EOL);
        fwrite($out, $TAB3 . '\'length\'=>' . $length2write . ',' . $EOL);
        fwrite($out, $TAB3 . '\'left\'=>array(0),' . $EOL);
        fwrite($out, $TAB3 . '\'next\'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,' . $EOL);
        fwrite($out, $TAB3 . '\'tags\'=>array(qtype_preg_cross_tester::TAG_FROM_AT_AND_T));' . $EOL );
        fwrite($out, $EOL);
        fwrite($out, $TAB2 . 'return array( \'regex\'=>"' . $regex . '",' . $EOL);
        if (/*$modifiers !== ''*/strpos($modifiers, 'i') !== false) {
            fwrite($out, $TAB4 . '\'modifiers\'=>\'' . /*$modifiers*/'i' . '\',' . $EOL);
        }
        fwrite($out, $TAB4 . '\'tests\'=>array($test1));' . $EOL);
        fwrite($out, $TAB1 . '}' . $EOL . $EOL);
    }

    fclose($in);
    fclose($out);
    echo 'Done!' . $EOL;
?>
