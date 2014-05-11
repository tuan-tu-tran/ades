<?php 
/**
 * Copyright (c) 2014 Educ-Action
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
include ("inc/prive.inc.php");
include ("inc/fonctions.inc.php");
include ("config/constantes.inc.php");
Normalisation();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
        <title><?php echo ECOLE ?></title>
        <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
        <link media="print" rel="stylesheet" href="config/print.css" type="text/css">
        <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
        <script language="javascript" type="text/javascript" src="inc/fonctions.js">
        </script>
    </head>
    <body>
        <?php
        // autorisations pour la page
        autoriser ("admin");
        // menu
        require ("inc/menu.inc.php");
        ?>
        <div id="texte">
            <h2>Importation d'un fichier CSV</h2>
            <?php
            const csvFile = "../local/eleves.csv";
            $mode = isset($_POST['mode']) ? $_POST['mode'] : Null;

            switch ($mode){
                case 'Confirmer': 
                    // ouvrir la BD
                    include ("config/confbd.inc.php");
                    $lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
                    mysql_select_db ($sql_bdd);

                    $handle = fopen(csvFile, "r");
                    $ligne = 1;
                    $bad_lines=array();
                    $errors=array();
                    $inserts=0;
                    while (($data = fgetcsv($handle, 5000, ",","\"")) !== FALSE) 
                    {
                        $num = count($data);
                        if ($ligne == 1)
                        {
                            // sur la première ligne, on trouve les intitulés des colonnes
                            //bug #10: the csv line may contain an extra 12th empty field
                            if($num==12 && $data[11]==NULL){
                                $pop_last=true;
                                unset($data[11]);
                            }else if($num==11){
                                $pop_last=false;
                            }else{
                                mysql_close($lienDB);
                                exit("Le fichier csv contient $num champs au lieu de 11 ( ou 12 avec un dernier champ vide");
                            }
                            $num = count($data);
                            $debutsql = "INSERT INTO ades_eleves (";
                            for ($i=0; $i < $num; $i++) 
                            {
                                $debutsql .= "$data[$i]";
                                if ($i < $num-1) $debutsql .= ",";
                            }
                            $debutsql .= ") VALUES (";
                        }
                        else
                        {
                            //bug #10: pop the last item if needed
                            if($pop_last && count($data)==12){
                                unset($data[11]);
                            }else{
                                $bad_lines[]="La ligne $ligne du fichier contient ".count($data)." champs au lieu de ".($pop_last?12:11)."<br/>\n";
                                continue;
                            }
                            $num = count($data);
                            // sur les lignes suivantes, on trouve les infos à introduire dans la BD
                            $sql = $debutsql;
                            for ($i=0; $i < $num; $i++) 
                            {
                                $sql .= "'".mysql_real_escape_string($data[$i])."'";
                                if ($i < $num-1) $sql .= ","; else $sql .= ");";
                            }
                            mysql_query($sql);
                            if (mysql_error()) 
                            { 
                                $errors[]=mysql_error() ."<br>\n";  
                                $erreur = true;
                            }
                            else{
                                $inserts++;
                            }
                        }
                        $ligne++;
                    }
                    fclose($handle);
                    mysql_close ($lienDB);
                    if ((!isset($erreur) || $erreur == false) && count($bad_lines)==0)
                    {
                        $texte = "L'importation des données semble s'être bien passée.";
                        redir ("index.php","",$texte, 5000);
                    }
                    else 
                    {
                        echo "<p class='avertissement'>Il s'est produit une erreur durant l'importation.</p>";
                        echo "<p>Le fichier comporte ".($ligne-2)." enregistrements</p>";
                        echo "<p>$inserts enregistrements ont été importés.</p>";
                        echo "<p>".count($bad_lines)." lignes ont été ignorées car elles ne comportent pas le nombre attendu de champs</p>";
                        echo "<p>".count($errors)." erreurs se sont produites avec la base de données</p>";
                        if(count($bad_lines)){
                            echo "<h3>Lignes ignorées</h3>";
                            echo implode("",$bad_lines);
                        }
                        if($errors){
                            echo "<h3>Erreurs db</h3>";
                            echo implode("",$errors);
                        }
                    }
                    break;
                case 'Envoyer':
                    // recopie du fichier sous un nom définitif
                    $nomTemporaire = $_FILES['fichierCSV']['tmp_name'];
                    if( !move_uploaded_file($nomTemporaire, csvFile) )
                    exit("Impossible de copier le fichier.");

                    echo "<div style=\"text-align: center\">\n";
                    echo "<form name=\"form1\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">\n";
                    echo "<p>Le fichier CSV a été transmis au serveur.</p>\n";
                    echo "<p>Veuillez confirmer l'importation des données.</p>\n";
                    echo "<p>\n<input type=\"reset\" name=\"submit\" value=\"Annuler\"";
                    echo "onclick=\"javascript:history.go(-1)\">\n";
                    echo "<input type=\"submit\" value=\"Confirmer\" name=\"mode\"></p>\n";
                    echo "</form>\n";
                    echo "</div>\n";

                    // tableau de prévisualisation
                    ob_start();
                    echo "<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\">\n";
                    $handle = fopen(csvFile, "r");
                    $line=1;
                    $non_fixable=0;
                    $ok_rows="";
                    $not_ok_rows="";
                    while (($data = fgetcsv($handle, 5000, ",","\"")) !== FALSE) 
                    {
                        $num = count($data);
                        if($line==1){
                            $pop_last=$num==12 && $data[11]==NULL;
                            $headers_count=$num;
                        }
                        if($pop_last && $num==12 || $num==11){
                            //ok
                            $ok=true;
                        }else{
                            $non_fixable++;
                            $ok=false;
                        }
                        ob_start();
                        echo "<tr ".($ok?"":"style='background-color:red'").">\n";
                        echo "<td>$line</td>";
                        for ($i=0; $i < $num; $i++) 
                            echo "<td>".$data[$i] . "</td>\n";
                        echo "</tr>\n";
                        $table_row=ob_get_contents();
                        ob_end_clean();
                        if($ok) $ok_rows.=$table_row;
                        else $not_ok_rows.=$table_row;
                        $line++;
                    }
                    fclose($handle);
                    echo $not_ok_rows;
                    echo $ok_rows;
                    echo "</table>\n";
                    $table=ob_get_contents();
                    ob_end_clean();

                    if($headers_count==11){
                        //all good
                    }else if($headers_count==12 && $pop_last){
                        echo "<p><strong>Un 12ème champs vide a été détecté dans le fichier csv. Il sera ignoré lors de l'importation</strong></p>";
                    }else{
                        echo "<p><strong>ATTENTION!!! le fichier importé contient $headers_count champs au lieu de 11</strong></p>";
                    }
                    if($non_fixable){
                        echo "<p><strong>$non_fixable enregistrements ne pourront pas êtres importés</strong></p>";
                    }
                    echo $table;
                    break;
                default:
                    echo "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\" ";
                    echo "name=\"form1\" enctype=\"multipart/form-data\">\n";
                    echo "<input name=\"fichierCSV\" type=\"file\">\n";
                    echo "<input name=\"mode\" value=\"Envoyer\" type=\"submit\">\n";
                    echo "</form>\n";
                    break;
            }
            ?>
        </div>
        <div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
    </body>
</html>
