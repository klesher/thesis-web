<?php
$dom = new DOMDocument;
$dom->load('testData.ref.xml');
$dom->relaxNGValidate(grammar.rng)
$html = $dom->saveHTML();

echo "$html";
#echo $dom;
?>
