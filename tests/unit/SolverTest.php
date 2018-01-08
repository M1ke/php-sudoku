<?php
require_once '_bootstrap.php';

use Sudoku\Solver;
use Sudoku\SudokuException;

class SolverTest extends PHPUnit_Framework_TestCase {

	public function testImportASudokuFromAFile(){
		$file_path = TEST_DATA.'sudoku-file';

		$solver = new Solver($file_path);

		$expected = [
			[[0, 0, 4,], [3, 5, 0,], [1, 8, 9,],],
			[[0, 1, 0,], [0, 2, 0,], [3, 0, 6,],],
			[[7, 9, 0,], [0, 1, 8,], [0, 0, 5,],],
			[[0, 0, 0,], [0, 0, 1,], [0, 0, 0,],],
			[[2, 0, 0,], [0, 4, 0,], [0, 0, 3,],],
			[[0, 0, 0,], [2, 0, 0,], [0, 0, 0,],],
			[[1, 0, 0,], [7, 3, 0,], [0, 2, 8,],],
			[[5, 0, 7,], [0, 8, 0,], [0, 6, 0,],],
			[[4, 8, 2,], [0, 6, 9,], [5, 0, 0,],],
		];

		$this->assertEquals($expected, $solver->getGrid());
	}

	public function testImportFailsDueToFile(){
		$file_path = TEST_DATA.'sudoku-file-fail';

		try {
			new Solver($file_path);
			$this->fail("An exception was not thrown for a bad file");
		}
		catch (SudokuException $e){
			$this->assertContains("problem", $e->getMessage());
		}
	}

	public function testValidGrid(){
		$grid = [
			[[0, 0, 4,], [3, 5, 0,], [1, 8, 9,],],
			[[0, 1, 0,], [0, 2, 0,], [3, 0, 6,],],
			[[7, 9, 0,], [0, 1, 8,], [0, 0, 5,],],
			[[0, 0, 0,], [0, 0, 1,], [0, 0, 0,],],
			[[2, 0, 0,], [0, 4, 0,], [0, 0, 3,],],
			[[0, 0, 0,], [2, 0, 0,], [0, 0, 0,],],
			[[1, 0, 0,], [7, 3, 0,], [0, 2, 8,],],
			[[5, 0, 7,], [0, 8, 0,], [0, 6, 0,],],
			[[4, 8, 2,], [0, 6, 9,], [5, 0, 0,],],
		];

		try {
			$this->checkGrid($grid);

			$this->assertTrue(true);
		}
		catch (SudokuException $e){
			$this->fail("Grid was deemed not valid, error was: {$e->getMessage()}");
		}
	}

	public function testInvalidGridRow(){

		$grid = [
			[[0, 0, 4,], [3, 5, 0,], [1, 8, 9,],],
			[[0, 1, 0,], [0, 2, 0,], [3, 0, 6,],],
			[[7, 9, 0,], [0, 1, 8,], [0, 0, 5,],],
			[[0, 0, 0,], [9, 0, 1,], [0, 9, 0,],],
			[[2, 0, 0,], [0, 4, 0,], [0, 0, 3,],],
			[[0, 0, 0,], [2, 0, 0,], [0, 0, 0,],],
			[[1, 0, 0,], [7, 3, 0,], [0, 2, 8,],],
			[[5, 0, 7,], [0, 8, 0,], [0, 6, 0,],],
			[[4, 8, 2,], [0, 6, 9,], [5, 0, 0,],],
		];

		try {
			$this->checkGrid($grid);

			$this->fail("Grid was deemed valid, should have found a row error");
		}
		catch (SudokuException $e){
			$this->assertContains("9 appeared twice in row 4", $e->getMessage());
		}
	}

	public function testInvalidGridColumn(){

		$grid = [
			[[0, 0, 4,], [3, 5, 0,], [1, 8, 9,],],
			[[0, 1, 0,], [7, 2, 0,], [3, 0, 6,],],
			[[7, 9, 0,], [0, 1, 8,], [0, 0, 5,],],
			[[0, 0, 0,], [0, 0, 1,], [0, 0, 0,],],
			[[2, 0, 0,], [0, 4, 0,], [0, 0, 3,],],
			[[0, 0, 0,], [2, 0, 0,], [0, 0, 0,],],
			[[1, 0, 0,], [7, 3, 0,], [0, 2, 8,],],
			[[5, 0, 7,], [0, 8, 0,], [0, 6, 0,],],
			[[4, 8, 2,], [0, 6, 9,], [5, 0, 0,],],
		];

		try {
			$this->checkGrid($grid);

			$this->fail("Grid was deemed valid, should have found a column error");
		}
		catch (SudokuException $e){
			$this->assertContains("7 appeared twice in column 4", $e->getMessage());
		}
	}

	public function testInvalidGridBlock(){

		$grid = [
			[[0, 0, 4,], [3, 5, 0,], [1, 8, 9,],],
			[[0, 1, 0,], [0, 2, 0,], [3, 0, 6,],],
			[[7, 9, 0,], [0, 1, 8,], [0, 0, 5,],],
			[[0, 2, 0,], [0, 0, 1,], [0, 0, 0,],],
			[[2, 0, 0,], [0, 4, 0,], [0, 0, 3,],],
			[[0, 0, 0,], [2, 0, 0,], [0, 0, 0,],],
			[[1, 0, 0,], [7, 3, 0,], [0, 2, 8,],],
			[[5, 0, 7,], [0, 8, 0,], [0, 6, 0,],],
			[[4, 8, 2,], [0, 6, 9,], [5, 0, 0,],],
		];

		try {
			$this->checkGrid($grid);

			$this->fail("Grid was deemed valid, should have found a block error");
		}
		catch (SudokuException $e){
			$this->assertContains("2 appeared twice in block 4", $e->getMessage());
		}
	}

	/**
	 * @param array $grid
	 */
	private function checkGrid(array $grid){
		$solver = new Solver();
		$solver->setGrid($grid);
		$solver->checkGrid();
	}
}
