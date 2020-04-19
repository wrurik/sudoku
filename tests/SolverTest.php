<?php


use PHPUnit\Framework\TestCase;
use Wrurik\Sudoku\Exceptions\InvalidInputException;
use Wrurik\Sudoku\Exceptions\InvalidSudokuException;
use Wrurik\Sudoku\Solver;

class SolverTest extends TestCase
{
    private $sudoku = [
        7, 9, 1, 2, 5, 4, 6, 8, 3,
        2, 4, 0, 6, 8, 7, 0, 0, 5,
        8, 0, 0, 0, 1, 3, 0, 2, 0,

        4, 0, 0, 0, 0, 2, 0, 0, 0,
        0, 0, 2, 0, 0, 8, 7, 6, 0,
        0, 8, 6, 4, 0, 0, 1, 0, 0,

        3, 0, 4, 8, 0, 0, 2, 5, 0,
        1, 5, 8, 0, 0, 0, 3, 4, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0
    ];

    private $resolvedSudoku = [
        7, 9, 1, 2, 5, 4, 6, 8, 3,
        2, 4, 3, 6, 8, 7, 9, 1, 5,
        8, 6, 5, 9, 1, 3, 4, 2, 7,

        4, 3, 7, 1, 6, 2, 5, 9, 8,
        9, 1, 2, 5, 3, 8, 7, 6, 4,
        5, 8, 6, 4, 7, 9, 1, 3, 2,

        3, 7, 4, 8, 9, 1, 2, 5, 6,
        1, 5, 8, 7, 2, 6, 3, 4, 9,
        6, 2, 9, 3, 4, 5, 8, 7, 1
    ];

    /**
     * @throws InvalidSudokuException
     */
    public function testSudokuResolved()
    {
        $solver = new Wrurik\Sudoku\Solver($this->sudoku);

        $this->assertEquals($this->resolvedSudoku, $solver->resolve());
    }

    /**
     * @throws InvalidSudokuException
     * @throws InvalidInputException
     */
    public function testSudokuAsRowsResolved()
    {
        $solver = Solver::fromRows(array_chunk($this->sudoku, 9));

        $this->assertEquals($this->resolvedSudoku, $solver->resolve());
    }

    /**
     * @throws InvalidInputException
     * @throws InvalidSudokuException
     */
    public function testSudokuAsColumnsResolved()
    {
        $col = 0;
        $columns = [];
        foreach ($this->sudoku as $item) {
            $columns[$col][] = $item;

            $col++;

            if ($col === 9) {
                $col = 0;
            }
        }

        $solver = Solver::fromColumns($columns);

        $this->assertEquals($this->resolvedSudoku, $solver->resolve());
    }

    /**
     * @throws InvalidSudokuException
     */
    public function testSetEmptyValue()
    {
        $ar = array_replace($this->sudoku,
            array_fill_keys(
                array_keys($this->sudoku, 0),
                'testing'
            )
        );

        $solver = new Solver($ar);
        $this->assertEquals($this->resolvedSudoku, $solver->setEmptyValue('testing')->resolve());
    }
}