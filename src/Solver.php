<?php


namespace Wrurik\Sudoku;


use Wrurik\Sudoku\Exceptions\InvalidInputException;
use Wrurik\Sudoku\Exceptions\InvalidSudokuException;
use function count;

class Solver
{
    /**
     * An array with exactly 81 element,
     * representing the squares in a Sudoku-puzzle from top left to bottom right.
     *
     * empty squares should be null
     *
     * @var array
     */
    private $sudoku;

    /**
     * @var array
     */
    private $emptyCells;

    /**
     * @var mixed
     */
    private $emptyValue = 0;

    /**
     * Solver constructor.
     * @param $sudoku
     */
    public function __construct($sudoku)
    {
        $this->sudoku = $sudoku;
    }

    /**
     * @return mixed
     * @throws InvalidSudokuException
     */
    public function resolve()
    {
        $this->emptyCells = array_filter($this->sudoku, function ($value) {
            return $value === $this->emptyValue;
        });

        return $this->recursiveResolve($this->sudoku);
    }


    /**
     * @param $sudoku
     * @return mixed
     * @throws InvalidSudokuException
     */
    private function recursiveResolve($sudoku)
    {
        $solution = $sudoku;

        foreach ($solution as $boxNr => $boxValue) {

            if ($boxValue !== $this->emptyValue) {
                continue;
            }

            for ($i = 1; $i <= 9; $i++) {
                $solution[$boxNr] = $i;

                if ($this->isRowValid($solution) && $this->isColumnValid($solution) && $this->isBoxValid($solution)) {
                    return $this->recursiveResolve($solution);
                }
            }

            $previousEmptyCell = $this->getPreviousEmptyCell($boxNr);

            while (isset($solution[$previousEmptyCell]) && $solution[$previousEmptyCell] === 9) {
                $solution[$previousEmptyCell] = $this->emptyValue;
                $previousEmptyCell = $this->getPreviousEmptyCell($previousEmptyCell);
            }

            if ($previousEmptyCell === $this->emptyValue) {
                throw new InvalidSudokuException();
            }

            $solution[$boxNr] = $this->emptyValue;
            $solution[$previousEmptyCell]++;

            return $this->recursiveResolve($solution);
        }

        return $solution;
    }

    /**
     * @param $cell
     * @return int|null
     */
    private function getPreviousEmptyCell($cell)
    {
        $keys = array_flip(array_keys($this->emptyCells));
        $values = array_keys($this->emptyCells);

        if (!isset($keys[$cell]) || !isset($values[$keys[$cell] - 1])) {
            return null;
        }

        return $values[$keys[$cell] - 1];
    }

    /**
     * @param $sudoku
     * @return bool
     */
    private function isColumnValid($sudoku)
    {
        $columns = [];

        for ($i = 0; $i <= 8; $i++) {
            for ($y = $i; $y <= 80; $y += 9) {
                $columns[$i][] = $sudoku[$y];
            }
        }

        foreach ($columns as $column) {
            if (!$this->isCollectionUnique($column)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $sudoku
     * @return bool
     */
    private function isRowValid($sudoku)
    {
        foreach (array_chunk($sudoku, 9) as $row) {
            if (!$this->isCollectionUnique($row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $sudoku
     * @return bool
     */
    private function isBoxValid($sudoku)
    {
        $boxes = [];

        $box = 0;
        $cell = 0;

        for ($i = 0; $i < 80; $i += 3) {
            for ($y = 0; $y < 3; $y++) {
                $cell = $i + $y;
                $boxes[$box][] = $sudoku[$cell];
            }

            if ((($cell + 1) % 27) === 0) {
                $box++;
            } else if ((($cell + 1) % 9) === 0) {
                $box -= 2;
            } else {
                $box++;
            }
        }

        foreach ($boxes as $box) {
            if (!$this->isCollectionUnique($box)) {
                return false;
            }
        }

        return true;

    }

    /**
     * @param $input
     * @return bool
     */
    private function isCollectionUnique($input)
    {
        $onlyNumbers = $this->onlyNumbers($input);
        if (array_unique($onlyNumbers) !== $onlyNumbers) {
            return false;
        }

        return true;
    }

    /**
     * @param $input
     * @return array
     */
    private function onlyNumbers($input)
    {
        return array_filter($input, function ($value) {
            return $value !== $this->emptyValue;
        });
    }


    /**
     * @param $rows
     * @return Solver
     * @throws InvalidInputException
     */
    public static function fromRows($rows)
    {
        $sudoku = [];

        if (count($rows) !== 9) {
            throw new InvalidInputException('There must be exactly 9 rows');
        }

        array_map(function ($row) use (&$sudoku) {
            if (count($row) !== 9) {
                throw new InvalidInputException('Each row must have exactly 9 values');
            }

            foreach ($row as $value) {
                $sudoku[] = $value;
            }
        }, $rows);

        return new self($sudoku);
    }

    /**
     * @param $columns
     * @return Solver
     * @throws InvalidInputException
     */
    public static function fromColumns($columns)
    {
        $sudoku = [];

        if (count($columns) !== 9) {
            throw new InvalidInputException('There must be exactly 9 columns');
        }

        $col = 0;

        foreach ($columns as $column) {
            if (count($column) !== 9) {
                throw new InvalidInputException('Each column must have exactly 9 values');
            }

            $row = 0;

            foreach ($column as $value) {
                $sudoku[$col + $row] = $value;
                $row += 9;
            }

            $col++;
        }

        ksort($sudoku);

        return new self($sudoku);
    }

    /**
     * @param mixed $emptyValue
     * @return Solver
     */
    public function setEmptyValue($emptyValue)
    {
        $this->emptyValue = $emptyValue;
        return $this;
    }
}