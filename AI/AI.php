<?php 

// $command = escapeshellcmd('D:/Subject HK192/AI/MachineLearning/Lab1/Test.py');
// $output = shell_exec($command);
$output = shell_exec('python37 test.py');
echo $output;

?>