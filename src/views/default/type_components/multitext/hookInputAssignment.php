<?php
$multitext = "";
for ($i = 0; $i <= count($this->arr[$name]) - 1; $i++) {
    $multitext .= $this->arr[$name][$i]."|";
}
$multitext = substr($multitext, 0, strlen($multitext) - 1);
$this->arr[$name] = $multitext;