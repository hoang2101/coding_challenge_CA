<?php
namespace App\Helper;
use App\Entity\RankName;

class QuarterCalculatorHelper
{
    public function quartile_25($array) {
        return $this->quartile($array, 0.25);
    }

    public function quartile_75($array) {
        return $this->quartile($array, 0.75);
    }

    function getRankFromArray($array, $value) : string {
        if ($value < $this->quartile_25($array)) {
            return RankName::BOTTOM;
        }
        if ($value > $this->quartile_75($array)) {
            return RankName::TOP;
        }
        return RankName::NONE;
    }


    public function quartile($array, $Quartile) {
        sort($array);
        $pos = (count($array) - 1) * $Quartile;

        $base = floor($pos);
        $rest = $pos - $base;

        if( isset($array[$base+1]) ) {
            return $array[$base] + $rest * ($array[$base+1] - $array[$base]);
        } else {
            return $array[$base];
        }
    }
}