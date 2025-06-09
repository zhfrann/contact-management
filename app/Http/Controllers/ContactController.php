<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    private function getContact(User $user, int $idContact): Contact | HttpResponseException
    {
        $contact = Contact::query()->where('id', '=', $idContact)->where('user_id', '=', $user->id)->first();
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

    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \App\Models\Contact $contact */
        // $contact = new Contact($data);
        // $contact->user_id = $user->id;
        // $contact->save();

        // Or
        $contact = $user->contacts()->create($data);

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $id);

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $id);

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $id);

        $contact->delete();
        return response()->json([
            'data' => true
        ], 200);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 15);

        $contacts = Contact::query()->where('user_id', '=', $user->id);
        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', "%$name%");
                    $builder->orWhere('last_name', 'like', "%$name%");
                });
            }

            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', "%$email%");
            }

            $phone = $request->input('phone');
            if ($phone) {
                $builder->where('phone', 'like', "%$phone%");
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
