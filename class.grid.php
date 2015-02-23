<?php
//----------------------------------------------------------------------
//  AUTHOR	: Jean-Francois GAZET
//  WEB		: http://www.jeffprod.com
//  TWITTER	: @JeffProd
//  MAIL	: jeffgazet@gmail.com
//  LICENCE	: GNU GENERAL PUBLIC LICENSE Version 2, June 1991
//----------------------------------------------------------------------

class Grid  {
    
    // SETTINGS ------------------------------------------------
    const MIN_LEN_WORD = 3; // longueur min des mots en base
    const MAX_LEN_WORD = 12; // longueur max des mots en base
    const DEFAULT_GRID_SIZE = 10; // taille grille par défaut
    // END SETTINGS --------------------------------------------
    
    const RENDER_HTML = 0; // afficher la grille en HTML
    const RENDER_TEXT = 1; // afficher la grille en mode texte
    
    private $_size; // (Int) longueur du coté de la grille carrée
    private $_cells; // (tableau de size*size éléments String) cellules de la grille, chacune contenant une lettre
    private $_wordsList; // (tableau d'objets Word) : liste des mots à trouver
    private $_arrayCOL; // tableau (Int) des numéros des colonnes d'après les index des cellules
    private $_db; // base de données de mots SQLite
    private $_errorMsg; // chaine de texte non nulle en cas d'erreur
    
    public function __construct($size=self::DEFAULT_GRID_SIZE) {
        
        $this->_errorMsg='';
        
        if($size<self::MIN_LEN_WORD || $size>self::MAX_LEN_WORD) {
            $this->_errorMsg='size must be between '.self::MIN_LEN_WORD.' and '.self::MAX_LEN_WORD;
            echo $this->_errorMsg;
            return;
            }
        
		$this->_size=$size;
        $this->_wordsList=array();
        $this->_cells=array_fill(0,$this->_size*$this->_size,'');
        $this->_db = new SQLite3('words.db',SQLITE3_OPEN_READONLY);
        
        // index des colonnes dans 2 grilles apposées verticalement
        // pour gérer les dépassement en bas
        $this->_arrayCOL = array(); 
        for($i=0;$i<(2*$this->_size*$this->_size);$i++) {
            $this->_arrayCOL[$i]=self::COL($i);
            }
		}
        
    public function __destruct() {
        if($this->_errorMsg!='') {return;}
        $this->_db->close();
        }
        
    public function getWordsList($end=' ') {
        
        // Retourne la liste des mots à trouver dans la grille par ordre alphabétique.
        // $end : séparateur de mots défini par l'utilisateur (\n, <br>, espace...)
        
        if($this->_errorMsg!='') {return;}
        $arr=array();
        foreach($this->_wordsList as $word) {
            $label=$word->getLabel();
            if($word->isReversed()) {$label=strrev($label);}
            $arr[]=$label;
            }
        sort($arr);
        $r='';
        foreach($arr as $label) {
            $r.=$label.$end;
            }        
        return $r;
        }
        
    public function getNbWords() {
        return count($this->_wordsList);
        }
        
    public function gen() {        

        // Cré une nouvelle grille        

        if($this->_errorMsg!='') {return;}        

        $size2=$this->_size*$this->_size;
        $i=rand(0,$size2-1); // on part d'une case au hasard
        
        // on parcourt toutes les cases
        $cpt=0;
        while($cpt<$size2) {
            $this->placeWord($i);
            $cpt++;
            $i++;
            if($i==$size2) {$i=0;}
            }
        } // gen()
    
