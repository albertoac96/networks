<?php

namespace App\Http\Controllers\proyectos;

use App\Http\Controllers\Controller;
use App\Models\Grafo;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Proyecto;
use App\Models\Table;

class grafosControl extends Controller
{
    public function CalculateNodesControl(Request $request){
        //return $request;
        $PaDataSource = $this->traerDataSource($request->info["idProject"]);
        $max_nodes = count($PaDataSource); // Stores total number of nodes
       
        $grafoInfo = Grafo::where('idGrafo', $request->idGrafo)->get();
        $grafoInfo = json_decode($grafoInfo[0]->cContenido);
       // return $grafoInfo->adjacencyList;
        $AdjacencyList = $grafoInfo->adjacencyList;
        //return $max_nodes;
        //$controlValuesArray = array(); // Stores control values
        $controlValuesArray = array_fill(0, $max_nodes, 0);
        $indices = array(); // Stores ID of each node
        

        for($i=0; $i<$max_nodes; $i++){
            $power_share = 0;
            $control_value = 0;

            $indices[$i] = $i;
            //return ($AdjacencyList);
            $degree = count($AdjacencyList[$i]);

            if($degree != 0){
                $power_share = (1.0 / $degree);
            } else {
                $power_share = number_format(0, 20, '.', '');;
            }

            $neighbours_list = $AdjacencyList[$i];
            //return $neighbours_list;

            foreach ($neighbours_list as $neighbour) {
                //return $neighbour;
                // Obtén el valor actual de control para este vecino particular
                $control_value = $controlValuesArray[$neighbour[0]];
        
                // Actualiza el valor de control sumando la nueva fracción de poder
                $control_value += $power_share;
        
                // Almacena el valor actualizado de control en el array
                $controlValuesArray[$neighbour[0]] = $control_value;
            }



        }

        $meanControl = $this->calculateMeanControl($controlValuesArray);

        $relativeAssymmetry = $this->calculateRelativeAsymmetry($grafoInfo);

        

        //return $relativeAssymmetry;

       // return $meanControl;

       // return $controlValuesArray;

        return response()->json([
            'meanControl' => $meanControl,
            'controlValuesArray' => $controlValuesArray,
            'relativeAssymmetry' => $relativeAssymmetry
        ]);
    }

    public function calculateMeanControl($controlValuesArray){
        $max_nodes = count($controlValuesArray);
        $control_sum = 0;
        $control_mean = 0;

        if ($max_nodes > 0) {
            foreach ($controlValuesArray as $value) {
                
                $control_sum = $control_sum + $value;
                
            }
           

            // Calcula el promedio
            $control_mean = $control_sum / $max_nodes;
           
        }
    
        // Retorno del promedio de control
        return $control_mean;
    }

    function calculateRelativeAsymmetry($request) {
        $max_nodes = $max_nodes = count($request->adjacencyList);
        
        // Creates a new list to store values of relative asymmetry (i.e. ra)
        $ra = [];
        $count = 0;
        $sumRA = 0.0;

    
        for ($v = 0; $v < $max_nodes; $v++) {
            $relAsy = $this->getDepth($v, $request->adjacencyList, $max_nodes);
            $ra[] = $relAsy;
            $sumRA += $ra[$v];
            $count++;
        }
    
        $meanGraphRA = $sumRA / $count; 
    
        return $ra;
    }

    function getDepth($v, $adjacencyList, $nodesCount) {
        $queue = new \SplQueue();
        $colour = array_fill(0, $nodesCount, -1); // WHITE
        $distance = array_fill(0, $nodesCount, INF); // INFINITY
        $predecessor = array_fill(0, $nodesCount, -INF); // NULL
    
        $colour[$v] = 0; // GRAY
        $distance[$v] = 0;
        $predecessor[$v] = $v; // v is the root
    
        $queue->enqueue($v);
        
        while (!$queue->isEmpty()) {
            $u = $queue->dequeue();
    
            if (!empty($adjacencyList[$u])) {
                foreach ($adjacencyList[$u] as $t) {
                    if ($colour[$t[0]] == -1) { // WHITE
                        $colour[$t[0]] = 0; // GRAY
                        $distance[$t[0]] = $distance[$u] + 1;
                        $predecessor[$t[0]] = $u;
                        $queue->enqueue($t[0]);
                    }
                }
            }
            $colour[$u] = 1; // BLACK
        }
    
        $sumDepth = 0;
        $numElements = 0;
    
        foreach ($distance as $d) {
            if ($d != INF) {
                $sumDepth += $d;
                $numElements++;
            }
        }
    
        if ($numElements == 1) {
            $meanDepth = 0.0;
            $relativeAssymmetry = 1.0;
        } elseif ($numElements == 2) {
            $meanDepth = 1.0;
            $relativeAssymmetry = 0.0;
        } else {
            $meanDepth = $sumDepth / ($numElements - 1);
            $relativeAssymmetry = (2 * ($meanDepth - 1)) / ($numElements - 2);
        }
    
        return $relativeAssymmetry;
    }

    private function traerDataSource($idProject){
        $aDataSource = Table::where('idProject', $idProject)->get();
        $aTable = json_decode($aDataSource[0]->aTable);
        $aNodes = json_decode($aDataSource[0]->aNodes);
        $aHeaders = json_decode($aDataSource[0]->aHeaders);

        $positions = array();

        if($aNodes->id != null){
            $pos = array_search($aNodes->id, $aHeaders);
            $positions[] = array('name' => $aNodes->id, 'index' => $pos);
        } else {
            $positions[] = array('name' => 'NodeID', 'index' => 0);
        }
        if($aNodes->name != null){
            $pos = array_search($aNodes->name, $aHeaders);
            $positions[] = array('name' => $aNodes->name, 'index' => $pos);
        } else {
            $positions[] = array('name' => 'Name', 'index' => '');
        }
        if($aNodes->x != null){
            $pos = array_search($aNodes->x, $aHeaders);
            $positions[] = array('name' => $aNodes->x, 'index' => $pos);
        }
        if($aNodes->y != null){
            $pos = array_search($aNodes->y, $aHeaders);
            $positions[] = array('name' => $aNodes->y, 'index' => $pos);
        }

        $dataSource = array();
        foreach($aTable as $item){
            $dataSource[] = array(
                'NodeID' => $item[$positions[0]['index']],
                'NodeName' => $item[$positions[1]['index']],
                'NodeX' => $item[$positions[2]['index']],
                'NodeY' => $item[$positions[3]['index']],
                'ControlValue' => 0,
                'RelativeAssymetry' => 0
            );
        }

        return $dataSource;
        
        
    }

    public function traeGrafos($clave){
        $aGrafos = Grafo::where('idProyecto', $clave)->get();
        return $aGrafos;
    }

    
    
}
