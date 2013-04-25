<?php
/**
 * Defines classes relates with graph.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
 * A node of explaining graph.
 */
class qtype_preg_explaining_graph_tool_node {

    public $shape  = 'ellipse';  // shape of node on image
    public $color  = 'black';    // color of node on image
    public $owner  = NULL;       // owner of node
    public $label  = '';         // data of node on image
    public $id     = -1;         // id of node
    public $fill   = '';         // filling of node on image
    public $invert = FALSE;      // flag of inversion of node

    /**
     * Returns count of links in which node is. Searching executes in owner of node.
     * @param type - boolean parameter; true - node is destination, false - node is source.
     */
    public function links_count($type) {
        $cx = 0; // links counter
        foreach ($this->owner->links as $link) {
            if ($type) {
                if ($link->destination === $this)
                    ++$cx;
            } else {
                if ($link->source === $this)
                    ++$cx;
            }
        }
        return $cx;
    }

    /**
     * Returns array of links in which node is as any instance.
     */
    public function links() {
        $result = array();
        foreach ($this->owner->links as $link) {
            if ($link->destination == $this || $link->source == $this) {
                $result[] = $link;
            }
        }
        return $result;
    }

    public function __construct($lbl, $shp, $clr, &$ownr, $id, $fll = '') {
        $this->label = $lbl;
        $this->shape = $shp;
        $this->color = $clr;
        $this->fill = $fll;
        $this->owner = $ownr;
        $this->id = $id;
    }
}

/**
 * A link of explaining graph.
 */
class qtype_preg_explaining_graph_tool_link {

    public $source = null;      // source of link
    public $destination = null; // destination of link
    public $label = '';         // label of link on image
    public $style = '';       // visual style of link (for image)

    public function __construct($lbl, &$src, &$dst, $stl = 'normal') {
        $this->label = $lbl;
        $this->source = $src;
        $this->destination = $dst;
        $this->style = $stl;
    }
}

/**
 * A subgraph of explaining graph.
 */
class qtype_preg_explaining_graph_tool_subgraph {

    public $label       = '';           // label of subgraph on image
    public $style       = 'solid';      // style of subgraph on image
    public $nodes       = array();      // array of nodes in subgraph
    public $links       = array();      // array of links between nodes in subgraph
    public $subgraphs   = array();      // array of subgraphs in subgraph
    public $entries     = array();      // array if nodes "entries"
    public $exits       = array();      // array of nodes "exits"
    public $id          = -1;

    public function __construct($lbl, $stl, $id = -1) {
        $this->label = $lbl;
        $this->style = $stl;
        $this->id = $id;
    }

    /**
     * Creates text with dot instructions.
     */
    public function create_dot() {
        $this->regenerate_id();
        $instr = 'digraph { compound=true; rankdir = LR;';

        foreach ($this->nodes as $iter) {
            if ($iter->shape == 'record') {
                $instr .= '"nd' .$iter->id . '" [shape=record, color=black, label=' . qtype_preg_explaining_graph_tool_subgraph::compute_html($iter->label, $iter->invert) . $iter->fill . '];';
            } else {
                $instr .= '"nd' . $iter->id . '" [' . ($iter->shape == 'ellipse' ? '' : 'shape=' . $iter->shape . ', ') . 
                    ($iter->color == 'black' ? '' : 'color=' . $iter->color . ', ') . 
                    'label="' . str_replace(chr(10), '', str_replace('"', '\\"', $iter->label[0])) . '"' . $iter->fill . '];';
            }
        }

        foreach ($this->subgraphs as $iter) {
            qtype_preg_explaining_graph_tool_subgraph::process_subgraph($iter, $instr);
        }

        foreach ($this->links as $iter) {
            $instr .= '"nd' . $iter->source->id . '" -> "nd';

            $instr .= $iter->destination->id . '" [label="' . $iter->label . '", arrowhead=' . $iter->style . '];';
        }

        $instr .= '}';

        return $instr;
    }

