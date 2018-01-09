<?php

use Sudoku\Solver;

require 'autoload.php';

//$solver = new Solver();
//$grid = [
//	[[2, 0, 0,], [3, 0, 0,], [0, 0, 0,],],
//	[[8, 0, 4,], [0, 6, 2,], [0, 0, 3,],],
//	[[0, 1, 3,], [8, 0, 0,], [2, 0, 0,],],
//	[[0, 0, 0,], [0, 2, 0,], [3, 9, 0,],],
//	[[5, 0, 7,], [0, 0, 0,], [6, 2, 1,],],
//	[[0, 3, 2,], [0, 0, 6,], [0, 0, 0,],],
//	[[0, 2, 0,], [0, 0, 9,], [1, 4, 0,],],
//	[[6, 0, 1,], [2, 5, 0,], [8, 0, 9,],],
//	[[0, 0, 0,], [0, 0, 1,], [0, 0, 2,],],
//];
//$solver->setGrid($grid);
//
//$solver->solveGrid();
//
//echo $solver->getGridOutput();
//echo "\nCompleted in {$solver->getIterationsFormatted()} iterations taking {$solver->getTimeTaken()} seconds";

$solver = new Solver();
$grid = [
	[[0, 2, 0,], [0, 0, 0,], [0, 0, 0,],],
	[[0, 0, 0,], [6, 0, 0,], [0, 0, 3,],],
	[[0, 7, 4,], [0, 8, 0,], [0, 0, 0,],],
	[[0, 0, 0,], [0, 0, 0,], [0, 0, 2,],],
	[[0, 8, 0,], [0, 4, 0,], [0, 1, 0,],],
	[[6, 0, 0,], [5, 0, 0,], [0, 0, 0,],],
	[[0, 0, 0,], [0, 1, 0,], [7, 8, 0,],],
	[[5, 0, 0,], [0, 0, 9,], [0, 0, 0,],],
	[[0, 0, 0,], [0, 0, 0,], [0, 4, 0,],],
];
$solver->setGrid($grid);

$solver->solveGrid();

echo $solver->getGridOutput();
echo "\nCompleted in {$solver->getIterationsFormatted()} iterations taking {$solver->getTimeTaken()} seconds";
