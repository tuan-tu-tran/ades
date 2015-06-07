<?php
/**
 * Copyright (c) 2014 Educ-Action
 * Copyright (c) 2014 Tuan-Tu TRAN : added facts grouping
 * 
 * This file is part of ADES.
 * 
 * ADES is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ADES is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with ADES.  If not, see <http://www.gnu.org/licenses/>.
*/
// fonction de comparaison entre deux types de faits
// il faut considérer l'élément ['ordre'] de la liste. "compare" est la fonction callback
// utilisée dans usort (mis en oeuvre dans le constructeur)
function compare ($a, $b)
{
    if ($a['ordre'] == $b['ordre']) return 0;
    return ($a['ordre'] < $b['ordre']) ? -1 : 1;
}

class prototypeFait {
    // $liste est un tableau des types de faits existants, leur présentation et
    // la liste des champs relatifs à chacun de ces faits

    var $descriptionFaits = array();
    var $descriptionChamps = array();
    // --------------------------------------------
    // fonction constructeur
    function __construct()
    {
        // lecture de la description des faits et des détails des champs
        $faits = parse_ini_file("config/descriptionfaits.ini", TRUE);
        $champs = parse_ini_file("config/descriptionchamps.ini", TRUE);

        // Établissement d'un tableau indicé contenant la description des faits
        // dans $this->descriptionFaits
        foreach ($faits as $unFait) {
            $this->descriptionFaits[] = $unFait;
        }
        // la fonction "compare" réalise la comparaison
        // qui permet le tri de la liste des faits sur base du champ "ordre"
        usort ($this->descriptionFaits, "compare");

        // établissement d'un tableau indicé contenant la description des champs
        // dans $this->descriptionChamps
        foreach ($champs as $unChamp) {
            $this->descriptionChamps[] = $unChamp;
        }
    } // typeFait

    function descriptionFaitId($id_TypeFait)
    //-----------------------------------------------------------------------------
    // retourne la liste des caractéristiques du fait $id_TypeFait
    // les faits sont entièrement décrits par leur numéro id_TypeFait
    // un fait est décrit dans une structure du type
    /* Array
    (
    [id_TypeFait] => 0
    [titreFait] => Retard PM
    [couleurFond] => ccaa002
    [couleurTexte] => 000000
    [typeDeRetenue] => 0
    [ordre] => 1
    [listeChamps] => ladate,idorigine,ideleve,idfait,type,qui
    )
    */
    {
        foreach ($this->descriptionFaits as $sousListe)
        {
            if ($sousListe['id_TypeFait'] == $id_TypeFait) {
                return $sousListe;
            }
        }
    } // descriptionFaitNo

    //-----------------------------------------------------------------------------
    function listeChampsFaitId($id_TypeFait)
    // retourne un tableau de la liste des champs pour le fait de numéro $id_TypeFait
    // une liste des champs est une structure du type
    /* Array
    (
    [0] => ladate
    [1] =>  idorigine
    [2] =>  ideleve
    [3] =>  idfait
    [4] =>  professeur
    [5] =>  motif
    [6] =>  type
    [7] =>  qui
    ) */
    {
        $unFait = $this->descriptionFaitId($id_TypeFait);
        $listeChamps = explode (",", $unFait['listeChamps']);
        return $listeChamps;
    } // listeChampsFaitNo

    //-----------------------------------------------------------------------------
    function titreFaitId($id_TypeFait)
    {
        $unFait = $this->descriptionFaitId($id_TypeFait);
        return ($unFait['titreFait']);
    } // titreFaitNo

    function tableauTitresFaits ()
    {
        $titres = array();
        $i=0;
        foreach ($this->descriptionFaits as $unFait){
            $titres[$i]['id_TypeFait']=$unFait['id_TypeFait'];
            $titres[$i]['titreFait']=$unFait['titreFait'];
            $i++;
        }
        return $titres;
    }

    //-----------------------------------------------------------------------------
    function typeRetenueFaitId($id_TypeFait)
    {
        $unFait = $this->descriptionFaitId($id_TypeFait);
        return $unFait['typeDeRetenue'];
    } // typeRetenueFait

