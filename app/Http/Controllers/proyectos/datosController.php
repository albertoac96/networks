<?php

namespace App\Http\Controllers\proyectos;

use Illuminate\Support\Facades\App;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use App\Http\Controllers\Controller;
use App\Models\Grafo;
use Illuminate\Http\Request;
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


class datosController extends Controller
{

    public function listProjects()
    {
        $LcResp = Proyecto::where('idUsrAlta', Auth::id())->get();
        return $LcResp;
    }

    public function updateProyecto(Request $request)
    {
        $id = $request->id;
        $campo = $request->campo;
        $valor = $request->valor;

        Proyecto::where('idProject', $id)->update([
            $campo => $valor,
        ]);

        return "OK";
    }

    public function convertJSON(Request $request)
    {
        $PcArchivo = $_FILES;
        $file = $PcArchivo['archivo']['tmp_name'];




        $data = Excel::toJson($file);
        //$sheetNames = Excel::sheetNames($file);
        return $data;
    }

    public function newProject(Request $request)
    {
        $PaTabla = $request->datos;
        $PaHeaders = $request->titulos;
        $PaInfo = $request->info;
        $PaNodos = $request->nodos;

        $uuid = uniqid();



        $item = Proyecto::create([
            'cName' => $PaInfo['name'],
            'cDescription' => $PaInfo['desc'],
            'idUsrAlta' => Auth::id(),
            'uuid' => $uuid,
            'cSheet' => $request->hoja['name'],
            'cDocName' => $request->archivo
        ]);

        $tabla = Table::create([
            'aTable' => json_encode($PaTabla),
            'aNodes' => json_encode($PaNodos),
            'aHeaders' => json_encode($PaHeaders),
            'idProject' => $item->idProject
        ]);


        $data = $this->traerDataSource($item->idProject);

        Table::where('idTable', $tabla->idTable)->update([
            'aSingleTable' => json_encode($data)
        ]);

        return $item;
    }

    public function infoProyecto($id)
    {
        $info = Proyecto::where('idProject', $id)->get();
        $grafos = Grafo::where('idProyecto', $id)->get();
        $tabla = Table::where('idProject', $id)->get();
        //return $tabla;
        $tabla[0]->aTable = json_decode($tabla[0]->aTable);
        $tabla[0]->aNodes = json_decode($tabla[0]->aNodes);
        $tabla[0]->aHeaders = json_decode($tabla[0]->aHeaders);
        $tabla[0]->aSingleTable = json_decode($tabla[0]->aSingleTable);
        //return $item[0];

        return response()->json([
            'info' => $info[0],
            'table' => $tabla[0],
            'grafos' => $grafos
        ]);
    }

    public function subirArchivo(Request $request)
    {
        $PcArchivo = $_FILES;
        $LcResp = $this->upload($PcArchivo, 'proyectos/' . $request->id, $request->id);
        return $LcResp;
    }


    public function upload($archivo, $ruta, $nombre)
    {

        $extension = $this->fnExtension($archivo["archivo"]["name"]);


        try {
            //OBTIENE LOS PARAMETROS DE ENTRADA DEL ARCHIVO A SUBIR.

            $PcArchivo = $archivo;

            $target_dir = public_path($ruta);

            return $target_dir;

            if (!is_dir($target_dir)) {
                mkdir($target_dir);
            }

            //PROCEDE A SUBIRLO.		
            if (move_uploaded_file($PcArchivo['archivo']['tmp_name'], $target_dir . "/" . $nombre . $extension)) {


                $LcResp = "OK";
            } else {
                throw new Exception("ERROR");
            }
        } catch (Exception $e) {
            $LcResp = "ERROR";
        }

        return $LcResp;
    }

    public function fnExtension($nombre)
    {
        $last = strrchr($nombre, '.');
        return $last;
    }


