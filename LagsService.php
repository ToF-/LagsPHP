<?php

require_once("Ordre.php");

class LagsService {

        function __construct() {
            $this->ListOrdre = array();
        }

        // lit le fihier des ordres et calcule le CA
        function getFichierOrder($filename) {
            // The nested array to hold all the arrays
            $the_big_array = [];
            $h = fopen($filename, "r");
            if($h) {
                while (($data = fgetcsv($h, 50, ";")) !== FALSE)
                {
                    $the_big_array[] = $data;
                }
                fclose($h);
                foreach($the_big_array as $champs) {
                    $chp1 = $champs[0];
                    $chp2 = intval($champs[1]);
                    $champ3 = intval($champs[2]);
                    $chp4 = floatval($champs[3]);
                    $ordre = new Ordre($chp1, $chp2, $champ3, $chp4);
                    array_push($this->ListOrdre, $ordre);
                }
            }
            else{
               print("FICHIER ORDRES.CSV NON TROUVE.CREATION FICHIER."); 
               $this->writeOrdres($filename);
            }

        }

        // écrit le fichier des ordres
        function WriteOrdres($nomFich) {
            $fp = fopen($nomFich,"w");
            foreach($this->ListOrdre as $ordre) {
                $fields = array();
                array_push($fields, $ordre->id);
                array_push($fields, strval($ordre->debut));
                array_push($fields, strval($ordre->duree));
                array_push($fields, strval($ordre->prix));
                fputcsv($fp, $fields, ";");
            }
            fclose($fp);
        }
        // affiche la liste des ordres

        function Liste() {
            print("LISTE DES ORDRES\n");
            printf("%-8s %7s %5s %10s\n", "ID", "DEBUT", "DUREE", "PRIX");
            printf("%-8s %7s %5s %10s\n", "--", "-------", "-----", "-----------");
            foreach($this->ListOrdre as $ordre) {
                $this->AfficherOrdre($ordre);
            }
            printf("%-8s %7s %5s %10s\n", "--", "-------", "-----", "-----------");
        }
        function AfficherOrdre($ordre) {
            printf("%-8d %7d %5d %10.2f\n", $ordre->id, $ordre->debut, $ordre->duree, $ordre->prix);

        }
        // Ajoute un ordre; le CA est recalculé en conséquence

        function AjouteOrdre() {
            print("AJOUTER UN ORDRE\n");
            print("FORMAT = ID;DEBUT;FIN;PRIX\n");
            $line = readline();
            $champs = explode(";", $line);
            var_dump($champs);
            $id = $champs[0];
            $dep = intval($champs[1]);
            $dur = intval($champs[2]);
            $prx = floatval($champs[3]);
            $ordre = new Ordre($id, $dep, $dur, $prx);
            array_push($this->ListOrdre, $ordre);
            $this->writeOrdres("ORDRES.CSV");
        }
        //public void CalculerLeCA()
        //{
        //    print("CALCUL CA..\n");
        //    $laListe = usort($laListe,compareur);
        //    $ca = CA($laListe);
        //    printf("CA:%10.2f\n"$ca);
        //}

        function prochain_compat($j, $ordre) {
            for($k = $j+1; $k<count($this->ListOrdre); $k++) {
                if($this->ListOrdre[$k]->debut >= ($ordre->debut+$ordre->duree))
                    return $k;
            }
            return count($this->ListOrdre)-1;
        } 

        function CA($ordres, $debug) {

            // is aucun ordre, job done, TROLOLOLO..
            if(count($ordres) == 0) {
                return 0.0;
            }
            $ordre = $ordres[0];
            // attention ne marche pas pour les ordres qui depassent la fin de l'année 
            // voir ticket PLAF nO 4807 

            // ajoute un ordre super loin en fin de liste
            //
            array_push($this->ListOrdre, new Ordre(0,9999999,0,0));
            $cas = array();
            foreach($this->ListOrdre as $ordre) {
                array_push($cas,$ordre->prix);
            }
            var_dump($cas);

            for($i = count($this->ListOrdre)-2; $i>=0; $i--) {
                $ordre = $this->ListOrdre[$i];
                $k = $this->prochain_compat($i,$ordre);
                $ca_plus_prochain = $cas[$k] + $ordre->prix;
                $ca_suivant = $cas[$i+1];
                $cas[$i] = ($ca_plus_prochain > $ca_suivant ? $ca_plus_prochain : $ca_suivant);
                // Lapin compris?
                if($debug) {
                    printf("%d,%d %10.2f\n", $i,$k, $cas[$i]);
                }
                else {
                    print(".");
                }
            }
            unset($this->ListOrdre[count($this->ListOrdre)-1]);
            return $cas[0]; // LOL
        }

        function CalculerLeCA($debug) {
            print("CALCUL CA..\n");
            function compareur($ordre1, $ordre2) {
                if ($ordre1->debut == $ordre2->debut) {
                    return 0;
                }
                return ($ordre1->debut < $ordre2->debut) ? -1 : 1;
            }
            usort($this->ListOrdre, "compareur");
            $this->Liste();
            $ca = $this->CA($this->ListOrdre, $debug);
            printf("CA: %10.2f\n", $ca);
        }

        // MAJ du fichier

        function Suppression() {
            print("SUPPRIMER UN ORDRE\n");
            print("ID:");
            $id = strtoupper(readline());
            for($i = 0; $i < count($this->ListOrdre); $i++) {
                if($this->ListOrdre[$i]->id=$id)
                    unset($this->ListOrdre[$i]);
            }
            writeOrdres("ORDRES.CSV");

        }
}


$lagsService = new LagsService();
$lagsService->getFichierOrder("ORDRES.CSV");
$lagsService->Liste();
$lagsService->CalculerLeCA(true);

?>
