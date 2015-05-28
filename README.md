# Word Search

A "word search" puzzle generator in PHP.

### Screenshot

<img src="http://fr.jeffprod.com/img/2015-02-23-motsmeles.png">

### Required

PHP

### Installation

Extract files on your web server or your computer.

### Usage

In your browser, load the `index.php` page.  
To generate a grid in command line, type `php index.php` :

```bash
require_once 'class.grid.php';
require_once 'class.word.php';

$grid=new Grid(5); // grid size, from 3 to 20
$grid->gen(); // generate the puzzle
echo $grid->render(Grid::RENDER_TEXT); // display it. Use $grid->render() for HTML output
echo "Words to find (".$grid->getNbWords().") :\n";
echo $grid->getWordsList("\n"); // argument is word separator
```

For example, the result in command line is :

```bash
I R E A C 
I D O X R 
T V E X A 
S K I E N 
Z W L V S 
Words to find (6) :
CRANS
IDEE
OEIL
REAC
SKIE
VEXA
```
    
