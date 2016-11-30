<?php 
function CalculatePayback($kwh_price, $escalaction_rate, $price_of_sales, $kwh_saved, $debug_print = false) {
    /*
    See PaybackCalculator_v2.ods for details and testing of the formula/process for payback period
    */
    $B1 = $kwh_price;
    $B2 = $escalaction_rate;
    if ($B2 > 0) {
        $B2 = $B2 / 100;            //Convert to a fraction Excel does this automatically with percentages
    }
    $B3 = $kwh_saved;               //VLOOKUP(B37,Calculation_Table,MATCH("Kwh Saved",Calculation_Table1,0),0)
    $B4 = $price_of_sales;

    $A8 = log10(1 + (($B4 * $B2)/(12 * $B1 * $B3 ))) / log10(1 + $B2);                          //LOG(1+((B4 * B2)/(12 * B1 * B3 ))) / LOG(1+B2)
    $B8 = (12 * $B1 * $B3) * ((pow(1+ $B2, intval($A8)) -1) / $B2);                             //=(12*B1*B3)*((POWER(1+B2,INT(A8))-1)/B2)
    $C8 = $B4;
    $D8 = $C8 - $B8;
    $E8 = $D8 / ($B1 * $B3 * pow(1 + $B2, intval($A8)));                                        //=D8/(B1*B3*POWER(1+B2,INT(A8)))
    $F8 = number_format(12 * intval($A8) + $E8, 7);                                                              //=ROUND(12*INT(A8)+E8,7)
    if ($F8 < 0 || $F8 == 0) {
        $F8 = "No Payback";
    }else{
        $F8 = number_format($F8, 2);
    }
    if ($debug_print) {
        $html .= "<br>B1: ".$B1;
        $html .= "<br>B2: ".$B2;
        $html .= "<br>B3: ".$B3;
        $html .= "<br>B4: ".$B4;
        $html .= "<br>A8: ".$A8;
        $html .= "<br>B8: ".$B8;
        $html .= "<br>C8: ".$C8;
        $html .= "<br>D8: ".$D8;
        $html .= "<br>E8: ".$E8;
        $html .= "<br>F8: ".$F8;
    }
    return $F8;
}
?>