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

if($typeimpression == "Paysage")
{
    $pdf=new FPDF('L','mm','A5');
}else{
    $pdf=new FPDF('P','mm','A4');
}
$pdf->AddPage();

if($imageenteteecole) {
    $pdf->Image($imageenteteecole, 15, 10, 40,40, $imgType);
} else {
    $pdf->SetFont('Arial','',14);
    $pdf->SetXY(15,10);
    $pdf->Cell(40,40,"#LOGO", 1, 2, 'C', 0);
}
$pdf->SetFont('Arial','',14);
$pdf->SetXY(90,10);
$pdf->Cell(100,5,$nomecole, 0, 2, 'C', 0);
$pdf->SetXY(90,15);
$pdf->Cell(100,5,$adresseecole, 0, 2, 'C', 0);
$pdf->SetXY(90,20);
$pdf->Cell(100,5,"Téléphone:".$telecole, 0, 2, 'C', 0);

$pdf->SetFont('Arial','',12);
$dt = date("d/m/y");
$pdf->SetXY(140,35);
$pdf->Cell(50,5,$lieuecole.', le '.$dt, 0, 2, 'R');

$pdf->SetFont('','B',24);
$pdf->SetXY(70,45);
$pdf->Cell(110,10, $intitule, 1, 0, 'C');

$pdf->SetXY(10,65);
$pdf->SetFont('', 'B',10);
$chaine = "$prenom $nom en classe de $classe\n";
$pdf->Cell(200,5, $chaine, 0,0,'L');
//$pdf->Write(5, $chaine);

$pdf->SetXY(10,70);
$pdf->SetFont('');
$chaine = "sera en retenue de $duree h ce $dateRetenue à $heure ";
$chaine .= "(local $local) pour le motif suivant\n";
$pdf->Cell(200,5, $chaine, 0,0,'L');
//$pdf->Write(5, $chaine);
$pdf->SetXY(10,75);
$pdf->SetFont('','B',12);
$pdf->Write(5, $motif);
$pdf->SetFont('', '', 10);
$pdf->SetXY(10,90);

$pdf->Write(5, "Matériel à apporter: Journal de classe - ");
$pdf->SetFont('', 'B', 10);
$pdf->Write(5, "Billet de retenue signé");
$pdf->SetFont('', '');
$chaine=" - Matériel scolaire (stylo, bloc de feuilles, etc...)";
if($materiel){
    $chaine.=" - $materiel";
}
$chaine.="\n";
$chaine .= "Travail à réaliser: $travail.\n";

$pdf->Write(5, $chaine);

$pdf->SetXY(10,110);
$pdf->Cell(30,5,$signature1, 0, 0, 'L', 0);
$pdf->SetXY(80,110);
$pdf->Cell(30,5,$signature2, 0, 0, 'L', 0);
$pdf->SetXY(150,110);
$pdf->Cell(30,5,$signature3, 0, 0, 'L', 0);

$pdf->Output();
