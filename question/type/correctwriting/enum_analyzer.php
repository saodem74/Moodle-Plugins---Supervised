<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

//Other necessary requires
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/string_pair.php');

class  qtype_correctwriting_enum_analyzer {

    /**
     * Array which contains orders of all enumerations, and give maximum length of LCS of correctstring and correctedstring
     * @var array
     */
    private $orders;

    private $fitness;               // Fitness for response.

    /**
     * Function to find order of changing enumeration, and included enumerations to all enumerations
     * @param array $enumdescription enumerations description
     */
    public function get_enum_change_order($enumdescription) {
        $enum1; // Description of enumeration-one.
        $enum2; // Description of enumeration-two.
        $enum1_number; // Number of enumeration-one.
        $enum2_number; // Number of enumeration-two.
        $change_order_included_enums = new stdClass();
        // Add fields to stdClass object.
        $change_order_included_enums->order = array(); // Enumerations change order.
        $change_order_included_enums->included_enums = array();// Included enumeration numbers to all enumerations.
        // Add empty arrays of included enumerations.
        for ($i = 0; $i < count($enumdescription); $i++) {
            $change_order_included_enums->included_enums[$i] = array();
        }
        $all_included_enums_in_order = true;// Variable show that all, included in current enumeration, enumerations are fill...
                                            // ...in enumerations change order.
        // Find included enumerations to all enumerations.
        $enum1_number = 0;
        $enum2_number = 0;
        foreach ($enumdescription as $enum1) {
            $enum2_number = 0;
            foreach ($enumdescription as $enum2) {
                // If is not same enumerations.
                if ( $enum1 != $enum2) {
                    // Boolean variables to check including of enumerations.
                    reset($enum1);// Set iterator to first element in first enumeration.
                    reset($enum2);// Set iterator to first element in second enumeration.
                    $compare_left_borders_of_enums = current($enum2)->begin - current($enum1)->begin;
                    end($enum2);// Set iterator to last element in first enumeration.
                    end($enum1);// Set iterator to last element in second enumeration.
                    $compare_right_borders_of_enums = current($enum2)->end - current($enum1)->end;
                    // If left borders of j enum rather than i enum and right borders of i enum rather than j enum and...
                    // ...in included enums array for i enum not contains j.
                    if ($compare_left_borders_of_enums >= 0 && $compare_right_borders_of_enums <= 0
                        && !in_array($enum2_number, $change_order_included_enums->included_enums[$enum1_number])) {
                        // Add j to included enums array for i enum.
                        $change_order_included_enums->included_enums[$enum1_number][] = $enum2_number;
                    }
                }
                $enum2_number++;
            }
            $enum1_number++;
            unset($enum2);
        }
        // Create enumerations change order.
        while ( count($change_order_included_enums->order) != count($enumdescription)) {
            for ($i = 0; $i < count($change_order_included_enums->included_enums); $i++) {
                $all_included_enums_in_order = true;
                // Check that all included enumerations are in order.
                for ($j = 0; $j < count($change_order_included_enums->included_enums[$i]); $j++) {
                    if (!in_array($change_order_included_enums->included_enums[$i][$j], $change_order_included_enums->order)) {
                        $all_included_enums_in_order = false;
                    }
                }
                // If all included enumerations are in order and current enumeration aren't in order...
                if ($all_included_enums_in_order && !in_array($i, $change_order_included_enums->order)) {
                    // ...add current enumeration to order.
                    $change_order_included_enums->order[] = $i;
                }
            }
        }
        // Ending of included arrays to enumeration which don't contains others enumerations.
        for ($i = 0; $i< count($change_order_included_enums->included_enums); $i++) {
            // If array of included enumeration is empty...
            if (count($change_order_included_enums->included_enums[$i]) == 0) {
                // ...add -1 to array.
                $change_order_included_enums->included_enums[$i][] = -1;
            }
        }
        return $change_order_included_enums;
    }

