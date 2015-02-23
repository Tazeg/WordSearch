# Word Search

A "word search" puzzle generator in PHP.

### Screenshot

<img src="http://fr.jeffprod.com/blog/images/2015-02-23-motsmeles.png">

### Required

PHP

### Installation

Put files on your web server, then load the `index.php` page. 

### Usage

To generate a grid in command line, write this for example in `index.php` :

```bash
require_once 'class.grid.php';
require_once 'class.word.php';
$grid=new Grid(5);
$grid->gen();
echo $grid->render(Grid::RENDER_TEXT); // use $grid->render() for HTML output
echo "Words to find (".$grid->getNbWords().") :\n";
echo $grid->getWordsList("\n"); // argument is word separator
```

Then type the command `php index.php`, it will display :

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
    
