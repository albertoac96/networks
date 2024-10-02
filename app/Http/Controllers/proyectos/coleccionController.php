<?php

namespace App\Http\Controllers\proyectos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Coleccion;
use App\Models\relProjectCol;
use App\Models\Grafo;

use Illuminate\Support\Facades\Storage;


use Maatwebsite\Excel\Excel;
use App\Exports\MultiSheetExport;

use ZipArchive;

use STS\ZipStream\Facades\Zip;

class coleccionController extends Controller
{
    public function listColeccion()
    {
        $LcResp = Coleccion::where('cEstatus', 'A')->get();
        return $LcResp;
    }

    public function addColeccion(Request $request)
    {
        $cName = $request->name;
        $cDescripcion = $request->desc;
        $uuid = uniqid();

        $item = Coleccion::create([
            'uuid' => $uuid,
            'cNombre' => $cName,
            'cDescripcion' => $cDescripcion,
            'idUsrAlta' => Auth::id()
        ]);

        $LcResp = Coleccion::where('cEstatus', 'A')->get();
        return $LcResp;
    }

    public function updateColeccion(Request $request)
    {
        $cName = $request->nombre;
        $cDescripcion = $request->desc;

        Coleccion::where('idColeccion', $request->idColeccion)->update([
            'cNombre' => $cName,
            'cDescripcion' => $cDescripcion
        ]);
    }

    public function estatusColeccion($id, $estatus)
    {
        Coleccion::where('idColeccion', $id)->update([
            'cEstatus' => $estatus
        ]);
    }

    public function publicColeccion($id, $public)
    {
        Coleccion::where('idColeccion', $id)->update([
            'iPublic' => $public
        ]);
    }

    public function addRelProject($idCol, $idProject)
    {
        // Verificar si ya existe la relación
        $exists = relProjectCol::where('idProject', $idProject)
            ->where('idColeccion', $idCol)
            ->exists();

        // Si no existe, crear la relación
        if (!$exists) {
            relProjectCol::create([
                'idProject' => $idProject,
                'idColeccion' => $idCol,
                'idUsrAlta' => Auth::id()
            ]);
        } else {
            // Opcional: manejar el caso en que ya existe la relación
            // Puedes lanzar una excepción, retornar un mensaje o simplemente no hacer nada
            return response()->json(['message' => 'La relación ya existe'], 409);
        }
    }

    public function deleteRelProject($idRel)
    {
        // Buscar la relación
        $relation = relProjectCol::where('idRel', $idRel)->first();
        // Verificar si la relación existe
        if ($relation) {
            // Eliminar la relación
            $relation->delete();
            return response()->json(['message' => 'Relación eliminada con éxito'], 200);
        } else {
            // Opcional: manejar el caso en que no existe la relación
            return response()->json(['message' => 'La relación no existe'], 404);
        }
    }

    public function colPrueba()
    {
        // Paso 1: Consulta para obtener los proyectos
        $projects = DB::table('projects')
            ->whereIn('idProject', [23, 26, 30, 34, 35])
            ->get();

        $filteredGraphs = $this->verGrafo($projects[0]->idProject);

        $projects[0]->grafos = $filteredGraphs;




        return $projects;
    }

    public function verGrafo($idProyecto)
    {
        $graphs = DB::table('grafos')
            ->where('idProyecto', $idProyecto)
            ->get();


        $filteredGraphs = [];
        foreach ($graphs as $graph) {
            // Suponiendo que cContenido es un JSON y quieres extraer ciertos valores
            $contenidoArray = json_decode($graph->cContenido, true); // Decodificar cContenido

            // Extrae solo los valores que necesitas de cContenido
            $selectedValues = [
                'distFunction' => $contenidoArray['distFunction'] ?? null, // Cambia 'campo1' por el nombre del campo que necesites
                'nBeta' => $contenidoArray['nBeta'] ?? null, // Cambia 'campo2' por el nombre del campo que necesites
                'netType' => $contenidoArray['netType'] ?? null,
                // Añade más campos según sea necesario
            ];

            // Guardar el id del grafo y los valores seleccionados
            $filteredGraphs[] = [
                'id' => $graph->idGrafo, // Guardar el id del grafo
                'created_at' => $graph->created_at,
                'cContenido' => $selectedValues, // Guardar los valores seleccionados
            ];
        }


        return $filteredGraphs;
    }