    /**
     * Function to find orders specified enumeration in correctstring, based on analyze correctedstring
     * @param array $correctanswer - correct answer
     * @param array $correctedanswer - corrected student answer
     * @param array $enumdescription - enumerations description
     * @param integer $number - number of enumeration to search orders
     * @return array of find orders
     */
    public function find_enum_orders_in_corrected_string($correctanswer, $correctedanswer, $enumdescription, $number) {
        $indexes_of_tokens = array();// Array with contains indexes of tokens which are members of enumeration...
                                                     // ... in corrected student answer.
        $indexes_of_elements = array();// Array with indexes of elements of enumeration in corrected answer.
        $element_indexes = array(); // Array with indexes of one element of enumeration in corrected answer.
        $previewelement = null; // Contain preview element value.
        $key = 0; // Current key of some array.
        $elements_in_corrected_answer = array();// Array to keep indexes elements of enumeration in order, which...
                                                // ... it has in corrected student answer, with included missed elements.
        $current_order = array();// Array to keep current order of enumeration elements.
        $token_number = 0;// Number of tokens in enumeration, whose indexes are searched in corrected answer on current iteration.
        $insert_place = 0;// Place to insert in one of arrays.
        $have_next_order = false;// Is has in $elements_in_corrected_answer next order.
        $number_of_element_to_skip = 0;// Element number, which will be skip in elements_in_corrected_answer on current iteration.
        $enum_orders=array();// Array which contain find orders of enumeration.
        // For all elements of enumeration create array of indexes in corrected answers, which are kept in ascending order.
        for ($i = 0; $i < count($enumdescription[$number]); $i++) {
            // Find tokens which include in current element of enumeration individually.
            $indexes_of_tokens = array();
            $token_number = 0;
            // For all tokens of current element of enumeration find indexes of equal tokens in corrected student answer.
            for ($j = $enumdescription[$number][$i]->begin; $j < $enumdescription[$number][$i]->end +1; $j++) {
                $indexes_of_tokens[$token_number] = array_keys($correctedanswer, $correctanswer[$j]);
                $token_number++;
            }
            // Create array of indexes tokens of current element enumeration in corrected student answer.
            $indexes_of_elements[] = array();
            for ($j = 0; $j < count($indexes_of_tokens); $j++) {
                // If token number j are find in corrected answer, add it's indexes in $indexes_of_elements_in_corrected_answer[i].
                if ($indexes_of_tokens[$j] != null) {
                    // For all indexes find place to insert, because array is kept in  ascending order.
                    for ($k = 0; $k < count($indexes_of_tokens[$j]); $k++) {
                        // Find place to insert.
                        $insert_place = 0;
                        while ($insert_place < count($indexes_of_elements[$i]) &&
                                   $indexes_of_elements[$i][$insert_place] < $indexes_of_tokens[$j][$k]) {
                            $insert_place++;
                        }
                        // Insert current index in array.
                        array_splice($indexes_of_elements[$i], $insert_place, 0, $indexes_of_tokens[$j][$k]);
                    }
                }
            }
        }
        // Fill array of indexes elements of enumeration in ascending order.
        foreach ($indexes_of_elements as $element_indexes) {
            // Find place to insert in array for all indexes.
            for ($j = 0; $j < count($element_indexes); $j++) {
                // Find place to insert.
                $insert_place = 0;
                while ($insert_place < count($elements_in_corrected_answer) &&
                           $elements_in_corrected_answer[$insert_place] < $element_indexes[$j]) {
                    $insert_place++;
                }
                // Insert current index in array.
                array_splice($elements_in_corrected_answer, $insert_place, 0, $element_indexes[$j]);
            }
        }
        // Change indexes by numbers of elements enumeration, execute repeat contiguous elements.
        $previewelement = null;
        foreach ($elements_in_corrected_answer as $key => $i) {
            // Find element by index.
            $j = 0;
            unset($element_indexes);
            foreach ($indexes_of_elements as $element_indexes) {
                if ( in_array($i, $element_indexes)) {
                    // Change index by element number.
                    $elements_in_corrected_answer[$key] = $j;
                    // If element are repeated.
                    if ($previewelement != null && $previewelement == $i) {
                        unset($elements_in_corrected_answer[$key]);
                    } else {
                        $previewelement = $i;
                    }
                }
                $j++;
            }
        }
        // Add to array number of element, which do not contains in corrected student answer.
        for ($i = 0; $i < count($enumdescription[$number]); $i++) {
            // Check that contains current element in order or not.
            if (!in_array($i, $elements_in_corrected_answer)) {
                // If element does not contains in order, add it between all elements pairs and to begin and end of order.
                for ($j = 0; $j < count($elements_in_corrected_answer); $j += 2) {
                    array_splice($elements_in_corrected_answer, $j, 0, $i);
                }
                // Add to end of order.
                if (count($elements_in_corrected_answer)==0 or end($elements_in_corrected_answer) != $i) {
                    $elements_in_corrected_answer[] = $i;
                }
            }
        }
        // Create orders array based on array of elements numbers which are ordered like in corrected student answer.
        do {
            $number_of_element_to_skip = 0;
            do {
                $current_order = array();// Clear current enumeration order.
                // Fill current order by $elements_in_corrected_answer.
                $i = 0;
                unset($j);
                foreach ($elements_in_corrected_answer as $j) {
                    // If that element number does not contains in current order and...
                    // ...his index does not equal number to skip or number to skip is zero.
                    if (!in_array($j, $current_order) &&
                            ($i != $number_of_element_to_skip || $number_of_element_to_skip == 0)) {
                        // Add element to order.
                        $current_order[] = $j;
                    }
                    $i++;
                }
                $number_of_element_to_skip++;// Inc number to skip.
                $enum_orders[] =$current_order;// Add order to array of enum orders.
            } while ($number_of_element_to_skip != count($elements_in_corrected_answer));
            // Remove duplicate orders.
            foreach ($enum_orders as $current_order) {
                $duplicates = array_keys($enum_orders, $current_order);
                array_shift($duplicates);
                foreach ($duplicates as $remove_index) {
                    unset($enum_orders[$remove_index]);
                }

            }
            // Remove first element from array of elements numbers which are ordered like in corrected student answer.
            array_shift($elements_in_corrected_answer);
            // Check that have next order in array of elements numbers which are ordered like in corrected student answer.
            $have_next_order = true;
            for ($i = 0; $i < count($enumdescription[$number]); $i++) {
                if (!in_array($i, $elements_in_corrected_answer)) {
                    $have_next_order = false;
                }
            }
        } while ($have_next_order);
        // Remove from array of orders not complete orders.
        foreach ($enum_orders as $key => $current_order) {
            if (count($current_order) != count($enumdescription[$number])) {
                unset($enum_orders[$key]);
            }
        }
        return $enum_orders;
    }

