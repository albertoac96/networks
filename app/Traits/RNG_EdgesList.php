class RNG_EdgeList {
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