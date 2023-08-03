<?php

function is_even(int $int) {
    return $int % 2 == 0 ? true : false;
}

function array_median(array $data) {
    $count = count($data);
    if (is_even($count)) {
        $med1 = $data[($count / 2) - 1];
        $med2 = $data[$count / 2];
        $med = ($med1 + $med2) / 2;
    } else {
        $med = ($count + 1) / 2;
        $med = $data[$med - 1];
    }
    return $med;
}

// function array_mode(array $data = []) {
//     sort($data);
//     $count = count($data); 
//     $i = 0;
//     $num_count = [];
//     while ($i < $count) {
//         $num = $data[$i]; // current_number
//         //echo $num . "\n";
//         $total = 0;
//         for ($x = 0; $x < $count; $x++) {
//             if ($data[$x] == $num) {
//                 $total++;
//             }
//         }
//         $num_count[$num] = $total;
//         $i += 1;
//     }
//     foreach ($num_count as $key => $val) {
//         $nc[$val] = $key;
//     }
//     $highest = 0;
//     foreach ($nc as $key => $val) {
//         if ($highest < $key) {
//             $highest = $key;
//         }
//     }
//     return $nc[$highest];
    
// }


function modes_of_array($arr) {
    $values = [];
    foreach ($arr as $v) {
      if (isset($values[$v])) {
        $values[$v] ++;
      } else {
        $values[$v] = 1;  // counter of appearance
      }
    } 
    arsort($values);  // sort the array by values, in non-ascending order.
    $modes = [];
    $x = $values[key($values)]; // get the most appeared counter
    reset($values); 
    foreach ($values as $key => $v) {
      if ($v == $x) {   // if there are multiple 'most'
        $modes[] = $key;  // push to the modes array
      } else {
        break;
      }
    } 
    return $modes;

}


function countInRange($numbers,$lowest,$highest){
    //bounds are included, for this example
      return count(array_filter($numbers,function($number) use ($lowest,$highest){
        return ($lowest<=$number && $number < $lowest + ($highest - $lowest + 1)); 
      }));
  }

// echo countInRange([       // 'echo' for output
//   20, 10, 36, 67, 55, 35, 48, 70, 22, 44
// ], 10, 40);