    public function descargar(Request $request)
    {
        $PcTipo = $request->tipo;
        $PoItem = $request->item;
        $idGrafo = $PoItem["id"];

        $grafo = Grafo::where('idGrafo', $idGrafo)->get();

        $cGrafo = $grafo[0];
        $cGrafo->cContenido = json_decode($cGrafo->cContenido, true);



        if ($PcTipo == "csv") {
            return $cGrafo->cContenido["nodes"];
        }
        if ($PcTipo == "json") {
            return $cGrafo->cContenido["geo"];
        }
        if ($PcTipo == "shape") {

            return $this->descargarShape($cGrafo);
        }
        if ($PcTipo == "xlsx") {

            $filePath = $this->descargarExcel($cGrafo);
            return Storage::download('public' . DIRECTORY_SEPARATOR . $filePath);
        }
        if ($PcTipo == "zip") {

            return $this->descargarZip($cGrafo);
        }
    }

    public function descargarShape($grafo)
    {
        $folderPath = 'exports_graphs' . DIRECTORY_SEPARATOR . $grafo['idGrafo'];
        $contenido = $grafo->cContenido;
        $date = new \DateTime($grafo['created_at']);
        $name = $contenido['netType'] . "_" . $contenido['nBeta'] . "_" . $date->format('Y-m-d');
    
        // Crear la carpeta si no existe
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }
    
        // Guardar GeoJSON y obtener ruta de nombre para conversión
        $geoJsonFilePath = $this->guardarGeoJSON($name, $folderPath, $contenido['geo']);
    
        // Convertir GeoJSON a Shapefile
        $shapeFolder = $this->convertToShp($folderPath, $name, $geoJsonFilePath);
    