    /**
     * Function to find orders of all enumerations in corrected answer.
     * @param array $correctanswer - correct answer
     * @param array $correctedanswer - corrected student answer
     * @param array $enumdescription - enumerations description
     * @return array of find orders
     */
    public function find_all_enum_orders_in_corrected_string($correctanswer, $correctedanswer, $enumdescription) {
        $enum_orders = array(); // Array to keep orders of one enumeration.
        $all_enum_orders = array(); // Array to keep orders of all enumerations.
        $complete_enum_orders = array(); // Array to keep complete orders of enumerations
        $current_order = array(); // Array to keep current order of enumeration elements.
        $count_of_all_enum_orders = 0; // Count of all enumeration orders.
        $rows_for_one_order = 0; // Count of rows which will be keep same order for all enumerations.
        // Find orders for all enumerations alternatively.
        for ($i = 0; $i < count($enumdescription); $i++) {
            $all_enum_orders[]=$this->find_enum_orders_in_corrected_string($correctanswer, $correctedanswer, $enumdescription, $i);
        }
        // Find count of complete orders of enumerations.
        $count_of_all_enum_orders = 1;
        for ($i = 0; $i < count($enumdescription); $i++) {
            $count_of_all_enum_orders *= count($all_enum_orders[$i]);
        }
        // Paste together all enum orders.
        $rows_for_one_order = $count_of_all_enum_orders;
        for ($i = 0; $i < count($enumdescription); $i++) {
            // Add to all complete orders, orders of enumeration alternatively.
            $rows_for_one_order /= count($all_enum_orders[$i]);
            for ($j = 0; $j < $count_of_all_enum_orders; $j) {
                foreach ($all_enum_orders[$i] as $enum_orders) {
                    for ($k = 0; $k < $rows_for_one_order; $k++) {
                        if (!array_key_exists($j, $complete_enum_orders)) {
                            $complete_enum_orders[$j] = array();
                        }
                        $complete_enum_orders[$j] = array_merge($complete_enum_orders[$j], $enum_orders);
                        $j++;
                    }
                }
            }
            // Add space if it needed.
            if ($i!= count($enumdescription)-1) {
                for ($j=0; $j < $count_of_all_enum_orders; $j++) {
                    $complete_enum_orders[$j][] = -1;
                }
            }
        }
        return $complete_enum_orders;
    }
    /**
     * Function to change enumeration order in correct answer and enumeration description,
     * @param array $correctanswer - correct answer
     * @param array $enumdescription - enumerations description
     * @param array $enum_change_order - enumeration change order
     * @param array $include_enums - array of included enumerations, for all enumerations
     * @param array $new_enum_order - new orders for all enumeration
     * @return object $changed_data - changed correct answer and description
     */
    private function change_enum_order($correctanswer, $enumdescription, $enum_change_order, $include_enums, $new_enum_order) {
        $enums_orders = array(); // Array to keep enums orders separately.
        $current_order = array(); // Array to keep current order of enum.
        $for_change_enum_order = array(); // Array keep temp information for change enumeration order.
        $elem_number = 0; // Element number, which contains current included enumeration.
        $enum_number = 0; // Enumeration number, whose order are changing in current iteration.
        $elem_find = false; // Is searched element find?
        $place_find = false; // Is place to insert element find?
        $insert_place = 0; // Place to insert element in enumeration.
        $elements_distances = array(); // Array to keep distances between elements of enumerations
        $distances = array (); // Array to keep for all enumerations numbers of elements in other enumeration, which contains...
                               // ...it, and distance from element begin to enumeration begin.
        $changed_data = new stdClass(); // Object to keep result of function work: ...
        $changed_data->correctanswer = array(); // ...changed correct answer ...
        $changed_data->enumdescription = array(); // ... and updated enumerations descriptions.
        $left_border_of_elem = 0; // Left border of current element.
        $left_border_of_elem_new = 0; // New left border of current element.
        $left_border_of_enum = 0; // Left border of current enumeration.
        $right_border_of_elem = 0; // Right border of current element.
        $right_border_of_prev_elem = 0; // Right border of previews element.
        $right_border_of_elem_new = 0; // New right border of current element.
        $right_border_of_enum = 0; // Right border of current enumeration.
        $first_index = 0; // Array first index, need to arrays, which indexes are difficult to calculate.
        $second_index = 0; // Array second index, need to arrays, which indexes are difficult to calculate.
        // Fill array to keep enums orders separately.
        for ($i = 0; $i < count($new_enum_order); $i++) {
            // For all enumerations order end by -1 or end of array.
            if ($new_enum_order[$i] == -1) {
                $enum_number++;
            } else {
                $enums_orders[$enum_number][] = $new_enum_order[$i];
            }
        }
        // Change enumerations orders and enumerations descriptions.
        for ($i = 0; $i < count($enumdescription); $i++) {
            $enum_number = $enum_change_order[$i];
            // For all included enumerations save important information: number of element, which contain it and ...
            // ...distance from element begin to enumeration begin.
            // If current enumeration have included enumerations.
            if ($include_enums[$enum_number][0] != -1) {
                // For all included enumerations...
                for ($j = 0; $j < count($include_enums[$enum_number]); $j++) {
                    $elem_find = false;
                    // ...find element, which contain current included enumeration.
                    for ($elem_number = 0; $elem_number < count($enumdescription[$enum_number]) && !$elem_find; $elem_number++) {
                        $left_border_of_elem = $enumdescription[$enum_number][$elem_number]->begin;
                        $left_border_of_enum = $enumdescription[$include_enums[$enum_number][$j]][0]->begin;
                        $right_border_of_elem = $enumdescription[$enum_number][$elem_number]->end;
                        $right_border_of_enum = array_pop($enumdescription[$include_enums[$enum_number][$j]])->end;
                        // If enumeration borders are between element borders, then element find.
                        if ($left_border_of_elem <= $left_border_of_enum && $right_border_of_elem >= $right_border_of_enum) {
                            $elem_find = true;
                        }
                    }
                    if ($elem_number != 0) {
                        $elem_number--;
                    }
                    $distances[$j*2] = $elem_number;
                    // Find distance between element and enumeration which it contain.
                    $distances[$j*2+1] = $left_border_of_elem - $left_border_of_enum;
                }
            }
            // Find current order of enumeration.
            $current_order = array();
            $current_order[] = 0;
            for ($j = 1; $j < count($enumdescription[$enum_number]); $j++) {
                $place_find = false;
                for ($insert_place = 0; $insert_place < count($current_order) && !$place_find; $insert_place++) {
                    $left_border_of_elem = $enumdescription[$enum_number][$current_order[$insert_place]]->begin;
                    $left_border_of_elem_new = $enumdescription[$enum_number][$j]->begin;
                    // If left border of new element are less then left border of element in order, insert place are find.
                    if ($left_border_of_elem > $left_border_of_elem_new) {
                        $place_find = true;
                    }
                }
                if ($insert_place != 0 && $place_find) {
                    $insert_place--;
                }
                // Add element to order.
                array_splice($current_order, $insert_place, 0, $j);
            }
            if ($current_order != $enums_orders[$enum_number]) {
                // Copy current enumeration to temp array and remove it from correct answer.
                $for_change_enum_order = array();
                $left_border_of_enum = $enumdescription[$enum_number][$current_order[0]]->begin;
                $right_border_of_enum = $enumdescription[$enum_number][array_pop($current_order)]->end;
                for ($j = 0; $j < $right_border_of_enum-$left_border_of_enum+1; $j++) {
                    $for_change_enum_order[] = $correctanswer[$enumdescription[$enum_number][$current_order[0]]->begin];
                    unset($correctanswer[$enumdescription[$enum_number][$current_order[0]]->begin]);
                }
                // Change current order to new in enumeration.
                // Copy elements in new order.
                for ($j=0; $j < count($enums_orders[$enum_number]); $j++) {
                    // Copy element, token for token.
                    $left_border_of_elem = $enumdescription[$enum_number][$enums_orders[$enum_number][$j]]->begin;
                    $right_border_of_elem = $enumdescription[$enum_number][$enums_orders[$enum_number][$j]]->end;
                    $left_border_of_enum = $enumdescription[$enum_number][$current_order[0]]->begin;
                    for ($k = 0; $k < $right_border_of_elem-$left_border_of_elem+1; $k++) {
                        $for_change_enum_order[] = $for_change_enum_order[$left_border_of_elem-$left_border_of_enum+$k];
                    }
                    // If we have separates between elements, that copy they to and of temp array.
                    if ( $j != count($enums_orders[$enum_number])-1) {
                        $left_border_of_elem_new = $enumdescription[$enum_number][$current_order[$j+1]]->begin;
                        $right_border_of_elem = $enumdescription[$enum_number][$current_order[$j]]->end;
                        for ($z = 0; $z < $left_border_of_elem_new-$right_border_of_elem-1; $z++) {
                            $for_change_enum_order[0] = $for_change_enum_order[$z+$right_border_of_elem-$left_border_of_enum+1];
                        }
                    }
                }
                // Remove old order from temp array.
                $right_border_of_enum = $enumdescription[$enum_number][array_pop($current_order)]->end;
                array_splice($for_change_enum_order, 0, $right_border_of_enum - $left_border_of_enum +1);
                // Copy enumeration in correct answer, token for token.
                array_splice($correctanswer, $left_border_of_enum, 0, $for_change_enum_order);
                // Change enumeration description.
                // Find distance between elements of enumeration.
                $elements_distances = array();
                $elements_distances[] = 0;
                for ($j = 0; $j < count($enumdescription[$enum_number])-1; $j++) {
                    $left_border_of_elem_new = $enumdescription[$enum_number][$current_order[$j+1]]->begin;
                    $right_border_of_elem = $enumdescription[$enum_number][$current_order[$j]]->end;
                    $elements_distances[] = $left_border_of_elem_new-$right_border_of_elem;
                }
                // Change description of enumeration, element for element.
                for ($j = 0; $j < count($enumdescription[$enum_number]); $j++) {
                    // Take current element old desription.
                    $left_border_of_elem = $enumdescription[$enum_number][$enums_orders[$enum_number][$j]]->begin;
                    $right_border_of_elem = $enumdescription[$enum_number][$enums_orders[$enum_number][$j]]->end;
                    // If current element not first...
                    if ($j != 0) {
                        // Calculate new description, use previews element description, and current element old description.
                        $right_border_of_prev_elem = $enumdescription[$enum_number][$enums_orders[$enum_number][$j-1]]->end;
                        $left_border_of_elem_new = $right_border_of_prev_elem+$elements_distances[$j];
                        $right_border_of_elem_new = $right_border_of_elem+ $left_border_of_elem_new-$left_border_of_elem;
                    } else {
                        // ...else.
                        // Calculate new description, use first element description, and current element old description.
                        $left_border_of_elem_new = $enumdescription[$enum_number][$current_order[0]]->begin;
                        $right_border_of_elem_new = $left_border_of_elem_new+$right_border_of_elem-$left_border_of_elem;
                    }
                    // Change description of element.
                    $enumdescription[$enum_number][$enums_orders[$enum_number][$j]]->begin = $left_border_of_elem_new;
                    $enumdescription[$enum_number][$enums_orders[$enum_number][$j]]->end = $right_border_of_elem_new;
                }
                // If current enumeration contains included enumerations...
                if ($include_enums[$enum_number][0] != -1) {
                    // ...update their descriptions.
                    for ($j = 0; $j < count($include_enums[$enum_number]); $j++) {
                        // Find distance between elements of enumeration.
                        $elements_distances = array();
                        $elements_distances[] = $distances[$j*2+1];
                        for ($k = 0; $k < count($enumdescription[$include_enums[$enum_number][$j]])-1; $k++) {
                            $first_index = $include_enums[$enum_number][$j];
                            $second_index = $enums_orders[$include_enums[$enum_number][$j]][$k];
                            $right_border_of_prev_elem = $enumdescription[$first_index][$second_index]->end;
                            $second_index = $enums_orders[$include_enums[$enum_number][$j]][$k+1];
                            $left_border_of_elem = $enumdescription[$first_index][$second_index]->begin;
                            $elements_distances[] = $left_border_of_elem-$right_border_of_prev_elem;
                        }
                        // Update included enumeration description, element for element.
                        for ($k = 0; $k < count($enumdescription[$include_enums[$enum_number][$j]]); $k++) {
                            // Take current element old desription.
                            $first_index = $include_enums[$enum_number][$j];
                            $second_index = $enums_orders[$include_enums[$enum_number][$j]][$k];
                            $right_border_of_elem = $enumdescription[$first_index][$second_index]->end;
                            $left_border_of_elem = $enumdescription[$first_index][$second_index]->begin;
                            // If current element not first...
                            if ($k != 0) {
                                // Calculate new description, use previews element description, and current element old description.
                                $second_index = $enums_orders[$include_enums[$enum_number][$j]][$k-1];
                                $right_border_of_prev_elem = $enumdescription[$first_index][$second_index]->end;
                                $second_index = $enums_orders[$include_enums[$enum_number][$j]][$k];
                                $left_border_of_elem_new = $right_border_of_prev_elem+$elements_distances[$k];
                                $right_border_of_elem_new = $left_border_of_elem_new+$right_border_of_elem-$left_border_of_elem;
                            } else {
                                // ...else.
                                // Calculate new description, use description of element, which contains current enumeration,...
                                // ...and current element old description.
                                $right_border_of_prev_elem = $enumdescription[$enum_number][$distances[$j*2]]->begin;
                                $left_border_of_elem_new = $right_border_of_prev_elem+$elements_distances[$k];
                                $right_border_of_elem_new = $left_border_of_elem_new+$right_border_of_elem-$left_border_of_elem;
                            }
                            // Update description of element.
                            $enumdescription[$first_index][$second_index]->begin = $left_border_of_elem_new;
                            $enumdescription[$first_index][$second_index]->end = $right_border_of_elem_new;
                        }
                    }
                }
            }
        }
        $changed_data->correctanswer = $correctanswer;
        $changed_data->enumdescription = $enumdescription;
        return $changed_data;
    }
}

class enum_element {
    public $begin; // Index of first element token.
    public $end; // Index of last element token.

    /**
     * Function create enumeration element, which start on $first token, and ended by $last token.
     * @param integer $first - first element token.
     * @param integer $last - last element token.
     */
    public function __construct($first, $last) {
        $this->begin = $first;
        $this->end = $last;
    }
}