    private function placeWord($start) {

        // Tente de placer un mot dans la case de départ $start, avec au hasard :
        // - horizontal,vertical,diagonal
        // - inversé

        // nouveau mot, case de départ donné en param ($start)
        $word=new Word(
            $start, // index de la case de départ
            -1, // fin, on verra plus bas selon l'orientation et la longueur du mot
            rand(0,3), // orientation
            '', // libellé tiré au dernier moment
            (rand(0,1) == 1) // inversé : true ou false au hasard
            );

        $inc=1; // incrément
        $len=rand(self::MIN_LEN_WORD,$this->_size); // longueur d'un mot au hasard, de MIN_LEN_WORD à _size
        
        switch($word->getOrientation()) {

            case Word::HORIZONTAL:
                $inc=1;
                $word->setEnd($word->getStart()+$len-1);
                 // si mot placé sur 2 lignes on décale à gauche
                while( $this->_arrayCOL[$word->getEnd()] < $this->_arrayCOL[$word->getStart()] ) {
                    $word->setStart($word->getStart()-1);
                    $word->setEnd($word->getStart()+$len-1);
                    }
                break;

            case Word::VERTICAL:
                $inc=$this->_size;
                $word->setEnd($word->getStart()+($len*$this->_size)-$this->_size);
                // si le mot dépasse la grille en bas, on décale vers le haut
                while($word->getEnd()>($this->_size*$this->_size)-1) {
                    $word->setStart($word->getStart()-$this->_size);
                    $word->setEnd($word->getStart()+($len*$this->_size)-$this->_size);
                    }
                break;

            case Word::DIAGONAL_LEFT_TO_RIGHT:
                $inc=$this->_size+1;
                $word->setEnd($word->getStart()+($len*($this->_size+1))-($this->_size+1));
                // si le mot dépasse la grille à droite, on décale à gauche
                while( $this->_arrayCOL[$word->getEnd()] < $this->_arrayCOL[$word->getStart()] ) {
                    $word->setStart($word->getStart()-1);
                    $word->setEnd($word->getStart()+($len*($this->_size+1))-($this->_size+1));
                    }
                // si le mot dépasse la grille en bas, on décale vers le haut
                while($word->getEnd()>($this->_size*$this->_size)-1) {
                    $word->setStart($word->getStart()-$this->_size);
                    $word->setEnd($word->getStart()+($len*($this->_size+1))-($this->_size+1));
                    }
                break;

            case Word::DIAGONAL_RIGHT_TO_LEFT:
                $inc=$this->_size-1;
                $word->setEnd($word->getStart()+(($len-1)*($this->_size-1)));
                // si le mot sort de la grille à gauche, on décale à droite
                while( $this->_arrayCOL[$word->getEnd()] > $this->_arrayCOL[$word->getStart()] ) {
                    $word->setStart($word->getStart()+1);
                    $word->setEnd($word->getStart()+(($len-1)*($this->_size-1)));
                    }
                // si le mot dépasse la grille en bas, on décale vers le haut
                while($word->getEnd()>($this->_size*$this->_size)-1) {
                    $word->setStart($word->getStart()-$this->_size);
                    $word->setEnd($word->getStart()+(($len-1)*($this->_size-1)));
                    }
                break;
            }

        // on construit le pattern SQL ("A__O___") si le mot croise des lettres présentes dans la grille
        $s='';
        $flag=false;
        for($i=$word->getStart();$i<=$word->getEnd();$i+=$inc) {
            if($this->_cells[$i]=='') {$s.='_';}
            else {
                $s.=$this->_cells[$i];
                $flag=true;
                }
            }
   
        // si on trouve que des '_' => pas de chevauchement on ajoute le mot
        if(!$flag) {
            $word->setLabel($this->getRandomWord($len)); // on doit tirer un mot de longueur len
            if($word->isReversed()) {$word->setLabel(strrev($word->getLabel()));}
            $this->addWord($word);
            }

        // sinon
        else {
            // si le pattern est un texte entier on quitte
            if(strpos($s,'_')===false) {return;}

            // on en pioche un avec ce pattern
            $word->setLabel($this->getWordLike($s));
            $word->setReverse(false); // le nouveau mot pioché n'est pas inversé

            // ajout du mot (test null dans addmot)
            $this->addWord($word);
            }

        } // placeWord()
       
