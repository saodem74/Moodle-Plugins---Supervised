﻿<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_string.php');

/**
 * Unit tests for preg string class.
 *
 * @copyright  2012 Valeriy Streltsov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_string_test extends UnitTestCase {

    function test_string() {
        $str1 = new qtype_preg_string('аzб');
        $str2 = new qtype_preg_string('йц者');
        $str3 = new qtype_preg_string($str1 . $str2);

        $this->assertTrue(is_a($str3, 'qtype_preg_string'));
        $this->assertTrue($str3->string() === 'аzбйц者');
        $this->assertTrue($str3->length() === 6);
        $this->assertTrue($str3[-1] === null);
        $this->assertTrue($str3[0] === 'а');
        $this->assertTrue($str3[1] === 'z');
        $this->assertTrue($str3[2] === 'б');
        $this->assertTrue($str3[3] === 'й');
        $this->assertTrue($str3[4] === 'ц');
        $this->assertTrue($str3[5] === '者');
        $this->assertTrue($str3[6] === null);

        $str3[-1] = 'Q';
        $str3[0] = 'W';
        $str3[1] = 'E';
        $str3[4] = '者';
        $str3[6] = 'ه';
        $str3->concatenate('ab');
        $str3->concatenate(new qtype_preg_string('ёя'));

        $this->assertTrue($str3->length() === 11);
        $this->assertTrue($str3[-1] === null);
        $this->assertTrue($str3[0] === 'W');
        $this->assertTrue($str3[1] === 'E');
        $this->assertTrue($str3[2] === 'б');
        $this->assertTrue($str3[3] === 'й');
        $this->assertTrue($str3[4] === '者');
        $this->assertTrue($str3[5] === '者');
        $this->assertTrue($str3[6] === 'ه');
        $this->assertTrue($str3[7] === 'a');
        $this->assertTrue($str3[8] === 'b');
        $this->assertTrue($str3[9] === 'ё');
        $this->assertTrue($str3[10] === 'я');
        $this->assertTrue($str3[11] === null);
    }
}

class qtype_preg_unicode_properties_test extends UnitTestCase {

    function test_ranges() {
        $props = array('C', //'Cc', 'Cf', 'Cn', 'Co', 'Cs',
                       'L', //'Ll', 'Lm', 'Lo', 'Lt', 'Lu',
                       'M', //'Mc', 'Me', 'Mn',
                       'N', //'Nd', 'Nl', 'No',
                       'P', //'Pc', 'Pd', 'Pe', 'Pf', 'Pi', 'Po', 'Ps',
                       'S',// 'Sc', 'Sk', 'Sm', 'So',
                       'Z'//, 'Zl', 'Zp', 'Zs'
                       );
        foreach ($props as $prop) {
            $funcname = 'qtype_preg_unicode::' . $prop . '_ranges';
            $ranges = call_user_func($funcname);
            $counter = 0;
            foreach ($ranges as $range) {
                for ($i = $range['left']; $i <= $range['right']; $i++) {
                    $counter++;
                    $matched = preg_match('/(*UTF8)\p{' . $prop . '}/', qtype_preg_unicode::code2utf8($i));
                    if (!$matched) {
                        $this->assertTrue(false, 'U+' . dechex($i) . ' should not have been matched by ' . $prop);
                    }
                }

            }
        }

        /*for ($i = 0; $i <= 0x10FF; $i++) {
            foreach ($props as $prop) {
                $funcname = 'qtype_preg_unicode::is_' . $prop;
                $char = qtype_preg_unicode::code2utf8($i);
                $thisres = call_user_func($funcname, $char);
                $pcreres = (bool)preg_match('/(*UTF8)\p{' . $prop . '}/', $char);
                $boolstr = array(true => 'TRUE', false => 'FALSE');
                $fail = ((bool)$pcreres !== (bool)$thisres);
                $this->assertFalse(($pcreres xor $thisres), 'Fail on unicode property ' . $prop . ' check: character ' . $char . ', code 0x' . strtoupper(dechex($i)) . ', expected '.$boolstr[$pcreres] . ', obtained '.$boolstr[$thisres]);
            }
        }*/
    }
}

/**
 * Unit tests for unicode ranges intersection.
 *
 * @author Valeriy Streltsov
 */
