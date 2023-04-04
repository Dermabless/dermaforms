<?php

namespace app\controllers;

use yii\web\Controller; 
use Yii;
use rudissaar\fpdf\FPDF;
use yii\filters\Cors;
use app\models\Generate;
use app\models\Micelania;
use app\models\Microblading;

class GenerateController extends Controller
{
    public function beforeAction($action)
    {
        // if ($action->id == 'index' || $action->id == "micelania") {
        // }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    'Origin'                           => ["http://localhost", "https://makingchanges.prodev.app"],
                    'Access-Control-Request-Method'    => ['POST', 'GET'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age'           => 3600,
                ],
            ],

        ]);
    }

    public function actionIndex()
    {
        $header = header('Access-Control-Allow-Origin: *');
        
        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $default = "No";

            $uploadedImgPath = Yii::getAlias('@uploads') . "/imgs/pestanas/";
            if (!file_exists( $uploadedImgPath )) {
                mkdir($uploadedImgPath, 0777, true);
            }

            $inicioImg = \yii\web\UploadedFile::getInstanceByName('inicio');
            $inicioImgName = $uploadedImgPath . time() . "_inicio_" . $inicioImg->name;
            $inicioImg->saveAs( $inicioImgName );

            $mapingImg = \yii\web\UploadedFile::getInstanceByName('maping');
            $mapingImgName = $uploadedImgPath . time() . "_maping_" . $mapingImg->name;
            $mapingImg->saveAs( $mapingImgName );
            
            $eyesImg = \yii\web\UploadedFile::getInstanceByName('eyes');
            $eyesImgName = $uploadedImgPath . time() . "_eyes_" . $eyesImg->name;
            $eyesImg->saveAs( $eyesImgName );

            $finalImg = \yii\web\UploadedFile::getInstanceByName('final');
            $finalImgName = $uploadedImgPath . time() . "_final_" . $finalImg->name;
            $finalImg->saveAs( $finalImgName );

            $pdfPath = Yii::getAlias('@uploads') . "/pdf/pestanas/";
            if (!file_exists( $pdfPath )) {
                mkdir($pdfPath, 0777, true);
            }
            $pdfPath .= '/' . $data["name"] . " " . date("Y-m-d", strtotime($data["date"])) . ".pdf";
            
            $imgDirPath = Yii::getAlias('@uploads') . '/../images/themes/';
            $pdf = new FPDF();
            $pdf->setTitle('Frame PESTANAS');

            $pdf->AliasNbPages();
            $pdf->AddPage('P', 'A4');
            $pdf->SetMargins('12', '7', '10');
            
            //set layout images
            $pdf->Image( $imgDirPath . 'top-left.png', 5, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 11.5, 42, 0.5);
            $pdf->Image( $imgDirPath . 'horizontals.png', 123, 11.5, 45, 0.5);
            $pdf->Image( $imgDirPath . 'top-right.png', 165, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'verticals.png', 5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'verticals.png', 204.5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'bottom-left.png', 5, 250, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 289.6, 125, 0.5);
            $pdf->Image( $imgDirPath . 'bottom-right.png', 165, 250, 40, 40);
            
            // set mark iamge
            $pdf->Image( $imgDirPath . 'mark.png', 75, 3.5, 60, 45);
            
            // Add fonts
            $pdf->SetTextColor(184, 142, 140);
            $pdf->AddFont('Montserrat','','Montserrat.php');
            $pdf->AddFont('NunitoExtraLight','','Nunito-ExtraLight.php');
            $pdf->AddFont('NunitoRegular','','Nunito-Regular.php');

            $pdf->SetFont('Montserrat', '', 30);
            $pdf->Cell(0, 91, utf8_decode('PESTAÑAS'), 0, 0, 'C');

            // $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('NunitoExtraLight', '', 18);
            $pdf->text(20, 67, $data["name"]);
            
            $pdf->SetFont('NunitoExtraLight', '', 13);
            $pdf->text(20, 74, "Procedimiento: " . (isset($data["procedimiento"]) ? $data["procedimiento"] : ""));
            $pdf->text(20, 80, "Modificacion: " . (isset($data["modificacion"]) ? $data["modificacion"] : $default));
            $pdf->text(20, 86, "Fecha Procedimiento: " . $data["date"]);
            $pdf->text(20, 92, utf8_decode("Hora Inicio y Conclusión: ") . date("H:i", strtotime($data["time"])) . " - " . date("H:i", strtotime($data["send_time"])));
            $pdf->text(20, 98, "Consentimiento actualizado: " . (isset($data["consentimiento"]) ? $data["consentimiento"] : ""));
            $pdf->text(20, 104, "Alergia: " . (isset($data["alergia"]) ? utf8_decode($data["alergia"]) : ""));
            
            $pdf->text(20, 113, "FOTOS DEL PROCEDIMIENTO");
            $pdf->Image( $inicioImgName, 20, 116, 80, 60);
            $pdf->Image( $mapingImgName, 106, 116, 80, 60);

            $pdf->SetFont('NunitoRegular', '', 11);
            $pdf->text(50, 182, "Inicial Pic");
            $pdf->text(136, 182, "Maping Pic");

            $pdf->Image( $eyesImgName, 20, 186, 80, 60);
            $pdf->Image( $finalImgName, 106, 186, 80, 60);
            
            $pdf->text(50, 252, "Eyes Pic");
            $pdf->text(136, 252, "Final Pic");

            $pdf->setFontSize(13);
            $pdf->text(20, 259, "Productos Mtto Comprados: " . (isset($data["productos_mtto"]) ? utf8_decode($data["productos_mtto"]) : "") );
            $pdf->text(20, 266, "Productos Adicionales Comprados: " . (isset($data["servicio_agendados"]) ? utf8_decode($data["servicio_agendados"]) : "") );
            $pdf->text(20, 273, "Servicios Agendados: " . (isset($data["servicio_adiciona"]) ? utf8_decode($data["servicio_adiciona"]) : "") );

            $pdf->Output($pdfPath, 'F');

            $saveAry = array(
                'name' => $data["name"],
                'generate_date' => date("Y-m-d", strtotime($data["date"])),
                'generate_time' => date("H:i:s", strtotime($data["time"])),
                'send_time' => date("H:i:s", strtotime($data["send_time"])),
                'procedimiento' =>  (isset($data["procedimiento"]) ? $data["procedimiento"] : ""),
                'modificacion' => (isset($data["modificacion"]) ? $data["modificacion"] : $default),
                'consentimiento' => (isset($data["consentimiento"]) ? $data["consentimiento"] : ""),
                'alergia' => (isset($data["alergia"]) ? $data["alergia"] : ""),
                'productos_comprados' => (isset($data["productos_mtto"]) ? utf8_encode($data["productos_mtto"]) : ""),
                'servicio_adiciona' => (isset($data["servicio_adiciona"]) ? utf8_encode($data["servicio_adiciona"]) : ""),
                'servicio_agendados' => (isset($data["servicio_agendados"]) ? utf8_encode($data["servicio_agendados"]) : ""),
                'pdflink' => $pdfPath,
                'inicio' => $inicioImgName,
                'maping' => $mapingImgName,
                'eyes' => $eyesImgName,
                'final' => $finalImgName,
            );

            $model = Generate::find()->where([ 'name' => $saveAry["name"], 'generate_date' => $saveAry["generate_date"] ])->one(); 
        
            if (!$model) {
                $model = new Generate();
            }
        
            $model->name = $saveAry["name"];
            $model->generate_date = $saveAry["generate_date"];
            $model->generate_time = $saveAry["generate_time"];
            $model->procedimiento = $saveAry["procedimiento"];
            $model->modificacion = $saveAry["modificacion"];
            $model->consentimiento = $saveAry["consentimiento"];
            $model->alergia = $saveAry["alergia"];
            $model->productos_comprados = $saveAry["productos_comprados"];
            $model->servicio_adiciona = $saveAry["servicio_adiciona"];
            $model->servicio_agendados = $saveAry["servicio_agendados"];
            $model->pdflink = $saveAry["pdflink"];
            $model->inicio = $saveAry["inicio"];
            $model->maping = $saveAry["maping"];
            $model->eyes = $saveAry["eyes"];
            $model->final = $saveAry["final"];
            $model->send_time = $saveAry["send_time"];
            // $model->load($saveAry);
            // print_r($model);exit;
            $model->save();
         
            exit(json_encode(["status" => "success", "message" => "Request is successfully submitted"]));
        }
        exit(json_encode(["status" => "error", "message" => "Request is failed"]));
    }

    public function actionMicelania()
    {
        $header = header('Access-Control-Allow-Origin: *');
        
        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $default = "No";

            $uploadedImgPath = Yii::getAlias('@uploads') . "/imgs/micelania/";
            if (!file_exists( $uploadedImgPath )) {
                mkdir($uploadedImgPath, 0777, true);
            }

            $inicioImgName = "";
            $finalImgName = "";
            // If selected item is not Parafina
            if ($data["status"] != 1) {
                $inicioImg = \yii\web\UploadedFile::getInstanceByName('inicio');
                $inicioImgName = $uploadedImgPath . time() . "_inicio_" . $inicioImg->name;
                $inicioImg->saveAs( $inicioImgName );
    
                $finalImg = \yii\web\UploadedFile::getInstanceByName('final');
                $finalImgName = $uploadedImgPath . time() . "_final_" . $finalImg->name;
                $finalImg->saveAs( $finalImgName );
            }
            $pdfPath = Yii::getAlias('@uploads') . "/pdf/micelania/";
            if (!file_exists( $pdfPath )) {
                mkdir($pdfPath, 0777, true);
            }
            $pdfPath .= $data["name"] . " " . date("Y-m-d", strtotime($data["date"])) . ".pdf";
            
            $imgDirPath = Yii::getAlias('@uploads') . '/../images/themes/';
            $pdf = new FPDF();
            $pdf->setTitle('Frame Miscelaneas');

            $pdf->AliasNbPages();
            $pdf->AddPage('P', 'A4');
            $pdf->SetMargins('12', '7', '10');
            
            //set layout images
            $pdf->Image( $imgDirPath . 'top-left.png', 5, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 11.5, 42, 0.5);
            $pdf->Image( $imgDirPath . 'horizontals.png', 123, 11.5, 45, 0.5);
            $pdf->Image( $imgDirPath . 'top-right.png', 165, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'verticals.png', 5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'verticals.png', 204.5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'bottom-left.png', 5, 250, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 289.6, 125, 0.5);
            $pdf->Image( $imgDirPath . 'bottom-right.png', 165, 250, 40, 40);
            
            // set mark iamge
            $pdf->Image( $imgDirPath . 'mark.png', 75, 3.5, 60, 45);
            
            // Add fonts
            $pdf->SetTextColor(184, 142, 140);
            $pdf->AddFont('Montserrat','','Montserrat.php');
            $pdf->AddFont('NunitoExtraLight','','Nunito-ExtraLight.php');
            $pdf->AddFont('NunitoRegular','','Nunito-Regular.php');

            // set application name
            $pdf->SetFont('Montserrat', '', 30);
            $pdf->Cell(0, 91, utf8_decode('Misceláneas'), 0, 0, 'C');

            // $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('NunitoExtraLight', '', 18);
            $pdf->text(20, 67, $data["name"]);
            
            $pdf->SetFont('NunitoExtraLight', '', 13);
            $pdf->text(20, 74, "Procedimiento: " . (isset($data["procedimiento"]) ? $data["procedimiento"] : "") . (" (" . $data["date"] . ")"));
            $pdf->text(20, 80, utf8_decode("Hora Inicio y Conclusión: ") . date("H:i", strtotime($data["time"])) . " - " . date("H:i", strtotime($data["send_time"])));
            $pdf->text(20, 86, "Modificacion: " . (isset($data["modify"]) ? utf8_decode($data["modify"]) : $default));
            $pdf->text(20, 92, "Especialista: " . (isset($data["especialista"]) ? $data["especialista"] : $default));
            $pdf->text(20, 98, "Consentimiento actualizado: " . (isset($data["consentimiento"]) ? $data["consentimiento"] : ""));
            $pdf->text(20, 104, utf8_decode("Hora Inicio y Conclusión: ") . date("H:i", strtotime($data["time"])) . " - " . date("H:i", strtotime($data["send_time"])));
            $pdf->text(20, 110, "Alergia: " . (isset($data["alergia"]) ? utf8_decode($data["alergia"]) : ""));
            
            // if Parafina is selected
            if ( $data["status"] == 1) {
                $anot_gene = 118;
                $prod_adic = 124;
                $serv_furt = 130;
            } else {
                $pdf->text(20, 119, "FOTOS DEL PROCEDIMIENTO");
                $pdf->Image( $inicioImgName, 20, 121, 80, 60);
                $pdf->Image( $finalImgName, 106, 121, 80, 60);
    
                $pdf->SetFont('NunitoRegular', '', 11);
                $pdf->text(50, 186, "Inicial Pic");
                $pdf->text(136, 186, "Final Pic");
    
                $pdf->setFontSize(13);

                $anot_gene = 193;
                $prod_adic = 199;
                $serv_furt = 205;
                
                if ( $data["status"] == 2 ) {
                    $pdf->text(20, 193, utf8_decode("Tamaño de Molde: ") . (isset($data["tamno"]) ? utf8_decode($data["tamno"]) : "") );
                    
                    $anot_gene = 199;
                    $prod_adic = 205;
                    $serv_furt = 211;
                } else if ( $data["status"] == 3 ) {
                    $pdf->text(20, 193, "Sesion: " . (isset($data["enzimas_sesion"]) ? utf8_decode($data["enzimas_sesion"]) : "") );
                    $pdf->text(20, 199, "Base: " . (isset($data["enzimas_base"]) ? utf8_decode($data["enzimas_base"]) : "") );
                    if ($data["enzimas_tipo"]) {
                        $str = str_replace(" ", " / ", $data["enzimas_tipo"]);
                        $tops = explode("_", $str);
                        $enzimas_tipo = "";
                        foreach ($tops as $tipo) {
                            $enzimas_tipo .= " " . ucfirst($tipo);
                        }
                    }
                    $pdf->text(20, 205, "Tipo de Piel:" . (isset($data["enzimas_tipo"]) ? $enzimas_tipo : "") );

                    $anot_gene = 211;
                    $prod_adic = 217;
                    $serv_furt = 223;
                } else if ( $data["status"] == 4 ) {
                    $pdf->text(20, 193, "Area: " . (isset($data["laser_area"]) ? utf8_decode($data["laser_area"]) : "") );
                    $pdf->text(20, 199, "Sesion: " . (isset($data["laser_sesion"]) ? utf8_decode($data["laser_sesion"]) : "") );
                    $pdf->text(20, 205, "Base: " . (isset($data["laser_base"]) ? utf8_decode($data["laser_base"]) : "") );
                    if ($data["laser_forto"]) {
                        $str = str_replace(" ", " / ", $data["laser_forto"]);
                        $fortos = explode("_", $str);
                        $laser_forto = "";
                        foreach ($fortos as $foto) {
                            $laser_forto .= " " . ucfirst($foto);
                        }
                    }
                    $pdf->text(20, 211, "Fototipo de Piel: " . (isset($data["laser_forto"]) ? utf8_decode($laser_forto) : "") );
                    $pdf->text(20, 217, "Frecuencia: " . (isset($data["laser_frec"]) ? utf8_decode($data["laser_frec"]) : "") );
                    $pdf->text(20, 223, "Intensidad: " . (isset($data["laser_intent"]) ? utf8_decode($data["laser_intent"]) : "") );
                    $pdf->text(20, 229, "Disparos: " . (isset($data["laser_disparos"]) ? utf8_decode($data["laser_disparos"]) : "") );

                    $anot_gene = 235;
                    $prod_adic = 241;
                    $serv_furt = 247;
                }
            }
            $pdf->text(20, $anot_gene, "Anotaciones Generales: " . (isset($data["anotaciones_generales"]) ? utf8_encode($data["anotaciones_generales"]) : "") );
            $pdf->text(20, $prod_adic, "Productos Adicionales Comprados: " . (isset($data["product_adicion"]) ? utf8_encode($data["product_adicion"]) : ""));
            $pdf->text(20, $serv_furt, "Servicios Futuros: " . (isset($data["servicio_furtros"]) ? utf8_encode($data["servicio_furtros"]) : ""));

            $pdf->Output($pdfPath, 'F');

            $saveAry = array(
                'name' => $data["name"],
                'generate_date' => date("Y-m-d", strtotime($data["date"])),
                'generate_time' => date("H:i:s", strtotime($data["time"])),
                'send_time' => date("H:i:s", strtotime($data["send_time"])),
                'procedimiento' =>  (isset($data["procedimiento"]) ? $data["procedimiento"] : ""),
                'modificacion' => (isset($data["modify"]) ? $data["modify"] : $default),
                'especialista' => (isset($data["especialista"]) ? $data["especialista"] : ""),
                'consentimiento' => (isset($data["consentimiento"]) ? $data["consentimiento"] : ""),
                'alergia' => (isset($data["alergia"]) ? $data["alergia"] : ""),
                'tamno' => (isset($data["tamno"]) ? utf8_encode($data["tamno"]) : ""),
                'enzimas_sesion' => (isset($data["enzimas_sesion"]) ? utf8_encode($data["enzimas_sesion"]) : ""),
                'enzimas_base' => (isset($data["enzimas_base"]) ? utf8_encode($data["enzimas_base"]) : ""),
                'enzimas_tipo' => (isset($data["enzimas_tipo"]) ? utf8_encode($enzimas_tipo) : ""),
                'laser_area' => (isset($data["laser_area"]) ? utf8_encode($data["laser_area"]) : ""),
                'laser_sesion' => (isset($data["laser_sesion"]) ? utf8_encode($data["laser_sesion"]) : ""),
                'laser_base' => (isset($data["laser_base"]) ? utf8_encode($data["laser_base"]) : ""),
                'laser_forto' => (isset($data["laser_forto"]) ? utf8_encode($laser_forto) : ""),
                'laser_frec' => (isset($data["laser_frec"]) ? utf8_encode($data["laser_frec"]) : ""),
                'laser_intent' => (isset($data["laser_intent"]) ? utf8_encode($data["laser_intent"]) : ""),
                'laser_disparos' => (isset($data["laser_disparos"]) ? utf8_encode($data["laser_disparos"]) : ""),
                'anotaciones_generales' => (isset($data["anotaciones_generales"]) ? utf8_encode($data["anotaciones_generales"]) : ""),
                'product_adicion' => (isset($data["product_adicion"]) ? utf8_encode($data["product_adicion"]) : ""),
                'servicio_furtros' => (isset($data["servicio_furtros"]) ? utf8_encode($data["servicio_furtros"]) : ""),
                'status' => $data["status"],
                'pdflink' => $pdfPath,
                'inicio' => $inicioImgName,
                'final' => $finalImgName,
            );

            $model = Micelania::find()->where([ 'name' => $saveAry["name"], 'generate_date' => $saveAry["generate_date"] ])->one(); 
        
            if (!$model) {
                $model = new Micelania();
            }
        
            $model->name = $saveAry["name"];
            $model->generate_date = $saveAry["generate_date"];
            $model->generate_time = $saveAry["generate_time"];
            $model->send_time = $saveAry["send_time"];
            $model->procedimiento = $saveAry["procedimiento"];
            $model->modificacion = $saveAry["modificacion"];
            $model->especialista = $saveAry["especialista"];
            $model->consentimiento = $saveAry["consentimiento"];
            $model->alergia = $saveAry["alergia"];
            $model->tamno = $saveAry["tamno"];
            $model->enzimas_sesion = $saveAry["enzimas_sesion"];
            $model->enzimas_base = $saveAry["enzimas_base"];
            $model->enzimas_tipo = $saveAry["enzimas_tipo"];
            $model->laser_area = $saveAry["laser_area"];
            $model->laser_sesion = $saveAry["laser_sesion"];
            $model->laser_base = $saveAry["laser_base"];
            $model->laser_forto = $saveAry["laser_forto"];
            $model->laser_frec = $saveAry["laser_frec"];
            $model->laser_intent = $saveAry["laser_intent"];
            $model->laser_disparos = $saveAry["laser_disparos"];
            $model->anotaciones_generales = $saveAry["anotaciones_generales"];
            $model->product_adicion = $saveAry["product_adicion"];
            $model->servicio_furtros = $saveAry["servicio_furtros"];
            $model->pdflink = $saveAry["pdflink"];
            $model->inicio = $saveAry["inicio"];
            $model->final = $saveAry["final"];
            $model->status = $saveAry["status"];
            $model->save();
         
            exit(json_encode(["status" => "success", "message" => "Request is successfully submitted"]));
        }
        exit(json_encode(["status" => "error", "message" => "Request is failed"]));
    }

    public function actionMicroblading()
    {
        $header = header('Access-Control-Allow-Origin: *');
        
        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $default = "No";

            $uploadedImgPath = Yii::getAlias('@uploads') . "/imgs/microblading/";
            if (!file_exists( $uploadedImgPath )) {
                mkdir($uploadedImgPath, 0777, true);
            }
            
            /** Define uploaded images */
            $inicialImg1 = \yii\web\UploadedFile::getInstanceByName('inicial_1');
            $inicialImgName1 = $uploadedImgPath . time() . "_inicial_" . $inicialImg1->name;
            $inicialImg1->saveAs( $inicialImgName1 );
            
            $inicialImg2 = \yii\web\UploadedFile::getInstanceByName('inicial_2');
            $inicialImgName2 = $uploadedImgPath . time() . "_inicial_" . $inicialImg2->name;
            $inicialImg2->saveAs( $inicialImgName2 );
            
            $inicialImg3 = \yii\web\UploadedFile::getInstanceByName('inicial_3');
            $inicialImgName3 = $uploadedImgPath . time() . "_inicial_" . $inicialImg3->name;
            $inicialImg3->saveAs( $inicialImgName3 );
            
            $inicialImg4 = \yii\web\UploadedFile::getInstanceByName('inicial_4');
            $inicialImgName4 = $uploadedImgPath . time() . "_inicial_" . $inicialImg4->name;
            $inicialImg4->saveAs( $inicialImgName4 );
            
            $designImg1 = \yii\web\UploadedFile::getInstanceByName('design_1');
            $designImgName1 = $uploadedImgPath . time() . "_design_" . $designImg1->name;
            $designImg1->saveAs( $designImgName1 );

            $designImg2 = \yii\web\UploadedFile::getInstanceByName('design_2');
            $designImgName2 = $uploadedImgPath . time() . "_design_" . $designImg2->name;
            $designImg2->saveAs( $designImgName2 );
            
            $finalImg1 = \yii\web\UploadedFile::getInstanceByName('final_1');
            $finalImgName1 = $uploadedImgPath . time() . "_final_" . $finalImg1->name;
            $finalImg1->saveAs( $finalImgName1 );
            
            $finalImg2 = \yii\web\UploadedFile::getInstanceByName('final_2');
            $finalImgName2 = $uploadedImgPath . time() . "_final_" . $finalImg2->name;
            $finalImg2->saveAs( $finalImgName2 );
            
            $finalImg3 = \yii\web\UploadedFile::getInstanceByName('final_3');
            $finalImgName3 = $uploadedImgPath . time() . "_final_" . $finalImg3->name;
            $finalImg3->saveAs( $finalImgName3 );
            
            $finalImg4 = \yii\web\UploadedFile::getInstanceByName('final_4');
            $finalImgName4 = $uploadedImgPath . time() . "_final_" . $finalImg4->name;
            $finalImg4->saveAs( $finalImgName4 );
            
            
            $pdfPath = Yii::getAlias('@uploads') . "/pdf/microblading/";
            if (!file_exists( $pdfPath )) {
                mkdir($pdfPath, 0777, true);
            }
            $pdfPath .= $data["name"] . " " . date("Y-m-d", strtotime($data["date"])) . ".pdf";
            
            $imgDirPath = Yii::getAlias('@uploads') . '/../images/themes/';
            $pdf = new FPDF();
            $pdf->setTitle('Frame Miscelaneas');

            $pdf->AliasNbPages();
            $pdf->AddPage('P', 'A4');
            $pdf->SetMargins('12', '7', '10');
            
            //set layout images
            $pdf->Image( $imgDirPath . 'top-left.png', 5, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 11.5, 42, 0.5);
            $pdf->Image( $imgDirPath . 'horizontals.png', 123, 11.5, 45, 0.5);
            $pdf->Image( $imgDirPath . 'top-right.png', 165, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'verticals.png', 5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'verticals.png', 204.5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'bottom-left.png', 5, 250, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 289.6, 125, 0.5);
            $pdf->Image( $imgDirPath . 'bottom-right.png', 165, 250, 40, 40);
            
            // set mark iamge
            $pdf->Image( $imgDirPath . 'mark.png', 75, 3.5, 60, 45);
            
            // Add fonts
            $pdf->SetTextColor(184, 142, 140);
            $pdf->AddFont('Montserrat','','Montserrat.php');
            $pdf->AddFont('NunitoExtraLight','','Nunito-ExtraLight.php');
            $pdf->AddFont('NunitoRegular','','Nunito-Regular.php');

            // set application name
            $pdf->SetFont('Montserrat', '', 30);
            $pdf->Cell(0, 91, utf8_decode('Microblading'), 0, 0, 'C');

            // $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('NunitoExtraLight', '', 18);
            $pdf->text(20, 67, $data["name"]);
            
            $pdf->SetFont('NunitoExtraLight', '', 13);
            $pdf->text(20, 74, "Procedimiento: " . (isset($data["procedimiento"]) ? $data["procedimiento"] : ""));
            $pdf->text(20, 80, "Modificacion: " . (isset($data["modify"]) ? utf8_decode($data["modify"]) : $default));
            $pdf->text(20, 86, "Fecha procedimiento: " . (isset($data["date"]) ? $data["date"] : ""));
            $pdf->text(20, 92, utf8_decode("Hora Inicio y Conclusión: ") . date("H:i", strtotime($data["time"])) . " - " . date("H:i", strtotime($data["send_time"])));
            $pdf->text(20, 98, "Consentimiento actualizado: " . (isset($data["consentimiento"]) ? $data["consentimiento"] : ""));
            $pdf->text(20, 104, "Condiciones previas: " . (isset($data["condiciones_previas"]) ? $data["condiciones_previas"] : $default));
            $pdf->text(20, 110, "Tiempo: " . (isset($data["tiempo"]) ? $data["tiempo"] : ""));
            $pdf->text(20, 116, "Color anterior: " . (isset($data["color_anterior"]) ? $data["color_anterior"] : ""));
            $pdf->text(20, 122, "Pigmentos: " . (isset($data["pigmentos"]) ? $data["pigmentos"] : ""));
            $pdf->text(20, 128, "Proporcion: " . (isset($data["proporcion"]) ? $data["proporcion"] : ""));
            $pdf->text(20, 134, utf8_decode("Color máscara: ") . (isset($data["mascar"]) ? $data["mascar"] : ""));
            $pdf->text(20, 140, "Sangrado: " . (isset($data["sangrado"]) ? $data["sangrado"] : ""));
            $pdf->text(20, 146, "Shading: " . (isset($data["shading"]) ? $data["shading"] : ""));
            $pdf->text(20, 152, "Anotaciones generales: " . (isset($data["anotaciones_generales"]) ? utf8_decode($data["anotaciones_generales"]) : "") );
            $pdf->text(20, 158, "Productos mantenimiento comprados: " . (isset($data["productos_mtto"]) ? utf8_decode($data["productos_mtto"]) : "") );
            $pdf->text(20, 164, "Productos adicionales comprados: " . (isset($data["product_adicion"]) ? utf8_decode($data["product_adicion"]) : ""));
            $pdf->text(20, 170, "Otros servicios vendidos: " . (isset($data["servicio_furtros"]) ? utf8_decode($data["servicio_furtros"]) : ""));

            $pdf->text(20, 180, "FOTOS DEL PROCEDIMIENTO");
            $pdf->text(20, 187, "Inicial Pics");
            $pdf->Image( $inicialImgName1, 28, 190, 72, 54);
            $pdf->Image( $inicialImgName2, 111, 190, 72, 54);

            $pdf->AliasNbPages();
            $pdf->AddPage('P', 'A4');
            $pdf->SetMargins('12', '7', '10');
            
            //set layout images
            $pdf->Image( $imgDirPath . 'top-left.png', 5, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 11.5, 142, 0.5);
            $pdf->Image( $imgDirPath . 'top-right.png', 165, 11.5, 40, 40);
            $pdf->Image( $imgDirPath . 'verticals.png', 5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'verticals.png', 204.5, 50, 0.5, 240);
            $pdf->Image( $imgDirPath . 'bottom-left.png', 5, 250, 40, 40);
            $pdf->Image( $imgDirPath . 'horizontals.png', 45, 289.6, 125, 0.5);
            $pdf->Image( $imgDirPath . 'bottom-right.png', 165, 250, 40, 40);

            $pdf->Image( $inicialImgName3, 28, 30, 72, 54);
            $pdf->Image( $inicialImgName4, 111, 30, 72, 54);

            $pdf->text(20, 90, "Design Pics");
            $pdf->Image( $designImgName1, 28, 94, 72, 54);
            $pdf->Image( $designImgName2, 111, 94, 72, 54);
            
            $pdf->text(20, 154, "Final Pics");
            $pdf->Image( $finalImgName1, 28, 158, 72, 54);
            $pdf->Image( $finalImgName2, 111, 158, 72, 54);
            $pdf->Image( $finalImgName3, 28, 214, 72, 54);
            $pdf->Image( $finalImgName4, 111, 214, 72, 54);
            
            $pdf->Output($pdfPath, 'F');

            $model = Microblading::find()->where([ 'name' => $data["name"], 'generate_date' => date("Y-m-d", strtotime($data["date"])) ])->one(); 
        
            if (!$model) {
                $model = new Microblading();
            }
        
            $model->name = $data["name"];
            $model->generate_date = date("Y-m-d", strtotime($data["date"]));
            $model->generate_time = $data["time"];
            $model->send_time = $data["send_time"];
            $model->procedimiento = (isset($data["procedimiento"]) ? ($data["procedimiento"]) : "");
            $model->modificacion = (isset($data["modify"]) ? ($data["modify"]) : $default);
            $model->tiempo = (isset($data["tiempo"]) ? $data["tiempo"] : "");
            $model->consentimiento = (isset($data["consentimiento"]) ? ($data["consentimiento"]) : "");
            $model->color_anterior = (isset($data["color_anterior"]) ? ($data["color_anterior"]) : "");
            $model->condiciones_previas = (isset($data["condiciones_previas"]) ? ($data["condiciones_previas"]) : "");
            $model->pigmentos = (isset($data["pigmentos"]) ? $data["pigmentos"] : "");
            $model->proporcion = (isset($data["proporcion"]) ? $data["proporcion"] : "");
            $model->sangrado = (isset($data["sangrado"]) ? $data["sangrado"] : "");
            $model->shading = (isset($data["shading"]) ? ($data["shading"]) : "");
            $model->mascar = (isset($data["mascar"]) ? ($data["mascar"]) : "");
            $model->anotaciones_generales = (isset($data["anotaciones_generales"]) ? ($data["anotaciones_generales"]) : "");
            $model->productos_mtto = (isset($data["productos_mtto"]) ? ($data["productos_mtto"]) : "");
            $model->product_adicion = (isset($data["product_adicion"]) ? ($data["product_adicion"]) : "");
            $model->servicio_furtros = (isset($data["servicio_furtros"]) ? ($data["servicio_furtros"]) : "");
            $model->pdflink = $pdfPath;
            $model->inicial_1 = $inicialImgName1;
            $model->inicial_2 = $inicialImgName2;
            $model->inicial_3 = $inicialImgName3;
            $model->inicial_4 = $inicialImgName4;
            $model->design_1 = $designImgName1;
            $model->design_2 = $designImgName2;
            $model->final_1 = $finalImgName1;
            $model->final_2 = $finalImgName2;
            $model->final_3 = $finalImgName3;
            $model->final_4 = $finalImgName4;
            $model->save();
         
            exit(json_encode(["status" => "success", "message" => "Request is successfully submitted"]));
        }
        exit(json_encode(["status" => "error", "message" => "Request is failed"]));
    }
}