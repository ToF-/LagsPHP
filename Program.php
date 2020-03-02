<?php
require_once("LagsService.php");

class Program {

    function __construct() {
        $this->debut = true;
    }

    function main() {
        $service = new LagsService();
        $service->getFichierOrder("ORDRES.CSV");
        $flag = false;
        // tant que ce n'est pas la fin du programme
        while (!$flag) {
            // met la commande a Z
            $commande = 'Z';
            while($commande != 'A' && $commande != 'L' && $commande != 'S' && $commande != 'Q' && $commande != 'C') {
                print("A)JOUTER UN ORDRE  L)ISTER   C)ALCULER CA  S)UPPRIMER  Q)UITTER\n");
                $line = readline();
                $commande = substr(strtoupper($line),0,1);

            }
            switch($commande) {
            case 'Q':
            {
                $flag = true;
                break;
            }
            case 'L':
            {
                $service->Liste();
                break;
            }
            case 'A':
            {
                $service->AjouterOrdre();
                break;
            }
            case 'S':
            {
                $service->Suppression();
                break;
            }
            case 'C':
            {
                $service->CalculerLeCA(debug);
                break;
            }
            }
        }

    }
}

$programme = new Program();
$programme->main();

?>
