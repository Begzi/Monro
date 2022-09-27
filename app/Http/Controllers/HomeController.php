<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Ilovepdf\Ilovepdf;
use Api2Pdf\Api2Pdf;

class HomeController extends Controller
{
    
    public function home(Request $request)
    {
        return view('welcome');
    }
    
    public function parse(Request $request)
    {
        $rules = [    //Как по мне круче этот
            'URL' => 'required|min:5',
        ];

        $messages = [
            'URL.required' => 'Заполните поле',
            'URL.min' => 'Не может быть ссылка меньше 5 символов',
        ];
        $validator = Validator::make($request->all(), $rules, $messages)->validate();
        // $pdf =  file_get_contents($request['URL']);
        // dd($pdf);
        $file_full_path = 'public/'; // Соответствует storage/app/public/
        $file_name = 'file.pdf';
        Storage::disk('local')->put($file_full_path  . $file_name, file_get_contents($request['URL']), 'public'); 



        $apiClient = new Api2Pdf('384b9c67-659e-42e1-bbfb-310c49410616');

        $result = $apiClient->libreOfficePdfToHtml($request['URL']);
        // $result = $apiClient->libreOfficePdfToHtml('https://psv4.userapi.com/c235031/u137607046/docs/d59/116f7f647def/file2.pdf?extra=_xZUUOeVsBgchxu00vHj1Ay7JRnzbTIPlvGQtAgQ2Kuc5h0RcdnzC07nA9CX808QrwMEfJ7UljEqONIlRAGIP2X3j5UGGfK8PBSoxbeNyfOlf79BnjpLAwro5-DpCa6C8cJGHkqzXs7KL6jHt');
        // https://psv4.userapi.com/c235031/u137607046/docs/d29/8132de26a41b/resume_Begzi_Echis.pdf?extra=KpXrIVrrflKUCT4NZ2S3EZJvo0TuBjMi36uh3bj-EjRlOi2GqNmIEMI-x9TOQlKbD-lgClxs2dZAIhXYeMotY51vdbSYcs2NvqlbEkdkyd1mghbFL6wMmKFBUvaY285F9Keu7eDG-eJyLObt
        // https://psv4.userapi.com/c235031/u137607046/docs/d59/116f7f647def/file2.pdf?extra=_xZUUOeVsBgchxu00vHj1Ay7JRnzbTIPlvGQtAgQ2Kuc5h0RcdnzC07nA9CX808QrwMEfJ7UljEqONIlRAGIP2X3j5UGGfK8PBSoxbeNyfOlf79BnjpLAwro5-DpCa6C8cJGHkqzXs7KL6jH
        // https://psv4.userapi.com/c235031/u137607046/docs/d22/f7b70679baf0/file3.pdf?extra=L2sj6lESOc-9J_1q717FXu7Yts28h08Md7gcM-UHdwrbij6sqloEqFiGRHkagSBWeat5Hclb2hK12YIha4x1uJMi-iTLC9QTTKXpdzg4T_lClPVUs6VUipePyNrryi78Bnrr8IVn06sZW7xI
        $file_full_path = 'public/'; // Соответствует storage/app/public/
        $file_name = 'file.html';
        Storage::disk('local')->put($file_full_path  . $file_name, file_get_contents($result->getFile()), 'public'); 


        $html = file_get_contents(asset('/storage/file.html'), "r");
        $text = substr($html, strpos($html, '<h1></h1>') + 11, strlen($html));

        $array_text = explode("\r\n", $text);
        $main_array=[];
        $skills_array=[];
        $experience_array=[];
        $k_for_next = 0;

        for ($i = 0; $i < count($array_text); $i++){
            $line = (cut_beg_end($array_text[$i]));
            if($line == 'Желаемая должность и зарплата' or $line == 'Специализации:' or strlen($line) < 10 or
                strpos($array_text[$i], 'style="page-break-before:always; "><')  !== false )
                {
                continue;
            }
            elseif (strpos($line, 'Опыт работы') !== false){
                $k_for_next = $i;
                break;

            }
            if (strpos($line, '<b>') !== false){
                $line = cut_beg_end($line);
            }
            array_push($main_array, $line);
        }


        array_push($experience_array, cut_beg_end($array_text[$k_for_next]));
        $check = true;
        for ($i = $k_for_next + 1; $i < count($array_text); $i++){
            if(strpos($array_text[$i], '<h1>') !== false  or strpos($array_text[$i], 'Резюме обновлено') !== false or
                strpos($array_text[$i], 'style="page-break-before:always; "><')  !== false )
            {
                continue;
            }
            elseif (strpos($array_text[$i], 'Образование') !== false){
                $k_for_next = $i;
                break;

            }
            else{
                if (strpos($array_text[$i], '<b>')){
                    $line1 = (cut_beg_end($array_text[$i]));
                    $line1 = (cut_beg_end($line1));
                    $line2 = (cut_beg_end($array_text[$i + 1]));
                    array_push($experience_array, 'Компания: ' . $line1);
                    array_push($experience_array, 'Должность: ' . $line2);
                    $i++;
                    $check = false;
                    continue;
                }
                elseif ($check){
                    continue;
                }
                else{
                    $line = (cut_beg_end($array_text[$i]));

                    if (strpos($line, '<b>')){
                        $line = cut_beg_end($line);
                    }
                    array_push($experience_array, $line);

                }

            }
        }

        array_push($main_array, 'Образование: ' . cut_beg_end($array_text[$k_for_next + 2]));

        for($i = $k_for_next + 3; $i < count($array_text); $i++){
            $line = (cut_beg_end($array_text[$i]));
            if(strlen($line) < 10 or strpos($line, '<h1>') !== false  or strpos($line, 'Резюме обновлено') !== false or
                strpos($array_text[$i], 'style="page-break-before:always; "><')  !== false )
            {
                continue;
            }
            elseif (strpos($line, 'Ключевые навыки') !== false){
                $k_for_next = $i;
                break;

            }
            if (strpos($line, '<b>') !== false){
                $line = cut_beg_end($line);
                if (strpos($array_text[$i + 1], '<b>') !== false){
                    $line2 = cut_beg_end($array_text[$i + 1]);
                    $line2 = cut_beg_end($line2);
                    $line = $line . ' ' . $line2;
                    $i++;
                }
            }
            array_push($skills_array, $line);

        }

        for($i = $k_for_next + 1; $i < count($array_text); $i++){
            $line = 'Ключевые навыки: ' . (cut_beg_end($array_text[$i]));
            if(strpos($line, 'Знание языков') !== false or strpos($line, 'Навыки') !== false or strlen($line) < 10 or strpos($line, '<h1>') !== false  or strpos($line, 'Резюме обновлено') !== false or
                strpos($array_text[$i], 'style="page-break-before:always; "><')  !== false )
            {
                continue;
            }
            elseif (strpos($line, 'Дополнительная информация') !== false){
                $k_for_next = $i;
                break;

            }
            if (strpos($line, '<b>') !== false){
                $line = cut_beg_end($line);
            }
            array_push($skills_array, $line);

        }


        for($i = $k_for_next + 2; $i < count($array_text); $i++){
            $line = 'Дополнительная информация: ' . (cut_beg_end($array_text[$i]));
            if(strlen($line) < 10 or strpos($line, '<h1>') !== false  or strpos($line, 'Резюме обновлено') !== false or
                strpos($array_text[$i], 'style="page-break-before:always; "><')  !== false )
            {
                continue;
            }
            elseif (strpos($line, 'История общения с кандидатом') !== false){
                $k_for_next = $i + count($array_text);
                break;

            }
            if (strpos($line, '<b>') !== false){
                $line = cut_beg_end($line);
            }
            array_push($main_array, $line);

        }

        if ($k_for_next > count($array_text)){

            for($i = $k_for_next - count($array_text) + 1; $i < count($array_text); $i++){
                $line = 'История общения с кандидатом: ' . (cut_beg_end($array_text[$i]));
                if(strlen($line) < 10 or strpos($line, '<h1>') !== false  or strpos($line, 'Резюме обновлено') !== false or
                strpos($array_text[$i], 'style="page-break-before:always; "><')  !== false )
                {
                    continue;
                }
                if (strpos($line, '<b>') !== false){
                    $line = cut_beg_end($line);
                }
                array_push($main_array, $line);

            }

        }

        return view('welcome', compact('main_array', 'experience_array', 'skills_array'));
    }
}

function cut_beg_end($str){

    return substr($str, 3, strlen($str) - 7);

}