    /**
     * Creates html of character class for dot instructions
     * @param lbl - label of node in graph
     * @param invert - is charclass invert
     */
    private static function compute_html($lbl, $invert) {
        $elements = array();
        $result = '';
        if (count($lbl)) {
            if (count($lbl) == 1) {
                if ($invert || strlen($lbl[0]) != 1)
                    $elements[] = $lbl[0];
                else
                    return '"' . $lbl[0] . '"';
            } else {
                for ($i = 0; $i < count($lbl); ++$i) {
                        $elements[] = $lbl[$i];
                }
            }

            $result .= '<<TABLE BORDER="0" CELLBORDER="1" CELLSPACING="0" CELLPADDING="4"><TR><TD COLSPAN="';
            $result .= count($elements);
            if ($invert)
                $result .= '"><font face="Arial">' . get_string('explain_any_char_except', 'qtype_preg') . '</font></TD></TR><TR>';
            else
                $result .= '"><font face="Arial">' . get_string('explain_any_char', 'qtype_preg') . '</font></TD></TR><TR>';

            for ($i = 0; $i != count($elements); ++$i) {
                if ($elements[$i][0] == chr(10))
                    $result .= '<TD><font color="blue">' . substr($elements[$i], 1) . '</font></TD>';
                else
                    $result .= '<TD>' . str_replace('"', '&#34;', $elements[$i]) . '</TD>';
            }

            $result .= '</TR></TABLE>>';
        }

        $result = str_replace(']', '&#93;', $result);
        $result = str_replace('[', '&#91;', $result);
        $result = str_replace('\\', '&#92;', $result);
        $result = str_replace('{', '&#123;', $result);
        $result = str_replace('}', '&#125;', $result);

        return $result;
    }

    /**
     * Creates dot instructions for subgraph
     * @param gr - subgraph
     * @param instr - array of dot instructions
     */
    private static function process_subgraph(&$gr, &$instr) {
        $instr .= 'subgraph "cluster_' . $gr->id . '" {';
        $instr .= 'style=' . $gr->style . ';';
        $instr .= 'label="' . $gr->label . '";';

        foreach ($gr->nodes as $iter) {
            if ($iter->shape == 'record')
                $instr .= '"nd' . $iter->id . '" [shape=record, color=black, label=' . qtype_preg_explaining_graph_tool_subgraph::compute_html($iter->label, $iter->invert) . $iter->fill . '];';
            else {
                $instr .= '"nd' . $iter->id . '" [' . ($iter->shape == 'ellipse' ? '' : 'shape=' . $iter->shape . ', ') . 
                    ($iter->color == 'black' ? '' : 'color=' . $iter->color . ', ') . 
                    'label="' . str_replace(chr(10), '', str_replace('"', '\\"', $iter->label[0])) . '"' . $iter->fill . '];';
            }
        }

        foreach ($gr->subgraphs as $iter)
            qtype_preg_explaining_graph_tool_subgraph::process_subgraph($iter, $instr);

        foreach ($gr->links as $iter) {
            $instr .= '"nd' . $iter->source->id . '" -> "nd';

            $instr .= $iter->destination->id . '" [label="' . $iter->label . '", arrowhead=' . $iter->style . '];';
        }

        $instr .= '}';
    }

    private function find_max_id() {
        $maxid = -1;
        foreach ($this->nodes as $node) {
            $maxid = max($maxid, $node->id);
        }

        foreach ($this->subgraphs as $subgraph) {
            $tmpid = $subgraph->find_max_id();
            $maxid = max($maxid, $tmpid);
        }

        return $maxid;
    }

    private function regenerate_id($maxid = -1) {
        $maxid = $maxid == -1 ? $this->find_max_id() : $maxid;

        foreach ($this->nodes as $node) {
            if ($node->id == -1) {
                $node->id = ++$maxid;
            }
        }

        foreach ($this->subgraphs as $subgraph) {
            $maxid = $subgraph->regenerate_id($maxid);
        }

        return $maxid;
    }
}

?>
