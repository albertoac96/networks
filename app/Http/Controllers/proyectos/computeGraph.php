<?php

namespace App\Http\Controllers\proyectos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Proyecto;
use App\Models\Table;

use Maatwebsite\Excel\Excel;

use Ballen\Cartographer\Core\LatLong;
use Ballen\Cartographer\LineString;

use App\Models\clases\EdgesList;

class computeGraph extends Controller
{
    public function  ComputeGraph(Request $request){
        return $request;
        $PaDataSource = $this->traerDataSource($request->info['idProject']);
        $PiBeta = intval($request->beta);
        $PiSigma = $request->sigma;
        $PcDistFunction = $request->distFuntion;
        $PcNetType = $request->netType;

        
        // Creates an array to store distances between each pair of points
        $distanceMatrix = $this->MakeDistanceMatrix($PaDataSource, $PcDistFunction);
        
       
        $PiEdgesCount = 0;

        $tmp_Sdist_ij = 0.0;
        $tmp_Bdist_ij = 0.0;

        $tmp_dist_kci = 0;
        $tmp_dist_kcj = 0;
        $tmp_dist_ki = 0;
        $tmp_dist_kj = 0;
        $current_k_max = 0;

        $edgeList = array();
        $adjacencyListA = array();
        $adjacencyList = $this->RNG_AdjacencyList(count($PaDataSource));

        $geoCoordinates = $this->creaGeoJSON('FeatureCollection','modelo1','urn:ogc:def:crs:EPSG::8992');

        $Pcoordinates = array();

        $cve = 0;

       // Perform the Test of Region Emptiness
       for ($i = 0; $i < count($PaDataSource); $i++){
      
        $maxColumn = 1 + $i;
        for ($j = 0; $j < $maxColumn; $j++){
           
            if($i>$j){
                $piX = doubleval($PaDataSource[$i]['NodeX']);
                $piY = doubleval($PaDataSource[$i]['NodeY']);

                $pjX = doubleval($PaDataSource[$j]['NodeX']);
                $pjY = doubleval($PaDataSource[$j]['NodeY']);

               
                
               
                

                if($PiBeta > 0){
                    if($PiBeta>=1){
                        // Compute the radio of the region of influence

                        
                       
                        $tmp_Bdist_ij = (($PiBeta * intval($distanceMatrix[$i][$j])) / 2);
                        $tmp_Sdist_ij = ($PiSigma * intval($distanceMatrix[$i][$j]));
     
                        // Calculate the coordinates of the centers of two circles ci and cj
                        // who intersection delimits the region of influence of node i and node j
     
                        $tmp_ci_X = ((((1 - ($PiBeta / 2)) * $piX)) + (($PiBeta / 2) * ($pjX)));
                        $tmp_ci_Y = ((((1 - ($PiBeta / 2)) * $piY)) + (($PiBeta / 2) * ($pjY)));
     
                        $tmp_cj_X = (((($PiBeta / 2) * $piX)) + ((1 - $PiBeta / 2) * ($pjX)));
                        $tmp_cj_Y = (((($PiBeta / 2) * $piY)) + ((1 - $PiBeta / 2) * ($pjY)));

                       
                     } else { //Beta es < 1
                         $tmp_Bdist_ij = $distanceMatrix[$i][$j] / (2 * $PiBeta);
                         //echo $tmp_Bdist_ij;
                     }
                } else { 
                    return "Beta no puede ser negativo";
                }

                $PbDrawCurrentEdge = true;  // Assumes that an edge is to be included
                //BARRE LOS PUNTOS PARA COMPROBAR SU EXISTENCIA EN EL AREA
                for($k=0;$k<count($PaDataSource); $k++){
                    if($k!=$i && $k!=$j){
                        $pkX = doubleval($PaDataSource[$k]['NodeX']);
                        $pkY = doubleval($PaDataSource[$k]['NodeY']);

                      
                        if($PcDistFunction != 'e'){
                            $tmp_dist_kci = $this->haversineDistance($pkX, $pkY, $tmp_ci_X, $tmp_ci_Y, $PcDistFunction);
                            $tmp_dist_kcj = $this->haversineDistance($pkX, $pkY, $tmp_cj_X, $tmp_cj_Y, $PcDistFunction);
                        } else {
                            $tmp_dist_kci = $this->EuclideanDistance($pkX, $pkY, $tmp_ci_X, $tmp_ci_Y);
                            $tmp_dist_kcj = $this->EuclideanDistance($pkX, $pkY, $tmp_cj_X, $tmp_cj_Y);
                        }

                        if ($tmp_dist_kci > $tmp_dist_kcj){
                            $current_k_max = $tmp_dist_kci;
                        } else {
                            $current_k_max = $tmp_dist_kcj;
                        }

                        /*return response()->json([
                            'piX' => $piX,
                            'piY' => $piY,
                            'PjX' => $pjX,
                            'pjY' => $pjY,
                            'beta' => $PiBeta,
                            'tmpBdistij' => $tmp_Bdist_ij,
                            'tmpSdistij' => $tmp_Sdist_ij,
                            'tmp_ci_X' => $tmp_ci_X,
                            'tmp_ci_Y' => $tmp_ci_Y,
                            'tmp_dist_kci' => $tmp_dist_kci,
                            'tmp_dist_kcj' => $tmp_dist_kcj
                        ]);*/

                        

                        if($current_k_max <= $tmp_Bdist_ij){
                            // The point lies withing the region of influence of point i and point j
                            // Do not add an edge
                            $PbDrawCurrentEdge = false;
                        } else {
                            // If the edge is not rejected in the previious test,
                            // perform the second test as follows:
                            if($PcDistFunction != 'e'){
                                $tmp_dist_kci = $this->haversineDistance($pkX, $pkY, $piX, $piY, $PcDistFunction);
                                $tmp_dist_kcj = $this->haversineDistance($pkX, $pkY, $pjX, $pjY, $PcDistFunction);
                            } else {
                                $tmp_dist_kci = $this->EuclideanDistance($pkX, $pkY, $piX, $piY);
                                $tmp_dist_kcj = $this->EuclideanDistance($pkX, $pkY, $pjX, $pjY);
                            }

                            if($tmp_dist_ki<$tmp_Sdist_ij){
                                $PbDrawCurrentEdge = false;
                            }

                            if($tmp_dist_kj<$tmp_Sdist_ij){
                                $PbDrawCurrentEdge = false;
                            }


                        }

                    }
                }
                 
                
                if($PbDrawCurrentEdge){
                   
                    // Add a new edge in the EdgesFeatureSet
                    $coord1 = "{$piX},{$piY}";
                    $coord1 = explode(',', $coord1);
                    $coord1[0] = floatval($coord1[0]);
                    $coord1[1] = floatval($coord1[1]);
                    $coord2 = "{$pjX},{$pjY}";
                    $coord2 = explode(',', $coord2);
                    $coord2[0] = floatval($coord2[0]);
                    $coord2[1] = floatval($coord2[1]);

                    $coordinates = array();
                    $coordinates[] = array($coord1,$coord2);

                    $geoCoordinates = $this->addGeoJSON("MultiLineString", $cve++, "linea", $coordinates, $geoCoordinates);

                    
                   // $edgeList->addEdge($i, $j);
                    $edgeList[] = array($i, $j);

                    $adjacencyListA[] = array($i, $j, $distanceMatrix[$i][$j]);
                    $this->AddEdgeAtEnd($adjacencyList, $i, $j, $distanceMatrix[$i][$j]);

                    //SE ALMACENA EL MISMO DATOS DE LA MATRIZ PORQUE EN J-I ESTA EN 0
                    $adjacencyListA[] = array($j, $i, $distanceMatrix[$i][$j]);
                    $this->AddEdgeAtEnd($adjacencyList, $j, $i, $distanceMatrix[$i][$j]);
                   
                    

                    
                    
                }

               
            }

          
        }

       

       }


       //return json_encode($geoCoordinates);
      
        return response()->json([
            'infoProyecto' => $request->info,
            'nBeta' => $PiBeta,
            'nSigma' => $PiSigma,
            'distFunction' => $PcDistFunction,
            'netType' => $PcNetType,
            'distanceMatrix' => $distanceMatrix,
            'PbDrawCurrentEdge' => $PbDrawCurrentEdge,
            'EdgesList' => $edgeList,
            'adjacencyList' => $adjacencyList,
            'adjacencyListArray' => $adjacencyListA,
            'coordinates' => $Pcoordinates,
            'geo' => $geoCoordinates
        ]);

        



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
                'NodeY' => $item[$positions[3]['index']]
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


    private function creaGeoJSON($type, $name, $crsname){
        $LcResp = array();
        $LcResp["type"] = $type;
        $LcResp["name"]= $name;
        $LcResp["crs"]= array();
        $LcResp["crs"]["type"]=$crsname;
        $LcResp["crs"]["properties"]=array();
        $LcResp["crs"]["properties"]["name"]=$crsname;
        $LcResp["features"]= array();
        return $LcResp;
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


    public function addGeoJSON($type, $id, $name, $coordinates, $json){
        $json["features"][$id] = array();
        $json["features"][$id]["type"] = "Feature";
        $json["features"][$id]["properties"]["id"] = $id;
        $json["features"][$id]["properties"]["name"] = $name;
        $json["features"][$id]["geometry"]["type"] = $type;
        $json["features"][$id]["geometry"]["coordinates"] = $coordinates;
        return $json;
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
