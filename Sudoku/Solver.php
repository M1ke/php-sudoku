<?php
namespace Sudoku;

use InvalidArgumentException;

class Solver {
	/**
	 * @var array
	 */
	private $grid = [];
	private $iterations = 0;
	private $time_taken;

	/**
	 * Solver constructor.
	 * @param string $file_path
	 */
	public function __construct(string $file_path = ''){
		if (!empty($file_path)){
			$this->loadFile($file_path);
		}
	}

	/**
	 * @return array
	 */
	public function getGrid(){
		return $this->grid;
	}

	/**
	 * @return string
	 */
	public function getGridOutput(){
		return $this->outputGrid($this->grid);
	}

	/**
	 * @param string $file_path
	 * @throws SudokuException
	 */
	private function loadFile(string $file_path){
		if (!is_readable($file_path)){
			throw new InvalidArgumentException("The file path specified ($file_path) was not readable.");
		}

		$file_content = file_get_contents($file_path);
		$file_content = trim($file_content);
		$file_content = str_replace(' ', '', $file_content);

		$rows = explode("\n", $file_content);

		$grid = [];
		foreach ($rows as $row_num => $row){
			if (strlen($row)!==9){
				throw new SudokuException("There was a problem importing row $row_num which was not 9 characters long. You can use any character other than a space to represent a blank field, e.g. '0', '-', '.'");
			}

			$grid_row = [[], [], []];
			for ($n = 0; $n<9; $n++){
				$block = (int)floor($n / 3);
				$grid_row[$block][] = (int)$row[$n];
			}
			$grid[] = $grid_row;
		}

		$this->grid = $grid;
	}

	/**
	 * @param array $grid
	 * @return $this
	 * @throws SudokuException
	 */
	public function setGrid(array $grid){
		if (!empty($this->grid)){
			throw new SudokuException("The grid is already set. You may not re-set the grid, but you can create a new Solver object");
		}

		$this->grid = $grid;

		return $this;
	}

	/**
	 * @param array $grid
	 * @throws EmptyGridException
	 * @throws InvalidGridException
	 */
	private function checkGridInternal(array $grid){
		if (empty($this->grid)){
			throw new EmptyGridException("The grid was empty. Set one using setGrid or submit a file to the constructor");
		}

		$this->checkRows($grid);

		$this->checkColumns($grid);

		$this->checkBlocks($grid);
	}

	public function checkGrid(){
		$grid = $this->grid;

		$this->checkGridInternal($grid);
	}

	/**
	 * @param array $grid
	 * @throws InvalidGridException
	 */
	private function checkRows(array $grid){
		$rows = [];
		foreach ($grid as $row_num => $blocks){
			$row = [];
			foreach ($blocks as $block){
				foreach ($block as $num){
					$row[] = $num;
				}
			}
			$rows[] = $row;
		}

		$this->checkRange($rows, 'row');
	}

	/**
	 * @param array $grid
	 * @throws InvalidGridException
	 */
	private function checkColumns(array $grid){
		$columns = [];
		foreach ($grid as $row_num => $row){
			$blocks = $row;
			$col_num = 0;
			foreach ($blocks as $block){
				foreach ($block as $num){
					$columns[$col_num][] = $num;
					$col_num++;
				}
			}
		}

		$this->checkRange($columns, 'column');
	}

	/**
	 * @param array $grid
	 * @throws InvalidGridException
	 */
	private function checkBlocks(array $grid){
		$blocks = [[], [], [], [], [], [], [], [], [],];
		foreach ($grid as $row_num => $row){
			$row_blocks = $row;
			$block_base = (int)floor($row_num / 3);
			foreach ($row_blocks as $n => $block){
				$block_num = ($block_base * 3)+$n;
				$blocks[$block_num] = array_merge($blocks[$block_num], $block);
			}
		}

		$this->checkRange($blocks, 'block');
	}

	/**
	 * @param array $ranges
	 * @param string $range_name
	 * @throws InvalidGridException
	 */
	private function checkRange(array $ranges, $range_name){
		foreach ($ranges as $range_num => $range){
			$numbers_appeared = [];

			foreach ($range as $num){
				if ((int)$num===0){
					continue;
				}
				if (in_array($num, $numbers_appeared)){
					$range_num++;
					throw new InvalidGridException("The number $num appeared twice in $range_name $range_num");
				}

				$numbers_appeared[] = $num;
			}
		}
	}

	/**
	 * @throws SudokuException
	 */
	public function solveGrid(){
		try {
			$this->checkGrid();
		}
		catch (SudokuException $e) {
			throw new SudokuException("The grid should not start off in an invalid state. The checker reported: {$e->getMessage()}");
		}

		$grid = $this->grid;

		if ($this->allSquaresFilled($grid)){
			return;
		}

		$start_time = microtime(true);

		try {
			$solved_grid = $this->backtrack($grid);
		}
		catch (SudokuException $e){
			throw new SudokuException("A problem occurred when backtracking; this might mean the backtracker is wrong or the grid is not possible to solve. The error reported was: {$e->getMessage()}");
		}

		$this->time_taken = microtime(true) - $start_time;

		$this->grid = $solved_grid;
	}

	/**
	 * @param array $grid
	 * @param int $depth
	 * @return array
	 * @throws RangeExhaustedException
	 */
	private function backtrack(array $grid, $depth = 1){
		for ($fill_val = 1; $fill_val<=9; $fill_val++){
			try {
				$filled_grid = $this->fillFirstAvailableSquare($grid, $fill_val);
			}
			catch (GridFullException $e){
				return $grid;
			}
			$this->iterations++;

			try {
				$this->checkGridInternal($filled_grid);
			}
			catch (InvalidGridException $e) {
				continue;
			}

			try {
				$next_grid = $this->backtrack($filled_grid, ($depth+1));
			}
			catch (RangeExhaustedException $e) {
				continue;
			}
		}

		if (empty($next_grid)){
			throw new RangeExhaustedException;
		}

		return $next_grid;
	}

	/**
	 * @param array $grid
	 * @return bool
	 */
	private function allSquaresFilled(array $grid){
		try {
			$this->fillFirstAvailableSquare($grid, 1);
		}
		catch (GridFullException $e) {
			return true;
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function getIterations(){
		return $this->iterations;
	}

	/**
	 * @return string
	 */
	public function getIterationsFormatted(){
		return number_format($this->iterations, 0);
	}

	/**
	 * @param array $grid
	 * @param int $val
	 * @return array
	 * @throws GridFullException
	 */
	private function fillFirstAvailableSquare(array $grid, $val){
		foreach ($grid as $row_key => &$blocks){
			foreach ($blocks as $block_key => &$block){
				foreach ($block as $num_key => &$num){
					if ((int)$num===0){
						$num = $val;

						return $grid;
					}
				}
			}
		}

		throw new GridFullException;
	}

	/**
	 * @param array $grid
	 * @return string
	 */
	private function outputGrid(array $grid){
		$output = '';
		foreach ($grid as $blocks){
			foreach ($blocks as $block){
				$output .= implode(' ', $block).' ';
			}
			$output .= "\n";
		}

		return $output;
	}

	/**
	 * @return float
	 */
	public function getTimeTaken(){
		return round($this->time_taken, 3);
	}
}
