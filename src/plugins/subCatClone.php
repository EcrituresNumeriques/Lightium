<?php
$clone = $file_db->prepare("INSERT INTO `item_assoc`(`id_item`,`id_subcat`) VALUES (:item,:subcat);");
$clone->bindParam(":item",$id_item);
$clone->bindParam(":subcat",$settings['public2']);
$getItem = $file_db->prepare("SELECT id_item FROM item_assoc WHERE id_subcat = :subcat");
$getItem->bindParam(":subcat",$settings['public1']);
$getItem->execute() or die('Unable to get items');
$items = $getItem->fetchAll(PDO::FETCH_ASSOC);
foreach ($items as $item) {
  $id_item = $item['id_item'];
  $clone->execute() or die('unable to clone item');
}
?>
