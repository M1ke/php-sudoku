<?php
namespace Sudoku;

use InvalidArgumentException;

class Solver {
	/**
	 * @var array
	 */
	private $grid = [];

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
	 */
	public function setGrid(array $grid){
		$this->grid = $grid;

		return $this;
	}

	public function checkGrid(){
		$grid = $this->grid;

		$this->checkRows($grid);

		$this->checkColumns($grid);

		$this->checkBlocks($grid);
	}

	/**
	 * @param array $grid
	 * @throws SudokuException
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
	 * @throws SudokuException
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
	 * @throws SudokuException
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
					throw new SudokuException("The number $num appeared twice in $range_name $range_num");
				}

				$numbers_appeared[] = $num;
			}
		}
	}
}
