<?php
class Ordre {
    function __construct($id, $debut, $duree, $prix) {
        $this->id = $id;
        $this->debut = $debut;
        $this->duree = $duree;
        $this->prix = $prix;
    } 
}
?>