class qtype_preg_unicode_ranges_intersection_test extends UnitTestCase {

    function test_intersect_positive_ranges() {
        $range11 = array('negative' => false, 'left' => 0, 'right' => 10);
        $range12 = array('negative' => false, 'left' => 3, 'right' => 13);
        $range13 = array('negative' => false, 'left' => 2, 'right' => 7);
        $ranges1 = array($range11, $range12, $range13);

        $range21 = array('negative' => false, 'left' => 20, 'right' => 30);
        $range22 = array('negative' => false, 'left' => 22, 'right' => 28);
        $range23 = array('negative' => false, 'left' => 21, 'right' => 32);
        $ranges2 = array($range21, $range22, $range23);

        $result = qtype_preg_unicode::intersect_ranges(array($ranges1, $ranges2));

        $this->assertTrue(count($result) === 2);
        $this->assertTrue($result[0]['left'] === 3);
        $this->assertTrue($result[0]['right'] === 7);
        $this->assertTrue($result[1]['left'] === 22);
        $this->assertTrue($result[1]['right'] === 28);
    }

    function test_intersect_negative_ranges() {
        $range11 = array('negative' => true, 'left' => 0, 'right' => 100);
        $range12 = array('negative' => true, 'left' => 300, 'right' => 0x10FFFD);
        $range13 = array('negative' => true, 'left' => 150, 'right' => 250);
        $ranges1 = array($range11, $range12, $range13);

        $range21 = array('negative' => true, 'left' => 100, 'right' => 400);
        $range22 = array('negative' => true, 'left' => 200, 'right' => 300);
        $range23 = array('negative' => true, 'left' => 200, 'right' => 300);
        $ranges2 = array($range21, $range22, $range23);

        $result = qtype_preg_unicode::intersect_ranges(array($ranges1, $ranges2));

        $this->assertTrue(count($result) === 4);
        $this->assertTrue($result[0]['left'] === 100);
        $this->assertTrue($result[0]['right'] === 150);
        $this->assertTrue($result[1]['left'] === 250);
        $this->assertTrue($result[1]['right'] === 300);
        $this->assertTrue($result[2]['left'] === 0);
        $this->assertTrue($result[2]['right'] === 100);
        $this->assertTrue($result[3]['left'] === 400);
        $this->assertTrue($result[3]['right'] === 0x10FFFD);
    }

    function test_intersect_mixed_ranges() {
        $range11 = array('negative' => true, 'left' => 200, 'right' => 300);
        $range12 = array('negative' => false, 'left' => 100, 'right' => 400);
        $ranges1 = array($range11, $range12);

        $range21 = array('negative' => true, 'left' => 200, 'right' => 300);
        $range22 = array('negative' => false, 'left' => 100, 'right' => 230);
        $range23 = array('negative' => false, 'left' => 240, 'right' => 400);
        $ranges2 = array($range21, $range22, $range23);

        $range31 = array('negative' => false, 'left' => 100, 'right' => 500);
        $range32 = array('negative' => false, 'left' => 200, 'right' => 300);
        $range33 = array('negative' => false, 'left' => 300, 'right' => 400);
        $ranges3 = array($range31, $range32, $range33);

        $range41 = array('negative' => true, 'left' => 200, 'right' => 400);
        $range42 = array('negative' => false, 'left' => 100, 'right' => 500);
        $range43 = array('negative' => false, 'left' => 200, 'right' => 450);
        $ranges4 = array($range41, $range42, $range43);

        $result = qtype_preg_unicode::intersect_ranges(array($ranges1, $ranges2, $ranges3, $ranges4));

        $this->assertTrue(count($result) === 5);
        $this->assertTrue($result[0]['left'] === 100);
        $this->assertTrue($result[0]['right'] === 200);
        $this->assertTrue($result[1]['left'] === 300);
        $this->assertTrue($result[1]['right'] === 400);
        $this->assertTrue($result[2]['left'] === 300);
        $this->assertTrue($result[2]['right'] === 300);
        $this->assertTrue($result[3]['left'] === 200);
        $this->assertTrue($result[3]['right'] === 200);
        $this->assertTrue($result[4]['left'] === 400);
        $this->assertTrue($result[4]['right'] === 450);
    }
}
