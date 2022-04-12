<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use Image;

class FeedController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function create (Request $request)
    {
        //POST api/feed (type=text/photo, body, photo)
        $array = ['message' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png']; //Formatos de imagens que serão aceitos

        $type  = $request->input('type');
        $body  = $request->input('body');
        $photo = $request->file('photo');

        if ($type){
            switch ($type){
                case 'text':
                    if (!$body){
                        $array['message'] = 'Texto não enviado!';
                        return $array;
                    }
                break;

                case 'photo':
//                    dd($request->toArray()['photo']);
                    //dd($photo);
                    if ($photo) {
                        //dd($photo);
                        if (in_array($photo->getClientMimeType(), $allowedTypes)) {

                            $filename = md5(time() . rand(0, 9999)) . '.jpg';

                            $destPath = public_path('/media/uploads');

                            $img = Image::make($photo->path())
                                ->resize(800, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                })
                                ->save($destPath . '/' . $filename);

                            $body = $filename;

                            $array['url'] = url('/media/uploads/' . $filename);
                            $array['message'] = 'Post realizado com sucesso!';
                        } else {
                            $array['message'] = 'Arquivo não suportado.';
                            return $array;
                        }
                    }
                break;
                default:
                    $array['message'] = 'Tipo de postagem inexistente';
                    return $array;
                break;

            }

            if ($body){
                $newPost                = new Post();
                $newPost->id_user       = $this->loggedUser['id'];
                $newPost->type          = $type;
                $newPost->created_at    = date('Y-m-d H:i:s');
                $newPost->body          = $body;
                $newPost->save();

                $array['message'] = 'Dados registrados com sucesso!';
                return $array;

            }

        }else{
            $array['message'] = 'Dados não enviados';
        }

        return $array;
    }
}
