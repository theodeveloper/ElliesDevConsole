<?php  
   $text = file_get_contents('editForWeb.txt');
    $array = explode("\n", $text);
    foreach($array as $ar)
    {
    $new_text .= "print '".$ar."';\n";
    }
    file_put_contents('editForWeb.txt', $new_text);
?>