    public function render($type=Grid::RENDER_HTML) {
        
        // Affiche la grille complète au format
        // TEXTE ou HTML (par défaut)
        
        if($this->_errorMsg!='') {return;}
        
        $r='';
        
        switch($type) {
            case Grid::RENDER_HTML:
            $cpt=0;
            $r.='<style type="text/css">
                table.gridtable {
                    font-family: verdana,arial,sans-serif;
                    font-size:16px;
                    color:#333333;
                    border-width: 1px;
                    border-color: #666666;
                    border-collapse: collapse;
                    }
                table.gridtable td {
                    border-width: 0px;
                    padding: 8px;
                    border-style: solid;
                    border-color: #666666;
                    background-color: #ffffff;
                    }
                </style>'.PHP_EOL;
            $r.='<table class="gridtable">'.PHP_EOL;
            foreach($this->_cells as $letter) {
                if($cpt==0) {$r.='<tr>';}
                if($letter=='') {$r.='<td>'.chr(rand(65,90)).'</td>';}
                else {$r.='<td>'.$letter.'</td>';}
                $cpt++;
                if($cpt==$this->_size) {$r.='</tr>'.PHP_EOL; $cpt=0;}
                }
            $r.='</table>'.PHP_EOL;
            break;
            
            case Grid::RENDER_TEXT:
            $cpt=0;
            foreach($this->_cells as $letter) {
                if($letter=='') {$r.=chr(rand(65,90));}
                else {$r.=$letter;}
                $r.=' ';
                $cpt++;
                if($cpt==$this->_size) {$r.="\n"; $cpt=0;}
                }            
            break;
            }
                        
        return $r;
        }
        
    private function COL($x) {
        // IN : (int $x) = index de la case
        // OUT : (int) numéro de la colonne, de 1 à $this->_size
        return ($x % $this->_size)+1;
        }        

    private function getRandomWord($len) {
        // IN (Int) : longueur du mot $len
        // OUT (String) : un mot au hasard de longueur $len
        $rqtxt='SELECT word FROM words WHERE LENGTH(word)='.$len.' ORDER BY RANDOM() LIMIT 1';
        return $this->_db->querySingle($rqtxt);
        }
        
    private function getWordLike($pattern) {
        // Retourne un mot qui ressemble au pattern.
        // IN (String) : $pattern, ex : A__U__S
        // OUT (String) : un mot au hasard qui correspond, "" sinon
        $rqtxt='SELECT word FROM words WHERE word LIKE "'.$pattern.'" ORDER BY RANDOM() LIMIT 1';
        return $this->_db->querySingle($rqtxt);
        }        

    private function addWord($word) {

        // ajoute un mot :
        // - dans les cases de la grille
        // - à la liste des mots à trouver
        
        if($word->getLabel()=='') {return;}

        // Ajout dans les cases de la grille
        $j=0;
        switch($word->getOrientation()) {

            case Word::HORIZONTAL:
                for ($i=$word->getStart(); $j<strlen($word->getLabel()); $i++) {
                    $this->_cells[$i]=substr($word->getLabel(),$j,1);
                    $j++;
                    }
                break;

            case Word::VERTICAL:
                for($i=$word->getStart(); $j<strlen($word->getLabel()); $i+=$this->_size) {
                    $this->_cells[$i]=substr($word->getLabel(),$j,1);
                    $j++;
                    }
                break;

            case Word::DIAGONAL_LEFT_TO_RIGHT:
                for($i=$word->getStart(); $j<strlen($word->getLabel()); $i+=$this->_size+1) {
                    $this->_cells[$i]=substr($word->getLabel(),$j,1);
                    $j++;
                    }
                break;

            case Word::DIAGONAL_RIGHT_TO_LEFT:
                for($i=$word->getStart(); $j<strlen($word->getLabel()); $i+=$this->_size-1) {
                    $this->_cells[$i]=substr($word->getLabel(),$j,1);
                    $j++;
                    }
                break;

            } // switch

        // Ajout du mot à la liste
        $this->_wordsList[]=$word;

        } // addWord()        

    } // class Grid
?>
