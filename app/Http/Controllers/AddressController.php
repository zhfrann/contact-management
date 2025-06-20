<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact): Contact | HttpResponseException
    {
        $contact = Contact::query()->where('user_id', '=', $user->id)->where('id', '=', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        'message' => [
                            'Contact not found'
                        ]
                    ]
                ], 404)
            );
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress): Address | HttpResponseException
    {
        $address = Address::query()->where('id', '=', $idAddress)->where('contact_id', '=', $contact->id)->first();
        if (!$address) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        'message' => [
                            'Address not found'
                        ]
                    ]
                ], 404)
            );
        }

        return $address;
    }

    public function create(int $idContact, AddressCreateRequest $requets): JsonResponse
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $idContact);

        $data = $requets->validated();

        /** @var \App\Models\Address $address */
        // $address = new Address($data);
        // $address->contact_id = $contact->id;
        // $address->save();

        // Or
        $address = $contact->addresses()->create($data);

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function list(int $idContact): JsonResponse
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $idContact);

        $addresses = Address::query()->where('contact_id', '=', $contact->id)->get();
        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }
}
