<?php

namespace Clue;

use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;

class GraphComposer
{
    /**
     * 
     * @param string $dir
     * @return \Fhaculty\Graph\Graph
     */
    public function createGraph($dir)
    {
        $analyzer = new \JMS\Composer\DependencyAnalyzer();
        $dependencyGraph = $analyzer->analyze($dir);
        
        $graph = new Graph();
        
        foreach ($dependencyGraph->getPackages() as $package) {
            $name = $package->getName();
            $start = $graph->createVertex($name, true);
            
            $label = $name;
            if ($package->getVersion() !== null) {
                $label .= ': ' . $package->getVersion();
            }
            
            $start->setLayout(array(
                'label' => $label,
                'fillcolor' => '#eeeeee',
                'style' => 'filled',
                'shape' => 'box'
            ));
        
            foreach ($package->getOutEdges() as $requires) {
                $targetName = $requires->getDestPackage()->getName();
                $target = $graph->createVertex($targetName, true);
                
                $label = $requires->getVersionConstraint();
                
                $start->createEdgeTo($target)->setLayout(array(
                    'label' => $label,
                    'fontcolor' => '#999999',
                    'fontsize' => 10
                ));
            }
        }
        
        $graph->getVertex($dependencyGraph->getRootPackage()->getName())->setLayout(array(
            'fontcolor' => 'red'
        ));
        
        return $graph;
    }
        
    public function displayGraph($dir)
    {
        $graph = $this->createGraph($dir);
        
        $graphviz = new GraphViz($graph);
        $graphviz->setFormat('svg');
        $graphviz->display();
    }
}
