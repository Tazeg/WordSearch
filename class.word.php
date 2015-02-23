<?php
//----------------------------------------------------------------------
//  AUTHOR	: Jean-Francois GAZET
//  WEB		: http://www.jeffprod.com
//  TWITTER	: @JeffProd
//  MAIL	: jeffgazet@gmail.com
//  LICENCE	: GNU GENERAL PUBLIC LICENSE Version 2, June 1991
//----------------------------------------------------------------------

class Word {
    
    const HORIZONTAL = 0;
    const VERTICAL = 1;
    const DIAGONAL_LEFT_TO_RIGHT = 2;
    const DIAGONAL_RIGHT_TO_LEFT = 3;
    
    private $_orientation; // orientation du mot
    private $_label; // intitulé du mot
    private $_reversed; // libellé inversé ou non (true,false)
    private $_start; // index de la case de départ
    private $_end; // index de la case d'arrivée
        
    public function __construct($start,$end,$orientation,$label,$reversed) {
        $this->_start=$start;
        $this->_end=$end;
		$this->_orientation=$orientation;
        $this->_label=$label;
        $this->_reversed=$reversed;
		}    
       
    public function setStart($start) {
        $this->_start=$start;
        }
        
    public function setEnd($end) {
        $this->_end=$end;
        }
        
    public function setLabel($label) {
        $this->_label=$label;
        }      
        
    public function setReverse($reverse) {
        $this->_reversed=$reverse;
        }  
      
    public function getStart() {
        return $this->_start;
        }

    public function getEnd() {
        return $this->_end;
        }
        
    public function getLabel() {
        return $this->_label;
        }        
        
    public function isReversed() {
        return $this->_reversed;
        }    

    public function getOrientation() {
        return $this->_orientation;
        }
        
    } // class Word
?>
