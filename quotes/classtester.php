<?php 
require("inc/config.php");
require("inc/functions.php");
require("inc/techtype.class.php");
require("inc/techitem.class.php");
require("inc/quote.class.php");
require("inc/customer.class.php");
require("inc/quoteitem.class.php");

/*
$sql = "ALTER TABLE `tech_items` ADD COLUMN `old_product` varchar(250) DEFAULT NULL;";
$sqlres = mysqli_query($GLOBALS["link"],$sql);

$sql = "ALTER TABLE `tech_items` ADD COLUMN `new_product` varchar(250) DEFAULT NULL;";
$sqlres = mysqli_query($GLOBALS["link"],$sql);

$sql = "ALTER TABLE `tech_items` ADD KEY `old_product` (`old_product`);";
$sqlres = mysqli_query($GLOBALS["link"],$sql);

$sql = "ALTER TABLE `tech_items` ADD KEY `new_product` (`new_product`);";
$sqlres = mysqli_query($GLOBALS["link"],$sql);

//$sql = "DELETE FROM `quotes`";
//$sqlres = mysqli_query($GLOBALS["link"],$sql);

//$sql = "DELETE FROM `quote_items`";
//$sqlres = mysqli_query($GLOBALS["link"],$sql);


$sql = "SELECT * FROM `tech_items`";
$sqlres = mysqli_query($GLOBALS["link"],$sql);

while($row = mysqli_fetch_assoc($sqlres)) {
    $topos = strpos($row["option"], "To");
    $old = substr($row["option"], 0, $topos);
    $new = substr($row["option"], $topos + 2);
    $old = trim($old);
    $new = trim($new);
    $sql = "UPDATE `tech_items` SET `old_product` = '".mysqli_real_escape_string($GLOBALS["link"],$old)."', `new_product` = '".mysqli_real_escape_string($GLOBALS["link"],$new)."' WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$row["id"])."'";
    mysqli_query($GLOBALS["link"],$sql);
}
*/
/*
//print "<br>".QuoteItem::CalculateActualNYears(1.2, 0.12, 249.9, 78.96);
print "<br>".QuoteItem::CalculatePayback(1.2, 0.12, 249.9, 78.96);
print "<hr>";
//print "<br>".QuoteItem::CalculateActualNYears(1.2, 0.12, 399, 423.69);
print "<br>".QuoteItem::CalculatePayback(1.2, 0.12, 399, 423.69);

print "<hr>";
//print "<br>".QuoteItem::CalculateActualNYears(1.2, 0.12, 299, 9.38);
print "<br>".QuoteItem::CalculatePayback(1.2, 0.12, 299, 9.38);

exit();
$quoteitem = new QuoteItem(76);
$techitem = new TechItem($quoteitem->TechID);

$quoteitem->Qty = 10;
$quoteitem->CalculateCostSavings();
$quoteitem->SalesPrice = $techitem->Price * $quoteitem->Qty;
//$quoteitem->TechItem = $techitem;

DebugPrint($quoteitem);

print "<hr>";

$quoteitem = new QuoteItem(66);
$techitem = new TechItem($quoteitem->TechID);

$quoteitem->Qty = 1;
$quoteitem->CalculateCostSavings();
$quoteitem->SalesPrice = $techitem->Price * $quoteitem->Qty;
$quoteitem->TechItem = $techitem;

DebugPrint($quoteitem);
*/

?>