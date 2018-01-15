#!/usr/bin/env php
<?php

use Sudoku\Solver;
use Sudoku\SudokuException;

require 'autoload.php';

$opts = getopt('', [
	'file:',
	'output',
]);

if (empty($opts['file'])){
	exit("You must specify '--file' with a file path as the first argument\n");
}

try {
	$solver = new Solver($opts['file']);

	$solver->solveGrid(isset($opts['output']));

	echo $solver->getGridOutput();
	echo "\nCompleted in {$solver->getIterationsFormatted()} iterations taking {$solver->getTimeTaken()}\n";
}
catch (SudokuException $e) {
	echo "A problem occurred with the sudoku solver: {$e->getMessage()}\n";
}
catch (\Exception $e) {
	exit("A general problem occurred with the application: {$e->getMessage()}\n");
}
