<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;

use App\Models\Grafo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Proyecto;
use App\Models\Table;

use Maatwebsite\Excel\Excel;
use App\Exports\MultiSheetExport;
use Illuminate\Support\Facades\Storage;

use Ballen\Cartographer\Core\LatLong;
use Ballen\Cartographer\LineString;

use App\Models\clases\EdgesList;

use App\Http\Controllers\proyectos\grafosControl;

use Symfony\Component\HttpFoundation\Response;

use ZipArchive;

use App\Services\KDTree;

class graphController extends Controller
{
    

    public function ComputeGraphOptimized(Request $request)
    {
        
        set_time_limit(3000);

        ini_set('memory_limit', '512M'); // O un valor mayor como '1G' si es necesario


        // Extraer datos iniciales
        $PaDataSource = $this->traerDataSource($request->info['idProject']);
        $PiBeta = $request->beta;
        $PiSigma = $request->sigma;
        $PcDistFunction = $request->distFuntion;
        $PcName = $request->nameGrafo;

        // Crear el grafo
        $grafo = Grafo::create(['idProyecto' => $request->info['idProject']]);

        // Crear la matriz de distancias
        $distanceMatrix = $this->MakeDistanceMatrix($PaDataSource, $PcDistFunction);

        // Construir el KDTree
        $kdtree = new KDTree($PaDataSource);
     
        //dd($kdtree);


        // Inicializar estructuras
        $edgeList = [];
        
        $adjacencyList = $this->RNG_AdjacencyList(count($PaDataSource));
        
        $geoCoordinates = $this->creaGeoJSON('FeatureCollection', $PcName, 'urn:ogc:def:crs:EPSG::8992', $grafo->idGrafo);

        $cve = 0;

        foreach ($PaDataSource as $i => $pointI) {
            $radius = max($PiBeta * 0.1, 0.001); // Radio basado en PiBeta o un valor fijo
            $neighbors = $kdtree->findWithinRadius($pointI, $radius);
        
            if (empty($neighbors)) {
                //\Log::warning("Nodo $i no tiene vecinos en el radio $radius.");
                continue;
            }

            
        
            foreach ($neighbors as $pointJ) {
                $j = $pointJ['index']; // Ahora este índice siempre estará definido
                $booleano = $this->shouldAddEdge($i, $j, $distanceMatrix, $PiBeta, $PiSigma, $PcDistFunction);
                //echo $i."_".$j."-".$booleano."<br>";
                if ($i != $j && $this->shouldAddEdge($i, $j, $distanceMatrix, $PiBeta, $PiSigma, $PcDistFunction) != 1) {
                    //echo("Agregando borde: i=$i, j=$j");
        
                    $edgeList[] = [$i, $j];
                    $this->updateAdjacencyAndGeo($i, $j, $distanceMatrix, $PaDataSource, $adjacencyList, $geoCoordinates, $cve++);
                }
            }
        }
        
        

        // Preparar los datos para guardar
        $data = [
            'infoProyecto' => $request->info,
            'nBeta' => $PiBeta,
            'nSigma' => $PiSigma,
            'distFunction' => $PcDistFunction,
            'distanceMatrix' => $distanceMatrix,
            'EdgesList' => $edgeList,
            'adjacencyList' => $adjacencyList,
            'geo' => $geoCoordinates,
            'nodes' => $PaDataSource,
        ];

        return $geoCoordinates;

       

        Grafo::where('idGrafo', $grafo->idGrafo)->update([
            'cContenido' => json_encode($data),
        ]);


        $grafoController = App::make(grafosControl::class);
         // Construir el nuevo request con los datos necesarios
         $data = [
            'info' => [
                'idProject' => $request->input('info')['idProject']
            ],
            'idGrafo' => $grafo->idGrafo
        ];

        $newRequest = new Request($data);

        $grafoController->CalculateNodesControl($newRequest);
        

        
        $grafo = Grafo::where('idGrafo', $grafo->idGrafo)->get();

       

        return response()->json([
            "grafo" => $grafo
        ]);
    }

    private function shouldAddEdge($i, $j, $distanceMatrix, $PiBeta, $PiSigma, $PcDistFunction)
    {
        // Validar existencia de índices
        if (!isset($distanceMatrix[$i][$j])) {
            //\Log::warning("Índice no definido en distanceMatrix: i=$i, j=$j");
            return false;
        }
    
        $tmp_Bdist_ij = ($PiBeta * $distanceMatrix[$i][$j]) / 2;
    
        // Validar y retornar resultado
        return (bool)($distanceMatrix[$i][$j] <= $tmp_Bdist_ij);
    }
    


    

