<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function print_film($films){
     echo '
                    <div class="gallery">
                        <a href="product.php?id=' . $films->id . '">
                        <img src=' . $films->poster . ' alt="'.$films->titulo.'" width="100" height="100">
                    </a>
                        <div class="desc abbreviative">
                            <a href="product.php?id=' . $films->id . '" title="'.$films->titulo.'">
                            <b>' . $films->titulo . '</b>
                        </a>
                            <br>' . $films->director . '
                            <br>' . $films->precio . ' €
                        </div>
                    </div>';
     return;
}
function print_film_by_id($id, $xml){
    $film = $xml->xpath("/catalogo/pelicula[id='$id']")[0];
    print_film($film);
}

function xml_history_setting($path){
    $xml = new DOMDocument();
    $xml->load($path);
    if($xml->getElementsByTagName('historial')[0])//si estaba creado el xml
        return $xml;
    //si era un xml vacio, creamos el nodo raiz
    $xml_historial = $xml->createElement("historial");
    $xml->appendChild( $xml_historial );

    $xml->save($path);
    return $xml;
}

function add_film_to_history($id, $when, $xml, $path){
    //$xml = simplexml_load_file($path) or die("Error: Cannot create object");
    $xpath = new DOMXPath($xml);
    $root = $xml->getElementsByTagName('historial')[0];
    if(!$root){
        echo "error al añadir pelicula al historial";
        return;
    }
    $node = $xpath->query('//historial/fecha[text()="'.$when.'"]')[0];
    if(!$node){
        $node = $xml->createElement('fecha');
        $node->nodeValue = $when;
        $root->appendChild($node);
    }
    $xml_id = $xml->createElement("id");
    $xml_id->nodeValue = $id;
    $node->appendChild( $xml_id );

    $xml->save($path);
    return $xml;
}

function history_contains_id($path, $id){
    $xml = new DOMDocument();
    $xml->load($path);
    foreach($xml->getElementsByTagName('id') as $elem){
        if($elem->nodeValue == $id) return true;
    }
    return false;
}

function print_history($path){
    $xml = new DOMDocument();
    $xml->load($path);
    $catalogo_xml = simplexml_load_file("../xml/catalogo.xml") or die("Error: Cannot create object");
    $xpath = new DOMXPath($xml);
    foreach ($xml->getElementsByTagName('fecha') as $fecha_node) {
        echo '<div class="history_tag">';
            echo '<p>'.$xpath->query("text()",$fecha_node)[0]->nodeValue.' (<a href="#" class="toggler">expandir</a>)</p>';
            echo '<div class="toggled">';
                foreach ($xpath->query('id', $fecha_node) as $id_node){
                    echo '<div class="responsive">';
                        $film = $catalogo_xml->xpath("/catalogo/pelicula[id=".$id_node->nodeValue."]")[0];
                        print_film($film);//printea la informacion de la pelicula
                    echo '</div>';
                }
            echo '</div>';
        echo '</div>';
    }
    return;
}

?>