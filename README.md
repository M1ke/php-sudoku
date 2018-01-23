# Sudoku Solver

Brute force Sudoku solver using the backtracking method

### Points to improve

Current array format reads well for input but is far less efficient when checking the grid.
It would be easy to move to a simple ordered list and then define what a row, column and
block are.

### Testing learning points

Trying to write this with TDD worked well up to the solving part. The solver would use private
methods and thus the most sensible way to test a public method is a complete solution. However
this meant actually developing the solver was mostly by writing code that looked good and then
debugging it by running an example through a debugger, or even just echoing out grids.

This, as is always the case, looked quicker than working out a separation of class methods in order
to test properly, but in actual terms was likely longer as things like throwing exceptions to
halt the progress down a path were tricky to catch but would have been easy to test.

Overall it would have made more sense to just break common unit testing rules by writing tests
that checked private methods against known data. These could be discarded were the tool something
to be put into a larger production system later, where brittle internal tests wouldn't be necessary
to enforce the contract of this class with other parts of the system. So private method testing for
TDD where the algorithms are suitably complex may be a useful paradigm.