    //-----------------------------------------------------------------------------
    function detailDesChampsFaitId($id_TypeFait)
    {
        // extraire la tableau de tous les champs pour le fait de type $id_TypeFait
        $listeChamps = $this->listeChampsFaitNo($id_TypeFait);
        $detail = array();
        foreach ($listeChamps as $unChamp) {
            $detail[] = $this->descriptionChampParNom ($unChamp);
        }
        return $detail;
    } // detailDesChampsDuFait

    //-----------------------------------------------------------------------------
    function descriptionChampParNom($nom)
    // retourne tous les détails du champ dont on fournit le nom
    // se présente comme dans l'exemple ci-dessous
    /* Array
    (
    [champ] => professeur
    [label] => Professeur
    [contextes] => formulaire, tableau, minimum
    [typeDate] => 0
    [typeDateRetenue] => 0
    [typeChamp] => text
    [size] => 20
    [maxlength] => 30
    [colonnes] => 0
    [lignes] => 0
    [classCSS] => obligatoire
    [javascriptEvent] => onChange
    [javascriptCommand] => javascript:this.value=this.value.toUpperCase()
    ) */
    {
        foreach ($this->descriptionChamps as $sousListe) {
            if ($sousListe['champ'] == trim($nom)) {
                return ($sousListe);
            }
        }
    } //descriptionChampParNom

    //-----------------------------------------------------------------------------
    function detailDesChampsPourContexte ($id_TypeFait, $contexte)
    {
        // extraire la tableau de *tous* les champs pour le fait $n
        $listeChamps = $this->listeChampsFaitId($id_TypeFait);
        $detail = array();
        // sélection des chamsps qui doivent apparaÃ®tre dans le "contexte"
        foreach ($listeChamps as $unChamp) {
            $descriptionChamp = $this->descriptionChampParNom ($unChamp);
            // si le champ doit apparaître dans le contexte précisé, on l'ajoute à la série
            if (strpos($descriptionChamp['contextes'], $contexte)!==FALSE) {
                $detail[] = $descriptionChamp;
            }
        }
        return $detail;
    } // detailDesChampsPourContexte

    //-----------------------------------------------------------------------------
    // retourne une ligne de tableau HTML contenant les titres du tableau
    // pour un fait de la fiche disciplinaire
    function htmlTitreColonnesTableau ($id_TypeFait, $icones=true, $author=FALSE)
    {
        $liste = $this->detailDesChampsPourContexte($id_TypeFait, 'tableau');
        $html = "<tr>\n";
        if ($this->typeRetenueFaitId($id_TypeFait) && $icones)
        $html .= "\t<td>&nbsp;</td>\n";
        foreach ($liste as $champ) {
            $html .= "\t<td>{$champ['label']}</td>\n";
        }
        if($author){
            $html.="\r<td>Auteur</td>\n";
        }
        // une colonne pour icônes édition et suppression
        if ($icones) {
            $html .= "\t<td>&nbsp;</td>\n";
        }
        $html.="</tr>";
        return $html;
    }

    //-----------------------------------------------------------------------------
    // retourne une ligne de tableau HTML contenant les noms des champs pour 
    // un tableau dans la fiche disciplinaire
    function htmlChampsTableau ($id_TypeFait, $icones=true, $author=FALSE)
    {
        $liste = $this->detailDesChampsPourContexte($id_TypeFait, 'tableau');
        $html = "<tr>\n";
        if ($this->typeRetenueFaitId($id_TypeFait) && $icones) {
            $html .= "<td width=\"16px\">##PRINT##</td>\n";
        }
        foreach ($liste as $champ) {
            $html .= "\t<td>##".$champ['champ']."##</td>\n";
        }
        if($author){
            $html.="\r<td>##AUTHOR##</td>\n";
        }
        // une colonne pour icônes édition et suppression
        if ($icones) {
            $html .= "\t<td width=\"32px\">##ED####SUP##</td>\n";
        }
        $html .= "</tr>\n";
        return $html;
    }

    function cssOngletFaitDisciplinaire ($id_TypeFait)
    {
        $description = $this->descriptionFaitId($id_TypeFait);
        $style = "background-color:#". ($description['couleurFond']).";";
        $style .= " color:#". ($description['couleurTexte']).";";
        $style = " style=\"$style\"";
        return $style;
    }

    function cssTableauFaitDisciplinaire ($id_TypeFait)
    {
        $css = $this->cssOngletFaitDisciplinaire ($id_TypeFait);
        return $css;
    }
}
