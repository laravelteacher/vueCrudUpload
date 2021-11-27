<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\uploadimage;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
   // show all datas that in database in Template
    public function indexx()
    {
        return uploadimage::latest()->paginate(1);
    }
// save or store all new data
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'photo' => 'required'
        ]);
         // convert new image to data for save in database and save in Folder
        if($request->photo){

            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/').$name);
            $request->merge(['photo' => $name]);
           
        }
        uploadimage::create([
            'photo' => $request->photo,
            'name' => $request['name'],
        ]);
       // Upload::create($request->all());

        return ['message' => 'Success'];

    }

// this function is for Update image or crud
    public function update(Request $request, $id)
    {
        $upload = uploadimage::find($id);

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'photo' => 'required'
        ]);

        $currentPhoto = $upload->photo;

        if($request->photo != $currentPhoto){

            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/').$name);
            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/').$currentPhoto;

            if(file_exists($userPhoto)){

                @unlink($userPhoto);
                
            }
           
        }

        $upload->update($request->all());

        return ['message' => 'Success'];
    }
// remove data from database and remive Image from img folder
    public function destroy($id)
    {
        $upload = uploadimage::findOrFail($id);

        $upload->delete();

        $currentPhoto = $upload->photo;

        $userPhoto = public_path('img/').$currentPhoto;

        if(file_exists($userPhoto)) {

            @unlink($userPhoto);
                
        }

        return [
         'message' => 'Photo deleted successfully'
        ];
    }
}