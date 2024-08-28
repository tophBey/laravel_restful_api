<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use Auth;
use App\Models\Contact;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ContactController extends Controller
{

    private function findContactById(int $id) : Contact
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'error' => [
                    'message' => ['not found']
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    public function create(ContactCreateRequest $request) :JsonResponse{

        $data = $request->validated();

        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);

    }


    public function get(int $id) : ContactResource{
       
        $contact = $this->findContactById($id);
        return new ContactResource($contact);
    }


    public function update(int $id, ContactUpdateRequest $request) : ContactResource {

        $data = $request->validated();
        $contact = $this->findContactById($id);

        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id) : JsonResponse{
        $user = Auth::user();
        $contact = $this->findContactById($id);

        $contact->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }


    public function search(Request $request) : ContactCollection{

        $user = Auth::user();

        $page = $request->input('page', 1);
        $size = $request->input('size',10); //default 10 klo gk ada


        $contacts = Contact::query()->where('user_id','=', $user->id);

        $contacts = $contacts->where(function(Builder $builder) use ($request){

            $name = $request->input('name');

            if($name){
                $builder->where(function(Builder $builder) use ($name){
                    $builder->orWhere('first_name', 'like', '%'. $name . '%');
                    $builder->orWhere('last_name', 'like', '%' .$name . '%');

                });
            }

            $email = $request->input('email');
            if($email){
                $builder->where('email', 'like', '%' . $email . '%');
            }


            $phone = $request->input('phone');
            if($phone){
                $builder->where('phone','like', '%'. $phone . '%');
            }
        });

        $contacts =  $contacts->paginate(perPage: $size, page: $page);


        return new ContactCollection($contacts);
    }
}
