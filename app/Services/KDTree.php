<?php

namespace App\Services;

class KDTree
{
    private $tree;
    private $dimension;

    public function __construct(array $points, $dimension = 2)
    {
        $this->dimension = $dimension;
        $this->tree = $this->buildTree($points, 0);
    }

    private function buildTree(array $points, $depth)
{
    if (empty($points)) {
        return null;
    }

    $axis = $depth % $this->dimension;

    // Agrega el índice al punto
    $pointsWithIndices = array_map(function ($point, $index) {
        $point['index'] = $index; // Agrega el índice
        return $point;
    }, $points, array_keys($points));

    // Ordenar por el eje actual
    usort($pointsWithIndices, function ($a, $b) use ($axis) {
        $key = $axis === 0 ? 'NodeX' : 'NodeY';
        return $a[$key] <=> $b[$key];
    });

    $median = floor(count($pointsWithIndices) / 2);

    return [
        'point' => $pointsWithIndices[$median],
        'left' => $this->buildTree(array_slice($pointsWithIndices, 0, $median), $depth + 1),
        'right' => $this->buildTree(array_slice($pointsWithIndices, $median + 1), $depth + 1),
    ];
}

public function findWithinRadius($target, $radius)
{
    $neighbors = [];
    $this->search($this->tree, $target, $radius, 0, $neighbors);

    // Los vecinos ya incluyen el índice, no necesitas mapear nuevamente
    return $neighbors;
}

    
    

private function search($node, $target, $radius, $depth, &$neighbors)
{
    if (!$node) {
        return;
    }

    $axis = $depth % $this->dimension;
    $dist = $this->distance($node['point'], $target);

    if ($dist <= $radius) {
        $neighbors[] = $node['point'];
    }

    $targetCoord = $target[$axis === 0 ? 'NodeX' : 'NodeY'];
    $nodeCoord = $node['point'][$axis === 0 ? 'NodeX' : 'NodeY'];

    $nextBranch = $targetCoord < $nodeCoord ? $node['left'] : $node['right'];
    $otherBranch = $targetCoord < $nodeCoord ? $node['right'] : $node['left'];

    $this->search($nextBranch, $target, $radius, $depth + 1, $neighbors);

    if (abs($targetCoord - $nodeCoord) <= $radius) {
        $this->search($otherBranch, $target, $radius, $depth + 1, $neighbors);
    }
}


    private function distance($a, $b)
    {
        return sqrt(pow($a['NodeX'] - $b['NodeX'], 2) + pow($a['NodeY'] - $b['NodeY'], 2));
    }
}
