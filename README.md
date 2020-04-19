# Sudoku

## Solver

### Usage
```
$solver = new Wrurik\Sudoku\Solver($array);
$solution = $solver->resolve();
```

#### Input
The input should be an array of exactly 81 elements, representing the cells in a sudoku puzzle counting from top left to bottom right.

You can also supply the sudoku as a multi-dimensional array containing 9 rows or 9 columns:
```
$input = [
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0,0,0]
];

$solver = Wrurik\Sudoku\Solver::fromRows($input);
$solver = Wrurik\Sudoku\Solver::fromColumns($input);
```

#### Handling empty cells
By default `0` is used for empty cells. You can change it:
```
$solver->setEmptyValue($yourPreferedEmptyValue);
```

#### Solution
Solution is returned from the `$solver->resolve()` method.  
Solution is provided as an array of 81 elements. Even if your input was from rows or columns.

#### Exceptions
If no solution can be found an `InvalidSudokuException` is thrown.  
If the input is in an incorrect format an `InvalidInputException` is thrown.