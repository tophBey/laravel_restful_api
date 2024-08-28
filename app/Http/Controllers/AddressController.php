<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\AddressResource;
class AddressController extends Controller
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

    private function findAddressById(int $idAddress, Contact $contact) : Address{
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();

        if (!$address) {
            throw new HttpResponseException(response()->json([
                'error' => [
                    'message' => ['not found']
                ]
            ])->setStatusCode(404));
        }
        return $address;

    }


    public function create(int $idContack, AddressRequest $request): JsonResponse {
        
        $contact = $this->findContactById($idContack);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }


    public function get(int $idContact, int $idAddress) :AddressResource{

        $contact = $this->findContactById($idContact);

        $address = $this->findAddressById($idAddress, $contact);

        return new AddressResource($address);
    }


    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource {

        $data = $request ->validated();
        $contact = $this->findContactById($idContact);
        $address = $this->findAddressById($idAddress, $contact);

        $address->fill($data);
        $address->save();

        return new AddressResource($address);

    }

    public function delete(int $idContact, int $idAddress) : JsonResponse {
        $contact = $this->findContactById($idContact);
        $address = $this->findAddressById($idAddress, $contact);
        $address->delete();
        return response()->json([
            "data" => true
        ]);
    }

    public function list(int $idContact) : JsonResponse{


        $contact = $this->findContactById($idContact);

        $address = Address::where('contact_id', $contact->id)->get();

        return (AddressResource::collection($address))->response()->setStatusCode(200);


    }
}