        // Ruta del ZIP
        $zipFileName = $name . '_shp';
        $zipFilePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderPath . DIRECTORY_SEPARATOR . $zipFileName);
    
        $zip = new ZipArchive;
    
        // Abre o crea el archivo ZIP
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
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
                    $zip->addFile($filePath, $name . '.' . $extension);
                }
            }
    
            // Cerrar el archivo ZIP
            if (!$zip->close()) {
                throw new \Exception('Error al cerrar el archivo ZIP');
            }
        } else {
            throw new \Exception('No se pudo abrir o crear el archivo ZIP: ' . $zipFilePath);
        }


        Zip::create("package.zip", [
            $shapeFolder . DIRECTORY_SEPARATOR . $name . ".shx" =>  $name . ".shx",
            $shapeFolder . DIRECTORY_SEPARATOR . $name . ".shp" =>  $name . ".shp",
            $shapeFolder . DIRECTORY_SEPARATOR . $name . ".dbf" =>  $name . ".dbf",
            $shapeFolder . DIRECTORY_SEPARATOR . $name . ".prj" =>  $name . ".prj",
        ])->saveTo(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderPath));
    
        // Crear la respuesta de descarga
        $response = response()->download($zipFilePath);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $zipFileName . '"');
    
        return $response;
    }
    
    
    


    public function descargarExcel($grafo)
    {

        $folderPath = 'exports_graphs' . DIRECTORY_SEPARATOR . $grafo['idGrafo'];
        $contenido = $grafo->cContenido;
        $date = new \DateTime($grafo['created_at']);
        $name = $contenido['netType'] . "_" . $contenido['nBeta'] . "_" . $date->format('Y-m-d');

        $fileName = $name . '.xlsx';

        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        $idProject = $contenido['infoProyecto']['idProject'];

        $datos = DB::select('select * from grafos where idProyecto =' . $idProject);
        $datos = $datos[0];
        //jsondata = json_decode($datos->aHeaders);
        //return $datos;
        $headers = json_decode($datos->headers);
        $formatedData = json_decode($datos->formatedData);
        //return $headers->adjacencyList;

        $data = [
            "Original_Data" => [
                "headers" => $headers->dataOriginal,
                "data" => $formatedData->dataOriginal
            ],
            "Nodes" => [
                "headers" => $headers->singleTable,
                "data" => $formatedData->singleTable
            ],
            "Adjacency_List" => [
                "headers" => $headers->adjacencyList,
                "data" => $formatedData->adjacencyList
            ],
            "Edges" => [
                "headers" => $headers->edges,
                "data" => $formatedData->edges
            ],

        ];

        $adjacency = [
            "adjacencyListOriginal" => $formatedData->distanceMatrix
        ];

        // Crear la carpeta si no existe
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }



        $export = new MultiSheetExport($data, $adjacency);
        $export->store($filePath, 'public');

        return $filePath;

        //return Storage::download('public' . DIRECTORY_SEPARATOR . $filePath);
    }

    public function guardarGeoJSON($name, $folderPath, $geo){
        // Guardar el archivo GeoJSON
        $geoJsonFileName = $name . '.geojson';
        $geoJsonFilePath = $folderPath . DIRECTORY_SEPARATOR . $geoJsonFileName;
        $geoJsonData = json_encode($geo);
        Storage::disk('public')->put($geoJsonFilePath, $geoJsonData);
        return $geoJsonFilePath;
    }

    public function convertToShp($folderPath, $name, $geoJsonFilePath){
         // Definir la carpeta de salida para archivos Shapefile
         $shapeFolderPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderPath . DIRECTORY_SEPARATOR . 'shape_file');
    
         // Asegúrate de que la carpeta para los archivos shapefile exista
         if (!file_exists($shapeFolderPath)) {
             mkdir($shapeFolderPath, 0777, true); // Crear la carpeta de salida si no existe
         }
     
         // Definir la ruta de salida (solo el directorio, sin extensión)
         $outputPath = $shapeFolderPath; // Sin la extensión
     
         // Ejecutar el comando ogr2ogr
         $inputPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $geoJsonFilePath);
         $command = "ogr2ogr -f 'ESRI Shapefile' -t_srs EPSG:4326 $outputPath $inputPath"; // Redirigir errores
         $output = shell_exec($command);
         return $outputPath;
    }


    public function descargarZip($grafo)
{
    $folderPath = 'exports_graphs' . DIRECTORY_SEPARATOR . $grafo['idGrafo'];
    $contenido = $grafo->cContenido;
    $date = new \DateTime($grafo['created_at']);
    $name = $contenido['netType'] . "_" . $contenido['nBeta'] . "_" . $date->format('Y-m-d');

   
    // Crear la carpeta si no existe
    if (!Storage::disk('public')->exists($folderPath)) {
        Storage::disk('public')->makeDirectory($folderPath);
    }

    

    $filePathExcel = $this->descargarExcel($grafo);

    // Guardar GeoJSON y obtener ruta de nombre para conversión
    $geoJsonFilePath = $this->guardarGeoJSON($name, $folderPath, $contenido['geo']);
    
    // Convertir GeoJSON a Shapefile
    $shapeFolder = $this->convertToShp($folderPath, $name, $geoJsonFilePath);
    

    
    // Ruta del ZIP
    $zipFileName = $name;
    $zipFilePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderPath . DIRECTORY_SEPARATOR . $zipFileName . ".zip");

    $zip = new ZipArchive;

    // Abre o crea el archivo ZIP
    if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {


        $zip->addFile(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR .$filePathExcel), $name . '.xlsx');
         $zip->addFile(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR .$geoJsonFilePath), $name . '.geojson');

         // Verificar y agregar el archivo Excel
        //if (file_exists(storage_path($filePathExcel))) {
            //$zip->addFile(storage_path($filePathExcel), $name . '.xlsx');
        //} else {
            //throw new \Exception('El archivo Excel ' . $name . '.xlsx no existe en la ruta: ' . storage_path($filePathExcel));
        //}

         // Crear una carpeta dentro del ZIP
         $zip->addEmptyDir('shape'); // Crea una carpeta principal

         

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
                $zip->addFile($filePath, 'shape/' . $name . '.' . $extension);
            }
        }

        // Cerrar el archivo ZIP
        if (!$zip->close()) {
            throw new \Exception('Error al cerrar el archivo ZIP');
        }
    } else {
        throw new \Exception('No se pudo abrir o crear el archivo ZIP: ' . $zipFilePath);
    }

    // Crear la respuesta de descarga
    $response = response()->download($zipFilePath);
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $zipFileName . '"');

    return $response;
}

    
}