    private function updateAdjacencyAndGeo($i, $j, $distanceMatrix, $PaDataSource, &$adjacencyList, &$geoCoordinates, $cve)
    {
        // Validar existencia de índices en distanceMatrix
        if (!isset($distanceMatrix[$i][$j])) {
            //\Log::warning("Índice no definido en distanceMatrix: i=$i, j=$j");
            return $geoCoordinates; // Retornar sin hacer cambios
        }
    
        // Obtener coordenadas de los puntos
        $piX = doubleval($PaDataSource[$i]['NodeX']);
        $piY = doubleval($PaDataSource[$i]['NodeY']);
        $pjX = doubleval($PaDataSource[$j]['NodeX']);
        $pjY = doubleval($PaDataSource[$j]['NodeY']);
    
        $coord1 = [$piX, $piY];
        $coord2 = [$pjX, $pjY];
        $coordinates = [[$coord1, $coord2]];
    
        // Actualizar geoJSON
        $geoCoordinates = $this->addGeoJSON("MultiLineString", $cve, "linea", $coordinates, $geoCoordinates);
    
        // Validar existencia de listas de adyacencia
        if (!isset($adjacencyList[$i])) {
            $adjacencyList[$i] = [];
        }
    
        if (!isset($adjacencyList[$j])) {
            $adjacencyList[$j] = [];
        }
    
        // Actualizar lista de adyacencia
        $adjacencyList[$i][] = [$j, $distanceMatrix[$i][$j]];
        $adjacencyList[$j][] = [$i, $distanceMatrix[$i][$j]];
    
        return $geoCoordinates;
    }
    


    private function creaGeoJSON($type, $name, $crsname, $idGrafo){
        $LcResp = array();
        $LcResp["type"] = $type;
        $LcResp["name"]= $name;
        $LcResp["id"] = $idGrafo;
        $LcResp["crs"]= array();
        $LcResp["crs"]["type"]=$crsname;
        $LcResp["crs"]["properties"]=array();
        $LcResp["crs"]["properties"]["name"]=$crsname;
        $LcResp["properties"]["stroke-width"] = 4;
        $LcResp["properties"]["stroke"] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        $LcResp["features"]= array();
        return $LcResp;
    }

    public function addGeoJSON($type, $id, $name, $coordinates, $json){
        $json["features"][$id] = array();
        $json["features"][$id]["type"] = "Feature";
        $json["features"][$id]["properties"]["id"] = $id;
        $json["features"][$id]["properties"]["name"] = $name;
        $json["features"][$id]["geometry"]["type"] = $type;
        $json["features"][$id]["geometry"]["coordinates"] = $coordinates;
        return $json;
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

    private function MakeDistanceMatrix($dataSource, $distFunction){

        $distance_matrix = array();
        
        for ($i = 0; $i < count($dataSource); $i++) {
            for ($j = 0; $j < count($dataSource); $j++) {

                if($i == $j){
                    $distance_matrix[$i][$j] = 0.000000000000;
                }

                if($i > $j){
                    if($distFunction != 'e'){
                        $distance_matrix[$i][$j] = $this->haversineDistance($dataSource[$i]['NodeX'], $dataSource[$i]['NodeY'], $dataSource[$j]['NodeX'], $dataSource[$j]['NodeY'], $distFunction);
                    } else {
                        
                        $distance_matrix[$i][$j] = $this->EuclideanDistance($dataSource[$i]['NodeX'], $dataSource[$i]['NodeY'], $dataSource[$j]['NodeX'], $dataSource[$j]['NodeY']);
                    }
                }
                
                
                
            }
        }


        //$dataCombine = $this->traerCombinaciones($dataSource);
        return $distance_matrix;
    }

    private function traerCombinaciones($dataSource){
    



        $dataCombine = array();
        foreach($dataSource as $i){
            $items = array();
            $items[] = array('id' => $i['NodeID'], 'x' => $i['NodeX'], 'y' => $i['NodeY']);
            foreach($dataSource as $j){
                if($j['NodeID'] != $i['NodeID']){
                    $items[] = array('id' => $j['NodeID'], 'x' => $j['NodeX'], 'y' => $j['NodeY']);
                }
            }
            $dataCombine[] = array($items);
        }
        return $dataCombine;
    }

    

    private function EuclideanDistance($x1, $y1, $x2, $y2) {
        $distance = sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2));
        return $distance;
    }

  

    function haversineDistance($x1, $y1, $x2, $y2, $type) {
        $Rkm = 6371; // Earth's radius in kilometers
        $Rmi = 3958.8;

        $phi1 = deg2rad($y1);
        $phi2 = deg2rad($y2);
        $deltaPhi = ($y2 - $y1) * (3.1415926536 / 180);
        $deltaTheta = ($x2 -$x1) * (3.1415926536 / 180);
        //$λ1 = deg2rad($lon1);
        //$λ2 = deg2rad($lon2);

        $a = pow(sin($deltaPhi / 2), 2) + cos($phi1) * cos($phi2) * pow(sin($deltaTheta / 2), 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        if($type == 'hk'){
            $distance = $Rkm * $c;
        } else {
            $distance = $Rmi * $c;
        }
        
        return $distance;

    }


    function RNG_AdjacencyList($vertices) {
        
        $adjacencyList = array();
    
        for ($i = 0; $i < $vertices; ++$i) {
            $adjacencyList[$i] = array();
        }

    
        return $adjacencyList;
    }


    function AddEdgeAtEnd(&$adjacencyList, $startVertex, $endVertex, $weight) {
        $adjacencyList[$startVertex][] = array($endVertex, $weight);
    }

    function AddEdgeAtBeginning(&$adjacencyList, $startVertex, $endVertex, $weight) {
        array_unshift($adjacencyList[$startVertex], array($endVertex, $weight));
    }

    function GetOutwardEdges($adjacencyList, $index) {
        return new \SplDoublyLinkedList($adjacencyList[$index]);
    }



}
