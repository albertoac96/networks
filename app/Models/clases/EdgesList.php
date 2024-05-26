<?php

namespace App\Models\clases;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EdgesList extends Model
{
   
        public $edges;
    
        public function __construct() {
            $this->edges = array();
        }
    
        public function addEdge($from, $to) {
            $this->edges[] = array($from, $to);
        }
    
        public function getEdges() {
            return $this->edges;
        }
    
}