    private function creaGeoJSON($type, $name, $crsname, $idGrafo)
    {
        $LcResp = array();
        $LcResp["type"] = $type;
        $LcResp["name"] = $name;
        $LcResp["id"] = $idGrafo;
        $LcResp["crs"] = array();
        $LcResp["crs"]["type"] = $crsname;
        $LcResp["crs"]["properties"] = array();
        $LcResp["crs"]["properties"]["name"] = $crsname;
        $LcResp["properties"]["stroke-width"] = 4;
        $LcResp["properties"]["stroke"] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        $LcResp["features"] = array();
        return $LcResp;
    }

    public function addGeoJSON($type, $id, $name, $coordinates, $json)
    {
        $json["features"][$id] = array();
        $json["features"][$id]["type"] = "Feature";
        $json["features"][$id]["properties"]["id"] = $id;
        $json["features"][$id]["properties"]["name"] = $name;
        $json["features"][$id]["geometry"]["type"] = $type;
        $json["features"][$id]["geometry"]["coordinates"] = $coordinates;
        return $json;
    }

    public function  ComputeGraph(Request $request)
    {
        set_time_limit(3000);
        $PaDataSource = $this->traerDataSource($request->info['idProject']);
        $PiBeta = $request->beta;
        $PiSigma = $request->sigma;
        $PcDistFunction = $request->distFuntion;
        $PcNetType = $request->netType;
        $PcName = $request->nameGrafo;

        $nContador = 0;

        $grafo = Grafo::create([
            'idProyecto' => $request->info['idProject']
        ]);



        // Creates an array to store distances between each pair of points
        $distanceMatrix = $this->MakeDistanceMatrix($PaDataSource, $PcDistFunction);

        //CREAR VARIABLES A USAR
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

        $geoCoordinates = $this->creaGeoJSON('FeatureCollection', $PcName, 'urn:ogc:def:crs:EPSG::8992', $grafo->idGrafo);

        $Pcoordinates = array();

        $cve = 0;


        $debug = "";
        $k = 0;
        // Perform the Test of Region Emptiness
        for ($i = 0; $i < count($PaDataSource); $i++) {
            $debug = $debug . "i=" . $i . "   ";
            $maxColumn = 1 + $i;
            for ($j = 0; $j < $maxColumn; $j++) {
                $debug = $debug . "    j=" . $j . " ";

                if ($i > $j) {
                    $k++;

                    $debug = $debug . "    ENTRE   ";
                    $piX = doubleval($PaDataSource[$i]['NodeX']);
                    $piY = doubleval($PaDataSource[$i]['NodeY']);

                    $pjX = doubleval($PaDataSource[$j]['NodeX']);
                    $pjY = doubleval($PaDataSource[$j]['NodeY']);


                    if ($PiBeta > 0) {
                        if ($PiBeta >= 1) {
                            // Compute the radio of the region of influence
                            $tmp_Bdist_ij = (($PiBeta * $distanceMatrix[$i][$j]) / 2);
                            $tmp_Sdist_ij = ($PiSigma * $distanceMatrix[$i][$j]);

                            // Calculate the coordinates of the centers of two circles ci and cj
                            // who intersection delimits the region of influence of node i and node j
                            $tmp_ci_X = ((((1 - ($PiBeta / 2)) * $piX)) + (($PiBeta / 2) * ($pjX)));
                            $tmp_ci_Y = ((((1 - ($PiBeta / 2)) * $piY)) + (($PiBeta / 2) * ($pjY)));

                            //$tmp_ci_X = ((1-($PiBeta/2)) * $piX) + (($PiBeta/2) * $pjX);
                            //$tmp_ci_Y = (($PiBeta/2)   * $piY) + ((1-($PiBeta/2)) * $pjY);

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
                    for ($k = 0; $k < count($PaDataSource); $k++) {
                        if ($k != $i && $k != $j) {
                            $pkX = doubleval($PaDataSource[$k]['NodeX']);
                            $pkY = doubleval($PaDataSource[$k]['NodeY']);
                            //RECONOCE LA FUNCION HAVERSINE (hk, hm) Y EUCLIDIAN (e)
                            if ($PcDistFunction != 'e') {
                                $tmp_dist_kci = $this->haversineDistance($pkX, $pkY, $tmp_ci_X, $tmp_ci_Y, $PcDistFunction);
                                $tmp_dist_kcj = $this->haversineDistance($pkX, $pkY, $tmp_cj_X, $tmp_cj_Y, $PcDistFunction);
                            } else {
                                $tmp_dist_kci = $this->EuclideanDistance($pkX, $pkY, $tmp_ci_X, $tmp_ci_Y);
                                $tmp_dist_kcj = $this->EuclideanDistance($pkX, $pkY, $tmp_cj_X, $tmp_cj_Y);
                            }

                            if ($tmp_dist_kci > $tmp_dist_kcj) {
                                $current_k_max = $tmp_dist_kci;
                            } else {
                                $current_k_max = $tmp_dist_kcj;
                            }

                            if ($current_k_max <= $tmp_Bdist_ij) {
                                // The point lies withing the region of influence of point i and point j
                                // Do not add an edge
                                $PbDrawCurrentEdge = false;
                            } else {
                                // If the edge is not rejected in the previious test,
                                // perform the second test as follows:
                                if ($PcDistFunction != 'e') {
                                    $tmp_dist_kci = $this->haversineDistance($pkX, $pkY, $piX, $piY, $PcDistFunction);
                                    $tmp_dist_kcj = $this->haversineDistance($pkX, $pkY, $pjX, $pjY, $PcDistFunction);
                                } else {
                                    $tmp_dist_kci = $this->EuclideanDistance($pkX, $pkY, $piX, $piY);
                                    $tmp_dist_kcj = $this->EuclideanDistance($pkX, $pkY, $pjX, $pjY);
                                }

                                if ($tmp_dist_ki < $tmp_Sdist_ij) {
                                    $PbDrawCurrentEdge = false;
                                }

                                if ($tmp_dist_kj < $tmp_Sdist_ij) {
                                    $PbDrawCurrentEdge = false;
                                }
                            }
                        }
                    }


                    if ($PbDrawCurrentEdge) {

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
                        $coordinates[] = array($coord1, $coord2);

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

        $data = [
            'infoProyecto' => $request->info,
            'nBeta' => $PiBeta,
            'nSigma' => $PiSigma,
            'distFunction' => $PcDistFunction,
            'netType' => $PcNetType,
            'distanceMatrix' => $distanceMatrix,
            'PbDrawCurrentEdge' => $PbDrawCurrentEdge,
            'EdgesList' => $edgeList,
            'adjacencyList' => $adjacencyList,
            'geo' => $geoCoordinates,
            'nodes' => $PaDataSource
        ];

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


    private function traerDataSource($idProject)
    {
        $aDataSource = Table::where('idProject', $idProject)->get();
        $aTable = json_decode($aDataSource[0]->aTable);
        $aNodes = json_decode($aDataSource[0]->aNodes);
        $aHeaders = json_decode($aDataSource[0]->aHeaders);

        $positions = array();

        if ($aNodes->id != null) {
            $pos = array_search($aNodes->id, $aHeaders);
            $positions[] = array('name' => $aNodes->id, 'index' => $pos);
        } else {
            $positions[] = array('name' => 'NodeID', 'index' => 0);
        }
        if ($aNodes->name != null) {
            $pos = array_search($aNodes->name, $aHeaders);
            $positions[] = array('name' => $aNodes->name, 'index' => $pos);
        } else {
            $positions[] = array('name' => 'Name', 'index' => '');
        }
        if ($aNodes->x != null) {
            $pos = array_search($aNodes->x, $aHeaders);
            $positions[] = array('name' => $aNodes->x, 'index' => $pos);
        }
        if ($aNodes->y != null) {
            $pos = array_search($aNodes->y, $aHeaders);
            $positions[] = array('name' => $aNodes->y, 'index' => $pos);
        }

        $dataSource = array();
        foreach ($aTable as $item) {
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

    private function MakeDistanceMatrix($dataSource, $distFunction)
    {

        $distance_matrix = array();

        for ($i = 0; $i < count($dataSource); $i++) {
            for ($j = 0; $j < count($dataSource); $j++) {

                if ($i == $j) {
                    $distance_matrix[$i][$j] = 0.000000000000;
                }

                if ($i > $j) {
                    if ($distFunction != 'e') {
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

    private function traerCombinaciones($dataSource)
    {




        $dataCombine = array();
        foreach ($dataSource as $i) {
            $items = array();
            $items[] = array('id' => $i['NodeID'], 'x' => $i['NodeX'], 'y' => $i['NodeY']);
            foreach ($dataSource as $j) {
                if ($j['NodeID'] != $i['NodeID']) {
                    $items[] = array('id' => $j['NodeID'], 'x' => $j['NodeX'], 'y' => $j['NodeY']);
                }
            }
            $dataCombine[] = array($items);
        }
        return $dataCombine;
    }



    private function EuclideanDistance($x1, $y1, $x2, $y2)
    {
        $distance = sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2));
        return $distance;
    }



    function haversineDistance($x1, $y1, $x2, $y2, $type)
    {
        $Rkm = 6371; // Earth's radius in kilometers
        $Rmi = 3958.8;

        $phi1 = deg2rad($y1);
        $phi2 = deg2rad($y2);
        $deltaPhi = ($y2 - $y1) * (3.1415926536 / 180);
        $deltaTheta = ($x2 - $x1) * (3.1415926536 / 180);
        //$λ1 = deg2rad($lon1);
        //$λ2 = deg2rad($lon2);

        $a = pow(sin($deltaPhi / 2), 2) + cos($phi1) * cos($phi2) * pow(sin($deltaTheta / 2), 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        if ($type == 'hk') {
            $distance = $Rkm * $c;
        } else {
            $distance = $Rmi * $c;
        }

        return $distance;
    }


    function RNG_AdjacencyList($vertices)
    {
        $adjacencyList = array();

        for ($i = 0; $i < $vertices; ++$i) {
            $adjacencyList[$i] = array();
        }

        return $adjacencyList;
    }


    function AddEdgeAtEnd(&$adjacencyList, $startVertex, $endVertex, $weight)
    {
        $adjacencyList[$startVertex][] = array($endVertex, $weight);
    }

    function AddEdgeAtBeginning(&$adjacencyList, $startVertex, $endVertex, $weight)
    {
        array_unshift($adjacencyList[$startVertex], array($endVertex, $weight));
    }

    function GetOutwardEdges($adjacencyList, $index)
    {
        return new \SplDoublyLinkedList($adjacencyList[$index]);
    }


    function DescargarTodo(Request $request)
    {



        $jsondata = $request->json()->all();



        $contenido = json_decode($request->grafo['cContenido']);
        $date = new \DateTime($jsondata['grafo']['created_at']);
        $name = $date->format('d-m-Y') . "_" . $jsondata['grafo']['idProyecto'] . "_" . $jsondata['grafo']['idGrafo'] . "_" . $contenido->netType . "_" . $contenido->nBeta . "_" . $contenido->nSigma;




        $data = [
            "adjacencyList" => [
                "headers" => $jsondata['headers']['adjacencyList'],
                "data" => $jsondata['formatedData']['adjacencyList']
            ],
            "Edges" => [
                "headers" => $jsondata['headers']['edges'],
                "data" => $jsondata['formatedData']['edges']
            ],
            "dataOriginal" => [
                "headers" => $jsondata['headers']['dataOriginal'],
                "data" => $jsondata['formatedData']['dataOriginal']
            ],
            "singleTable" => [
                "headers" => $jsondata['headers']['singleTable'],
                "data" => $jsondata['formatedData']['singleTable']
            ]
        ];

        $adjacency = [
            "adjacencyListOriginal" => $jsondata['formatedData']['distanceMatrix']
        ];

        $folderPath = 'exports_graphs' . DIRECTORY_SEPARATOR . $jsondata['grafo']['idGrafo'];

        // Crear la carpeta si no existe
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }

        $fileName = $name . '.xlsx';

        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        $export = new MultiSheetExport($data, $adjacency);
        $export->store($filePath, 'public');

        $geojson = $jsondata['grafo']['cContenido'];
        $geojson = json_decode($geojson);
        $geoJsonData = json_decode(json_encode($geojson), true);
        //return $geoJsonData['geo'];
        // Guardar el archivo GeoJSON
        $geoJsonFileName = $name . '.geojson';
        $geoJsonFilePath = $folderPath . DIRECTORY_SEPARATOR . $geoJsonFileName;
        $geoJsonData = json_encode($geoJsonData['geo']);
        Storage::disk('public')->put($geoJsonFilePath, $geoJsonData);


        // Definir la carpeta de salida para archivos Shapefile
        $shapeFolder = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderPath . DIRECTORY_SEPARATOR . 'shape_file');
        $outputPath = $shapeFolder . DIRECTORY_SEPARATOR . $name . '.shp';
        $inputPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $geoJsonFilePath);


        // Comando para convertir el archivo
        $command = [
            'ogr2ogr',
            '-f',
            'ESRI Shapefile', // Formato de salida
            $shapeFolder, // Carpeta de salida para archivos Shapefile
            $inputPath,  // Archivo GeoJSON de entrada
        ];


        // echo $outputPath;
        // echo "<br>";
        // echo $inputPath;

        // Asegúrate de que la carpeta para los archivos shapefile exista
        if (!file_exists($shapeFolder)) {
            mkdir($shapeFolder, 0777, true); // Crear la carpeta de salida si no existe
        }

        $command = "ogr2ogr --debug ON -f 'ESRI Shapefile' -t_srs EPSG:4326 $outputPath $inputPath";


        $output = shell_exec($command);

          // Crear el archivo ZIP
          $zipFileName = $name . '.zip';
          $zipFilePath = $folderPath . DIRECTORY_SEPARATOR . $zipFileName;

         // Verificar si el archivo ZIP ya existe
         if (file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $zipFilePath))) {
            // Eliminar el archivo existente
            unlink(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $zipFilePath));
            //echo "Archivo ZIP eliminado: " . $zipFilePath . "\n";
        } else {
            //echo "No existe un archivo ZIP previo con el nombre: " . $zipFilePath . "\n";
        }

      

        $crearZip = "zip -r ".storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR .$zipFilePath)." ".storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR .$folderPath);
        shell_exec($crearZip);

     
       


   /*


        $zip = new ZipArchive;
        if ($zip->open(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $zipFilePath), ZipArchive::CREATE) === TRUE) {

            // Crear una carpeta dentro del ZIP
            $folderName = 'shape/';
            $zip->addEmptyDir($folderName);

            // Rutas de archivos shapefile
            $shapeFiles = [
                'shx' => ($shapeFolder . DIRECTORY_SEPARATOR . $name . ".shx"),
                'shp' => ($shapeFolder . DIRECTORY_SEPARATOR . $name . ".shp"),
                'dbf' => ($shapeFolder . DIRECTORY_SEPARATOR . $name . ".dbf"),
                'prj' => ($shapeFolder . DIRECTORY_SEPARATOR . $name . ".prj"),
            ];

            foreach ($shapeFiles as $extension => $filePath) {

                if (!file_exists($filePath)) {
                    throw new \Exception('El archivo ' . basename($filePath) . ' no existe en la ruta: ' . $filePath);
                } else {
                    // Depuración: imprimir la ruta del archivo
                    echo "Agregando archivo: " . $filePath . "\n"; // Imprime la ruta

                    // Agregar el archivo dentro de la carpeta 'shape' en el ZIP
                    $zip->addFile($filePath, $folderName . $name . '.' . $extension);
                }
            }



            $excel = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderPath . "/" . $fileName);


            $zip->addFile($excel, $fileName);
            $zip->addFile(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $geoJsonFilePath), $geoJsonFileName);
            $zip->close();
        } else {
            throw new \Exception('No se pudo crear el archivo ZIP');
        }*/

        // Retornar el archivo ZIP para descargar
        //return response()->download(storage_path('app/public/' . $zipFilePath));

        // Crear la respuesta de descarga
        $response = response()->download(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $zipFilePath));

        // Añadir un header personalizado con el nombre del archivo
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $name . '"');



        return $response;


        return $data;
    }


    public function DescargarProyecto(Request $request)
    {
        return $request->id;
    }
}
